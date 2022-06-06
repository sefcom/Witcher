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



bool pattern_in_bytes(unsigned char *target, int target_len, unsigned char *pattern, int pattern_len){
  if (target_len <= pattern_len){
      return false;
  }
  for (int i = 0; i < target_len-pattern_len; i ++) {
      bool found = true;
      for (int j = 0; j < pattern_len; j ++) {

          if (pattern[j] == '.'){
              if (target[i]>= 0x20 && target[i] < 0x7f) {
                printf("%c",target[i]);
            } else {
                printf("\\x%02x",target[i]);
            }
              i++;
              continue;
          } else if (pattern[j] == '~'){
            if (target[i]>= 0x20 && target[i] < 0x7f) {
                while (target[i]>= 0x20 && target[i] < 0x7f) {
                  printf("%c",target[i]);
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
          if (target[i]>= 0x20 && target[i] < 0x7f) {
                printf("%c",target[i]);
            } else {
                printf("\\x%02x",target[i]);
            }
          i++;

      }
      printf("\n");
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

    print_repr(stderr, cptr, len);
    fprintf(stderr, "\n");
    if (pattern_in_bytes(cptr, len, jdbc_msg4, 3)){
        if (pattern_in_bytes(cptr, len, jdbc_msg1, 18)){
            char* strict = getenv("STRICT");
            //raise(SIGSEGV);
            if (strict){
                printf("FOUND STRICT=%s, RAISING SIGSEGV\n", strict);
                raise(SIGSEGV);
            } else {
                printf("RECV ERROR FROM DATABASE FOUND!!!!! But not escalating... \n");
            }
            fprintf(stderr, "[*] Saw JDBC message  ");
            print_repr(stderr, cptr, len);
            fprintf(stderr, "\n");
            return;
        }
    }
    printf("\nNOT FOUND\n");
}

int main(){

   unsigned char *t1 = "\x02\x00\x00\x00,\x00\x00\x00\x17unexpected token: SMITH\x00\x00\x00\x05\x34\x32\x35\x38\x31\xff\xff\xea\x33\x7f";
   unsigned char *t2 = "1\xff\xff\xea\x33\x7f";
   jdbc_error_check(t1, 46);
   unsigned char *t3 = "\x02\x00\x00\x00(\x00\x00\x00\x13malformed string: '\x00\x00\x00\x05\x34\x32\x35\x38\x31\xff\xff\xea\x33\x7f";
   jdbc_error_check(t3, 42);


}