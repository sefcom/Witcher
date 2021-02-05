#include <unistd.h>
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

bool error_in_buf(char *buf, int len);

ssize_t (*real_send)(int sockfd, const void *buf, size_t len, int flags) = NULL;

ssize_t send(int sockfd, const void *buf, size_t len, int flags){
	real_send= dlsym(RTLD_NEXT, "send");

	ssize_t results = real_send(sockfd, buf, len, flags);
	char *wl_fpath = "/tmp/do_witcher_log.env";

	if (getenv("WITCHER_LOG") || access( wl_fpath, F_OK ) == 0 ){
		printf("writing out for witcher log");
		unsigned char *cptr = (unsigned char *)(buf);
		bool found = false;
		for (int lp=0; lp < 10 && lp < len; lp++){
			if (cptr[lp] == 0) {
				found = true;
			}
		}
		if (found){
			FILE *errorlog= fopen("/tmp/sqlcmds.log","a+");
			fprintf(errorlog,"SEND >>>>>");

			fprintf(errorlog, "\nERROR:\n");
			for (int lp=0; lp < len; lp++){
				if (cptr[lp]>= 0x20 && cptr[lp] < 0x7f) {
				  fprintf(errorlog,"%c",cptr[lp]);
				} else {
					fprintf(errorlog,"\\x%02x",cptr[lp]);

				}
			}
			fprintf(errorlog,"END SEND <<<<< \n");
			fflush(errorlog);
			fclose(errorlog);
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
			FILE *errorlog= fopen("/tmp/sqlerrors.log","a+");
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
	char error_msg[] = "syntax error at or\x00";
  int error_msg_len = strlen(error_msg) +1;

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
