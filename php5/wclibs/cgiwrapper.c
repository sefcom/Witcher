

#include <unistd.h>
#include <string.h>     /* For the real memset prototype.  */
#include <signal.h>
#include <stdio.h>
#include <errno.h>
#include <limits.h>
#include <stdlib.h>
#include <stdbool.h>
#include <dirent.h>

#include <sys/socket.h>

#include <sys/types.h>
#include <sys/shm.h>
#include <sys/wait.h>
#include <sys/stat.h>

#include "cgiwrapper.h"

#define __USE_GNU
#include <dlfcn.h>

#define MAPSIZE 65536
#define TRACE_SIZE 128*(1024*1024) // X * megabytes

#define SHM_ENV_VAR         "__AFL_SHM_ID"

#define STDIN_FILENO 0

static int last = 0;
static int op = 0;

//static FILE *myfp = NULL;
//static FILE *tout = NULL;

static unsigned char *afl_area_ptr = NULL;
//static unsigned char *afl_tracer = NULL;

unsigned int afl_forksrv_pid = 0;
static unsigned char afl_fork_child;

#define FORKSRV_FD 198
#define TSL_FD (FORKSRV_FD - 1)

#define MAX_VARIABLES 1024
char *variables[3][MAX_VARIABLES];
unsigned char variables_used[3][MAX_VARIABLES];
int variables_ptr[3]={0,0,0};

char *traceout_fn, *traceout_path;

int nextVar2_is_a_var = -1;
bool wc_extra_instr = true;

static bool start_tracing = false;

static char* env_vars[2] = { "HTTP_COOKIE","QUERY_STRING"};

char *main_filename;
char session_id[40];
int saved_session_size = 0;
//static int (*lib_setenv)(const char *envname, const char *envval, int overwrite) = NULL;

int trace[TRACE_SIZE];
int trace_index = 0;

int pipefds[2];

int top_pid=0;
//int _setenv(const char *envname, const char *envval, int overwrite){
//  lib_setenv = dlsym(RTLD_NEXT, "setenv");
//
//  setenv(envname, envval, overwrite);
//  return lib_setenv(envname, envval, overwrite);
//
//}
struct rollback_entry {
    char* varname;
    bool rolledback;
    int matchcnt;
    int bitmapLoc;
    struct rollback_entry *next;
};

struct test_process_info {
    int initialized;
    int afl_id;
    int port;
    int reqr_process_id;
    int process_id;
    char error_type[20]; /* SQL, Command */
    char error_msg[100];
    bool capture;
};

struct rollback_entry *rollback_ll[3];

void add_var_to_rollback(char* varname, int matchcnt, int bitmapLoc, int which){

  struct rollback_entry * v = malloc(sizeof(struct rollback_entry));

  v->varname      = strdup(varname);
  v->rolledback = false;
  v->matchcnt = matchcnt;
  v->bitmapLoc = bitmapLoc;

  if (rollback_ll[which]){
    v->next = rollback_ll[which];
    rollback_ll[which]= v;
  } else {
    rollback_ll[which] = v;
    v->next = NULL;
  }

}

char* replace_char(char* str, char find, char replace){
    char *current_pos = strchr(str,find);
    while (current_pos){
        *current_pos = replace;
        current_pos = strchr(current_pos,find);
    }
    return str;
}

off_t fsize_tbd(const char *filename) {
    struct stat st;
    printf("stat reskt = %d %s \n", stat(filename, &st), filename);
    if (stat(filename, &st) == 0)
        return st.st_size;
    printf( "Value of errno: %d %d\n", errno, getuid());
    int errnum = errno;
    printf("Error opening file: %s\n", strerror( errnum ));
    return -1;
}

int fsize(const char *filename)
{

    FILE *fp = NULL;
    long off;
    char cwd[PATH_MAX];
    fp = fopen(filename, "r");
    if (fp == NULL)
    {
        printf("failed to fopen %s\n", filename);
        //exit(EXIT_FAILURE);
    }

    if (fseek(fp, 0, SEEK_END) == -1)
    {
        printf("failed to fseek %s\n", filename);
        //exit(EXIT_FAILURE);
    }

    off = ftell(fp);
    if (off == (long)-1)
    {
        printf("failed to ftell %s\n", filename);
        //exit(EXIT_FAILURE);
    }

    printf("[*] fseek_filesize - file: %s, size: %ld\n", filename, off);

    if (fclose(fp) != 0)
    {
        printf("failed to fclose %s\n", filename);
        //exit(EXIT_FAILURE);
    }
    return off;
}

static void afl_forkserver() {

    static unsigned char tmp[4];

    //cgi_get_shm_mem();

    if (!afl_area_ptr) return;
    if (write(FORKSRV_FD + 1, tmp, 4) != 4) return;

    afl_forksrv_pid = getpid();

    /* All right, let's await orders... */
    int claunch_cnt = 0;
    while (1) {

        pid_t child_pid = -1;
        int status, t_fd[2];

        /* Whoops, parent dead? */
        //printf("\t\tawaiting orders %d\n", getpid());
        if (read(FORKSRV_FD, tmp, 4) != 4) exit(2);

        /* Establish a channel with child to grab translation commands. We'll
           read from t_fd[0], child will write to TSL_FD. */

        if (pipe(t_fd) || dup2(t_fd[1], TSL_FD) < 0) exit(3);
        printf("\t\testablished cahnnel with child %d\n", getpid());
        close(t_fd[1]);
        claunch_cnt ++;
        child_pid = fork();

        fflush(stdout);
        if (child_pid < 0) exit(4);

        if (!child_pid) {  // child_pid == 0, i.e., in child

            /* Child process. Close descriptors and run free. */
            printf("\t\t\tlaunch cnt = %d Child pid == %d, but current pid = %d\n", claunch_cnt, child_pid, getpid());
            fflush(stdout);
            afl_fork_child = 1;
            close(FORKSRV_FD);
            close(FORKSRV_FD + 1);
            close(t_fd[0]);
            return;

        }

        /* Parent. */

        close(TSL_FD);

        //printf("\t\tCheck for child status from Parent %d for %d \n", getpid(), child_pid);

        if (write(FORKSRV_FD + 1, &child_pid, 4) != 4) {
            printf("\t\tExiting Parent %d with 5\n", child_pid);
            exit(5);
        }

        /* Collect translation requests until child dies and closes the pipe. */

        //afl_wait_tsl(cpu, t_fd[0]);

        /* Get and relay exit status to parent. */
        int waitedpid = waitpid(child_pid, &status, 0);
        if (waitedpid < 0) {
            printf("\t\tExiting Parent %d with 6\n", child_pid);
            exit(6);
        }

        //printf("\t\tStats from child (%d) is %d \n", child_pid, status);
        if (write(FORKSRV_FD + 1, &status, 4) != 4) {
            printf("\t\tExiting child %d with 7\n", child_pid);
            exit(7);
        }
        //printf("\t\tEnd of Parent loop %d %d claunch cnt = %d \n", child_pid, getpid(), claunch_cnt);

    }
}

void load_variables(char *str, int var_type){
    char * tostr = strdup(str);
    char * end_str;
    char * token = strtok_r(tostr, "&", &end_str);

    while( token != NULL ) {
      char *end_token;
      char *dup_token = strdup(token);
      char *subtok = strtok_r(dup_token, "=", &end_token);

      if (subtok!= NULL && variables_ptr[var_type] < MAX_VARIABLES){
          char *first_part = strdup(subtok);
          subtok = strtok_r(NULL, "=", &end_token);
          int len = strlen(first_part) ;
          printf("[WC] %s %d\n", first_part, len);
          if (len > 2){
              bool unique=true;
              for (int i=0; i < variables_ptr[var_type];i++){
                  if (strcmp(first_part, variables[var_type][i]) == 0){
                      unique=false;
                      break;
                  }
              }
              if (unique){
                  int cur_ptr = variables_ptr[var_type];
                  variables[var_type][cur_ptr] = (char *) malloc(len + 1) ;
                  strncpy(variables[var_type][cur_ptr], first_part, len);
                  variables[var_type][cur_ptr][len] = '\x00';
                  variables_used[var_type][cur_ptr] = 0;
                  variables_ptr[var_type]++;
              }
          }
          token = strtok_r(NULL, "&", &end_str);
      } else {
          break;
      }

   }

}

char* format_to_json(char *str){

    char * tostr = strdup(str);
    char *outstr;
    outstr = (char*) malloc(strlen(str) + 1024);
    char * end_str;
    char * token = strtok_r(tostr, "&", &end_str);
    outstr = strcat(outstr, "{");

    while( token != NULL ) {
      char jsonEleOut[strlen(str)+7];
      char *end_token;
      char *dup_token = strdup(token);
      char *first_part = strtok_r(dup_token, "=", &end_token);
      char *sec_part = strtok_r(NULL, "=", &end_token);
      if (sec_part) {
          sprintf(jsonEleOut,"\"%s\":\"%s\",", first_part, sec_part);
      } else {
          sprintf(jsonEleOut,"\"%s\":\"\",", first_part);
      }
      outstr = strcat(outstr, jsonEleOut);
      token = strtok_r(NULL, "&", &end_str);
   }

   outstr[strlen(outstr)-1] = '}';
   outstr[strlen(outstr)] = '\x00';

   return outstr;
}

void setup_cgi_env(){
    printf("\e[31mStarting SETUP_CGI_ENV\e[0m\n");
    setenv("DOCUMENT_ROOT","/app", 1); //might be important if your cgi read/writes there
    setenv("HTTP_REDIRECT_STATUS","1",1);

//    //Not really sure if any cgi will really use these, but a couple of setenv don't cost too much:
    setenv("HTTP_ACCEPT", "*/*", 1);
    setenv("GATEWAY_INTERFACE", "CGI/1.1", 1);
    setenv("PATH", "/usr/bin:/tmp:/app", 1); //HTTP URL PATH
    setenv("REQUEST_METHOD", "POST", 1); //Usually GET or POST
    setenv("REMOTE_ADDR","127.0.0.1",1);
//
    setenv("CONTENT_TYPE","application/x-www-form-urlencoded",1);
    setenv("REQUEST_URI", "SCRIPT",1);
    // strict is set for the modified /bin/dash

    FILE *logfile = fopen ("/tmp/wrapper.log","a+");
    fprintf (logfile, "----Start----\n");
    //printf("starting\n");

    static int MAX_CMDLINE_LEN=128*1024;

    static int   num_env_vars = sizeof(env_vars) / sizeof(char*);
    printf("\tNUM_ENV_VARS=%d\n",num_env_vars);
    char  in_buf[MAX_CMDLINE_LEN];
    if (read(0, in_buf, MAX_CMDLINE_LEN - 2) < 0);

    int zerocnt = 0;
    for (int cnt=0;cnt<MAX_CMDLINE_LEN;cnt++){
        if (in_buf[cnt] == 0){
            printf("\t");
            zerocnt++;
        }
        printf("%c", in_buf[cnt]);
        if (zerocnt == 3){
            break;
        }
    }
    printf("\n");

    pipe(pipefds);

    dup2(pipefds[0], STDIN_FILENO);
    //close(STDIN_FILENO);

    int real_content_length = 0;
    char* saved_ptr;
    saved_ptr = (char *) malloc(MAX_CMDLINE_LEN);
    char* ptr = in_buf;
    int   rc  = 0;
    char* cwd;
    int errnum;
    //struct passwd *p = getpwuid(getuid());  // Check for NULL!
    long size = pathconf(".", _PC_PATH_MAX);
    char* dirbuf = (char *)malloc((size_t)size);
//    cwd = getcwd(dirbuf, (size_t)size);
//
//    if (cwd == NULL){
//       errnum = errno;
//       printf("ITs null!\n");
//       printf("Error num = %d\n", errno);
//       printf("Error: %s\n", strerror(errnum));
//    }
//    printf("\tOpening INPUT with CURRENT WORKING DIR: %s\n", cwd);

    // loop through the strings read via stdin and break at each \x00
    // Cookies, Query String, Post (via re-writting to stdin)
    char* cookie;
    cookie = (char *) malloc(MAX_CMDLINE_LEN);
    char* login_cookie = getenv("LOGIN_COOKIE");
    if (login_cookie){
        strcat(cookie, login_cookie);
        setenv(env_vars[0], cookie, 1);
        if (!strchr(login_cookie,';')){
            strcat(login_cookie,";");
        }
        printf("[WC-cgiwrapper] LOGIN COOKIE %s\n", login_cookie);
        char * name = strtok(login_cookie, ";=");
        while( name != NULL ) {
            printf( "TOKEN=%s\n", name ); //printing each token
            char * value = strtok(NULL, ";=");
            if (value != NULL){
                printf( "name=%s, value=%s\n", name, value); //printing each token
            }

            if (value != NULL){
                int thelen = strlen(value);
                if (thelen >= 24 && thelen <= 32){
                    printf("session_id = %s, len=%d\n", value, thelen);
                    strcpy(session_id, value);
                    char filename[64];
                    char sess_fn[64];
                    sprintf(sess_fn, "../../../../../../../tmp/sess_%s", value );
                    setenv("SESSION_FILENAME",sess_fn,1);

                    sprintf(filename, "../../../../../../../tmp/save_%s", value );

                    //saved_session_size = fsize(filename);

                    printf("\t[WC] SESSION ID = %s, saved session size = %d\n", filename, saved_session_size);
                    break;
                }
            }
            name = strtok(NULL, ";=");
        }
        printf("\t[WC] LOGIN ::> %s\n", cookie);
    }
    char* mandatory_cookie = getenv("MANDATORY_COOKIE");
    if (mandatory_cookie && strlen(mandatory_cookie) > 0){
        strcat(cookie, "; ");
        strcat(cookie, mandatory_cookie);
        printf("\nMANDATORY COOKIE = %s\n", cookie);
    }
    setenv(env_vars[0], cookie, 1);

    char* cgi;
    cgi = (char *) malloc(MAX_CMDLINE_LEN);
    char* mandatory_get = getenv("MANDATORY_GET");
    if (mandatory_get && strlen(mandatory_get) > 0){
        strcat(cgi, mandatory_get);
        printf("\nMANDATORY GETS = %s\n", cgi);
    }
    setenv(env_vars[1], cgi, 1);

    while (!*ptr){
        ptr++;
        rc++;
    }
    while (*ptr) {
        memcpy(saved_ptr, ptr, strlen(ptr)+1);

        printf("\tmy RC=%d\n",rc);
        if (rc < 3){
            load_variables(saved_ptr, rc);
        }


        if(rc < num_env_vars){

            if (rc == 0){
                strcat(cookie, "; ");
                strcat(cookie, saved_ptr);
                cookie = replace_char(cookie, '&',';');
                setenv(env_vars[rc], cookie, 1);

            } else if (rc == 1) {
                strcat(cgi, "&");
                strcat(cgi, saved_ptr);

                setenv(env_vars[rc], cgi, 1);

            } else {
                setenv(env_vars[rc], saved_ptr, 1);
            }


            if (afl_area_ptr != NULL) {
                afl_area_ptr[0xffdd]=1;
            }

        }else if(rc == num_env_vars){
            char *json = getenv("DO_JSON");
            if (json){
                saved_ptr = format_to_json(saved_ptr);
                printf("\e[32m\tDONE JSON=%s\e[0m\n", saved_ptr);
            }

            real_content_length = write(pipefds[1], saved_ptr, strlen(saved_ptr));
            write(pipefds[1], "\n", 1);

            printf("\tReading from %d and writing %d bytes to %d \n", real_content_length, pipefds[0], pipefds[1]);
            printf("\t%-15s = \033[33m%s\033[0m \n", "POST", saved_ptr);


            char snum[20];
            sprintf(snum, "%d", real_content_length);
            setenv("POST_DATA", saved_ptr, 1);
            setenv("CONTENT_LENGTH", snum, 1);
            printf("\t%-15s = \033[33m%s\033[0m \n", "CONTENT_LENGTH", getenv("CONTENT_LENGTH"));


        }
        printf("END RC = %d, incrementing\n", rc);
        rc++;
        while (*ptr)
            ptr++;
        ptr++;

    }
//    printf("[WC] SAVED VARS\n");
//
//    for (int i=0; i < 3;i++){
//        if (i == 0){
//            printf("\tCOOKIES\e[36m ");
//        } else if (i == 1){
//            printf("\tGETS\e[36m ");
//        } else if (i == 2){
//            printf("\tPOSTS\e[36m ");
//        }
//
//        for (int j=0; j < variables_ptr[i];j++){
//            printf("%s, ", variables[i][j]);
//        }
//        printf("\e[0m\n");
//    }
//    printf("\e[0m\n");
    if (afl_area_ptr != NULL) {
        afl_area_ptr[0xffdd]=1;
    }

    for (int i=0; i < num_env_vars; i++){
        printf("\t%-15s = \033[33m%s\033[0m  %d \n", env_vars[i], getenv(env_vars[i]), num_env_vars);
    }
    printf("@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@\n");
    fflush(stdout);

    free(saved_ptr);
    close(pipefds[0]);
    close(pipefds[1]);
    fprintf(logfile, "DONE reading in \n");
    fclose(logfile);
    printf("\nDONE reading in for WEB setup\n");
    fflush(stdout);

}



static int firsttime = 0;

static  char *(*real_getenv)(const char *name) = NULL;

static char* comp_envs[11] = { "HTTP_COOKIE","QUERY_STRING", "SERVER_SOFTWARE", "DOCUMENT_ROOT", "HTTP_REDIRECT_STATUS", "HTTP_ACCEPT", "REQUEST_METHOD", "CONTENT_TYPE", "SERVER_NAME", "GATEWAY_INTERFACE" };

int startsWith(const char *pre, const char *str)
{
    size_t lenpre = strlen(pre),
           lenstr = strlen(str);
    return lenstr < lenpre ? 0 : memcmp(pre, str, lenpre) == 0;
}

//static ssize_t (*real_recvfrom)(int sockfd, void *buf, size_t len, int flags, struct sockaddr *src_addr, socklen_t *addrlen) = NULL;
//
//ssize_t recvfrom(int sockfd, void *buf, size_t len, int flags,
//                        struct sockaddr *src_addr, socklen_t *addrlen) {
//
//  real_recvfrom= dlsym(RTLD_NEXT, "recvfrom");
//  ssize_t results = real_recvfrom(sockfd, buf, len, flags, src_addr, addrlen);
//  printf("!!!!!!!!!!!!!!!!!!! Thank you for using the special RECVFROM !!!!!!!!!!!!!!!!!!!!\n");
//  printf("\tBUF: %s", (char*) buf);
//  char* strict = getenv("STRICT");
//
//  unsigned char *cptr = (unsigned char *)(buf);
//
//  if(strstr(buf, "You have an error i") != NULL || strstr(buf, "You have an error i") != NULL  ) {
//    DIR* dir = opendir("/tmp/output");
//    if (dir) {
//        /* Directory exists. */
//        closedir(dir);
//        FILE *errorlog= fopen("/tmp/output/errors.log","a+");
//        fprintf(errorlog,"vvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvv   RECV FROM  vvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvv\n");
//        int num_env_vars = sizeof(env_vars) / sizeof(char*);
//        for (int i=0; i < num_env_vars; i++){
//            fprintf(errorlog, "\t%-15s = \033[33m%s\033[0m  \n", env_vars[i], getenv(env_vars[i]));
//        }
//        if (getenv("POST_DATA")){
//            fprintf(errorlog,"\t%-15s = \033[33m%s\033[0m  \n","POST", getenv("POST_DATA"));
//        }
//
//        fprintf(errorlog, "\nERROR:\n");
//        for (int lp=0; lp < len; lp++){
//            if (cptr[lp]>= 0x20 && cptr[lp] < 0x7f) {
//              fprintf(errorlog,"%c",cptr[lp]);
//            } else {
//              if (cptr[lp] == 0x0){
//                break;
//              } else {
//                fprintf(errorlog,"\\x%02x",cptr[lp]);
//              }
//
//            }
//
//        }
//        fprintf(errorlog,"\n^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^\n");
//        fflush(errorlog);
//        fclose(errorlog);
//
//    }
//
//    if (strict){
//        raise(SIGSEGV);
//    } else {
//        printf("ERROR ERROR ERROR FROM DATABASE\n");
//    }
//
//
//  }
//
//  return results;
//
//}

int jdbc_error_check(unsigned char *cptr, size_t len);
void print_repr(FILE *fp, unsigned char *cptr, size_t len);
bool pattern_in_bytes(unsigned char *target, int target_len, unsigned char *pattern, int pattern_len);
void mysql_error_check (unsigned char *cptr, size_t len);
void error_report(unsigned char *cptr, size_t len);

static ssize_t (*real_recv) (int sockfd, void *buf, size_t len, int flags) = NULL;

ssize_t recv (int sockfd, void *buf, size_t len, int flags) {

  real_recv= dlsym(RTLD_NEXT, "recv");
  ssize_t results = real_recv(sockfd, buf, len, flags);

  unsigned char *cptr = (unsigned char *)(buf);

  jdbc_error_check(cptr, len);
  mysql_error_check(cptr, len);

  return results;
}

bool pattern_in_bytes(unsigned char *target, int target_len, unsigned char *pattern, int pattern_len){
  if (target_len <= pattern_len){
      return false;
  }
  for (int i = 0; i < target_len-pattern_len; i ++) {
      bool found = true;
      for (int j = 0; j < pattern_len; j ++) {

          if (pattern[j] == '.'){
              i++;
              continue;
          } else if (pattern[j] == '~'){
            if (target[i]>= 0x20 && target[i] < 0x7f) {
                while (target[i]>= 0x20 && target[i] < 0x7f) {
                  i++;
                }
                continue;
                found = false;
                break;
            }
          }

          if (target[i] != pattern[j]){
              found = false;
              break;
          }

          i++;
      }
      if (found){
          return true;
      }

  }

  return false;
}

void print_repr(FILE *fp, unsigned char *cptr, size_t len){
    for (int lp=0; lp < len; lp++){
        if (cptr[lp]>= 0x20 && cptr[lp] < 0x7f) {
            fprintf(fp, "%c",cptr[lp]);
        } else {
            fprintf(fp, "\\x%02x",cptr[lp]);
        }
    }
}
int jdbc_error_check(unsigned char *cptr, size_t len){
    //\x02\x00\x00\x00,\x00\x00\x00\x17unexpected token: SMITH\x00\x00\x00\x0542581\xff\xff\xea3\x7f
    // \x02\x00\x00\x00(\x00\x00\x00\x13malformed string: '    \x00\x00\x00\x0542584\xff\xff\xea
    unsigned char *jdbc_msg1 = "\x02\x00\x00\x00.\x00\x00\x00.~\x00\x00\x00\x05~\xff\xff\xea"; // 18
    unsigned char *jdbc_msg4 = "\xff\xff\xea"; // 3

    if (pattern_in_bytes(cptr, len, jdbc_msg4, 3)){
        if (pattern_in_bytes(cptr, len, jdbc_msg1, 18)){
            error_report(cptr, len);
            return;
        }
    }
}

void send_signal(int strictval){
    int pid = 0;
    struct test_process_info *afl_info = NULL;
    printf("FOUND STRICT=%d\n", strictval);
    if (getenv("AFL_META_INFO_ID")){
        // clean up last shared memory area
        int mem_key = atoi(getenv("AFL_META_INFO_ID"));
        int shm_id = shmget(mem_key , sizeof(struct test_process_info), 0666);
        fprintf(stderr, "\033[36m [Witcher] who dat %d %d !!!\033[0m\n", mem_key, shm_id);
        if (shm_id  >= 0 ) {
            afl_info = (struct test_process_info *) shmat(shm_id, NULL, 0);  /* attach */
            if (afl_info && afl_info->reqr_process_id){

                pid = afl_info->reqr_process_id;
                fprintf(stderr, "pid=%d ", pid);
            }
        }
        fprintf(stderr, "\n");
    }
    if (pid > 0){
        strcpy(afl_info->error_type,"COMMAND");
        fprintf(stderr, "\033[36m [Witcher] sending SEGSEGV to %d %d %d !!!\033[0m\n", afl_info->reqr_process_id, afl_info->process_id, getpid());
        kill(pid, SIGSEGV);
    } else{
        if (strictval == 1 || strictval == 2){
            printf("FOUND STRICT=%s, RAISING SIGSEGV\n", strictval);
            raise(SIGSEGV);
        }  else if (strictval == 3 || strictval == 4){
            printf("FOUND STRICT=%s, RAISING SIGUSR1\n", strictval);
            raise(SIGUSR1);
        }
    }
}

void error_report(unsigned char *cptr, size_t len){
    char* strict = getenv("STRICT");
    if (strict){
        fprintf(stderr, "Error encountered, strict=%s\n", strict);
        int strictval = atoi(strict);
        send_signal(strictval);
    } else {
        printf("RECV ERROR FROM DATABASE FOUND!!!!! But not escalating... \n");
    }
    fprintf(stderr, "[*] Found error message  \n");
    print_repr(stderr, cptr, len);
    fprintf(stderr, "\n");
}

void mysql_error_check (unsigned char *cptr, size_t len) {

  //printf("!!!!!!!!!!!!!!!!!!! Thank you for using the special RECV --->> !!!!!!!!!!!!!!!!!!!!\n");
  unsigned char *mysql_msg = "You have an error i";
  int error_msg_len = strlen(mysql_msg);
  if (pattern_in_bytes(cptr, len, mysql_msg, error_msg_len)){
    error_report(cptr, len);
  }
//
//  if (len < error_msg_len){
//      return;
//  }
//
//  for (int i = 0; i < len; i ++) {
//        if ((i+error_msg_len) >= len){
//          break;
//        }
//        char cmp_str[error_msg_len];
//        memcpy(cmp_str, cptr+i, error_msg_len);
//        cmp_str[error_msg_len-1] = '\x00';
//        if (strcmp(error_msg, cmp_str) == 0 && strlen(cmp_str) >= strlen(error_msg)){
//
//            fprintf(stderr, "\nERROR:\n", cmp_str);
//            for (int lp=i; lp < len; lp++){
//                if (cptr[lp]>= 0x20 && cptr[lp] < 0x7f) {
//                  fprintf(stderr, "%c",cptr[lp]);
//                } else {
//                  if (cptr[lp] == 0x0){
//                    break;
//                  } else {
//                    fprintf(stderr, "\\x%02x",cptr[lp]);
//                  }
//
//                }
//            }
//            printf("\n");
//            fflush(stdout);
//            DIR* dir = opendir("/tmp/output");
//            if (dir) {
//                /* Directory exists. */
//                closedir(dir);
//                FILE *errorlog= fopen("/tmp/output/errors.log","a+");
//                fprintf(errorlog,"vvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvv   RECV   vvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvv\n");
//                if (getenv("WC_INSTRUMENTATION")){
//                    if (getenv("NO_WC_EXTRA")){
//                        fprintf(errorlog,"\t%-15s = \033[33m%s\033[0m  \n","Instrumentation", "WiC");
//                    } else {
//                        fprintf(errorlog,"\t%-15s = \033[33m%s\033[0m  \n","Instrumentation", "ExWiC");
//                    }
//                }
//                int num_env_vars = sizeof(env_vars) / sizeof(char*);
//                if (getenv("SCRIPT_FILENAME")){
//                    fprintf(errorlog,"\t%-15s = \033[33m%s\033[0m  \n","SCRIPT_FILENAME", getenv("SCRIPT_FILENAME"));
//                }
//                for (int x=0; x < num_env_vars; x++){
//                    fprintf(errorlog, "\t%-15s = \033[33m%s\033[0m  \n", env_vars[x], getenv(env_vars[x]));
//                }
//                if (getenv("POST_DATA")){
//                    fprintf(errorlog,"\t%-15s = \033[33m%s\033[0m  \n","POST", getenv("POST_DATA"));
//                }
//
//                fprintf(errorlog, "\nERROR:\n");
//                for (int lp=i; lp < len; lp++){
//                    if (cptr[lp]>= 0x20 && cptr[lp] < 0x7f) {
//                      fprintf(errorlog,"%c",cptr[lp]);
//                    } else {
//                      if (cptr[lp] == 0x0){
//                        break;
//                      } else {
//                        fprintf(errorlog,"\\x%02x",cptr[lp]);
//                      }
//
//                    }
//
//                }
//                fprintf(errorlog,"\n^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^\n");
//                fflush(errorlog);
//                fclose(errorlog);
//
//          }
//          dir = opendir("/results");
//          if (dir) {
//                /* Directory exists. */
//                closedir(dir);
//                FILE *errorlog= fopen("/results/gen_errors.log","a+");
//                fprintf(errorlog,"vvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvv   RECV   vvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvv\n");
//                int num_env_vars = sizeof(env_vars) / sizeof(char*);
//                if (getenv("WC_INSTRUMENTATION")){
//                    if (getenv("NO_WC_EXTRA")){
//                        fprintf(errorlog,"\t%-15s = \033[33m%s\033[0m  \n","Instrumentation", "WiC");
//                    } else {
//                        fprintf(errorlog,"\t%-15s = \033[33m%s\033[0m  \n","Instrumentation", "ExWiC");
//                    }
//                }
//
//                if (getenv("SCRIPT_FILENAME")){
//                    fprintf(errorlog,"\t%-15s = \033[33m%s\033[0m  \n","SCRIPT_FILENAME", getenv("SCRIPT_FILENAME"));
//                }
//                for (int x=0; x < num_env_vars; x++){
//                    fprintf(errorlog, "\t%-15s = \033[33m%s\033[0m  \n", env_vars[x], getenv(env_vars[x]));
//                }
//                if (getenv("POST_DATA")){
//                    fprintf(errorlog,"\t%-15s = \033[33m%s\033[0m  \n","POST", getenv("POST_DATA"));
//                }
//                if (getenv("SESSION_FILENAME")){
//                    char* sess_fn = getenv("SESSION_FILENAME");
//
//                    FILE* fptr = fopen(sess_fn, "r");
//                    if (fptr == NULL)
//                    {
//                        fprintf(errorlog, "Cannot open file %s \n", sess_fn);
//                    } else {
//                        fprintf(errorlog, "\nSession Data:\n");
//                        char c = fgetc(fptr);
//                        while (c != EOF)
//                        {
//                            if (c>= 0x20 && c < 0x7f) {
//                              fprintf(errorlog,"%c",c);
//                            } else {
//                                fprintf(errorlog,"\\x%02x",c);
//                            }
//                            c = fgetc(fptr);
//                        }
//
//                        fclose(fptr);
//                        fprintf(errorlog, "\n");
//                    }
//
//                    // Read contents from file
//
//
//
//                }
//                fprintf(errorlog, "\nERROR:\n");
//                for (int lp=i; lp < len; lp++){
//                    if (cptr[lp]>= 0x20 && cptr[lp] < 0x7f) {
//                      fprintf(errorlog,"%c",cptr[lp]);
//                    } else {
//                      if (cptr[lp] == 0x0){
//                        break;
//                      } else {
//                        fprintf(errorlog,"\\x%02x",cptr[lp]);
//                      }
//
//                    }
//
//                }
//                fprintf(errorlog,"\n^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^\n");
//                fflush(errorlog);
//                fclose(errorlog);
//
//          }
//          printf("\nBUF: match=%d %d %d '%s' \n", strcmp(error_msg, cmp_str), strlen(cmp_str), strlen(error_msg), cptr+i);
//          print_repr(stdout, cptr, i+50);
//          printf("RAISING SIGSEGV\n");
//          char* strict = getenv("STRICT");
//          printf("STRICT=%s\n",strict);
//          //raise(SIGSEGV);
//          if (strict){
//              raise(SIGSEGV);
//          } else {
//              printf("RECV ERROR FROM DATABASE FOUND!!!!! \n");
//          }
//
//          break;
//        }
//  }



}



/*****
 * borrowed from php source codes inline hash.
 ******/


unsigned char *cgi_get_shm_mem(char * ch_shm_id) {
    char *id_str;
    int shm_id;

    if (afl_area_ptr == NULL){
        id_str = getenv(SHM_ENV_VAR);
        if (id_str) {
            shm_id = atoi(id_str);
            afl_area_ptr = shmat(shm_id, NULL, 0);
//            FILE *myfp = fopen("/tmp/fork.log","w+");
//            fprintf(myfp, "Setting shm_id = %d \n", shm_id);
//            fprintf(myfp,"\t\tafl[0]=%d ptr=%p \n", afl_area_ptr[0], afl_area_ptr);
//
//            fclose(myfp);

        } else {

            afl_area_ptr = malloc(MAPSIZE);
        }
    }
    return afl_area_ptr;

}

void webcam_trace_init(char * ch_shm_id) {
    printf("[WC] in Library, webcam_trace_init\n\t\e[34mSCRIPT_FILENAME=%s\n\t\e[34mAFL_PRELOAD=%s\n\t\e[34mLD_LIBRARY_PATH=%s\e[0m\n", getenv("SCRIPT_FILENAME"), getenv("AFL_PRELOAD"), getenv("LD_LIBRARY_PATH"), getenv("LOGIN_COOKIE"));
    //setenv("STRICT","1",1);
    //unsetenv("STRICT");

    if (getenv("WC_INSTRUMENTATION")){
        start_tracing = true;
        printf("[WC] \e[34m WC INSTUMENTATION ENABLED \e[0m\n");
    } else {
        printf("[WC] \e[35m WC INSTUMENTATION DISABLED \e[0m\n");
    }
    //
    if (getenv("NO_WC_EXTRA")){
        wc_extra_instr = false;
        printf("[WC] \e[34m WC Extra Instrumentation DISABLED \e[0m\n");
    } else {
        printf("[WC] \e[35m WC Extra Instrumentation ENABLED \e[0m\n");
    }
    top_pid = getpid();
    cgi_get_shm_mem(ch_shm_id);

    char *id_str = getenv(SHM_ENV_VAR);

    if (id_str) {
        printf("\tStarting FORKER\n");
        afl_forkserver();
        printf("Returning with pid %d \n\n", getpid());
        fflush(stdout);
        int trace_sum = 0;
        int slot_cnt = 0;
        for (int x=0;x<MAPSIZE;x++){
            trace_sum += afl_area_ptr[x]*x;
            if (afl_area_ptr[x] > 0) {
              slot_cnt++;
            }
        }
        printf("[WC] Trace_sum=%d, Slot_cnt=%d", trace_sum, slot_cnt);
        fflush(stdout);
    }

    setup_cgi_env();

    printf("END webcam_trace_init\n");
    fflush(stdout);
}

void var_report(char *var1, int bitmapLoc){
    if (var1 != NULL){
        int in_var_len = strlen(var1);
        int comp_len;
        for (int var_type=0; var_type< 3; var_type++){
            for (int i =0; i < variables_ptr[var_type]; i++){
                if (variables_used[var_type][i] > 0){
                    continue;
                }
                comp_len = strlen(variables[var_type][i]);
                // take the bigger of the two
                comp_len = (comp_len > in_var_len) ? comp_len : in_var_len;

                if (strncmp(variables[var_type][i], var1, comp_len) == 0){
                    afl_area_ptr[bitmapLoc]++;
                    variables_used[var_type][i]=1;
                }
            }
        }

    }
}

void do_rollback(char* varname, int var_type){
    struct rollback_entry *re_search = rollback_ll[var_type];

    while (re_search){

        if (re_search->rolledback == false && strcmp(re_search->varname, varname) == 0){
            afl_area_ptr[re_search->bitmapLoc]--;
            for (int i=1; i < re_search->matchcnt; i++){
                afl_area_ptr[re_search->bitmapLoc + i] = 0;
            }
            re_search->rolledback = true;
        }

        re_search = re_search->next;

    }

}

void var_diff_report(char *var1, int bitmapLoc, int var_type){

    if (var1 != NULL){

        int in_var_len = strlen(var1);
        int comp_len;
        int best_change = 0, best_change_pointer =-1;
        printf("starting!!!!!\n");
        for (int i =0; i < variables_ptr[var_type]; i++){
            if (variables_used[var_type][i] == 3){
                 continue;
            }
            comp_len = strlen(variables[var_type][i]);
            // take the bigger of the two
            comp_len = (comp_len > in_var_len) ? comp_len : in_var_len;
            if (strncmp(variables[var_type][i], var1, comp_len) == 0){

                FILE *logfile2 = fopen("/tmp/match.dat","a+");
                fprintf(logfile2,"TOTAL MATCH '%s' '%s' \n ", var1, variables[var_type][i]);
                fflush(logfile2);
                fclose(logfile2);

                for (int x=0; x <= comp_len;x++){
                  afl_area_ptr[bitmapLoc+x]=1;
                }
                if (variables_used[var_type][i] == 1){
                    do_rollback(variables[var_type][i], var_type);
                }
                variables_used[var_type][i]=3;
                return;
            } else {
                if (variables_used[var_type][i] == 1){  // already used for a partial match, if already used for full match then if above catches
                  continue;
                }
                int change = value_diff_changes(var1, variables[var_type][i]);
                if (change > best_change){
                    best_change = change;
                    best_change_pointer = i;
                }

            }
        }
        if (best_change_pointer > -1){
            printf("reporting bafck to BOSSSSSSSSSSSSSSSSSSSSSSSSSSSS %d\n", best_change);
            FILE *logfile2 = fopen("/tmp/match.dat","a+");

            fprintf(logfile2,"BEST MATCH being used '%s' '%s' %d \n ", var1, variables[best_change_pointer], best_change);

            //fprintf(logfile2,"\tAFTER match=%d afl=%d \n", matchcnt, afl_area_ptr[bitmapLoc]);
            fflush(logfile2);
            fclose(logfile2);
            add_var_to_rollback(variables[var_type][best_change_pointer], best_change, bitmapLoc, var_type);
            variables_used[var_type][best_change_pointer] = 1;

            value_diff_report(var1, variables[var_type][best_change_pointer], bitmapLoc);
        }
    }
}


void value_diff_changes(char *var1, char *var2){
    if (var1 == NULL || var2 == NULL){
        return;
    }
    int changes = 0;
    int len1 = strlen(var1);
    int len2 = strlen(var2);

    if (len1 == len2){
        changes++;
    }

    if (len1 < 2 || len2 < 2){
        return changes;
    }

    int checklen = (len1 < len2) ? len1 : len2;
    int matchcnt = 0;
    for (int x=0; x<checklen;x++){
        if (var1[x] == var2[x]){
          matchcnt += 1;
        } else {
          break;
        }
    }
    changes += matchcnt;
    return changes;

}
void value_diff_report(char *var1, char *var2, int bitmapLoc){
    if (var1 == NULL || var2 == NULL){
        return;
    }
    int changes = 0;
    int len1 = strlen(var1);
    int len2 = strlen(var2);

    if (len1 == len2){
        afl_area_ptr[bitmapLoc]++;
    }

    if (len1 < 2 || len2 < 2){
        return;
    }

    int checklen = (len1 < len2) ? len1 : len2;
    int matchcnt = 0;
    for (int x=0; x<checklen;x++){
        if (var1[x] == var2[x]){
          matchcnt += 1;
        } else {
          break;
        }
    }

    if (matchcnt > 0){
        //afl_area_ptr[bitmapLoc] += (int) log2(matchcnt);
        // len check goes into first bitmapLoc, +1 for the rest.

        for (int x=1; x <= matchcnt; x++){
            afl_area_ptr[bitmapLoc+x] = 1;
        }
        if (getenv("WEBCAM_PRINT_OP")){
            char logfn[50];
            sprintf(logfn, "/tmp/trace-%s.dat", getenv("WEBCAM_PRINT_OP"));
            FILE *logfile2 = fopen("/tmp/match.dat","a+");
            fprintf(logfile2,"MATCH found '%s' '%s' match=%d bm=%d afl=%d afl+1=%d afl+2=%d afl+3=%d \tpid=%d\n ", var1, var2, matchcnt, bitmapLoc, afl_area_ptr[bitmapLoc], afl_area_ptr[bitmapLoc+1], afl_area_ptr[bitmapLoc+2],afl_area_ptr[bitmapLoc+3], getpid());
            fflush(logfile2);
            fclose(logfile2);
        }

    }
}
void webcam_trace_log_op(int lineno, int opcode, char *var1){
    webcam_trace_log_op2(lineno, opcode, var1,0);
}

void webcam_trace_log_op2(int lineno, int opcode, char *var1, char *var2)
{

	//static size_t last_len = 0;
    if (start_tracing) {

//        main_filename =  getenv("MAIN_FN");
//        if (main_filename && pyfile){
//            if (strcmp(pyfile, main_filename) != 0){
//                return;
//            }
//        }

        op = (lineno << 8) | opcode ; //opcode; //| (lineno << 8);

        if (last != 0) {
            int bitmapLoc = (op ^ last) % MAPSIZE;
//            printf("%04d) opcode=%04d --> %08x ^ %08x = %04x\n", lineno, opcode, op, last, bitmapLoc );
//            char *cook = getenv("LOGIN_COOKIE");
//            if (cook){
//                char logfn[50];
//                sprintf(logfn, "/tmp/trace-%d.dat",getpid());
//                FILE *tout_fp = fopen(logfn,"a");
//                setbuf(tout_fp, NULL);
//                fprintf(tout_fp, "%04d) opcode=%04d --> %08x ^ %08x = %04x\n", lineno, opcode, op, last, bitmapLoc );
//                fclose(tout_fp);
//            }
//            var_report(var1, (bitmapLoc*11) % MAPSIZE);
//            var_report(var2, (bitmapLoc*13) % MAPSIZE);
//            value_diff_report(var1, var2, (bitmapLoc*17) % MAPSIZE);
            // turned off to disable afl code tracing
            afl_area_ptr[bitmapLoc]++;
            if (wc_extra_instr && getenv(SHM_ENV_VAR) && opcode < 100){
                //printf("\t\t\tIN the SHM area, with %p %p %d %d\n", var1, var2, lineno, opcode );

                fflush(stdout);
                if (nextVar2_is_a_var > -1) {
                    var_diff_report(var1, (bitmapLoc*13) % MAPSIZE, nextVar2_is_a_var);
                } else {
                    var_report(var1, (bitmapLoc*11) % MAPSIZE);
                    var_report(var2, (bitmapLoc*13) % MAPSIZE);
                    value_diff_report(var1, var2, (bitmapLoc*17) % MAPSIZE);
                }
            }

            char *cook = getenv("LOGIN_COOKIE");
            if (cook && getenv("WEBCAM_PRINT_OP")){
                char logfn[50];
                sprintf(logfn, "/tmp/trace-%s.dat", getenv("WEBCAM_PRINT_OP"));

                //sprintf(logfn, "/tmp/trace",getppid());
                FILE *tout_fp = fopen("/tmp/match.dat","a");
                setbuf(tout_fp, NULL);
                for (int x=0; x < MAPSIZE;x++){
                    if (afl_area_ptr[x] > 0){
                        fprintf(tout_fp, "%04x ", x);
                    }
                }
                fprintf(tout_fp, "\n");
                for (int x=0; x < MAPSIZE;x++){
                    if (afl_area_ptr[x] > 0){
                        fprintf(tout_fp, " %02x  ", afl_area_ptr[x]);
                    }
                }
                fprintf(tout_fp, "\n");
                fclose(tout_fp);
            }
            //afl_area_ptr[0] = 1;
            //printf("[WC] %08x  %04x \n", bitmapLoc, afl_area_ptr[bitmapLoc]);
        }


        if (var1 != NULL && getenv(SHM_ENV_VAR) && opcode < 100){
            //printf("\t\t\tchecking out var1 for type %p opcode=%d\n", var1, opcode);
            fflush(stdout);
            if (strcmp(var1, "_GET") == 0 ){
                nextVar2_is_a_var = 1;
            } else if (strcmp(var1, "_POST") == 0){
                nextVar2_is_a_var = 2;
            } else {
                nextVar2_is_a_var = -1;
            }
        } else {
            nextVar2_is_a_var = -1;
        }
        if (trace_index < TRACE_SIZE){

            trace[trace_index++] = op;
            //printf("%d, %08x  %04x %02x \n", trace_index, op, (trace[trace_index-1] >> 8), (trace[trace_index-1] & 0xFF));
        }
        last = op;

    }


}


void webcam_trace_finish()
{
    start_tracing = false;
    //printf("[WC-AFL] webcam_trace_finish .. done with %d\n", getpid());
    if (getenv("WEBCAM_PRINT_OP")){
        char logfn[50];
        sprintf(logfn, "/tmp/trace-%s.dat", getenv("WEBCAM_PRINT_OP"));
        FILE *tout_fp = fopen(logfn,"a");
        setbuf(tout_fp, NULL);
        int cnt = 0;
        for (int x=0; x < MAPSIZE;x++){
            if (afl_area_ptr[x] > 0){
                cnt ++;
            }
        }
        fprintf(tout_fp, "BitMap has %d  \n", cnt);


        for (int x=0; x < MAPSIZE;x++){
            if (afl_area_ptr[x] > 0){
                fprintf(tout_fp, "%04x ", x);
            }
        }
        fprintf(tout_fp, "\n");
        for (int x=0; x < MAPSIZE;x++){
            if (afl_area_ptr[x] > 0){
                fprintf(tout_fp, " %02x  ", afl_area_ptr[x]);
            }
        }
        fprintf(tout_fp, "\n");

        //fprintf(logfile2,"\tAFTER match=%d afl=%d \n", matchcnt, afl_area_ptr[bitmapLoc]);

        fclose(tout_fp);
    }

    for(int i =0; i < 3; i++){
        struct rollback_entry *tmp;
        while (rollback_ll[i]){
            free(rollback_ll[i]->varname);
            tmp = rollback_ll[i];
            rollback_ll[i] = rollback_ll[i]->next;
            free(tmp);
        }

    }

//    char *script = getenv("SCRIPT_FILENAME");
//    bool needfree = false;
//
//    if (!script){
//        needfree = true;
//        printf("no script name\n");
//        script = (char*) malloc(32*sizeof(char));
//        strcpy(script,"nameless_script.py");
//        printf("\e[34mNo SCRIPT_FILENAME provieded, setting to default.%s \e[0m \n", script);
//    } else if (main_filename) {
//        needfree = true;
//        int newlen = strlen(script)+strlen(main_filename) + 2;
//        char *tempfn = (char *) malloc(newlen);
//        snprintf(tempfn, newlen, "%s~%s", main_filename, script);
//
//        script = tempfn;
//    }
//
//    script = replace_char(script, '/','+');
//    char touts_filename[1024];
//
//    sprintf(touts_filename, "/tmp/touts_%s-PID%05d", script, top_pid);
//    FILE *tout_fp = fopen(touts_filename,"a");
//    setbuf(tout_fp, NULL);
//
//    fprintf(tout_fp, "\n>>>");
//    for (int tcnt=0; tcnt < trace_index; tcnt++){
//        fprintf(tout_fp, "%d@%02x,", (trace[tcnt] >> 8), (trace[tcnt] & 0xFF));
//    }
//    fprintf(tout_fp, "<<<\n");
//    fclose(tout_fp);
//
//    if (needfree){
//        free(script);
//    }

    op = 0;
    last = 0;
    trace_index = 0;



}



/*static void afl_wait_tsl(CPUState *cpu, int fd) {

  struct afl_tsl t;
  TranslationBlock *tb;

  while (1) {

    *//* Broken pipe means it's time to return to the fork server routine. *//*

    if (read(fd, &t, sizeof(struct afl_tsl)) != sizeof(struct afl_tsl))
      break;

    tb = tb_htable_lookup(cpu, t.pc, t.cs_base, t.flags);

    if(!tb) {
      mmap_lock();
      tb_lock();
      tb_gen_code(cpu, t.pc, t.cs_base, t.flags, 0);
      mmap_unlock();
      tb_unlock();
    }

  }

  close(fd);

}*/

//
//char *getenv(const char *name){
//
//    real_getenv = dlsym(RTLD_NEXT, "getenv");
//
//    if (firsttime == 0 && real_getenv("WEBCGI")) {
//       //printf("IM HERE! FOUND what was sought. %s\n", name);
//      //FILE *logfile2 = fopen ("/tmp/wrapper2.log","a+");
//
//      //fprintf (logfile2, "----getenv(%s)----\n", name);
//      //fflush(logfile2);
//
//      int match = 0;
//      for (int i=0; comp_envs[i] != NULL; i++){
//        //fprintf (logfile2, "\t%d, firsttime=%d, match=%d\n",i, firsttime, match);
//        //fflush(logfile2);
//        if (strcmp(name, comp_envs[i]) == 0){
//          //fprintf (logfile2, "\tMATCH %s == %s\n",name, comp_envs[i]);
//          match = 1;
//          break;
//        } else {
//          //fprintf (logfile2, "\tNO MATCH %s =! %s\n",name, comp_envs[i]);
//        }
//
//      }
//
//      if (match == 1){
//          printf("~~~~~~~~~~~~ < firsttime=%d for=%s match=%d, pid=%d> ~~~~~~~~~~~~~\n", firsttime, name, match, getpid());
//
//          firsttime = 1;
//
//          setup_cgi_env();
//
//          printf("\tVALUE QUERY_STRING: %s\n", real_getenv("QUERY_STRING"));
//          printf("\tVALUE SCRIPT_FILENAME: %s\n", real_getenv("SCRIPT_FILENAME"));
//          printf("\tVALUE CONTENT_LENGTH=%s\n", real_getenv("CONTENT_LENGTH"));
//
//      }
//      //fprintf (logfile2, "\tFINAL %s=%s, firsttime=%d, match=%d\n", name, real_getenv(name), firsttime, match);
//      //fflush(logfile2);
//      //fclose(logfile2);
//
//    }
//
//    char *tmp = real_getenv(name);
//    return tmp;
//
//}
