

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

#include "sqlcatch.h"

#define __USE_GNU
#include <dlfcn.h>

static char* env_vars[2] = { "HTTP_COOKIE","QUERY_STRING"};

char* replace_char(char* str, char find, char replace){
    char *current_pos = strchr(str,find);
    while (current_pos){
        *current_pos = replace;
        current_pos = strchr(current_pos,find);
    }
    return str;
}

static  char *(*real_getenv)(const char *name) = NULL;

int startsWith(const char *pre, const char *str)
{
    size_t lenpre = strlen(pre),
           lenstr = strlen(str);
    return lenstr < lenpre ? 0 : memcmp(pre, str, lenpre) == 0;
}


static ssize_t (*real_recv) (int sockfd, void *buf, size_t len, int flags) = NULL;

ssize_t recv (int sockfd, void *buf, size_t len, int flags) {

  real_recv= dlsym(RTLD_NEXT, "recv");
  ssize_t results = real_recv(sockfd, buf, len, flags);

  unsigned char *cptr = (unsigned char *)(buf);
  //fprintf(stderr, "\033[36m!!!!!!!!!!!!!!!!!!! Thank you for using the special RECV --->> !!!!!!!!!!!!!!!!!!!!\n\033[0m");
  char error_msg[] = "You have an error i\x00";
  int error_msg_len = strlen(error_msg) +1;
  //fprintf(stderr, "BUF=");
  for (int lp=0; lp < len; lp++){
    if (cptr[lp]>= 0x20 && cptr[lp] < 0x7f) {
      fprintf(stderr, "%c",cptr[lp]);
    } else {
      if (cptr[lp] == 0x0){
        continue;
      } else {
        fprintf(stderr, "\\x%02x",cptr[lp]);
      }

    }
  }
  fprintf(stderr, "\n");
  fflush(stderr);
  //printf("%s\n", buf);

  for (int i = 0; i < len; i ++) {
        if ((i+error_msg_len) >= len){
          break;
        }
        char cmp_str[error_msg_len];
        memcpy(cmp_str, cptr+i, error_msg_len);
        cmp_str[error_msg_len-1] = '\x00';
        if (strcmp(error_msg, cmp_str) == 0){
            fprintf(stderr, "\nERROR:\n", cmp_str);
            for (int lp=i; lp < len; lp++){
                if (cptr[lp]>= 0x20 && cptr[lp] < 0x7f) {
                  fprintf(stderr, "%c",cptr[lp]);
                } else {
                  if (cptr[lp] == 0x0){
                    break;
                  } else {
                    fprintf(stderr, "\\x%02x",cptr[lp]);
                  }

                }
            }
            fprintf(stderr, "\n");
            fflush(stderr);
            DIR* dir = opendir("/tmp/output");
            if (dir) {
                /* Directory exists. */
                closedir(dir);
                FILE *errorlog= fopen("/tmp/output/errors.log","a+");
                fprintf(errorlog,"vvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvv   RECV   vvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvv\n");
                if (getenv("WC_INSTRUMENTATION")){
                    if (getenv("NO_WC_EXTRA")){
                        fprintf(errorlog,"\t%-15s = \033[33m%s\033[0m  \n","Instrumentation", "WiC");
                    } else {
                        fprintf(errorlog,"\t%-15s = \033[33m%s\033[0m  \n","Instrumentation", "ExWiC");
                    }
                }
                int num_env_vars = sizeof(env_vars) / sizeof(char*);
                if (getenv("SCRIPT_FILENAME")){
                    fprintf(errorlog,"\t%-15s = \033[33m%s\033[0m  \n","SCRIPT_FILENAME", getenv("SCRIPT_FILENAME"));
                }
                for (int x=0; x < num_env_vars; x++){
                    fprintf(errorlog, "\t%-15s = \033[33m%s\033[0m  \n", env_vars[x], getenv(env_vars[x]));
                }
                if (getenv("POST_DATA")){
                    fprintf(errorlog,"\t%-15s = \033[33m%s\033[0m  \n","POST", getenv("POST_DATA"));
                }

                fprintf(errorlog, "\nERROR:\n");
                for (int lp=i; lp < len; lp++){
                    if (cptr[lp]>= 0x20 && cptr[lp] < 0x7f) {
                      fprintf(errorlog,"%c",cptr[lp]);
                    } else {
                      if (cptr[lp] == 0x0){
                        break;
                      } else {
                        fprintf(errorlog,"\\x%02x",cptr[lp]);
                      }

                    }

                }
                fprintf(errorlog,"\n^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^\n");
                fflush(errorlog);
                fclose(errorlog);

          }
            dir = opendir("/results");
            if (dir) {
                /* Directory exists. */
                closedir(dir);
                FILE *errorlog= fopen("/results/gen_errors.log","a+");
                fprintf(errorlog,"vvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvv   RECV   vvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvv\n");
                int num_env_vars = sizeof(env_vars) / sizeof(char*);
                if (getenv("WC_INSTRUMENTATION")){
                    if (getenv("NO_WC_EXTRA")){
                        fprintf(errorlog,"\t%-15s = \033[33m%s\033[0m  \n","Instrumentation", "WiC");
                    } else {
                        fprintf(errorlog,"\t%-15s = \033[33m%s\033[0m  \n","Instrumentation", "ExWiC");
                    }
                }

                if (getenv("SCRIPT_FILENAME")){
                    fprintf(errorlog,"\t%-15s = \033[33m%s\033[0m  \n","SCRIPT_FILENAME", getenv("SCRIPT_FILENAME"));
                }
                for (int x=0; x < num_env_vars; x++){
                    fprintf(errorlog, "\t%-15s = \033[33m%s\033[0m  \n", env_vars[x], getenv(env_vars[x]));
                }
                if (getenv("POST_DATA")){
                    fprintf(errorlog,"\t%-15s = \033[33m%s\033[0m  \n","POST", getenv("POST_DATA"));
                }
                if (getenv("SESSION_FILENAME")){
                    char* sess_fn = getenv("SESSION_FILENAME");

                    FILE* fptr = fopen(sess_fn, "r");
                    if (fptr == NULL)
                    {
                        fprintf(errorlog, "Cannot open file %s \n", sess_fn);
                    } else {
                        fprintf(errorlog, "\nSession Data:\n");
                        char c = fgetc(fptr);
                        while (c != EOF)
                        {
                            if (c>= 0x20 && c < 0x7f) {
                              fprintf(errorlog,"%c",c);
                            } else {
                                fprintf(errorlog,"\\x%02x",c);
                            }
                            c = fgetc(fptr);
                        }

                        fclose(fptr);
                        fprintf(errorlog, "\n");
                    }

                    // Read contents from file



                }
                fprintf(errorlog, "\nERROR:\n");
                for (int lp=i; lp < len; lp++){
                    if (cptr[lp]>= 0x20 && cptr[lp] < 0x7f) {
                      fprintf(errorlog,"%c",cptr[lp]);
                    } else {
                      if (cptr[lp] == 0x0){
                        break;
                      } else {
                        fprintf(errorlog,"\\x%02x",cptr[lp]);
                      }

                    }

                }
                fprintf(errorlog,"\n^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^\n");
                fflush(errorlog);
                fclose(errorlog);

          }

            //printf("\nBUF:%s\n", buf);
            fprintf(stderr, "\033[32mRAISING SIGSEGV\n\033[0m");
            char* strict = getenv("STRICT");
            //raise(SIGSEGV);
            if (strict){
              int ppid = getpid();
              kill(ppid, SIGUSR1);
              //raise(SIGUSR1);
            } else {
              fprintf(stderr, "\033[32mRECV ERROR FROM DATABASE FOUND!!!!! \n\033[0m");
            }
            fflush(stderr);

            break;
        }
  }

  return results;

}


