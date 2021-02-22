//
// Created by erik on 4/15/2020.
//
/*
 * sudo aptitude install libcurl4-openssl-dev
 * Compile and run the code
 * g++ main.cc -o httpreqr -lcurl
 */

#include <sys/stat.h>
#include <iostream>
#include <string>
#include <curl/curl.h>
#include <assert.h>
#include <iostream>
#include <fstream>
#include <unistd.h>
#include <sys/wait.h>
#include <regex>
#include <csignal>
#include <iomanip>
#include <sys/shm.h>
#include <csignal>

#define HTTPREQR_ENV_VAR    "__HTTPREQR_SHM_ID"
struct httpreqr_info {
    int enable_logging;
    int reqr_process_id;
    int magic;
};
volatile struct httpreqr_info *httpreqr_info = NULL;

#define TARGET_ENV_VAR "HTTPREQR_LAUNCH_SCRIPT"
#define LOG(fmt, ...) do { \
  fprintf(stderr, fmt "\n", ## __VA_ARGS__); \
} while (0)

static pid_t target_pid;

/*
 * Launch target specified by TARGET_ENV_VAR
 */
pid_t launch_target(void)
{
  const char *target_script = getenv(TARGET_ENV_VAR);
  if ((target_script == NULL) || (strlen(target_script) == 0)) {
    LOG("Specify launcher script via " TARGET_ENV_VAR);
    exit(1);
  }

  pid_t p = fork();
  if (p != 0) {
    LOG("[*] Created child: %d", p);
    return p;
  }

  LOG("[*] Launching subprocess via system(\"%s\");", target_script);
  int r = execl("/bin/sh", "sh", "-c", target_script, (char *) NULL);
  assert(r == -1);
  LOG("[*] execl failed: %s", strerror(errno));
  exit(1);
  return 0;
}

bool poll_target(pid_t p)
{
  int wstatus;
  pid_t s = waitpid(p, &wstatus, WNOHANG);

  if (s == 0) {
    /* No change */
    return false;
  } else if (s == -1) {
    LOG("waitpid error");
    exit(1);
  } else {
    assert(s == p);
  }

  if (WIFSIGNALED(wstatus)) {
    if (WTERMSIG(wstatus) == SIGINT || WTERMSIG(wstatus) == SIGQUIT) {
      LOG("[!] Signaled: %d!", WTERMSIG(wstatus));
      return true;
    }
  } else if (WIFEXITED(wstatus)) {
    LOG("[*] Target exited: %d", WEXITSTATUS(wstatus));
    return true;
  } else {
    LOG("Unhandled exit");
    assert(0);
  }

  return false;
}







#define FORKSRV_FD 198
#define TSL_FD (FORKSRV_FD - 1)

using namespace std;

struct test_process_info {
    int initialized=0;
    int afl_id = 0;
    int port = 0;
    int reqr_process_id;
    int process_id=0;
    char error_type[20]; /* SQL, Command */
    char error_msg[100];
    bool capture;
};

#define TEST_PROCESS_INFO_SHM_ID 0x411911
#define TEST_PROCESS_INFO_MAX_NBR 100
#define TEST_PROCESS_INFO_SMM_SIZE 0x4000

test_process_info *test_process_info_ptr;
test_process_info *this_test_process_info;
bool isparent = true;
int tbd_shm_id = 0;
static unsigned char *afl_area_ptr =0;
int fake_afl_shm_id =0;

string ToHex(const string& s, bool upper_case /* = true */)
{
  ostringstream ret;

  for (string::size_type i = 0; i < s.length(); ++i)
    ret << std::hex << std::setfill('0') << std::setw(2) << (upper_case ? std::uppercase : std::nouppercase) << (int)s[i];

  return ret.str();
}

class RequestData {
    string url, cookies="", gets="", posts="", method, headers="", uri="", content_type="";
    bool json = false;
    bool loaded = false;
public:
    RequestData(string urlIn){
      url = urlIn;
    }
    void loadVariableData(){
      if (loaded) {
        return;
      }
      loaded = true;

      int inputcount = 0;
      cout << "Loading variable data :: " << endl;
      for (string line; getline(std::cin, line, '\x00');) {
        cout << ToHex(line, true) << endl;
        if (inputcount == 0){
          cookies = line;
          //cookies = "";
        } else if (inputcount == 1){
          gets = line;
          //gets = "b=/++";
        } else if (inputcount == 2){
          posts = string(line);
          //posts = "";
        } else if (inputcount == 3){
          headers = string(line);
        } else if (inputcount == 4){
          uri = string(line);
        }
        inputcount++;
      }
    }
    string getPort(){
      size_t first = url.find(":");
      size_t portpos = url.find(":", first+1);
      if (portpos == string::npos){
        return "80";
      }
      string port = regex_replace(url, regex("http://.*:([0-9]+).*"), string("$1"));
      if (port.length() == 0){
        return "";
      } else {
        return port;
      }
    }

    string getURL(){
      if (gets.size() == 0){
        return url + (uri.size() ? ("/" + uri) : "");
      } else {
        return url + (uri.size() ? ("/" + uri) : "") + "?" +  gets;
      }
    }
    bool hasCookies(){
      return cookies.size() > 0;
    }
    string getCookies(){
      return cookies;
    }

    string getGets(){
      return gets;
    }
    bool isPost(){
      return method == "POST";
    }
    string getPosts(){
      if (json){
        if (posts.front() != '{'){
          cout << "PARAMS='" << posts << "'" << endl;
          format_to_json(posts.c_str());
          cout << "AFTERJSON='" << posts << "'" << endl;
        }
      }

      return posts;
    }

    bool hasHeaders(){
        return headers.size() > 0;
    }
    string getHeaders(){
        return headers;
    }
    void  format_to_json(const char *str){

      char tostr[posts.length()+1]{};
      memcpy(tostr, posts.c_str(), posts.length());
      tostr[posts.length()] = '\x00';

      posts = string("{");

      char * end_str;
      char * token = strtok_r(tostr, "&", &end_str);

      while( token != NULL ) {
        char jsonEleOut[strlen(token)+7];
        char *end_token;
        char *dup_token = strdup(token);

        char *first_part = strtok_r(dup_token, "=", &end_token);
        char *sec_part = strtok_r(NULL, "=", &end_token);
        if (sec_part) {
          sprintf(jsonEleOut,"\"%s\":\"%s\",", first_part, sec_part);
        } else {
          sprintf(jsonEleOut,"\"%s\":\"\",", first_part);
        }
        posts.append(jsonEleOut);
        token = strtok_r(NULL, "&", &end_str);
      }
      posts.pop_back();
      posts.append("}\x00");
      cout << "JSON before exit " << posts << endl;
      //return outstr;
    }

    void setMethod(string method_in){
      for (auto & c: method_in) c = toupper(c);
      method = method_in;
      if (method.length() == 0){
        method = "GET";
      }
    }
    string getMethod(){
      return method;
    }
    void setJSON(bool jsonIn){
      json = jsonIn;
    }

    void setContentType(string content_type_in) {
       content_type = content_type_in;
    }
    string getContentType(){
      // If we were given a content type header to use, then use that
      if (content_type.length() != 0)
      {
         return "Content-Type: " + content_type;
      }
      if (json){
        return "Content-Type: application/json";
      } else {
        return "Content-Type: application/x-www-form-urlencoded";
      }

    }

};

inline bool fileExists (const std::string& name) {
  struct stat buffer;
  return (stat (name.c_str(), &buffer) == 0);
}

static size_t WriteCallback(void *contents, size_t size, size_t nmemb, void *userp)
{
  ((std::string*)userp)->append((char*)contents, size * nmemb);
  return size * nmemb;
}

void writeOutAFLSHM(string PORT){

  ofstream myfile;

  myfile.open ("/tmp/" +  PORT + ".afl");
  char * afl_shm_loc = getenv("__AFL_SHM_ID");
  if (afl_shm_loc){
    cout << "RECVD AFL_SHM_ID of " << hex << afl_shm_loc << " and wrote to " << PORT << "" << endl;
  } else {
    cout << "NO AFL_SHM_MEM but wrote 0 to " << PORT << "" << endl;
  }

  if (afl_shm_loc){
    myfile << afl_shm_loc << endl;
  } else {
    myfile << "0" << endl;
  }

  myfile.close();

}

void checkForServerErrors(string port){
#if 0
  string error;
  string serverErrorFile = "/tmp/" + port  + ".error";
  if (fileExists(serverErrorFile)){
    std::ifstream aflInfoFile (serverErrorFile);
    std::getline(aflInfoFile, error, '\n');
    ofstream errorFileOut;

    // clear file
    errorFileOut.open (serverErrorFile);
    errorFileOut << "" << endl;
    errorFileOut.close();

    if (error.compare("SQLERROR") == 0){

      raise(SIGSEGV);

    }
  }
#endif
}

CURLcode sendRequest(RequestData *reqD, bool debug = false ){
  CURL *curl;
  CURLcode code(CURLE_FAILED_INIT);
  long timeout = 30;
  if (getenv("DEBUG")){
      timeout = 600;
  }

  string readBuffer;
  cout << "SENDING REQUEST " << endl;
  curl = curl_easy_init();
  if(curl) {
    cout << "in da curl" << endl;
    cout << "in da curl" << endl;

    cout << "Cookies = " << reqD->getCookies() << endl << "gets = " << reqD->getGets() << endl << "posts = '" << reqD->getPosts() << "'" << endl << "PORT=" << reqD->getPort() << endl;
    cout << "Headers = " << reqD->getHeaders() << endl;

    curl_easy_setopt(curl, CURLOPT_BUFFERSIZE, 102400L);
    curl_easy_setopt(curl, CURLOPT_NOPROGRESS, 1L);
    curl_easy_setopt(curl, CURLOPT_HEADER, 1L);
    curl_easy_setopt(curl, CURLOPT_SSL_VERIFYPEER, 0L);
    curl_easy_setopt(curl, CURLOPT_SSL_VERIFYHOST, 0L);
    curl_easy_setopt(curl, CURLOPT_MAXREDIRS, 50L);
    curl_easy_setopt(curl, CURLOPT_TCP_KEEPALIVE, 1L);

    if (debug) {
       curl_easy_setopt(curl, CURLOPT_VERBOSE, 1L);
    }

    curl_easy_setopt(curl, CURLOPT_WRITEFUNCTION, WriteCallback);
    curl_easy_setopt(curl, CURLOPT_WRITEDATA, &readBuffer);
    assert (CURLE_OK == (code = curl_easy_setopt(curl, CURLOPT_TIMEOUT, timeout)));

    struct curl_slist *headers=NULL;

    printf("[WC][main] sending '%s'\n", reqD->getPosts().c_str());
    char test[reqD->getPosts().length()+1];
    strcpy(test, reqD->getPosts().c_str());
    printf("original str = '%s'\n", test);

    curl_easy_setopt(curl, CURLOPT_CUSTOMREQUEST, reqD->getMethod().c_str());

    if (reqD->hasCookies()){
      assert (CURLE_OK == curl_easy_setopt(curl, CURLOPT_COOKIE, reqD->getCookies().c_str()));
    }
    if (reqD->isPost()){
      printf("[WC][main] sending POST '%s'\n", reqD->getPosts().c_str());

      // POSTFIELDSIZE needs to be before COPYPOSTFIELDS
      curl_easy_setopt(curl, CURLOPT_POSTFIELDSIZE_LARGE, (curl_off_t) reqD->getPosts().size());
      curl_easy_setopt(curl, CURLOPT_COPYPOSTFIELDS , reqD->getPosts().c_str());
      headers = curl_slist_append(headers, reqD->getContentType().c_str());

    }
    cout << "[WC][main] URL=" << reqD->getURL() << endl;
    if (reqD->hasHeaders()){
        headers = curl_slist_append(headers, reqD->getHeaders().c_str());
    }
    curl_easy_setopt(curl, CURLOPT_HTTPHEADER, headers);

    assert (CURLE_OK == curl_easy_setopt(curl, CURLOPT_URL, reqD->getURL().c_str()));

    code = curl_easy_perform(curl);

    curl_easy_cleanup(curl);

    curl_slist_free_all(headers);
    headers = NULL;

    cout << "Readbuffer = " << readBuffer << endl;
  }

  return code;
}

string getArg(int argc, char *argv[], string param, bool getNext){
  for (int x=0; x < argc; x++){
    string pval = string((char*) argv[x]);
    if (pval.compare(param) == 0 ) {
      if (getNext) {
        if ((x + 1) < argc) {
          return string((char *) argv[x + 1]);
        } else {
          cout << "ERROR: the parameter" << param << " requires a value after it." << endl;
          exit(99);
        }
      } else {
        return "";
      }
    }
  }

  return "";
}


bool getArg(int argc, char *argv[], string param){
  for (int x=0; x < argc; x++) {
    string pval = string((char *) argv[x]);

    if (pval.compare(param) == 0) {
      return true;
    }
  }

  return false;
}

static void recvAFLRequests(RequestData *reqD) {

  static unsigned char tmp[4];
  static struct timespec start, finish;
  double elapsed;

  int parent_pid = getpid();
  printf("[WC][FORK]Starting up Forker %d __AFL_SHM_ID=%s\n", parent_pid, getenv("__AFL_SHM_ID"));
  bool infinite = true;

  if (write(FORKSRV_FD + 1, tmp, 4) != 4) {
    printf("[WC][FORK]\tfork server not running.\n");
    infinite = false;
  } else {
    printf("\e[1;32m[WC][FORK]\tWrote to FORKSRV_FD\e[0m\n");
  }

  /* All right, let's await orders... */
  int claunch_cnt = 0;
  while (infinite || claunch_cnt < 1) {
    if (poll_target(target_pid)) {
      LOG("Target exited...\n");
      // FIXME: Handle crashing case
      exit(1);
    }

    pid_t child_pid = -1;
    int status, t_fd[2];

    /* Whoops, parent dead? */
    if (claunch_cnt < 1){
      printf("[WC][FORK]\t\tINITIAL awaiting orders %d\n", getpid());
    } else {
      printf("\n[WC][FORK]\t\t\e[1;32mRESET and awaiting orders %d\e[0m\n", getpid());
    }

    if (infinite && read(FORKSRV_FD, tmp, 4) != 4) {
      fprintf(stderr, "Read failed, exiting\n");
      exit(2);
    }

    /* Establish a channel with child to grab translation commands. We'll
       read from t_fd[0], child will write to TSL_FD. */

    // if (pipe(t_fd) || dup2(t_fd[1], TSL_FD) < 0) exit(3);
    clock_gettime(CLOCK_MONOTONIC, &start);
    printf("[WC][FORK]\t\tCreating communication channel to new child from %d\n", getpid());
    // close(t_fd[1]);
    claunch_cnt ++;
    child_pid = fork();

    if (child_pid < 0) exit(4);

    if (child_pid==0) {  // child_pid == 0 when in the child

      /* Child process. Close descriptors and run free. */

      isparent=false;
      child_pid = getpid();
      std::string cpid = std::to_string(child_pid);

      httpreqr_info->reqr_process_id = child_pid;

      setenv("AFL_CHILD_PID", cpid.c_str(),1);
      printf("[WC][CHILD-FORK]\t\t\t\033[33mlaunch cnt = %d current process IS the child, pid == %d\033[0m\n", claunch_cnt,  getpid());
      close(FORKSRV_FD);
      close(FORKSRV_FD + 1);
      // close(t_fd[0]);


      string id_str = getenv("__AFL_SHM_ID");
      int shm_id = atoi(id_str.c_str());

      //shm_id = shmget(449988, 65536, 0666);
      afl_area_ptr = (unsigned char*)  shmat(shm_id, NULL, 0);

      // Set a bit so AFL doesn't give up
      afl_area_ptr[0] = 1;

      printf("[WC][CHILD-FORK] AFL_SHM_ID  = %x, AFL ptr = %p trace_value=%d \n", shm_id, afl_area_ptr, afl_area_ptr[0]);
      reqD->loadVariableData();

      httpreqr_info->enable_logging = true;
      CURLcode code = sendRequest(reqD);
      httpreqr_info->enable_logging = false;

      if (code == CURLE_URL_MALFORMAT) {
        fprintf(stderr, "[WC][CHILD-FORK] Malformed URL... Exiting\n");
        exit(0);
      }

      if (code != CURLE_OK && code != CURLE_GOT_NOTHING) {
        fprintf(stderr, "[WC][CHILD-FORK] Original request failed! %d Sleeping...\n", code);
        sleep(5);
      }

      // Send request a second time to see if the server is still alive
      code = sendRequest(reqD);

      if (code != CURLE_OK && code != CURLE_GOT_NOTHING) {
        fprintf(stderr, "[WC][CHILD-FORK] Follow-up request failed! %d Sleeping...\n", code);
        sleep(5);
      }

      // FIXME: Possible to hit an error after request finished? Possibly wait
      // until server idles

      // Request successful, exit normally
      exit(0);
    }

    /* Parent. */

    // close(TSL_FD);
    assert(parent_pid == getpid());
    printf("[WC][PARENT-FORK]\t\tCheck for child status from Parent %d for %d \n", getpid(), child_pid);

    this_test_process_info->reqr_process_id = child_pid;

    printf("[WC][PARENT-FORK]\t\tCheck for child status from Parent %d for %d \n", getpid(), this_test_process_info->reqr_process_id);

    if (infinite && write(FORKSRV_FD + 1, &child_pid, 4) != 4) {
      printf("\t\tExiting Parent %d with 5\n", child_pid);
      exit(5);
    }

    /* Collect translation requests until child dies and closes the pipe. */
    //afl_wait_tsl(cpu, t_fd[0]);

    /* Get and relay exit status to parent. */
    printf("[WC][PARENT-FORK]\t\tGoing to waitpid on %d from %d \n", child_pid, getpid());

    int waitedpid = waitpid(child_pid, &status, 0);
    if (waitedpid < 0) {
      printf("[WC][PARENT-FORK]\t\tError with waitedpid (%d), Exiting Parent %d with 6\n", waitedpid, child_pid);
      exit(6);
    }
    clock_gettime(CLOCK_MONOTONIC, &finish);
    elapsed = (finish.tv_sec - start.tv_sec);
    elapsed += (finish.tv_nsec - start.tv_nsec) / 1000000000.0;

    cout << "[WC][PARENT-FORK]\t\tChild exec of " << child_pid << " completed, completed in " << elapsed << " checking exit status, status=" << WEXITSTATUS(status) << " signal=" <<  WTERMSIG(status) << endl;
    httpreqr_info->enable_logging=false;
#if 0
    cout << "\033[36mAFL_ID = " << dec  << this_test_process_info->afl_id <<"\033[0m\n";
    int memcnt=0;
    if (this_test_process_info->afl_id){
        if (afl_area_ptr == NULL){
            afl_area_ptr = (unsigned char*) shmat(this_test_process_info->afl_id, NULL, 0);
        }
        for (int x=0; x < 65536; x++){
            if (afl_area_ptr[x] != 0){
                memcnt++;
            }
        }
    }

    printf("\033[36mMEMCNT from run is %d\n\033[0m", memcnt);
#endif
    if (WIFEXITED(status)) {
      printf("\t\t\tRESULTS exited, status=%d signal=%d, total_val=%d\n", WEXITSTATUS(status), WTERMSIG(status), status);
    } else if (WIFSIGNALED(status)) {
      printf("\t\t\tRESULTS killed by signal %d\n", WTERMSIG(status));
    } else if (WIFSTOPPED(status)) {
      printf("\t\t\tRESULTS stopped by signal %d\n", WSTOPSIG(status));
    } else if (WIFCONTINUED(status)) {
      printf("\t\t\tRESULTS continued\n");
    } else {
      printf("RESULTS ERROR: Child has not terminated correctly.\n");
    }

    if (infinite && write(FORKSRV_FD + 1, &status, 4) != 4) {
      printf("\t\tExiting child %d with 7\n", child_pid);
      exit(7);
    }

    printf("[WC][PARENT-FORK]\t\tEnd of Parent loop %d, finished with %d, claunch cnt = %d \n",getpid(), child_pid,  claunch_cnt);
    if (!infinite){
      printf("\t\tExiting first child...DONE\n");
      exit(0);
    }
  } // end of while infinite
}

void remove_shm(){
    printf("\033[34m RESETTING SHARED MEMORY \n\033[0m");
    memset(test_process_info_ptr, 0, TEST_PROCESS_INFO_SMM_SIZE);
}

void signal_handler(int signal){
    printf("[Witcher] caught signal, kill shm region up and exitting.\n");
    exit(33);
}

void initMemory(bool setToZero){
    key_t mem_key = ftok("/tmp",'W');
    mem_key = TEST_PROCESS_INFO_SHM_ID;
    printf("*** mem_key %x \n", mem_key);

    int shm_id = shmget(mem_key , TEST_PROCESS_INFO_SMM_SIZE, 0666);
    if (shm_id < 0 ) {
        printf("*** creating shm memory %x \n", mem_key);
        shm_id = shmget(mem_key , TEST_PROCESS_INFO_SMM_SIZE, IPC_CREAT | 0666);
        if (shm_id < 0 ) {
            perror("*** shmget error (server) *** ERROR: ");
            exit(1);
        }
    }
    tbd_shm_id=shm_id;
    printf("*** using %x got shm_id = %x\n", TEST_PROCESS_INFO_SHM_ID, shm_id);
    test_process_info_ptr = (test_process_info *) shmat(shm_id, NULL, 0);  /* attach */
    if ((long) test_process_info_ptr == -1) {
         printf("*** shmat attaching error (server) ***\n");
         exit(2);
    }
    if (getenv("RESETMEM")){
        printf("Reseting shared memory!\n");
        atexit(remove_shm);
    }

    printf("*** test_process_info_ptr = %p\n", test_process_info_ptr);
    if (setToZero){
        printf("\033[34m RESETTING SHARED MEMORY \n\033[0m");
        memset(test_process_info_ptr, 0, TEST_PROCESS_INFO_SMM_SIZE);
    }
}

void setupErrorMem(int port){
    initMemory(false);

    for (int x=0; x < TEST_PROCESS_INFO_MAX_NBR; x++){
        if (test_process_info_ptr[x].initialized == 31337){
            break;
        }
    }

    for (int x=0; x < TEST_PROCESS_INFO_MAX_NBR; x++){
        if (test_process_info_ptr[x].initialized != 31337){
            this_test_process_info = test_process_info_ptr + x;
            break;
        }
    }
    if (this_test_process_info == 0){
        this_test_process_info = test_process_info_ptr;
    }

    this_test_process_info->initialized = 31337;
    this_test_process_info->port = port;


    char * afl_shm_loc = getenv("__AFL_SHM_ID");
    printf("\033[36m*** process info @ %p for port %d using AFL %s \033[0m\n", test_process_info_ptr, port, afl_shm_loc);
    if (afl_shm_loc){
        this_test_process_info->afl_id = atoi(afl_shm_loc);
    } else {
        fake_afl_shm_id =  shmget(port , 65536, IPC_CREAT | 0666);
        this_test_process_info->afl_id = fake_afl_shm_id;
    }
    for (int x=0; x < TEST_PROCESS_INFO_MAX_NBR; x++){
        if (test_process_info_ptr[x].initialized == 31337){
            printf("\033[36m*** TEST_DATA: port=%d, afl_id=%d\033[0m\n", this_test_process_info->port, this_test_process_info->afl_id);
        }
    }
}


#define MAP_SIZE_POW2       16
#define MAP_SIZE            (1 << MAP_SIZE_POW2)
#define ck_alloc malloc
#define ck_free free
#define SHM_ENV_VAR         "__AFL_SHM_ID"
#define FATAL PFATAL
#define PFATAL(s) do { \
  perror(s); \
  exit(1); \
} while (0)
#define alloc_printf(_str...) ({ \
    char* _tmp; \
    int _len = snprintf(NULL, 0, _str); \
    if (_len < 0) FATAL("Whoa, snprintf() fails?!"); \
    _tmp = (char*)ck_alloc(_len + 1); \
    snprintf((char*)_tmp, _len + 1, _str); \
    _tmp; \
  })

int shm_id, shm_id2;
volatile void *trace_bits;
static void remove_shm2(void) {
  shmctl(shm_id, IPC_RMID, NULL);
}
static void remove_httpreqr_shm(void) {
  shmctl(shm_id2, IPC_RMID, NULL);
}

static void setup_shm(void) {
  char *shm_str;

  shm_id = shmget(IPC_PRIVATE, MAP_SIZE, IPC_CREAT | IPC_EXCL | 0600);
  if (shm_id < 0) PFATAL("shmget() failed");

  atexit(remove_shm2);

  shm_str = alloc_printf("%d", shm_id);
  setenv(SHM_ENV_VAR, shm_str, 1);
  ck_free(shm_str);

  trace_bits = shmat(shm_id, NULL, 0);
  if (trace_bits == (void *)-1) PFATAL("shmat() failed");
}

static void setup_httpreqr_shm(void) {
  char *shm_str;

  shm_id2 = shmget(IPC_PRIVATE, sizeof(httpreqr_info), IPC_CREAT | IPC_EXCL | 0600);
  if (shm_id2 < 0) PFATAL("shmget() failed");

  atexit(remove_httpreqr_shm);

  shm_str = alloc_printf("%d", shm_id2);
  setenv(HTTPREQR_ENV_VAR, shm_str, 1);
  ck_free(shm_str);

  httpreqr_info = (volatile struct httpreqr_info *)shmat(shm_id2, NULL, 0);
  if ((void*)httpreqr_info == (void *)-1) PFATAL("shmat() failed");
  memset((void*)httpreqr_info, 0, sizeof(httpreqr_info));
  httpreqr_info->magic = 0xdeadbeef;
}

/**
 * This program requires --url
 * @param argc
 * @param argv
 * @return
 */
int main(int argc, char *argv[])
{
    signal(SIGINT, signal_handler);
    signal(SIGTERM, signal_handler);

  bool doInitMemory = getArg(argc, argv, "--initmemory");

  if (doInitMemory){
    initMemory(true);
    return 0;
  }

  string url = getArg(argc, argv, "--url", true);
  if (url.size() == 0 ){
    fprintf(stderr, "You must provide a url\n");
    exit(11);
  }

  string method = getArg(argc, argv, "--method", true);

  string content_type = getArg(argc, argv, "--content-type", true);

  bool json = getArg(argc, argv, "--json");

  string test_input_file = getArg(argc, argv, "--test-input-file", true);

  bool debug = getArg(argc, argv, "--debug");

  RequestData *reqD = new RequestData(url);

  reqD->setMethod(method);
  reqD->setContentType(content_type);
  reqD->setJSON(json);
  cout << "TEST " << reqD->getURL() << " Using port:" << reqD->getPort() << " Content-Type to use " << reqD->getContentType() << endl;

  if (test_input_file.length() != 0)
  {
     cout << "Got test input file " << test_input_file << ", going to read from there";
     ifstream in(test_input_file);
     std::cin.rdbuf(in.rdbuf());
     reqD->loadVariableData();
  }


  setupErrorMem(stoi(reqD->getPort()));

  writeOutAFLSHM(reqD->getPort());

  setup_httpreqr_shm();

    // setup_shm();
  if (getenv("__AFL_SHM_ID")){
    LOG("Launching target...");
    target_pid = launch_target();
    sleep(5); // FIXME: Replace with proper detection of listen
    recvAFLRequests(reqD);
  } else {
    LOG("Launching target...");
    setup_shm();
    target_pid = launch_target();
    sleep(5); // FIXME: Replace with proper detection of listen
    if (poll_target(target_pid)) {
      fprintf(stderr, "Target quit?\n");
      exit(1);
    }

    for (int i = 0; i < 8; i++) {

      memset((void*)trace_bits, 0, MAP_SIZE); // Clear out the map before launching request
      LOG("Sending request...");
      httpreqr_info->enable_logging = 1;
      sendRequest(reqD, debug);
      httpreqr_info->enable_logging = 0;
      LOG("Request complete");


      for (int i = 0; i < MAP_SIZE/sizeof(uint64_t); i++) {
        uint64_t v = ((uint64_t *)trace_bits)[i];
        if (v == 0) continue;
        fprintf(stderr, "%04x: %016lx\n", i*sizeof(uint64_t), v);
      }

    }

#if 0
    LOG("About to send crashing request...\n");
    sleep(5);
    LOG("Time end...\n");


    delete reqD;
    memset((void*)trace_bits, 0, MAP_SIZE); // Clear out the map before launching request
    reqD = new RequestData("http://192.168.0.1/crash");
    reqD->setMethod(method);
    reqD->setJSON(json);
    LOG("Sending crashing request...");
    httpreqr_info->enable_logging = 1;
    sendRequest(reqD);
    sleep(2);
    httpreqr_info->enable_logging = 0;
    LOG("Request complete");
    for (int i = 0; i < MAP_SIZE/sizeof(uint64_t); i++) {
      uint64_t v = ((uint64_t *)trace_bits)[i];
      if (v == 0) continue;
      fprintf(stderr, "%04x: %016lx\n", i*sizeof(uint64_t), v);
    }
    sleep(2);
#endif


  }

  delete reqD;

  return 0;
}
