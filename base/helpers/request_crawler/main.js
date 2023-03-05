#! /usr/bin/env node

import fs from 'fs';
import path from 'path';
import http from 'http';
import process from  'process';

const COOKIE_INDEX = 0;
const GET_INDEX = 1;
const POST_DATA_INDEX = 2;

import {AppData, RequestExplorer} from "./input_sifter2.js";

// buildRequest consumes url and returns
// it does this by spliting the path on / and reversing the results.  Starting with the last value it
// finds the longest path that exists based on the provided path.
function get_fuzzer_output_dirname(apath, runcnt){

    //apath = apath.replace("/","+");
    var build_dn = "";
    var final_fop = "";
    apath.split("/").reverse().forEach(function (ele) {
        if (build_dn === ""){
            build_dn = ele;
        } else {
            build_dn = ele + "+" + build_dn;
        }

        let fuzzer_output_path = path.join(BASE_APPDIR,"fin_outs",runcnt + build_dn, "fuzzer-master","queue");
        if (fs.existsSync(fuzzer_output_path)){
            final_fop = fuzzer_output_path;
        }
    });

    return final_fop;

}

function convertFilePathToURL(apath){

    var build_url = "";
    let urls = [];
    apath.split("/").reverse().forEach(function (ele) {
        if (build_url === ""){
            build_url = ele;
        } else {
            build_url = ele + "/" + build_url;
        }

        let url = new URL(BASE_SITE + "/" + build_url);
        let options = {method: 'HEAD', host: url.hostname, port: url.port, path: url.pathname, headers:{Connection:"close"}};

        req = http.request(options, function(r) {
            //console.log(url.href, build_url, r.statusCode);
            if (r.statusCode === 200) {
                urls.push(url);
            } else if (r.statusCode === 302){
                // if ("location" in r.headers && isDefined(r.headers['location'])){
                //     url = new URL(r.headers['location']);
                //     onURLExists(fop, url);
                // }
                //onURLExists(fop, url);
            } else {
                // do nothing, keep trying
            }

        });
        req.end();
    });
    return urls;
}


function addInputsToRequestsFound(requestInputDir, url, appData){
    let fop_master_queue = requestInputDir;
    var paths_to_test = fs.readdirSync(fop_master_queue,'utf8');
    var finished = false;
    //paths_to_test.forEach((input_fn, index) =>{

    for (const inputFilename of paths_to_test) {

        if (finished){
            break;
        }
        var input_filepath = path.join(fop_master_queue,inputFilename);

        if (fs.lstatSync(input_filepath).isFile()){

            //finished = true;
            const input_data = fs.readFileSync(input_filepath, 'binary');
            let requestData = {base_url:url, [COOKIE_INDEX]:"", [GET_INDEX]:"", [POST_DATA_INDEX]:""};
            input_data.split("\x00").forEach(function(requestElement, index){
                // reads cookie, get, and post data from file and updates dictionary
                requestData[index] = requestElement
            });
            // const clen = requestData[COOKIE_INDEX].length;
            // const glen = requestData[GET_INDEX].length;
            const plen = requestData[POST_DATA_INDEX].length;
            var necessaryParams = "";
            let inputSearchParams = requestData[GET_INDEX];
            if (inputSearchParams.startsWith("?")){
                inputSearchParams = inputSearchParams.substring(1);
            }
            url.searchParams.forEach(function (value, key, parent){
                let param=`${key}=`;
                if (inputSearchParams.indexOf(param) === -1 && param.length>1){
                    necessaryParams += param + value + "&";
                }
            });
            let urlstr = "";
            if (necessaryParams.length > 0){
                urlstr = `${url.origin}${url.pathname}?${necessaryParams}${inputSearchParams}`;
            } else {
                urlstr = `${url.origin}${url.pathname}?${inputSearchParams}`;
            }

            console.log("STING URL HERE",urlstr);
            if (plen > 0 ){
                appData.addRequest(FoundRequest.requestParamFactory(urlstr, "POST", requestData[POST_DATA_INDEX], {}, "initialLoad", requestData[COOKIE_INDEX]));
            }
            appData.addRequest(FoundRequest.requestParamFactory(urlstr, "GET", requestData[GET_INDEX], {}, "initialLoad", requestData[COOKIE_INDEX]));

        }

    }
    //console.log(requestsFound);
}



async function explorationWorker(workernum, appData){
    await sleep(50);

    if (appData.numRequestsFound() === 0){
        let re = new RequestExplorer(appData, workernum, BASE_APPDIR);

        await re.start();
    }
    let nextRequest = appData.getNextRequest();
    while (nextRequest != null){

        let re = new RequestExplorer(appData, workernum, BASE_APPDIR, nextRequest);
        await re.start();

        console.log("\x1b[38;5;12m^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^ Completed " + appData.currentRequest.url() + " ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^ \x1b[0m\n");
        appData.updateReqsFromExternal()
        nextRequest = appData.getNextRequest();
    }

}

function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}

function startExploration(workers=1, appData){

    // if (appData.hasRequests()){
    //     console.log("ERROR, no requests in queue!");
    //     throw error("Failed to find any requests to process");
    // }
    for (let i=0; i < workers;i++){
        //sleep(i*10000);
        explorationWorker(i, appData);
    //    setTimeout(explorationWorker, 3000*i, i, appData);

        console.log("Started worker ", i)
    }
    let currentURLRound = appData.currentURLRound;

    console.log(`DoNeDoNeDoNeDoNeDoNeDoNeDoNe ${currentURLRound} DoNeDoNeDoNeDoNeDoNeDoNeDoNeDoNeDoNeDoNe`);
    console.log(`DoNeDoNeDoNeDoNeDoNeDoNeDoNe ${currentURLRound} DoNeDoNeDoNeDoNeDoNeDoNeDoNeDoNeDoNeDoNe`);
    console.log(`DoNeDoNeDoNeDoNeDoNeDoNeDoNe ${currentURLRound} DoNeDoNeDoNeDoNeDoNeDoNeDoNeDoNeDoNeDoNe`);
    console.log(appData.getRequestInfo());
    console.log(`DoNeDoNeDoNeDoNeDoNeDoNeDoNe ${currentURLRound} DoNeDoNeDoNeDoNeDoNeDoNeDoNeDoNeDoNeDoNe`);
    console.log(`DoNeDoNeDoNeDoNeDoNeDoNeDoNe ${currentURLRound} DoNeDoNeDoNeDoNeDoNeDoNeDoNeDoNeDoNeDoNe`);
    console.log(`DoNeDoNeDoNeDoNeDoNeDoNeDoNe ${currentURLRound} DoNeDoNeDoNeDoNeDoNeDoNeDoNeDoNeDoNeDoNe`);
}

if (process.argv.length > 4) {
    var offset = 0;
    console.log(process.argv)
    if (process.argv[0] === "timeout"){
    }
    var BASE_SITE = process.argv[3];
    var BASE_APPDIR = process.argv[4];

    var headless = true;
    if (process.argv.length>5 && process.argv[5] === "--no-headless"){

        headless = false;
    }
    var files_fn = path.join(BASE_APPDIR, "files.dat");
    var SEED_DIR = path.join(BASE_APPDIR, "input");

    //session_id = get_a_session();
    let doInit = (process.argv.length <= 4);

    let appData = new AppData(doInit, BASE_APPDIR, BASE_SITE, headless);

    if (fs.existsSync(files_fn)){
        let paths_to_test = fs.readFileSync(files_fn,'utf8');

        paths_to_test.split('\n').forEach(function(apath){

            let fuzzer_out_path = SEED_DIR;
            let urls = convertFilePathToURL(apath);
            for (let url of urls){
                if (process.argv.length > 4) {
                    appData.usingFuzzingDir();
                    fuzzer_out_path = get_fuzzer_output_dirname(apath, process.argv[4]);
                    if (fs.existsSync(fuzzer_out_path)){
                        addInputsToRequestsFound(fuzzer_out_path, url, appData);
                    }
                } else {
                    //appData.addRequest(url.href,"GET","","initial","");
                }
            }

        });

    }

    // wait a few seconds for a few url requests to complete first
    setTimeout(startExploration,2000, 1, appData);

} else {
    console.log(process.argv)
    console.log("ERROR, an input file was not provided");
    console.log("Usage:\n\tnode input_sifter.js \x1b[38;5;5mBASE_SITE BASE_APPDIR \x1b[38;5;4m[RUNCNT]\x1b[0m\n\n");

}
