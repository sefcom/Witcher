

#include <unistd.h>
#include <string.h>     /* For the real memset prototype.  */
#include <signal.h>
#include <stdio.h>
#include <errno.h>
#include <limits.h>
#include <stdlib.h>
#include <stdint.h>
#include <stdbool.h>
#include <sys/socket.h>

#include <sys/types.h>
#include <sys/shm.h>
#include <sys/wait.h>
#include <sys/stat.h>

#include "cgiwrapper.h"

//#define __USE_GNU
//#include <dlfcn.h>

#define STDIN_FILENO 0

int pipefds[2];
static int firsttime = 0;
static char *comp_envs[11] = {"HTTP_COOKIE", "QUERY_STRING", "SERVER_SOFTWARE", "DOCUMENT_ROOT", "HTTP_REDIRECT_STATUS",
                              "HTTP_ACCEPT", "REQUEST_METHOD", "CONTENT_TYPE", "SERVER_NAME", "GATEWAY_INTERFACE", "REQUEST_URI"};

char * __getenv(const char *name);


char *
replace_char(char *str, char find, char replace)
{
    char *current_pos = strchr(str, find);
    while (current_pos) {
        *current_pos = replace;
        current_pos = strchr(current_pos, find);
    }
    return str;
}

void
set_default_envs()
{
    setenv("DOCUMENT_ROOT", "/etc/nginx/conf", 0); //might be important if your cgi read/writes there
    setenv("REMOTE_ADDR", "93.184.216.34", 0); //example.com as a client
    setenv("REMOTE_HOST", "93.184.216.34", 0); //example.com as a client
    setenv("REMOTE_PORT", "65534", 0); //usually random client source port
    setenv("SERVER_ADMIN", "admin@example.com", 0);
    setenv("SERVER_NAME", "localhost", 0);
    setenv("SERVER_PORT", "8180", 0);
    setenv("SERVER_SOFTWARE", "AFL Apache 0.99b", 0);
    setenv("HTTPS", "off", 0);
    //Not really sure if any cgi will really use these, but a couple of setenv don't cost too much:
    setenv("HTTP_ACCEPT", "*/*", 0);
    setenv("GATEWAY_INTERFACE", "CGI/1.1", 0);
    setenv("HTTP_ACCEPT_CHARSET", "iso-8859-1,*,utf-8", 0);
    setenv("HTTP_ACCEPT_LANGUAGE", "en", 0);
    setenv("HTTP_CONNECTION", "Close", 0);
    setenv("TZ", ":US/Eastern", 0);
    setenv("HTTP_REDIRECT_STATUS", "1", 0);


    setenv("PATH", "/usr/bin:/tmp:/app", 0); //HTTP URL PATH
    setenv("REQUEST_METHOD", "GET", 0); //Usually GET or POST
//
    setenv("HTTP_COOKIE", "/opt/", 0); //HTTP Cookie header
    setenv("HTTP_HOST", "/opt/", 0); //HTTP Host header
    setenv("HTTP_REFERER", "/opt/", 0); //HTTP Referer header
    setenv("HTTP_USER_AGENT", "/opt/", 0); //HTTP User-Agent header
    setenv("PATH", "/cgi-bin/luci/admin/datamanager/usbeject", 0); //HTTP URL PATH
    //setenv("QUERY_STRING", "", 0);
    setenv("REMOTE_USER", "/opt/", 0);
    setenv("REQUEST_URI", "/cgi-bin/luci/admin/datamanager/usbeject?dev_name=$(PWNED)", 0);

    setenv("SCRIPT_NAME", "/cgi-bin/luci/admin/datamanager/usbeject", 0);
    setenv("SCRIPT_FILENAME", "/cgi-bin/luci/admin/datamanager/usbeject", 0);

    if (getenv("SCRIPT_FILENAME")) {
        setenv("SCRIPT_NAME", getenv("SCRIPT_FILENAME"), 1);
    } else if (getenv("SCRIPT_NAME")) {  // if SCRIPT_FILENAME not set then set it too.
        setenv("SCRIPT_FILENAME", getenv("SCRIPT_NAME"), 1);
    }

    setenv("CONTENT_TYPE", "application/x-www-form-urlencoded", 0);
    setenv("REQUEST_URI", "SCRIPT", 0);
}

void
setup_cgi_env()
{
    int pipefds[2];

    printf("\e[36m\t[WC] Starting CGI enviornment setup \e[0m\n");
    fflush(stdout);

    set_default_envs();
    // strict is set for the modified /bin/dash

    static int MAX_CMDLINE_LEN = 128 * 1024;

    char *extras = getenv("ENV_VARS_TO_FUZZ");
    if (!extras) {
        extras = "HTTP_COOKIE,QUERY_STRING";
    }
    char *thedup = strdup(extras);
    char *token = strtok(thedup, ",");
    char **env_vars = malloc(0);
    int num_env_vars = 0;
    while (token != NULL) {
        num_env_vars += 1;
        env_vars = realloc(env_vars, sizeof(char *) * num_env_vars);
        env_vars[num_env_vars - 1] = strdup(token);
        token = strtok(NULL, ",");
    }

    // read http request input
    char in_buf[MAX_CMDLINE_LEN];
    int read_len = read(0, in_buf, MAX_CMDLINE_LEN - 2);

    if (read_len < 0){
        printf("[WC] \033[31mERROR: read to in_buf failed!\033[0m\n");
    }

    pipe(pipefds);
    dup2(pipefds[0], STDIN_FILENO);

    int real_content_length = 0;
    char *saved_ptr;
    saved_ptr = (char *) malloc(MAX_CMDLINE_LEN);
    char *ptr = in_buf;
    int rc = 0;
    char *cwd;
    int errnum;

    //struct passwd *p = getpwuid(getuid());  // Check for NULL!
    long size = pathconf(".", _PC_PATH_MAX);
    char *dirbuf = (char *) malloc((size_t) size);
    cwd = getcwd(dirbuf, (size_t) size);

    if (cwd == NULL) {
        errnum = errno;
        printf("ITs null!\n");
        printf("Error num = %d\n", errno);
        printf("Error: %s\n", strerror(errnum));
    }
    printf("\tOpening INPUT with CURRENT WORKING DIR: %s\n", cwd);

    // loop through the strings read via stdin and break at each \x00
    // Cookies, Query String, Post (via re-writting to stdin)
    char *cookie;
    cookie = (char *) malloc(MAX_CMDLINE_LEN);

    char *mandatory_cookie = getenv("MANDATORY_COOKIE");
    if (mandatory_cookie) {
        strcat(cookie, mandatory_cookie);
        printf("\n\tMANDATORY COOKIE = %s\n", cookie);
    }
    char *login_cookie = getenv("LOGIN_COOKIE");
    if (login_cookie) {
        if (strlen(cookie) > 0){
            strcat(cookie," ;");
        }
        strcat(cookie, login_cookie);
        printf("\n\tLOGIN COOKIE = %s\n", cookie);
    }
    setenv(env_vars[0], cookie, 1);

    char *cgi;
    cgi = (char *) malloc(MAX_CMDLINE_LEN);
    char *mandatory_get = getenv("MANDATORY_GET");
    if (mandatory_get) {
        strcat(cgi, mandatory_get);
        printf("\n\tMANDATORY GETS = %s\n", cgi);
    }
    setenv(env_vars[1], cgi, 1);

    while (!*ptr) {
        ptr++;
        rc++;
    }
    while (*ptr) {
        memcpy(saved_ptr, ptr, strlen(ptr) + 1);

        printf("\tSTART RC=%d num_env_vars=%d value ='", rc, num_env_vars);

        for (int i=0;i<strlen(saved_ptr);i++){
                 if (saved_ptr[i] == 0x00){
                     printf(" || ");
                 } else if (saved_ptr[i] < 0x20 || saved_ptr[i] > 0x7e){
                     printf("\\x%02x", saved_ptr[i]);
                 } else {
                     printf("%c", saved_ptr[i]);
                 }
        }
        printf("' \n");
        fflush(stdout);

        if (rc < num_env_vars) {

            if (rc == 0) {
                strcat(cookie, "; ");
                strcat(cookie, saved_ptr);
                cookie = replace_char(cookie, '&', ';');
                setenv(env_vars[rc], cookie, 1);
                //printf("\t[WC] For the COOKY :: >> Setting %s as %s\n", env_vars[rc], cookie);
            } else if (rc == 1) {
                if (strlen(cgi) > 0) {
                    strcat(cgi, "&");
                }
                strcat(cgi, saved_ptr);
                setenv(env_vars[rc], cgi, 1);
                //printf("\t[WC] Setting %s as %s\n", env_vars[rc], cgi);
            } else {
                //printf("\t[WC] Setting %s as %s\n", env_vars[rc], saved_ptr);
                setenv(env_vars[rc], saved_ptr, 1);
            }

        } else if (rc == num_env_vars) {

            real_content_length = write(pipefds[1], saved_ptr, strlen(saved_ptr));
            write(pipefds[1], "\n", 1);

            printf("\tReading from %d and writing %d bytes to %d \n", real_content_length, pipefds[0], pipefds[1]);
            printf("\t%-15s = \033[33m%s\033[0m \n", "POST", saved_ptr);


            char snum[20];
            sprintf(snum, "%d", real_content_length);
            setenv("CONTENT_LENGTH", snum, 1);
            printf("\t%-15s = \033[33m%s\033[0m \n", "CONTENT_LENGTH", getenv("CONTENT_LENGTH"));
        }
        rc++;
        while (*ptr)
            ptr++;
        ptr++;

    }

    for (int i=0; i < num_env_vars; i++){
        char* tmp = getenv(env_vars[i]);
        if (tmp){
            printf("\t%-15s = \033[33m%s\033[0m\n", env_vars[i], tmp);
        }

    }
    fflush(stdout);

    free(saved_ptr);
    printf("DONE, closing pipes\n");
    fflush(stdout);
    close(pipefds[0]);
    close(pipefds[1]);

    printf("DONE, freeing vars\n");
    fflush(stdout);
    for (int i = 1; i < num_env_vars; i++) {
        free(env_vars[i]);
    }

    if (getenv("QUERY_STRING")){
        char* tmpfn = getenv("SCRIPT_FILENAME");
        char* tmpqs = getenv("QUERY_STRING");
        char rqui[strlen(tmpfn)+strlen(tmpqs)+3];
        strcat(rqui, tmpfn);
        strcat(rqui, "/?");
        strcat(rqui, tmpqs);

        setenv("REQUEST_URI", rqui,1);
        printf("\t%-15s = \033[33m%s\033[0m\n", "REQUEST_URI", getenv("REQUEST_URI"));

    }
    free(env_vars);
    printf("DONE and DONE \n");
    fflush(stdout);

}



/*****
 * borrowed from php source codes inline hash.
 ******/

char *
__getenv(const char *name) {
    size_t len = strlen(name);
    char **ep;
    uint16_t name_start;
    if (__environ == NULL || name[0] == '\0')
        return NULL;
    if (name[1] == '\0') {
        /* The name of the variable consists of only one character.  Therefore
           the first two characters of the environment entry are this character
           and a '=' character.  */
        name_start = ('=' << 8) | *(const unsigned char *) name;

        for (ep = __environ; *ep != NULL; ++ep) {
            // if _STRING_ARCH_unaligned
            uint16_t ep_start = *(uint16_t * ) * ep;

//          uint16_t ep_start = (((unsigned char *) *ep)[0]
//                               | (((unsigned char *) *ep)[1] << 8));
            if (name_start == ep_start)
                return &(*ep)[2];
        }
    } else {
        // if _STRING_ARCH_unaligned
        name_start = *(const uint16_t *) name;
        //else
        //name_start = (((const unsigned char *) name)[0]
        //              | (((const unsigned char *) name)[1] << 8));

        len -= 2;
        name += 2;
        for (ep = __environ; *ep != NULL; ++ep) {
            //if _STRING_ARCH_unaligned
            uint16_t ep_start = *(uint16_t * ) * ep;
            //else
            //uint16_t ep_start = (((unsigned char *) *ep)[0]
            //                     | (((unsigned char *) *ep)[1] << 8));

            if (name_start == ep_start && !strncmp(*ep + 2, name, len)
                && (*ep)[len + 2] == '=')
                return &(*ep)[len + 3];
        }
    }
    return NULL;
}

/**
* OVERRIDES libc's getenv function, to initiate receiving input from standard input
*/

char *
getenv(const char *name)
{

    //real_getenv = dlsym(RTLD_NEXT, "getenv");

    if (firsttime == 0 ) { //&& __getenv("WEBCAM_CGI_INJECT")) {

        int match = 0;
        for (int i = 0; comp_envs[i] != NULL; i++) {
            if (strcmp(name, comp_envs[i]) == 0) {
                match = 1;
                break;
            }
        }

        if (match == 1) {
            printf("\n~~~~~~~~~~~~ < firsttime=%d for=%s match=%d, pid=%d> ~~~~~~~~~~~~~\n", firsttime, name, match, getpid());

            firsttime = 1;

            setup_cgi_env();

//            printf("\t%-15s = \033[33m%s\033[0m\n", "REQUEST_METHOD", getenv("REQUEST_METHOD"));
//            printf("\t%-15s = \033[33m%s\033[0m\n", "SCRIPT_NAME", getenv("SCRIPT_NAME"));


            printf("\n\033[36m\t[WC] DONE Setting up CGI enviornment.\033[0m\n");
            fflush(stdout);

        }
    }

    char *tmp = __getenv(name);

    return tmp;

}
