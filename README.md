# Witcher

This repo contains the source code for Witcher a web application fuzzer that utilizes mutational fuzzing 
to explore web applications and fault escalation to detect command and SQL injection vulnerabilities.

Witcher is to be published in S&P in May 2023.
@inproceedings{trickelwitcher,
  title={Toss a fault to your witcher: Applying grey-box coverage-guided mutational fuzzing to detect sql and command injection vulnerabilities},
  author={Trickel, Erik and Pagani, Fabio and Zhu, Chang and Dresel, Lukas and Vigna, Giovanni and Kruegel, Christopher and Wang, Ruoyu and Bao, Tiffany and Shoshitaishvili, Yan and Doup{\'e}, Adam},
  booktitle={IEEE Symposium on Security and Privacy (SP), to appear},
  pages={116--133},
  year={2023}
}


This repo relies on submodules
`git submodule update --init --recursive`


The best way to utilize Witcher is to build the base docker containers and use them as the foundation for the web application container to be tested.

To get started, run `docker/build-all.sh`

Once completed, the script will have built all of Witcher's base containers. 
- witcher/php5run
- witcher/php7run
- witcher/python
- witcher/java
- witcher/nodejs
- witcher/ruby

When building the target web application, use one of the above containers in conjunction with the `FROM` field in the application's docker build file.

Currently, PHP 5 and 7 fuzz the application by accessing the PHP application via CGI. Whereas, Witcher fuzzes python, java, nodejs, and ruby using the target web application's interface.

This repository contains Witcher's source code, the scripts used to evaluate Witcher are available at https://github.com/sefcom/Witcher-experiment 

# Building Basic Docker Containers


To start, we suggest setting up a directory structure similar to what was done for the evaluation (See [Hospital Management](https://github.com/sefcom/Witcher-experiment/tree/main/interpreter-targets/openemr)). 
In general, create a folder for the web application where the Dockerfile and source code will reside.
Next, create a folder for configuration and results (e.g.,`user`), inside the new folder create a file named `witcher_config.json`
The new folder will be mapped to Witcher's container at run time so that the results are saved to your local drive.

`docker build --build-arg USE_INSTRUMENTED=1 -t openemr-user`

# Pulling the containers

The completely built containers exists on hub.docker, which can be pulled via the witcherfuzz user.
```docker pull witcherfuzz/php5run
docker pull witcherfuzz/php7run
docker pull witcherfuzz/python
docker pull witcherfuzz/java
docker pull witcherfuzz/nodejs
docker pull witcherfuzz/ruby
```



## Witcher Config File

The Witcher Config File, `witcher_config.json` should be created in the 

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


## Fuzzing the application

For the experiments, a bash [script](https://github.com/sefcom/Witcher-experiment/blob/main/scripts/run_single_experiment.sh) was used to configure the container and directories and run the fuzzer. 

`docker run -id --rm --privileged --shm-size=1G -e DISPLAY=$DISPLAY -v /tmp/.X11-unix:/tmp/.X11-unix  --name openerm-user -v openemr:/openemr -v openemr/user:/tmp/coverages ${docker_image_name}`

`docker exec -it -w /helpers/request_crawler/  openemr touch /tmp/start_test.dat`

### Running the Request Crawler

`docker exec -it -w /helpers/request_crawler/ -u wc  openemr bash -i'"'timeout --signal KILL $(( 4 * 60 * 60 ))s  node main.js request_crawler http://localhost /openemr/user --no-headless >> openemr/user/crawler.log '"'`

### Running the fuzzer

`docker exec -it openemr/user bash -c "echo 1 >/proc/sys/kernel/sched_child_runs_first && echo core > /proc/sys/kernel/core_pattern";`
`docker exec -it openemr/user bash -c 'for fn in /sys/devices/system/cpu/cpu*/cpufreq/scaling_gov*; do echo performance > $fn; done'`

`docker exec -it opeemr/user bash -i -c 'cd /app; chown wc:www-data -R .; chmod o+r . -R;if [ -d /var/instr/ ]; then chmod 666 -R /var/instr/*; fi`

`docker exec -it -w "openemr" -u wc openemr/user bash -i -c "p --testver WICHR `

After completing the last command, the fuzzer should start with results being printed to the terminal.



