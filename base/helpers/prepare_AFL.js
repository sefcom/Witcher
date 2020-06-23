
const fs = require('fs');
const path = require('path');
const http = require('http');
const process = require('process');
const iser = require("./request_crawler/input_sifter2.js");

if (process.argv.length < 3){
    process.exit(32);
}
const APP_PATH = process.argv[2];
const afl_target = process.argv[3];

const sifterdata = fs.readFileSync(`${APP_PATH}/../sifter_data.json`,"utf-8");
const jdata = JSON.parse(sifterdata);
const requestsFound = jdata["requestsFound"];

const INPUT_PATH = path.join(APP_PATH,"input");
if (fs.existsSync(INPUT_PATH)) {
    fs.rmdirSync(INPUT_PATH, { recursive: true });
}
fs.mkdirSync(INPUT_PATH);
strid=0;
console.log("Number of requests in json file", Array.from(Object.keys(requestsFound)).length);
for (let req of Object.values(requestsFound)){
    if (req._url.indexOf(afl_target) > -1){
        let strid = req["_id"].toLocaleString('en', {minimumIntegerDigits:5,minimumFractionDigits:0,useGrouping:false});
        let cookieData="";
        let tempurl = new URL(req._url);
        let queryString = tempurl.search.substring(1);
        let postData = "";
        if ("_cookieData" in req){
            cookieData =  req._cookieData;
        }
        if (queryString.search(",") > -1){
            queryString = queryString.replace(",","&");
        }
        if ("_postData" in req){
            postData = req._postData.replace(/,/g,"&");
        }
        let strout = `${cookieData}\x00${queryString}\x00${postData}`;
        if (cookieData.length > 0 || queryString.length  > 0 || postData.length > 0){
            fs.writeFileSync(path.join(INPUT_PATH,`seed-${strid}`), strout);
        }
    }
}

var files = fs.readdirSync(INPUT_PATH);
if (files.length===0){
    fs.writeFileSync(path.join(INPUT_PATH,`seed-${strid}`), "seed\x00me\x00seymour\x00");
}







