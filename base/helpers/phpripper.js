
const fs = require('fs');
const path = require('path');
const http = require('http');
const process = require('process');
const iser = require("./request_crawler/input_sifter2.js");
const FoundRequest = require("./request_crawler/FoundRequest.js");

if (process.argv.length < 3){
    console.log("ERROR, not enough parameteres, must pass in results directory and phpfilename");
    process.exit(32);
}
const resultsdir = process.argv[2];
const phpfilename = process.argv[3];

const phpdata = fs.readFileSync(phpfilename,"utf-8");
var pvars = {}, gvars={}, cvars={};
var all_vars = [cvars, gvars, pvars];

function getVars(vartype, vars, data){
    const regexpStr = "(" +  vartype + "|REQUEST).{1,3}[\"'](?<vname>.*?)[\"']";
    const regexpVV = new RegExp(regexpStr,"g");
    let v_match;
    while(null != (v_match=regexpVV.exec(data))) {
        let vn = v_match.groups.vname;
        vars[vn] = "Q";
        const phpvarRexExp = new RegExp(`/\$${vn}[ =]{1,6}['"](?<vval>.*?)["']/g`);

        let vval_result;
        while(null != (vval_result=phpvarRexExp.exec(data))) {
            vars[vn]=vval;
            console.log(vval_result.groups.vval);
        }

    }

}

getVars("POST", pvars, phpdata);
getVars("GET", gvars, phpdata);
getVars("COOKIE", gvars, phpdata);

function assignToRandom(all_vars, val){
    var randomProperty = function (obj) {
        var keys = Object.keys(obj);
        return keys[ keys.length * Math.random() << 0];
    };
    for (let obj of all_vars){
        for (let x =0; x < 10; x++){
            const chosenProp = randomProperty(obj);
            if (typeof chosenProp === 'undefined'){
                break;
            }
            console.log("ChosenProp",chosenProp);
            if (obj[chosenProp] === "Q"){
                obj[chosenProp] = val;
                break;
            }
        }

    }

}

const regexpCase = new RegExp(/case[ "]{0,6}(?<vname>.*?)[\"']/g) ;
let v_match;
while(null != (v_match=regexpCase.exec(phpdata))) {
    let vn = v_match.groups.vname;
    assignToRandom(all_vars, vn);
    console.log(vn, all_vars);
}
let appData = new iser.AppData(false,resultsdir );
for (obj of all_vars){
    for (let key in obj){
        if (obj.hasOwnProperty(key)){
            appData.addQueryParam(key, obj[key]);
        }

    }

}
let cookiesStr="", getsStr = "", postsStr="";
for (let cookie in cvars){
    cookiesStr+=`${cookie}=${cvars[cookie]}; `;
}
for (let getv in gvars){
    getsStr +=`${getv}=${gvars[getv]}&`;
}
if (getsStr.length > 0){
    getsStr=getsStr.slice(0, -1);
}
for (let postv in pvars){
    postsStr +=`${postv}=${pvars[postv]}&`;
}

if (postsStr.length > 0){
    method = "POST";
    postsStr = postsStr.slice(0,-1);
} else {
    method = "GET";
}
console.log("cookies", cookiesStr, "gets", getsStr, "posts",postsStr);
let urlstr = `${phpfilename}?${getsStr}`;
let headers = {};
headers["cookie"] = cookiesStr;

let foundRequest = FoundRequest.requestParamFactory(urlstr, method, postsStr, headers, "SCRIPT_ANALYSIS");
foundRequest.from = "localSearch";
console.log("URL", foundRequest._urlstr);
console.log("POST",foundRequest._postData);
console.log("HEADER", foundRequest._headers);
let addResult = appData.addRequest(foundRequest);
if (addResult){
    console.log(`\x1b[38;5;2mADDED ${foundRequest.toString()}  \x1b[0m`);
}

appData.save();

// for (let pv of posts){
//     console.log("Output : " + pv);
// }







