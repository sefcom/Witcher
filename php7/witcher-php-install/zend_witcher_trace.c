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

/***** END new for HTTP direct ********/

#define MAPSIZE 65536
#define TRACE_SIZE 128*(1024*1024) // X * megabytes

#define SHM_ENV_VAR         "__AFL_SHM_ID"

#define STDIN_FILENO 0

static int last = 0;
static int op = 0;

static int MAX_CMDLINE_LEN=128*1024;

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
char *login_cookie = NULL, *mandatory_cookie = NULL, *preset_cookie = NULL;
char *witcher_print_op = NULL;

char *main_filename;
char session_id[40];
int saved_session_size = 0;

int trace[TRACE_SIZE];
int trace_index = 0;

int pipefds[2];

int top_pid=0;

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
void prefork_cgi_setup(){
    debug_print(("[\e[32mWitcher\e[0m] Starting SETUP_CGI_ENV  \n"));
    char *tmp = getenv("DOCUMENT_ROOT");
    if (!tmp){
        setenv("DOCUMENT_ROOT","/app", 1); //might be important if your cgi read/writes there
    }
    setenv("HTTP_REDIRECT_STATUS","1",1);

    setenv("HTTP_ACCEPT", "*/*", 1);
    setenv("GATEWAY_INTERFACE", "CGI/1.1", 1);

    setenv("PATH", "/usr/bin:/tmp:/app", 1); //HTTP URL PATH
    tmp = getenv("REQUEST_METHOD");
    if (!tmp){
        setenv("REQUEST_METHOD", "POST", 1); //Usually GET or POST
    }
    setenv("REMOTE_ADDR","127.0.0.1",1);

    setenv("CONTENT_TYPE","application/x-www-form-urlencoded",1);
    setenv("REQUEST_URI", "SCRIPT",1);
    login_cookie = getenv("LOGIN_COOKIE");

    char* preset_cookie = (char *) malloc(MAX_CMDLINE_LEN);
    memset(preset_cookie, 0, MAX_CMDLINE_LEN);

    if (login_cookie){
        strcat(preset_cookie, login_cookie);
        setenv(env_vars[0], login_cookie, 1);
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
        debug_print(("[\e[32mWitcher\e[0m] LOGIN ::> %s\n", login_cookie));
    }
    mandatory_cookie = getenv("MANDATORY_COOKIE");
    if (mandatory_cookie && strlen(mandatory_cookie) > 0){
        strcat(preset_cookie, "; ");
        strcat(preset_cookie, mandatory_cookie);
        debug_print(("[\e[32mWitcher\e[0m] MANDATORY COOKIE = %s\n", preset_cookie));
    }
    witcher_print_op = getenv("WITCHER_PRINT_OP");
}
void setup_cgi_env(){

    // strict is set for the modified /bin/dash
#ifdef WITCHER_DEBUG
    FILE *logfile = fopen ("/tmp/wrapper.log","a+");
    fprintf (logfile, "----Start----\n");
    //printf("starting\n");
#endif


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
    if (preset_cookie){
        strcat(cookie, preset_cookie);
    }

    setenv(env_vars[0], cookie, 1);
    char* post_data = (char *) malloc(MAX_CMDLINE_LEN);
    memset(post_data, 0, MAX_CMDLINE_LEN);
    char* query_string = (char *) malloc(MAX_CMDLINE_LEN);
    memset(query_string, 0, MAX_CMDLINE_LEN);

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
    FILE *elog = fopen("/tmp/witcher.log","a+");
    if (elog){
        fprintf(elog, "\033[36m[Witcher] detected error in child but AFL_META_INFO_ID is not set. !!!\033[0m\n");
        fclose(elog);
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
    cgi_get_shm_mem(SHM_ENV_VAR);

    char *id_str = getenv(SHM_ENV_VAR);
    prefork_cgi_setup();
    if (id_str) {
        afl_forkserver();
        debug_print(("[\e[32mWitcher\e[0m] Returning with pid %d \n\n", getpid()));
    }
    // setup cgi must be after fork
    setup_cgi_env();

    //fflush(stdout);
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

    op = 0;
    last = 0;
    trace_index = 0;

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

        op = (opline->lineno << 8) | opline->opcode ; //opcode; //| (lineno << 8);

        if (last != 0) {
            int bitmapLoc = (op ^ last) % MAPSIZE;

            // turned off to disable afl code tracing
            afl_area_ptr[bitmapLoc]++;
        }
    }
    last = op;

    if (ofile){
        fflush(ofile);
        fclose(ofile);
    }
}

