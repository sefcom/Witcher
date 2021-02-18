
#include <string.h>     /* For the real memset prototype.  */
#include <signal.h>
#include <stdio.h>
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

bool file_exists (char *filename) {
  struct stat   buffer;
  bool retval = stat (filename, &buffer) == 0;
  return retval;
}

bool error_in_buf(char *buf, int len);

void report_cmd(char *const argv[]){
    char *wl_fpath = "/tmp/shell.log";

    FILE *cmdlog = fopen(wl_fpath,"a+");
    if (cmdlog){
        for (char * const* argv_temp = argv; *argv_temp != 0; argv_temp++)
        {
            char *thisArg = *argv_temp;
            fprintf(cmdlog, "%s,", thisArg);
        }
        fprintf(cmdlog, "\n");
        fclose(cmdlog);
    } else {
        printf("Unable to open command log %s\n", wl_fpath);

    }

}

int (*real_execve)(const char *filename, char *const argv[], char *const *envp) = NULL;
//int execl(const char *pathname, const char *arg, ... /* (char  *) NULL */);
//int execlp(const char *file, const char *arg, ... /* (char  *) NULL */);
//int execle(const char *pathname, const char *arg, .../*, (char *) NULL, char *const envp[] */);
int (*real_execv)(const char *pathname, char *const argv[]) = NULL;
int (*real_execvp)(const char *file, char *const argv[]) = NULL;
int (*real_execvpe)(const char *file, char *const argv[], char *const envp[]) = NULL;

int execv(const char *pathname, char *const argv[]) {

    fflush(stdout);
    real_execv= dlsym(RTLD_NEXT, "execv");

    report_cmd(argv);
	ssize_t results = real_execv(pathname, argv);


    return results;
}

int execvp(const char *file, char *const argv[]){
    real_execvp= dlsym(RTLD_NEXT, "execvp");

    report_cmd(argv);
	ssize_t results = real_execvp(file, argv);


    return results;
}

int execvpe(const char *file, char *const argv[], char *const envp[]){
    real_execvpe= dlsym(RTLD_NEXT, "execvpe");

    report_cmd(argv);
	ssize_t results = real_execvpe(file, argv, envp);

    return results;
}

int execve(const char *filename, char *const argv[], char *const *envp){
	real_execve= dlsym(RTLD_NEXT, "execve");

    report_cmd(argv);

	ssize_t results = real_execve(filename, argv, envp);

    return results;

}


ssize_t (*real_send)(int sockfd, const void *buf, size_t len, int flags) = NULL;

ssize_t send(int sockfd, const void *buf, size_t len, int flags){
    real_send= dlsym(RTLD_NEXT, "send");

	ssize_t results = real_send(sockfd, buf, len, flags);
	char *wl_fpath = "/tmp/do_witcher_log.env\x00";

	if (getenv("WITCHER_LOG") || file_exists(wl_fpath)){
		unsigned char *cptr = (unsigned char *)(buf);
		bool found = false;
		for (size_t lp=0; lp < 10 && lp < len; lp++){
			if (cptr[lp] == 0) {
				found = true;
			}
		}
		if (found){
		    char *slog_fpath = "/tmp/sqlcmds.log\x00";

			if (!file_exists(slog_fpath)){
                FILE *tf = fopen(slog_fpath,"a+");
                fclose(tf);
                chmod(slog_fpath, 0x1b6); // 0x1b6 == 0o666
            }
            FILE *slog_fp= fopen(slog_fpath,"a");
			fprintf(slog_fp,"SEND >>>>>");

			fprintf(slog_fp, "\nERROR:\n");
			for (size_t lp=0; lp < len; lp++){
				if (cptr[lp]>= 0x20 && cptr[lp] < 0x7f) {
				  fprintf(slog_fp,"%c",cptr[lp]);
				} else {
					fprintf(slog_fp,"\\x%02x",cptr[lp]);

				}
			}
			fprintf(slog_fp,"END SEND <<<<< \n");
			fflush(slog_fp);
			fclose(slog_fp);
		}

	}
	return results;

}

static ssize_t (*real_recvfrom)(int sockfd, void *buf, size_t len, int flags, struct sockaddr *src_addr, socklen_t *addrlen) = NULL;
ssize_t recvfrom(int sockfd, void *buf, size_t len, int flags, struct sockaddr *src_addr, socklen_t *addrlen) {

	real_recvfrom= dlsym(RTLD_NEXT, "recvfrom");
	ssize_t results = real_recvfrom(sockfd, buf, len, flags, src_addr, addrlen);
	char* strict = getenv("STRICT");

	if (error_in_buf(buf, len)) {
		if (strict){
		  raise(SIGSEGV);
		} else {
		  printf("RECV FROM ERROR FROM DATABASE FOUND!!!!! \n");
		}
	}
  	return results;
}

static ssize_t (*real_recv) (int sockfd, void *buf, size_t len, int flags) = NULL;

bool error_in_buf(char *cptr, int len){
	char psql_error_msg[] = "syntax error at or \x00";
	char mysql_error_msg[] ="You have an error i\x00";
	int cmplen = strlen(mysql_error_msg);
	char cmp_str[cmplen+1];
	for (int i = 0; i < len; i++) {
		memset(cmp_str, 0, cmplen+1);
        memcpy(cmp_str, cptr+i, cmplen);
        if (strncmp(psql_error_msg, cmp_str, cmplen) == 0 || strncmp(mysql_error_msg, cmp_str, cmplen) == 0) {
            char *serror_fpath = "/tmp/sqlerrors.log\x00";
			if (!file_exists(serror_fpath)){
                FILE *tf = fopen(serror_fpath,"a+");
                fclose(tf);
                chmod(serror_fpath, 0x1b6); // 0x1b6 == 0o666
            }
            FILE *errorlog= fopen(serror_fpath,"a");

			fprintf(errorlog,">>>>> RECV >>>>>");

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
			fprintf(errorlog,"<<<<< END <<<<< \n");
			fflush(errorlog);
			fclose(errorlog);
			return true;
        }
    }
    return false;
}

ssize_t recv (int sockfd, void *buf, size_t len, int flags) {

	real_recv= dlsym(RTLD_NEXT, "recv");
	ssize_t results = real_recv(sockfd, buf, len, flags);
	char* strict = getenv("STRICT");

	if (error_in_buf(buf, len)) {
		if (strict){
		  raise(SIGSEGV);
		} else {
		  printf("RECV ERROR FROM DATABASE FOUND!!!!! \n");
		}
	}
//	char error_msg[] = "syntax error at or\x00";
//  int error_msg_len = strlen(error_msg) +1;

  //printf("%s\n", buf);
//unsigned char *cptr = (unsigned char *)(buf);
//  for (int i = 0; i < len; i ++) {
//        if ((i+error_msg_len) >= len){
//          break;
//        }
//        char cmp_str[error_msg_len];
//        memcpy(cmp_str, cptr+i, error_msg_len);
//        cmp_str[error_msg_len-1] = '\x00';
//        if (strcmp(error_msg, cmp_str) == 0){
//			printf("\nBUF:%s\n", buf);
//			printf("RAISING SIGSEGV\n");
//
//			break;
//        }
//  }
  	return results;
}
