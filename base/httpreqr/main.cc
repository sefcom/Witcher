//
// Created by erik on 4/15/2020.
//
/*
 * sudo aptitude install libcurl4-openssl-dev
 * Compile and run the code
 * # g++ main.cc -o curl_example -lcurl
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

#define FORKSRV_FD 198
#define TSL_FD (FORKSRV_FD - 1)

using namespace std;

struct test_process_info {
    int initialized=0;
    int afl_id = 0;
    int port = 0;
    int reqr_process_id;
    int process_id=0;
    int start_recording=0;
    char error_type[20]; /* SQL, Command */
    char error_msg[100];
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
    string url, cookies="", gets="", posts="", method;
    bool json = false;
public:
    RequestData(string urlIn){
      url = urlIn;
    }
    void loadVariableData(){
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
        }
        inputcount++;
      }
    }
    string getPort(){
      string port = regex_replace(url, regex("http://.*:([0-9]+).*"), string("$1"));
      if (port.length() == 0){
        return "80";
      } else {
        return port;
      }
    }

    string getURL(){
      if (gets.size() == 0){
        return url;
      } else {
        return url + "?" +  gets;
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
    bool hasPosts(){
      return posts.size() > 0;
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

    string getContentType(){
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
}

void sendRequest(RequestData *reqD ){
  CURL *curl;
  CURLcode code(CURLE_FAILED_INIT);
  CURLcode res;
  long timeout = 30;
  string readBuffer;
  cout << "SENDING REQUEST " << endl;
  curl = curl_easy_init();
  if(curl) {
    cout << "in da curl" << endl;
    reqD->loadVariableData();
    cout << "in da curl" << endl;
    //cout << reqD->getPosts() << endl;

    cout << "Cookies = " << reqD->getCookies() << endl << "gets = " << reqD->getGets() << endl << "posts = '" << reqD->getPosts() << "'" << endl << "PORT=" << reqD->getPort() << endl;

    curl_easy_setopt(curl, CURLOPT_BUFFERSIZE, 102400L);
    curl_easy_setopt(curl, CURLOPT_NOPROGRESS, 1L);
    curl_easy_setopt(curl, CURLOPT_HEADER, 1L);
    curl_easy_setopt(curl, CURLOPT_SSL_VERIFYPEER, 0L);
    curl_easy_setopt(curl, CURLOPT_SSL_VERIFYHOST, 0L);
    curl_easy_setopt(curl, CURLOPT_MAXREDIRS, 50L);
    curl_easy_setopt(curl, CURLOPT_TCP_KEEPALIVE, 1L);

    curl_easy_setopt(curl, CURLOPT_WRITEFUNCTION, WriteCallback);
    curl_easy_setopt(curl, CURLOPT_WRITEDATA, &readBuffer);
    assert (CURLE_OK == (code = curl_easy_setopt(curl, CURLOPT_TIMEOUT, timeout)));

    struct curl_slist *headers=NULL;

    printf("[WC][main] sending '%s'\n", reqD->getPosts().c_str());
    char test[reqD->getPosts().length()+1];
    strcpy(test, reqD->getPosts().c_str());
    printf("original str = '%s'\n", test);
//    cout << "Test Hex = ";
//    for(int i=0; i<strlen(test); ++i)
//      std::cout << std::hex << (int)test[i];
//    cout << endl;

    curl_easy_setopt(curl, CURLOPT_CUSTOMREQUEST, reqD->getMethod().c_str());


    if (reqD->hasCookies()){
      assert (CURLE_OK == curl_easy_setopt(curl, CURLOPT_COOKIE, reqD->getCookies().c_str()));
    }
    if (reqD->hasPosts()){
      printf("[WC][main] sending POST '%s'\n", reqD->getPosts().c_str());

      // POSTFIELDSIZE needs to be before COPYPOSTFIELDS
      curl_easy_setopt(curl, CURLOPT_POSTFIELDSIZE_LARGE, (curl_off_t) reqD->getPosts().size());
      curl_easy_setopt(curl, CURLOPT_COPYPOSTFIELDS , reqD->getPosts().c_str());
      headers = curl_slist_append(headers, reqD->getContentType().c_str());

    }
    cout << "[WC][main] URL=" << reqD->getURL() << endl;

    curl_easy_setopt(curl, CURLOPT_HTTPHEADER, headers);

    assert (CURLE_OK == curl_easy_setopt(curl, CURLOPT_URL, reqD->getURL().c_str()));

    code = curl_easy_perform(curl);

    curl_easy_cleanup(curl);

    curl_slist_free_all(headers);
    headers = NULL;

    cout << "Readbuffer = " << readBuffer << endl;
  }

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
  unsigned int afl_forksrv_pid = 0;
  static unsigned char afl_fork_child;
  static double totaltracetime;

  int parent_pid = getpid();
  //cgi_get_shm_mem();
  printf("[WC][FORK]Starting up Forker %d __AFL_SHM_ID=%s\n", parent_pid, getenv("__AFL_SHM_ID"));
  bool infinite = true;

  if (write(FORKSRV_FD + 1, tmp, 4) != 4) {
    printf("[WC][FORK]\tfork server not running.\n");
    infinite = false;
  } else {
    printf("\e[1;32m[WC][FORK]\tWrote to FORKSRV_FD\e[0m\n");
  }

  afl_forksrv_pid = getpid();
  //printf("\tPARENT pid = %d\n", afl_forksrv_pid);
  /* All right, let's await orders... */
  int claunch_cnt = 0;
  while (infinite || claunch_cnt < 1) {

    pid_t child_pid = -1;
    int status, t_fd[2];

    /* Whoops, parent dead? */
    if (claunch_cnt < 1){
      printf("[WC][FORK]\t\tINITIAL awaiting orders %d\n", getpid());
    } else {
      printf("\n[WC][FORK]\t\t\e[1;32mRESET and awaiting orders %d\e[0m\n", getpid());
    }

    if (infinite && read(FORKSRV_FD, tmp, 4) != 4) exit(2);

    /* Establish a channel with child to grab translation commands. We'll
       read from t_fd[0], child will write to TSL_FD. */

    if (pipe(t_fd) || dup2(t_fd[1], TSL_FD) < 0) exit(3);
    clock_gettime(CLOCK_MONOTONIC, &start);
    printf("[WC][FORK]\t\tCreating communication channel to new child from %d\n", getpid());
    close(t_fd[1]);
    claunch_cnt ++;
    child_pid = fork();

    if (child_pid < 0) exit(4);

    if (child_pid==0) {  // child_pid == 0 when in the child

      /* Child process. Close descriptors and run free. */

      isparent=false;
      std::string cpid = std::to_string(getpid());

      setenv("AFL_CHILD_PID", cpid.c_str(),1);
      printf("[WC][CHILD-FORK]\t\t\t\033[33mlaunch cnt = %d current process IS the child, pid == %d\033[0m\n", claunch_cnt,  getpid());
      afl_fork_child = 1;
      close(FORKSRV_FD);
      close(FORKSRV_FD + 1);
      close(t_fd[0]);
      string id_str = getenv("__AFL_SHM_ID");
      int shm_id = atoi(id_str.c_str());

      //shm_id = shmget(449988, 65536, 0666);
      afl_area_ptr = (unsigned char*)  shmat(shm_id, NULL, 0);

      printf("[WC][CHILD-FORK] AFL_SHM_ID  = %x, AFL ptr = %p trace_value=%d \n", shm_id, afl_area_ptr, afl_area_ptr[0]);
      this_test_process_info->start_recording=1;
      sendRequest(reqD);
      checkForServerErrors(reqD->getPort());
      printf("[WC][CHILD-FORK] Error information => %s\n", this_test_process_info->error_type);

      return;

    }

    /* Parent. */

    close(TSL_FD);
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
    this_test_process_info->start_recording=0;
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
    //printf("\t\tStats from child (%d) is %d \n", child_pid, status);
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

//void trickAFLInstrumentChecker(){
//  chr *shm_id_str = getenv("__AFL_SHM_ID");
//
//  if (shm_id_str){
//
//  }
//
//
//}
void remove_shm(){
//    if (isparent){
//        this_test_process_info->initialized = 0;
//        this_test_process_info->port = 0;
//        this_test_process_info->afl_id = 0;
//        this_test_process_info->process_id = 0;
//        //memset(test_process_info_ptr, 0, TEST_PROCESS_INFO_SMM_SIZE);
//        //shmctl(tbd_shm_id, IPC_RMID, NULL);
//        if (fake_afl_shm_id != 0){
//          shmctl(fake_afl_shm_id, IPC_RMID, NULL);
//        }
//
//        printf("[Witcher] Cleaned up and exitting.\n");
//    }

}
void signal_handler(int signal){
    printf("[Witcher] caught signal, kill shm region up and exitting.\n");
    exit(33);
}
void initMemory(bool setToZero){
    key_t mem_key = ftok("/tmp",'W');
    printf("*** mem_key %x \n", mem_key);
    int shm_id = shmget(mem_key , TEST_PROCESS_INFO_SMM_SIZE, 0666);
    if (shm_id < 0 ) {
        shm_id = shmget(mem_key , TEST_PROCESS_INFO_SMM_SIZE, IPC_CREAT | 0666);
        if (shm_id < 0 ) {
            //printf("*** shmget error (server) ***\n");
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
    //atexit(remove_shm);
    printf("*** test_process_info_ptr = %p\n", test_process_info_ptr);
    if (setToZero){
        printf("\033[34m RESETTING SHARED MEMORY \n\033[0m");
        memset(test_process_info_ptr, 0, TEST_PROCESS_INFO_SMM_SIZE);
    }

}
void setupErrorMem(int port){
    bool inited = false;

    initMemory(false);

    for (int x=0; x < TEST_PROCESS_INFO_MAX_NBR; x++){
        if (test_process_info_ptr[x].initialized == 31337){
            inited = true;
            break;
        }
    }

    for (int x=0; x < TEST_PROCESS_INFO_MAX_NBR; x++){
        if (test_process_info_ptr[x].initialized != 31337){
            this_test_process_info = test_process_info_ptr + x;
            break;
        }
    }

    printf("\033[36m*** process info for this port = %p\033[0m\n", test_process_info_ptr);
    this_test_process_info->initialized = 31337;
    this_test_process_info->port = port;

    char * afl_shm_loc = getenv("__AFL_SHM_ID");
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
    printf ("You must provide a url");
    exit(11);
  }

  string method = getArg(argc, argv, "--method", true);

  bool json = getArg(argc, argv, "--json");

  RequestData *reqD = new RequestData(url);

  reqD->setMethod(method);
  reqD->setJSON(json);
  cout << "TEST " << reqD->getURL() << " Using port:" << reqD->getPort() << endl;

  setupErrorMem(stoi(reqD->getPort()));

  writeOutAFLSHM(reqD->getPort());

  if (getenv("__AFL_SHM_ID")){
    recvAFLRequests(reqD);
  } else {
    //trickAFLInstrumentChecker();
    sendRequest(reqD);
  }

  delete reqD;

  return 0;
}


