

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

#define __USE_GNU
#include <dlfcn.h>

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
    if (! strict){
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
                if (strstr(val, "STRICT")){
                    strict = val+7;
                }

            }
        }
    }
    if (strict){
        char* httpreqr_pidfile = "/tmp/httpreqr.pid";
        int kill_res = 0;
        FILE *fco = NULL;
        char *alt_fconfn = "/tmp/witcher.log";
        if( access( alt_fconfn, F_OK ) == 0 && access( alt_fconfn, W_OK ) == 0 ) {
            fco = fopen(alt_fconfn, "a");
        }
        if (fco) {
            fprintf(fco, "checking for pid file\n");
            fflush(fco);
        }
        if( access( httpreqr_pidfile, F_OK ) == 0) {
            int httpreqr_pid = 0;
            FILE *pidfile = fopen(httpreqr_pidfile, "r");
            fscanf (pidfile, "%d", &httpreqr_pid);
            fclose(pidfile);
            if (fco) {
                fprintf(fco, "\033[36m[Witcher-dash] sending SIGSEGV to reqr_pid=%d  \033[0m\n", httpreqr_pid );
            }
            if (httpreqr_pid != 0){
                kill_res = kill(httpreqr_pid, SIGSEGV);
            }
            if (fco) {
                fprintf(fco, "\033[36m kill_res = %d  \033[0m\n", kill_res);
            }
        } else {
            fprintf(stderr, "Error encountered, strict=%s\n", strict);
            int strictval = atoi(strict);
            send_signal(strictval);
       }
    } else {
        printf("RECV ERROR FROM DATABASE FOUND!!!!! But not escalating... STRICT=%s \n", strict);
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

}

