# Witcher



## Witcher Config File
- afl_preload is location of cgi wrapper for PHP and CGI web applications
- number_of_refuzzes is the number of times page will be fuzzed within a single trial, more than one encourages page to page interactions
- timeout in seconds
- script_skip_list - scripts to skip testing of
- script_random_order - 0: no randomization, 1: randomize order every trial, 2: randomize for refuzzes
- script_start/end_index - used to split up large script list for large web applications  
- cores - the number of cores to use for fuzzing.
- request_crawler - config data used for running the crawler
- direct - direct login information, used before creating the fuzzer instance to setup a login session
```
{
  "testname": "name of test",
  "afl_inst_interpreter_binary": "location of intrepreter with AFL instrumentation",
  "wc_inst_interpreter_binary": "path to intrepreter with Witcher instruemtnation",
  "base_url": "http://localhost/",
  "afl_path": "/afl",
  "ld_library_path":"/wclibs",
  "afl_preload":"/wclibs/lib_db_fault_escalator.so",
  "number_of_refuzzes": 3,
  "timeout" : 28800,
  "script_skip_list": ["test_5"],
  "script_random_order": 1,
  "script_start_index": 10,
  "script_end_index": 20,
  "cores": 3,
  "request_crawler": {
    "form_url" : "http://localhost/interface/login/login.php?site=default",
    "usernameSelector": "#authUser",
    "usernameValue": "admin",
    "passwordSelector": "#clearPass",
    "passwordValue": "password",
    "submitType": "enter",
    "positiveLoginMessage": "title=\"Current user\"",
    "method": "POST",
    "form_selector": ".form-login",
    "form_submit_selector": "input[type=submit]",
    "ignoreValues": [],
    "urlUniqueIfValueUnique": []
  },

  "direct":{
    "url": "http://localhost/interface/main/main_screen.php",
       "postData": "new_login_session_management=1&authProvider=TroubleMaker&authUser=admin&clearPass=password&languageChoice=1",
    "getData": "auth=login&site=default",
    "positiveHeaders": [{"Location":"/interface/main/tabs/main.php"}],
    "positiveBody": "",
    "method": "POST",
    "cgiBinary": "/php/php-cgi-mysqli-wc",
    "loginSessionCookie" : "OpenEMR",
    "mandatoryGet": "",
    "extra_authorized_requests": [{"url": "http://localhost/interface/patient_file/summary/demographics.php?set_pid=2"}]
  }

}
```
