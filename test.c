#include <unistd.h>
#include <string.h>     /* For the real memset prototype.  */
#include <stdio.h>
#include <stdlib.h>
#include <stdbool.h>

static int MAX_CMDLINE_LEN=2000;
#define MAX_VARIABLES 1024

static char* env_vars[2] = { "HTTP_COOKIE","QUERY_STRING"};
static int   num_env_vars = sizeof(env_vars) / sizeof(char*);
char *variables[3][MAX_VARIABLES];
unsigned char variables_used[3][MAX_VARIABLES];
int variables_ptr[3]={0,0,0};
int pipefds[2];

char* replace_char(char* str, char find, char replace){
    char *current_pos = strchr(str,find);
    while (current_pos){
        *current_pos = replace;
        current_pos = strchr(current_pos,find);
    }
    return str;
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
int main( int argc, char *argv[]) {
    char* saved_ptr = (char *) malloc(MAX_CMDLINE_LEN);
    int real_content_length = 0;

    int rc = 0;
    char* cookie = (char *) malloc(MAX_CMDLINE_LEN);
    memset(cookie, 0, 2000);
    char* post_data = (char *) malloc(MAX_CMDLINE_LEN);
    memset(post_data, 0, MAX_CMDLINE_LEN);
    char* query_string = (char *) malloc(MAX_CMDLINE_LEN);
    memset(query_string, 0, MAX_CMDLINE_LEN);
    char  in_buf[MAX_CMDLINE_LEN];
    memset(in_buf, 0, MAX_CMDLINE_LEN);
    size_t bytes_used = 0;
    printf("reading bytes\n");
    size_t bytes_read = read(0, in_buf, MAX_CMDLINE_LEN - 2);

    pipe(pipefds);

    dup2(pipefds[0], STDIN_FILENO);
    char *ptr = in_buf;

    while (!*ptr){
        bytes_used++;
        ptr++;
        rc++;
    }
    while (*ptr || bytes_used < bytes_read) {
        printf("bytes read =  %li, bytes used = %li\n", bytes_read, bytes_used);
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


        }else if(rc == num_env_vars){
//            char *json = getenv("DO_JSON");
//            if (json){
//                saved_ptr = format_to_json(saved_ptr);
//                printf(("\e[32m\tDONE JSON=%s\e[0m\n", saved_ptr));
//            }
            printf("%i %li \n", strlen(saved_ptr), (bytes_read - bytes_used) );
//            real_content_length = write(pipefds[1], saved_ptr, strlen(saved_ptr));
//            write(pipefds[1], "\n", 1);
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
        while (*ptr) {
            bytes_used++;
            ptr++;
        }
        ptr++;
        bytes_used++;
    }
    if (cookie){
        printf("  %-14s = \e[33m %s\e[0m\n", "COOKIES", cookie);
    }
    if (query_string){
        printf("  %-14s = \e[33m %s\e[0m\n", "QUERY_STRING", query_string);
    }
    if (post_data){
        printf("  %-9s (%s) = \e[33m %s\e[0m\n", "POST_DATA", getenv("CONTENT_LENGTH"), post_data );
    }
    close(pipefds[0]);
    close(pipefds[1]);
    return 0;
}