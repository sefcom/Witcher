/*
   american fuzzy lop - high-performance binary-only instrumentation
   -----------------------------------------------------------------

   Written by Andrew Griffiths <agriffiths@google.com> and
              Michal Zalewski <lcamtuf@google.com>

   Idea & design very much by Andrew Griffiths.

   Copyright 2015, 2016, 2017 Google Inc. All rights reserved.

   Licensed under the Apache License, Version 2.0 (the "License");
   you may not use this file except in compliance with the License.
   You may obtain a copy of the License at:

     http://www.apache.org/licenses/LICENSE-2.0

   This code is a shim patched into the separately-distributed source
   code of QEMU 2.10.0. It leverages the built-in QEMU tracing functionality
   to implement AFL-style instrumentation and to take care of the remaining
   parts of the AFL fork server logic.

   The resulting QEMU binary is essentially a standalone instrumentation
   tool; for an example of how to leverage it for other purposes, you can
   have a look at afl-showmap.c.

 */
#ifndef _QEMU_CPU_AFL_FORK
#define _QEMU_CPU_AFL_FORK

#include <sys/shm.h>
#include "../../config.h"

/***************************
 * VARIOUS AUXILIARY STUFF *
 ***************************/

/* A snippet patched into tb_find_slow to inform the parent process that
   we have hit a new block that hasn't been translated yet, and to tell
   it to translate within its own context, too (this avoids translation
   overhead in the next forked-off copy). */

#define AFL_QEMU_CPU_SNIPPET1 do { \
    afl_request_tsl(pc, cs_base, flags); \
  } while (0)

/* This snippet kicks in when the i nstruction pointer is positioned at
   _start and does the usual forkserver stuff, not very different from
   regular instrumentation injected via  afl-as.h. */

int do_setup = true;

/*#define AFL_QEMU_CPU_SNIPPET2 {\
    afl_maybe_log(itb->pc); \
} while (0)
*/

#define AFL_QEMU_CPU_SNIPPET2 do { \
    if(itb->pc == afl_entry_point) { \
      afl_setup(); \
      afl_forkserver(cpu); \
    } \
    afl_maybe_log(itb->pc); \
  } while (0)

/* We use one additional file descriptor to relay "needs translation"
   messages between the child and the fork server. */

#define TSL_FD (FORKSRV_FD - 1)

/* This is equivalent to afl-as.h: */

static unsigned char *afl_area_ptr;

/* Exported variables populated by the code patched into elfload.c: */

abi_ulong afl_entry_point, /* ELF entry point (_start) */
          afl_start_code,  /* .text start pointer      */
          afl_end_code;    /* .text end pointer        */

/* Set in the child process in forkserver mode: */

static unsigned char afl_fork_child;
unsigned int afl_forksrv_pid;

/* Instrumentation ratio: */

static unsigned int afl_inst_rms = MAP_SIZE;

/* Function declarations. */

static void afl_setup(void);
static void afl_forkserver(CPUState*);
static inline void afl_maybe_log(abi_ulong);
void inner_cgi();
//char* replace_char(char* str, char find, char replace);

static void afl_wait_tsl(CPUState*, int);
static void afl_request_tsl(target_ulong, target_ulong, uint64_t);

/* Data structure passed around by the translate handlers: */

struct afl_tsl {
  target_ulong pc;
  target_ulong cs_base;
  uint64_t flags;
};

/* Some forward decls: */

TranslationBlock *tb_htable_lookup(CPUState*, target_ulong, target_ulong, uint32_t);
static inline TranslationBlock *tb_find(CPUState*, TranslationBlock*, int);


void inner_cgi(){
  int pipefds[2];

  printf("\e[31mStarting SETUP_CGI_ENV inner_cgi() \e[0m\n");
  fflush(stdout);
//  setenv("DOCUMENT_ROOT", "/etc/nginx/conf", 1); //might be important if your cgi read/writes there
//  setenv("REMOTE_ADDR", "93.184.216.34", 1); //example.com as a client
//  setenv("REMOTE_HOST", "93.184.216.34", 1); //example.com as a client
//  setenv("REMOTE_PORT", "65534", 1); //usually random client source port
//  setenv("SERVER_ADMIN", "admin@example.com", 1);
//  setenv("SERVER_NAME", "localhost", 1);
//  setenv("SERVER_PORT", "8180", 1);
//  setenv("SERVER_SOFTWARE", "AFL Apache 0.99b", 1);
  setenv("HTTPS", "off", 1);
  //Not really sure if any cgi will really use these, but a couple of setenv don't cost too much:
//  setenv("HTTP_ACCEPT", "*/*", 1);
//  setenv("GATEWAY_INTERFACE", "CGI/1.1", 1);
//  setenv("HTTP_ACCEPT_CHARSET", "iso-8859-1,*,utf-8", 1);
//  setenv("HTTP_ACCEPT_LANGUAGE", "en", 1);
//  setenv("HTTP_CONNECTION", "Close", 1);
//  setenv("TZ", ":US/Eastern", 1);
//  setenv("HTTP_REDIRECT_STATUS","1",1);


  //setenv("PATH", "/usr/bin:/tmp:/app", 1); //HTTP URL PATH
  setenv("REQUEST_METHOD", "GET", 1); //Usually GET or POST
//
//  setenv("HTTP_COOKIE", "/opt/", 1); //HTTP Cookie header
//  setenv("HTTP_HOST", "/opt/", 1); //HTTP Host header
//  setenv("HTTP_REFERER", "/opt/", 1); //HTTP Referer header
//  setenv("HTTP_USER_AGENT", "/opt/", 1); //HTTP User-Agent header
//  setenv("PATH", "/cgi-bin/luci/admin/datamanager/usbeject", 1); //HTTP URL PATH
//  //setenv("QUERY_STRING", "", 1);
//  setenv("REMOTE_USER", "/opt/", 1);
//  setenv("REQUEST_URI", "/cgi-bin/luci/admin/datamanager/usbeject?dev_name=dev", 1);
  //setenv("SCRIPT_FILENAME", "/etc/nginx/conf/cgi-bin/luci/admin/datamanager/usbeject", 1);


  if (getenv("SCRIPT_FILENAME")){
    setenv("SCRIPT_NAME", getenv("SCRIPT_FILENAME"), 1);
  } else {
    setenv("SCRIPT_NAME", "/etc/nginx/conf/cgi-bin/luci/admin/datamanager/usbeject", 1);
  }

  setenv("CONTENT_TYPE","application/x-www-form-urlencoded",1);
  setenv("REQUEST_URI", "SCRIPT",1);
  // strict is set for the modified /bin/dash

  FILE *logfile = fopen ("/tmp/qemu_wrapper.log","a+");
  fprintf (logfile, "----Start----\n");
  //printf("starting\n");

  static int MAX_CMDLINE_LEN=128*1024;
  const char* env_vars[] = { "HTTP_COOKIE","QUERY_STRING"};

  static int   num_env_vars = sizeof(env_vars) / sizeof(char*);
  printf("\tNUM_ENV_VARS=%d\n",num_env_vars);
  char  in_buf[MAX_CMDLINE_LEN];
  int howmuch = read(0, in_buf, MAX_CMDLINE_LEN - 2);

  printf("\t[WC] I can read = %d grade levels\n", howmuch);

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
  cwd = getcwd(dirbuf, (size_t)size);

  if (cwd == NULL){
    errnum = errno;
    printf("ITs null!\n");
    printf("Error num = %d\n", errno);
    printf("Error: %s\n", strerror(errnum));
  }
  printf("\tOpening INPUT with CURRENT WORKING DIR: %s\n", cwd);

  // loop through the strings read via stdin and break at each \x00
  // Cookies, Query String, Post (via re-writting to stdin)
  char* cookie;
  cookie = (char *) malloc(MAX_CMDLINE_LEN);

  char* cgi;
  cgi = (char *) malloc(MAX_CMDLINE_LEN);
  char* mandatory_get = getenv("MANDATORY_GET");
  if (mandatory_get){
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
//    if (rc < 3){
//      load_variables(saved_ptr, rc);
//    }


    if(rc < num_env_vars){

      if (rc == 0){
//                char phpsessid[50];
//                 if (afl_area_ptr != NULL){
//                     sprintf(phpsessid, "&PHPSESSID=11deadcode22deadcode33deadcode4-%d&", getppid());
//                 } else {
//                     sprintf(phpsessid, "&PHPSESSID=11deadcode22deadcode33deadcode4-NONE&");
//                 }

        // phpbb cookies!
        //strcat(saved_ptr, ";io=e-Pth0gGZ-j02_T-AAae; language=en; welcomebanner_status=dismiss; cookieconsent_status=dismiss; token=eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdGF0dXMiOiJzdWNjZXNzIiwiZGF0YSI6eyJpZCI6MTcxMTEsInVzZXJuYW1lIjoiIiwiZW1haWwiOiJlNUBlNS5jb20iLCJwYXNzd29yZCI6IjdjNmExODBiMzY4OTZhMGE4YzAyNzg3ZWVhZmIwZTRjIiwicm9sZSI6ImN1c3RvbWVyIiwibGFzdExvZ2luSXAiOiIwLjAuMC4wIiwicHJvZmlsZUltYWdlIjoiZGVmYXVsdC5zdmciLCJ0b3RwU2VjcmV0IjoiIiwiaXNBY3RpdmUiOnRydWUsImNyZWF0ZWRBdCI6IjIwMjAtMDEtMDcgMTU6MDY6MTEuNTAxICswMDowMCIsInVwZGF0ZWRBdCI6IjIwMjAtMDEtMDcgMTU6MDY6MTEuNTAxICswMDowMCIsImRlbGV0ZWRBdCI6bnVsbH0sImlhdCI6MTU3ODQxMTAxOSwiZXhwIjoxNTc4NDI5MDE5fQ.lJULYCTINSISZlVIPqNmn8LJ8JMLVhue2_gZEy3okLxy4ceapTqUsNCh_exucwDkT8dq_95WzACgHmiv3n9j2IT_HteHFY8dXWjiCChZQAnaMSLdwsKrYOij2fQgJV8qucTT2xUH01s-j1ZTwqw1iPsc4KGkFOv_4MM2Q0rLb5Y; style_cookie=null; phpbb3_jy838_k=; phpbb3_jy838_u=1; phpbb3_jy838_sid=64875204dba037d2a713bab96de2d1a8; phpbb3_fiinw_u=2; phpbb3_fiinw_k=; phpbb3_fiinw_sid=33771b0508ad96b1ada9e69a43b05050");
        strcat(cookie, "; ");
        strcat(cookie, saved_ptr);
        cookie = replace_char(cookie, '&',';');
        setenv(env_vars[rc], cookie, 1);

        printf("\t[WC] For the COOKY :: >> Setting %s as %s\n", env_vars[rc], cookie);
      } else if (rc == 1) {
        //strcat(cgi, "&");
        strcat(cgi, saved_ptr);
       // printf("[WC] VARS=%s\n", cgi);

        //setenv(env_vars[rc], cgi, 1);

        //printf("[WC] Setting %s as %s\n", env_vars[rc], cgi);

      } else {
        printf("[WC] Setting %s as %s\n", env_vars[rc], saved_ptr);
        setenv(env_vars[rc], saved_ptr, 1);
      }

    }else if(rc == num_env_vars){

      printf("\tSetting HTTP body (stdin) to %s\n", saved_ptr);
      fprintf(logfile, "Setting HTTP body (stdin) to %s\n", saved_ptr);

      real_content_length = write(pipefds[1], saved_ptr, strlen(saved_ptr));
      write(pipefds[1], "\n", 1);
      fprintf(logfile, "Setting HTTP body (stdin) to %s\n", saved_ptr);
      printf("\t REAL content length written = %d to %d and readable from %d\n", real_content_length, pipefds[1], pipefds[0]);
      char snum[20];
      sprintf(snum, "%d", real_content_length);

      setenv("CONTENT_LENGTH", snum ,1);

    }
    printf("END RC = %d, incrementing\n", rc);
    rc++;
    while (*ptr)
      ptr++;
    ptr++;

  }
  if (!getenv("CONTENT_LENGTH")){
    setenv("CONTENT_LENGTH", "0" ,1);
  }
  printf("[WC] SCRIPT_FILENAME=%s\n",getenv("SCRIPT_FILENAME"));
  printf("[WC] SCRIPT_NAME=%s\n",getenv("SCRIPT_NAME"));
  printf("[WC] REQUEST_METHOD=%s\n",getenv("REQUEST_METHOD"));
  printf("[WC] CONTENT_LENGTH=%s\n",getenv("CONTENT_LENGTH"));
  printf("[WC] QUERY_STRING=%s\n",getenv("QUERY_STRING"));


//  for (int i=0; i < 3;i++){
//    if (i == 0){
//      printf("\tCOOKIES\e[36m ");
//    } else if (i == 1){
//      printf("\tGETS\e[36m ");
//    } else if (i == 2){
//      printf("\tPOSTS\e[36m ");
//    }
//
//    for (int j=0; j < variables_ptr[i];j++){
//      printf("%s, ", variables[i][j]);
//    }
//    printf("\e[0m\n");
//  }
  printf("\e[0m\n");
  free(saved_ptr);
  close(pipefds[0]);
  close(pipefds[1]);
  fprintf(logfile, "DONE reading in \n");
  fclose(logfile);
  printf("\nDONE reading in \n");
  fflush(stdout);
}


/*************************
 * ACTUAL IMPLEMENTATION *
 *************************/

/* Set up SHM region and initialize other stuff. */

static void afl_setup(void) {

  char *id_str = getenv(SHM_ENV_VAR),
       *inst_r = getenv("AFL_INST_RATIO");

  int shm_id;

  printf("I am in the VM of my ancestors WEBCGI=%s\n",getenv("WEBCGI"));

  if (inst_r) {

    unsigned int r;

    r = atoi(inst_r);

    if (r > 100) r = 100;
    if (!r) r = 1;

    afl_inst_rms = MAP_SIZE * r / 100;

  }

  if (id_str) {

    shm_id = atoi(id_str);
    afl_area_ptr = shmat(shm_id, NULL, 0);
    printf("BEFORE the exit\n");
    if (afl_area_ptr == (void*)-1) exit(1);
    printf("AFTER exit\n");
    /* With AFL_INST_RATIO set to a low value, we want to touch the bitmap
       so that the parent doesn't give up on us. */

    if (inst_r) afl_area_ptr[0] = 1;


  }

  if (getenv("AFL_INST_LIBS")) {

    afl_start_code = 0;
    afl_end_code   = (abi_ulong)-1;

  }
  printf("TOWARDS END OF SETUP");
  /* pthread_atfork() seems somewhat broken in util/rcu.c, and I'm
     not entirely sure what is the cause. This disables that
     behaviour, and seems to work alright? */

  rcu_disable_atfork();

}

/*char* replace_char(char* str, char find, char replace){
  char *current_pos = strchr(str,find);
  while (current_pos){
    *current_pos = replace;
    current_pos = strchr(current_pos,find);
  }
  return str;
}*/
/* Fork server logic, invoked once we hit _start. */

static void afl_forkserver(CPUState *cpu) {

  static unsigned char tmp[4];
  printf("FORKING STAR ALREADY \n");
  if (!afl_area_ptr) return;

  /* Tell the parent that we're alive. If the parent doesn't want
     to talk, assume that we're not running in forkserver mode. */

  if (write(FORKSRV_FD + 1, tmp, 4) != 4) return;

  afl_forksrv_pid = getpid();

  /* All right, let's await orders... */

  while (1) {

    pid_t child_pid;
    int status, t_fd[2];

    /* Whoops, parent dead? */

    if (read(FORKSRV_FD, tmp, 4) != 4) exit(2);

    /* Establish a channel with child to grab translation commands. We'll
       read from t_fd[0], child will write to TSL_FD. */

    if (pipe(t_fd) || dup2(t_fd[1], TSL_FD) < 0) exit(3);
    close(t_fd[1]);

    child_pid = fork();
    if (child_pid < 0) exit(4);

    if (!child_pid) {

      /* Child process. Close descriptors and run free. */
      printf("DARN children =%s\n",getenv("WEBCGI"));
      afl_fork_child = 1;
      close(FORKSRV_FD);
      close(FORKSRV_FD + 1);
      close(t_fd[0]);
      return;

    }

    /* Parent. */

    close(TSL_FD);

    if (write(FORKSRV_FD + 1, &child_pid, 4) != 4) exit(5);

    /* Collect translation requests until child dies and closes the pipe. */

    afl_wait_tsl(cpu, t_fd[0]);

    /* Get and relay exit status to parent. */

    if (waitpid(child_pid, &status, 0) < 0) exit(6);
    if (write(FORKSRV_FD + 1, &status, 4) != 4) exit(7);

  }

}


/* The equivalent of the tuple logging routine from afl-as.h. */

static inline void afl_maybe_log(abi_ulong cur_loc) {

  static __thread abi_ulong prev_loc;

  /* Optimize for cur_loc > afl_end_code, which is the most likely case on
     Linux systems. */

  if (cur_loc > afl_end_code || cur_loc < afl_start_code || !afl_area_ptr)
    return;

  /* Looks like QEMU always maps to fixed locations, so ASAN is not a
     concern. Phew. But instruction addresses may be aligned. Let's mangle
     the value to get something quasi-uniform. */

  cur_loc  = (cur_loc >> 4) ^ (cur_loc << 8);
  cur_loc &= MAP_SIZE - 1;

  /* Implement probabilistic instrumentation by looking at scrambled block
     address. This keeps the instrumented locations stable across runs. */

  if (cur_loc >= afl_inst_rms) return;

  afl_area_ptr[cur_loc ^ prev_loc]++;
  prev_loc = cur_loc >> 1;

}


/* This code is invoked whenever QEMU decides that it doesn't have a
   translation of a particular block and needs to compute it. When this happens,
   we tell the parent to mirror the operation, so that the next fork() has a
   cached copy. */

static void afl_request_tsl(target_ulong pc, target_ulong cb, uint64_t flags) {

  struct afl_tsl t;

  if (!afl_fork_child) return;

  t.pc      = pc;
  t.cs_base = cb;
  t.flags   = flags;

  if (write(TSL_FD, &t, sizeof(struct afl_tsl)) != sizeof(struct afl_tsl))
    return;

}

/* This is the other side of the same channel. Since timeouts are handled by
   afl-fuzz simply killing the child, we can just wait until the pipe breaks. */

static void afl_wait_tsl(CPUState *cpu, int fd) {

  struct afl_tsl t;
  TranslationBlock *tb;

  while (1) {

    /* Broken pipe means it's time to return to the fork server routine. */

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

}


#endif
