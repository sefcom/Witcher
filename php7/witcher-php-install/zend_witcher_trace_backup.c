#include "../Zend/zend_compile.h"
#include "zend.h"
#include "zend_modules.h"

#include <unistd.h>
#include <string.h>     /* For the real memset prototype.  */
#include <stdio.h>
#include <stdlib.h>
#include <stdbool.h>
#include <sys/types.h>
#include <sys/shm.h>
#include <sys/wait.h>

int value_diff_changes(char *var1, char *var2);
void value_diff_report(char *var1, char *var2, int bitmapLoc);
void var_diff_report(char *var1, int bitmapLoc, int var_type);
void dbg_printf(const char *fmt, ...);

#ifdef WITCHER_DEBUG
#define debug_print(xval) \
            do { dbg_printf xval; } while (0)
#else
#define debug_print(fmt, ...)
#endif

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
/***** new for HTTP direct ********/
static int afl_meta = 0,  current_afl_id=0, ins_count=0;
bool firstpass = true;
struct httpreqr_info_t {
    int initialized;
    int afl_id;
    int port;
    int reqr_process_id;
    int process_id;
    char error_type[20]; /* SQL, Command */
    char error_msg[100];
    bool capture;
};
static struct httpreqr_info_t *httpreqr_info = NULL;

/***** END new for HTTP direct ********/

#define MAPSIZE 65536
#define TRACE_SIZE 128*(1024*1024) // X * megabytes

#define SHM_ENV_VAR         "__AFL_SHM_ID"

#define STDIN_FILENO 0

static int last = 0;
static int op = 0;

static unsigned char *afl_area_ptr = NULL;

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
char *login_cookie = NULL;
char *witcher_print_op = NULL;

char *main_filename;
char session_id[40];
int saved_session_size = 0;

int trace[TRACE_SIZE];
int trace_index = 0;

int pipefds[2];

int top_pid=0;

struct rollback_entry *rollback_ll[3];

void dbg_printf(const char *fmt, ...)
{
    va_list args;
    va_start(args, fmt);
    vfprintf(stderr, fmt, args);
    va_end(args);
}

/**
* Mostly taken from the afl_forkserver code provided with AFL
* Injects a fork server into php_cgi to speed things up
*/
static void afl_forkserver() {

    static unsigned char tmp[4];

    if (!afl_area_ptr) return;
    if (write(FORKSRV_FD + 1, tmp, 4) != 4) return;

    afl_forksrv_pid = getpid();

    /* All right, let's await orders... */
    int claunch_cnt = 0;
    while (1) {

        pid_t child_pid = -1;
        int status, t_fd[2];

        /* Whoops, parent dead? */
        if (read(FORKSRV_FD, tmp, 4) != 4) exit(2);

        /* Establish a channel with child to grab translation commands. We'll
           read from t_fd[0], child will write to TSL_FD. */
        if (pipe(t_fd) || dup2(t_fd[1], TSL_FD) < 0) exit(3);
        close(t_fd[1]);
        claunch_cnt ++;
        child_pid = fork();

        fflush(stdout);
        if (child_pid < 0) exit(4);

        if (!child_pid) {  // child_pid == 0, i.e., in child

            /* Child process. Close descriptors and run free. */
            debug_print(("\t\t\tlaunch cnt = %d Child pid == %d, but current pid = %d\n", claunch_cnt, child_pid, getpid()));
            fflush(stdout);
            afl_fork_child = 1;
            close(FORKSRV_FD);
            close(FORKSRV_FD + 1);
            close(t_fd[0]);
            return;

        }

        /* Parent. */

        close(TSL_FD);

        if (write(FORKSRV_FD + 1, &child_pid, 4) != 4) {
            debug_print(("\t\tExiting Parent %d with 5\n", child_pid));
            exit(5);
        }


        /* Get and relay exit status to parent. */
        int waitedpid = waitpid(child_pid, &status, 0);
        if (waitedpid < 0) {
            printf("\t\tExiting Parent %d with 6\n", child_pid);
            exit(6);
        }

        if (write(FORKSRV_FD + 1, &status, 4) != 4) {
            exit(7);
        }
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

char* replace_char(char* str, char find, char replace){
    char *current_pos = strchr(str,find);
    while (current_pos){
        *current_pos = replace;
        current_pos = strchr(current_pos,find);
    }
    return str;
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

/**
 * sets up the cgi environment for a cgi request
 */
void setup_cgi_env(){
    debug_print(("[\e[32mWitcher\e[0m] Starting SETUP_CGI_ENV  \n"));
    setenv("DOCUMENT_ROOT","/app", 1); //might be important if your cgi read/writes there
    setenv("HTTP_REDIRECT_STATUS","1",1);

    setenv("HTTP_ACCEPT", "*/*", 1);
    setenv("GATEWAY_INTERFACE", "CGI/1.1", 1);
    setenv("PATH", "/usr/bin:/tmp:/app", 1); //HTTP URL PATH
    setenv("REQUEST_METHOD", "POST", 1); //Usually GET or POST
    setenv("REMOTE_ADDR","127.0.0.1",1);

    setenv("CONTENT_TYPE","application/x-www-form-urlencoded",1);
    setenv("REQUEST_URI", "SCRIPT",1);
    // strict is set for the modified /bin/dash
#ifdef WITCHER_DEBUG
    FILE *logfile = fopen ("/tmp/wrapper.log","a+");
    fprintf (logfile, "----Start----\n");
    //printf("starting\n");
#endif
    static int MAX_CMDLINE_LEN=128*1024;

    static int   num_env_vars = sizeof(env_vars) / sizeof(char*);

    char  in_buf[MAX_CMDLINE_LEN];
    memset(in_buf, 0, MAX_CMDLINE_LEN);
    size_t bytes_read = read(0, in_buf, MAX_CMDLINE_LEN - 2);

    int zerocnt = 0;
    for (int cnt=0;cnt<MAX_CMDLINE_LEN;cnt++){
        if (in_buf[cnt] == 0){
            zerocnt++;
        }
        if (zerocnt == 3){
            break;
        }
    }

    pipe(pipefds);

    dup2(pipefds[0], STDIN_FILENO);
    //close(STDIN_FILENO);

    int real_content_length = 0;
    char* saved_ptr = (char *) malloc(MAX_CMDLINE_LEN);
    char* ptr = in_buf;
    int   rc  = 0;
    char* cwd;
    int errnum;
    //struct passwd *p = getpwuid(getuid());  // Check for NULL!
    long size = pathconf(".", _PC_PATH_MAX);
    char* dirbuf = (char *)malloc((size_t)size);
    size_t bytes_used = 0;

    // loop through the strings read via stdin and break at each \x00
    // Cookies, Query String, Post (via re-writting to stdin)
    char* cookie = (char *) malloc(MAX_CMDLINE_LEN);
    memset(cookie, 0, MAX_CMDLINE_LEN);
    login_cookie = getenv("LOGIN_COOKIE");
    witcher_print_op = getenv("WITCHER_PRINT_OP");
    if (login_cookie){
        strcat(cookie, login_cookie);
        setenv(env_vars[0], cookie, 1);
        if (!strchr(login_cookie,';')){
            strcat(login_cookie,";");
        }
        debug_print(("[\e[32mWitcher\e[0m] LOGIN COOKIE %s\n", login_cookie));
        char * name = strtok(login_cookie, ";=");
        while( name != NULL ) {
            char * value = strtok(NULL, ";=");
            if (value != NULL){
                debug_print(("\t%s==>%s\n", name, value)); //printing each token
            } else {
                debug_print(("\t%s==> NADA \n", name )); //printing each token
            }

            if (value != NULL){
                int thelen = strlen(value);
                if (thelen >= 24 && thelen <= 32){
                    debug_print(("[\e[32mWitcher\e[0m] session_id = %s, len=%d\n", value, thelen));
                    strcpy(session_id, value);
                    char filename[64];
                    char sess_fn[64];
                    sprintf(sess_fn, "../../../../../../../tmp/sess_%s", value );
                    setenv("SESSION_FILENAME",sess_fn,1);

                    sprintf(filename, "../../../../../../../tmp/save_%s", value );

                    //saved_session_size = fsize(filename);

                    debug_print(("\t[WC] SESSION ID = %s, saved session size = %d\n", filename, saved_session_size));
                    break;
                }
            }
            name = strtok(NULL, ";=");
        }
        debug_print(("[\e[32mWitcher\e[0m] LOGIN ::> %s\n", cookie));
    }
    char* mandatory_cookie = getenv("MANDATORY_COOKIE");
    if (mandatory_cookie && strlen(mandatory_cookie) > 0){
        strcat(cookie, "; ");
        strcat(cookie, mandatory_cookie);
        debug_print(("[\e[32mWitcher\e[0m] MANDATORY COOKIE = %s\n", cookie));
    }
    setenv(env_vars[0], cookie, 1);
    char* post_data = (char *) malloc(MAX_CMDLINE_LEN);
    memset(post_data, 0, MAX_CMDLINE_LEN);
    char* query_string = (char *) malloc(MAX_CMDLINE_LEN);
    memset(query_string, 0, MAX_CMDLINE_LEN);

    char* mandatory_get = getenv("MANDATORY_GET");
    if (mandatory_get && strlen(mandatory_get) > 0){
        strcat(query_string, mandatory_get);
        debug_print(("\n[WC] MANDATORY GET = %s\n", query_string));
    }
    setenv(env_vars[1], query_string, 1);

    while (!*ptr){
        bytes_used++;
        ptr++;
        rc++;
    }
    while (*ptr || bytes_used < bytes_read) {
        memcpy(saved_ptr, ptr, strlen(ptr)+1);
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
                strcat(query_string, "&");
                strcat(query_string, saved_ptr);

                setenv(env_vars[rc], query_string, 1);

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
                debug_print(("\e[32m\tDONE JSON=%s\e[0m\n", saved_ptr));
            }

            real_content_length = write(pipefds[1], saved_ptr, strlen(saved_ptr));
            write(pipefds[1], "\n", 1);

            //debug_print(("\tReading from %d and writing %d bytes to %d \n", real_content_length, pipefds[0], pipefds[1]));
            //debug_print(("\t%-15s = \033[33m%s\033[0m \n", "POST", saved_ptr));

            char snum[20];
            sprintf(snum, "%d", real_content_length);
            memcpy(post_data, saved_ptr, strlen(saved_ptr)+1);
            setenv("E", saved_ptr, 1);
            setenv("CONTENT_LENGTH", snum, 1);
        }

        rc++;
        while (*ptr){
            ptr++;
            bytes_used++;
        }
        ptr++;
        bytes_used++;

    }
    debug_print(("[\e[32mWitcher\e[0m] %lib read / %lib used \n", bytes_read, bytes_used));
    if (afl_area_ptr != NULL) {
        afl_area_ptr[0xffdd]=1;
    }
    if (cookie){
        debug_print(("\t%-14s = \e[33m %s\e[0m\n", "COOKIES", cookie));
    }
    if (query_string){
        debug_print(("\t%-14s = \e[33m %s\e[0m\n", "QUERY_STRING", query_string));
    }
    if (post_data){
        debug_print(("\t%-9s (%s) = \e[33m %s\e[0m\n", "POST_DATA", getenv("CONTENT_LENGTH"), post_data ));
    }
    debug_print(("\n"));

    free(saved_ptr);
    free(cookie);
    free(query_string);
    free(post_data);

    close(pipefds[0]);
    close(pipefds[1]);
#ifdef WITCHER_DEBUG
    fclose(logfile);
#endif

    fflush(stderr);

}
/************************************************************************************************/
/********************************** HTTP direct **************************************************/
/************************************************************************************************/
void afl_error_handler(int nSignum) {
    // make sure most recent
    if (getenv("AFL_META_INFO_ID")){
        FILE *elog = fopen("/tmp/witcher.log","a+");
        int mem_key = atoi(getenv("AFL_META_INFO_ID"));
        int shm_id = shmget(mem_key , sizeof(struct httpreqr_info_t), 0666);
        if (shm_id  >= 0 ) {
            httpreqr_info = (struct httpreqr_info_t *) shmat(shm_id, NULL, 0);  /* attach */
            if (elog) {
                fprintf(elog, "\033[36m[Witcher] set httpreqr_info=%p!!!\033[0m\n", httpreqr_info);
            }
        }
        if (elog){
            fprintf(elog, "\033[36m[Witcher] sending SEGSEGV to reqr_process_id=%d pid=%d last_insn=%d afl_id=%d capture=%d!!!\033[0m\n",
                    httpreqr_info->reqr_process_id, getpid(), ins_count, httpreqr_info->afl_id, httpreqr_info->capture);
            fclose(elog);
        }
        if (httpreqr_info->reqr_process_id != 0){
            kill(httpreqr_info->reqr_process_id, SIGSEGV);
        }
        //strcpy(httpreqr_info->error_type,"COMMAND");
    } else {
        FILE *elog = fopen("/tmp/witcher.log","a+");
        if (elog){
            fprintf(elog, "\033[36m[Witcher] detected error in child but AFL_META_INFO_ID is not set. !!!\033[0m\n");
            fclose(elog);
        }
    }
}

void remove_shm(void){
    FILE *elog = fopen("/tmp/witcher.log","a+");
    if (elog) {
        fprintf(elog, "\n\n@@@@@@@@@@@@@@@@@ IN FUNC @@@@@@@@@@@@@@@@@\n\n");
        fclose(elog);
    }
    printf("\n\n@@@@@@@@@@@@@@@@@ IN FUNC @@@@@@@@@@@@@@@@@\n\n");
    if (httpreqr_info && httpreqr_info->afl_id != 0 ){
        printf("\n\n@@@@@@@@@@@@@@@@@ REMOVING SHM @@@@@@@@@@@@@@@@@\n\n");
        int mem_key;
        if (afl_meta){
            mem_key = afl_meta;
        } else if (getenv("")){
            mem_key = atoi(getenv("AFL_META_INFO_ID"));
        } else {
            printf("\n\n PSYCHE \n\n");
            return;
        }

        int shm_id = shmget(mem_key , sizeof(struct httpreqr_info_t), 0666);
        if (shm_id  >= 0 ) {
            shmctl(shm_id, IPC_RMID, NULL);
        }
    }
}

void init_shared_mem(void) {
    if (! getenv("AFL_META_INFO_ID")){
        char *fname = "/tmp/witcher.env";
        if( access( fname, R_OK ) == 0 ) {
            FILE *envf = fopen(fname,"r");
            char val[257], ch;
            int charindex = 0;

            if (envf){
                while((ch = fgetc(envf)) != EOF && charindex < 256) {
                    val[charindex] = ch;
                    charindex++;
                }
                if (strstr(val, "AFL_META_INFO_ID")){
                    setenv("AFL_META_INFO_ID", val+17, 1 );
                }
            }
        }
    }
    if (afl_meta == 0 && getenv("AFL_META_INFO_ID") ){
        afl_meta = atoi(getenv("AFL_META_INFO_ID") );
    }
    if (httpreqr_info == NULL && afl_meta != 0) {
        bool create_shm = true;
        // clean up last shared memory area
        int mem_key = atoi(getenv("AFL_META_INFO_ID"));
        int shm_id = shmget(mem_key , sizeof(struct httpreqr_info_t), 0666);
        if (shm_id  >= 0 ) {
            httpreqr_info = (struct httpreqr_info_t *) shmat(shm_id, NULL, 0);  /* attach */
            if (httpreqr_info && httpreqr_info->afl_id != 0 ){
                // if record exists we only clean up if afl_id is already set and then we fork,
                // hopefully this will limit clean up to when needed even when the damn thing forks
                shmctl(shm_id, IPC_RMID, NULL);
            } else {
                create_shm = false;
            }
        }
        if (create_shm){
            printf("\n\n*** creating shm memory %x \n", mem_key);
            shm_id = shmget(mem_key , sizeof(struct httpreqr_info_t), IPC_CREAT | 0666);
            if (shm_id < 0 ) {
                //printf("*** shmget error (server) ***\n");
                perror("*** shmget error (server) *** ERROR: ");
                exit(1);
            }

        } else {
            atexit(remove_shm);
            printf("\n\nUsing existing shm\n\n");
        }

        httpreqr_info = (struct httpreqr_info_t *) shmat(shm_id, NULL, 0);  /* attach */
        memset(httpreqr_info, 0, sizeof(struct httpreqr_info_t));

        httpreqr_info->process_id = getpid();
        if (httpreqr_info->initialized != 199){
            printf("\nAFL %d info afl_meta=%d httpreqr_id=%u state=%d AFL info addr=%p id=%d pid=%d, cap=%d", getpid(), afl_meta, shm_id, httpreqr_info->initialized, httpreqr_info, httpreqr_info->afl_id,  httpreqr_info->process_id, httpreqr_info->capture );
            FILE *elog = fopen("/tmp/witcher.log","a+");
            if (elog){
                fprintf(elog, "AFL %d info afl_meta=%d httpreqr_id=%u state=%d AFL info addr=%p id=%d pid=%d, cap=%d\n", getpid(), afl_meta, shm_id, httpreqr_info->initialized, httpreqr_info, httpreqr_info->afl_id,  httpreqr_info->process_id, httpreqr_info->capture );
                fclose(elog);
            }
        }

        httpreqr_info->initialized = 199;

        printf("\n");
    }


    if (httpreqr_info){
        if (firstpass){
            firstpass = false;
            printf("\033[36mWitcher is being executed and adding sig handler\n\033[0m");
            FILE *elog = fopen("/tmp/witcher.log","a+");
            if (elog){
                fprintf(elog, "\033[36mWitcher is being executed and adding sig handler\n\033[0m");
                fclose(elog);
            }
            signal(SIGUSR1, afl_error_handler);
            fflush(stdout);
        }

        //printf("[WC] %d \n", httpreqr_info->afl_id);
        if (afl_area_ptr == NULL && httpreqr_info->afl_id != 0){
            httpreqr_info->initialized = 10;
            //printf("[WC] Using %d to attach to afl_area_ptr\n", httpreqr_info->afl_id);
            current_afl_id = httpreqr_info->afl_id;
            afl_area_ptr = (unsigned char*)  shmat(httpreqr_info->afl_id, NULL, 0);
        }
//        if (httpreqr_info->initialized == 10){
//            printf("aap=%p init=%d afl_id=%d port=%d rpid=%d pid=%d err=%s->%s, cap=%d\n", afl_area_ptr, httpreqr_info->initialized, httpreqr_info->afl_id, httpreqr_info->port,
//               httpreqr_info->reqr_process_id, httpreqr_info->process_id, httpreqr_info->error_type, httpreqr_info->error_msg, httpreqr_info->capture);
//
//        }
    }

}


/************************************************************************************************/
/********************************** END HTTP direct **************************************************/
/************************************************************************************************/


unsigned char *cgi_get_shm_mem(char * ch_shm_id) {
    char *id_str;
    int shm_id;

    if (afl_area_ptr == NULL){
        id_str = getenv(SHM_ENV_VAR);
        if (id_str) {
            shm_id = atoi(id_str);
            afl_area_ptr = shmat(shm_id, NULL, 0);
        } else {

            afl_area_ptr = malloc(MAPSIZE);
        }
    }
    return afl_area_ptr;

}

/**
 * The witcher init, is needed at the start of the script and is only executed once per child
 * it sets up the tracing enviornment
 */
void witcher_cgi_trace_init(char * ch_shm_id) {
    debug_print(("[\e[32mWitcher\e[0m] in Witcher trace\n\t\e[34mSCRIPT_FILENAME=%s\n\t\e[34mAFL_PRELOAD=%s\n\t\e[34mLD_LIBRARY_PATH=%s\e[0m\n", getenv("SCRIPT_FILENAME"), getenv("AFL_PRELOAD"), getenv("LD_LIBRARY_PATH"), getenv("LOGIN_COOKIE")));

    if (getenv("WC_INSTRUMENTATION")){
        start_tracing = true;
        debug_print(("[Witcher] \e[32m WC INSTUMENTATION ENABLED \e[0m "));
    } else {
        debug_print(("[Witcher] WC INSTUMENTATION DISABLED "));
    }

    if (getenv("NO_WC_EXTRA")){
        wc_extra_instr = false;
        debug_print((" WC Extra Instrumentation DISABLED \n"));
    } else {
        debug_print((" \e[32m WC Extra Instrumentation ENABLED \e[0m\n"));
    }
    top_pid = getpid();
    cgi_get_shm_mem(ch_shm_id);

    char *id_str = getenv(SHM_ENV_VAR);

    if (id_str) {
        afl_forkserver();
        debug_print(("[\e[32mWitcher\e[0m] Returning with pid %d \n\n", getpid()));
//        fflush(stdout);
//        int trace_sum = 0;
//        int slot_cnt = 0;
//        for (int x=0;x<MAPSIZE;x++){
//            trace_sum += afl_area_ptr[x]*x;
//            if (afl_area_ptr[x] > 0) {
//                slot_cnt++;
//            }
//        }
//        debug_print(("[Witcher] Trace_sum=%d, Slot_cnt=%d \n", trace_sum, slot_cnt));
//        fflush(stdout);
    }

    setup_cgi_env();

    fflush(stdout);
}

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
        debug_print(("[Witcher] Starting var matcher, see /tmp/match.dat \n"));
        for (int i =0; i < variables_ptr[var_type]; i++){
            if (variables_used[var_type][i] == 3){
                continue;
            }
            comp_len = strlen(variables[var_type][i]);
            // take the bigger of the two
            comp_len = (comp_len > in_var_len) ? comp_len : in_var_len;
            if (strncmp(variables[var_type][i], var1, comp_len) == 0){
#ifdef WITCHER_DEBUG
                FILE *logfile2 = fopen("/tmp/match.dat","a+");
                fprintf(logfile2,"TOTAL MATCH '%s' '%s' \n ", var1, variables[var_type][i]);
                fflush(logfile2);
                fclose(logfile2);
#endif
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
#ifdef WITCHER_DEBUG
            FILE *logfile2 = fopen("/tmp/match.dat","a+");
            fprintf(logfile2,"BEST MATCH being used '%s' '%s' %d \n ", var1, variables[best_change_pointer], best_change);
            //fprintf(logfile2,"\tAFTER match=%d afl=%d \n", matchcnt, afl_area_ptr[bitmapLoc]);
            fflush(logfile2);
            fclose(logfile2);
#endif
            add_var_to_rollback(variables[var_type][best_change_pointer], best_change, bitmapLoc, var_type);
            variables_used[var_type][best_change_pointer] = 1;

            value_diff_report(var1, variables[var_type][best_change_pointer], bitmapLoc);
        }
    }
}


int value_diff_changes(char *var1, char *var2){
    if (var1 == NULL || var2 == NULL){
        return 0;
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
        if (witcher_print_op){
            char logfn[50];
            sprintf(logfn, "/tmp/trace-%s.dat", getenv("WITCHER_PRINT_OP"));
            FILE *logfile2 = fopen("/tmp/match.dat","a+");
            fprintf(logfile2,"MATCH found '%s' '%s' match=%d bm=%d afl=%d afl+1=%d afl+2=%d afl+3=%d \tpid=%d\n ", var1, var2, matchcnt, bitmapLoc, afl_area_ptr[bitmapLoc], afl_area_ptr[bitmapLoc+1], afl_area_ptr[bitmapLoc+2],afl_area_ptr[bitmapLoc+3], getpid());
            fflush(logfile2);
            fclose(logfile2);
        }

    }
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
void witcher_cgi_trace_log_op2(int lineno, int opcode, char *var1, char *var2);

void witcher_cgi_trace_log_op(int lineno, int opcode, char *var1){
    witcher_cgi_trace_log_op2(lineno, opcode, var1,0);
}

void witcher_cgi_trace_log_op2(int lineno, int opcode, char *var1, char *var2)
{

    //static size_t last_len = 0;
    if (start_tracing) {

        op = (lineno << 8) | opcode ; //opcode; //| (lineno << 8);

        if (last != 0) {
            int bitmapLoc = (op ^ last) % MAPSIZE;

            // turned off to disable afl code tracing
            afl_area_ptr[bitmapLoc]++;
//            if (wc_extra_instr && getenv(SHM_ENV_VAR) && opcode < 100){
//
//                fflush(stdout);
//                if (nextVar2_is_a_var > -1) {
//                    var_diff_report(var1, (bitmapLoc*13) % MAPSIZE, nextVar2_is_a_var);
//                } else {
//                    var_report(var1, (bitmapLoc*11) % MAPSIZE);
//                    var_report(var2, (bitmapLoc*13) % MAPSIZE);
//                    value_diff_report(var1, var2, (bitmapLoc*17) % MAPSIZE);
//                }
//            }

            if (login_cookie && witcher_print_op){
                char logfn[50];
                sprintf(logfn, "/tmp/trace-%s.dat", witcher_print_op);

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

        }


//        if (var1 != NULL && getenv(SHM_ENV_VAR) && opcode < 100){
//            fflush(stdout);
//            if (strcmp(var1, "_GET") == 0 ){
//                nextVar2_is_a_var = 1;
//            } else if (strcmp(var1, "_POST") == 0){
//                nextVar2_is_a_var = 2;
//            } else {
//                nextVar2_is_a_var = -1;
//            }
//        } else {
//            nextVar2_is_a_var = -1;
//        }
//        if (trace_index < TRACE_SIZE){
//
//            trace[trace_index++] = op;
//        }
        last = op;

    }


}

void witcher_cgi_trace_finish()
{
    start_tracing = false;

    if (witcher_print_op){
        char logfn[50];
        sprintf(logfn, "/tmp/trace-%s.dat", witcher_print_op);
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

    op = 0;
    last = 0;
    trace_index = 0;

}



zval* get_zval(zend_execute_data *execute_data, const zend_op *opline, zend_uchar op_type, const znode_op *node)
{
	zval *ret;
    switch (op_type) {
		case IS_CONST: // 1
			ret = RT_CONSTANT(opline, *node);
			break;
		case IS_TMP_VAR: // 2
		case IS_VAR:   // 4
			ret = EX_VAR(node->var);
			break;
		case IS_CV: // 8
			ret = EX_VAR(node->var);
			break;
		default:
			ret = NULL;
			break;
	}
	return ret;
}
void vld_start_trace(){
    if (getenv("WITCHER_PRINT_OP")){
        char tracefn[50];
        sprintf(tracefn, "/tmp/trace-%s.dat", getenv("WITCHER_PRINT_OP"));
        FILE *ofile = fopen(tracefn, "w");
        fclose(ofile);
    }

}

void vld_external_trace(zend_execute_data *execute_data, const zend_op *opline){
    FILE *ofile = NULL;

    if (witcher_print_op){
        const char *opname = zend_get_opcode_name(opline->opcode);
        char tracefn[50];
        sprintf(tracefn, "/tmp/trace-%s.dat", witcher_print_op);
        ofile = fopen(tracefn, "a");
        debug_print(("%d] %s (%d)   %d    %d \n",opline->lineno, opname, opline->opcode, opline->op1_type, opline->op2_type));
        fprintf(ofile, "%d] %s (%d)   %d    %d \n",opline->lineno, opname, opline->opcode, opline->op1_type, opline->op2_type);
    }

    if (start_tracing) {
        op = (lineno << 8) | opcode ; //opcode; //| (lineno << 8);

        if (last != 0) {
            int bitmapLoc = (op ^ last) % MAPSIZE;
            afl_area_ptr[bitmapLoc]++;
        }
    }
    last = op;

    // variable logging disabled
    //zval *wc_op1, *wc_op2, *wc_res;
//    wc_op1 = get_zval(execute_data, opline, opline->op1_type, &opline->op1);
//    wc_op2 = get_zval(execute_data, opline, opline->op2_type, &opline->op2);
//
//    if (wc_op1 != NULL && wc_op2 != NULL && Z_TYPE_P(wc_op1) == IS_STRING && Z_TYPE_P(wc_op2) == IS_STRING) {
//        witcher_cgi_trace_log_op2(opline->lineno, opline->opcode, Z_STR_P(wc_op1)->val, Z_STR_P(wc_op2)->val);
//        if (witcher_print_op){
//          debug_print(("\tBOTH => %s and %s\n", Z_STR_P(wc_op1)->val, Z_STR_P(wc_op2)->val));
//          fprintf(ofile, "\tBOTH => %s and %s\n", Z_STR_P(wc_op1)->val, Z_STR_P(wc_op2)->val);
//
//        }
//    } else if (wc_op1 != NULL && Z_TYPE_P(wc_op1) == IS_STRING) {
//        if (witcher_print_op){
//          debug_print(("\tFirst: %s  \n", Z_STR_P(wc_op1)->val));
//          fprintf(ofile, "\tFirst: %s  \n", Z_STR_P(wc_op1)->val);
//        }
//        //
//        witcher_cgi_trace_log_op(opline->lineno, opline->opcode, Z_STR_P(wc_op1)->val);
//    } else if (wc_op2 != NULL && Z_TYPE_P(wc_op2) == IS_STRING) {
//      if (witcher_print_op){
//        debug_print(("\t\tSecond: %s  \n", Z_STR_P(wc_op2)->val));
//        fprintf(ofile, "\t\tSecond: %s  \n", Z_STR_P(wc_op2)->val);
//      }
//        witcher_cgi_trace_log_op(opline->lineno, opline->opcode, Z_STR_P(wc_op2)->val);
//    }  else {
//        witcher_cgi_trace_log_op(opline->lineno, opline->opcode, 0);
//    }
    if (ofile){
        fflush(ofile);
        fclose(ofile);
    }
}

