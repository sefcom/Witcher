
const fs = require("fs");
const path = require("path");
const args = process.argv.slice(2);
const USE_URL = args.includes("--url");
const FILE_WRITE = (! args.includes("--no_file_write"));
var binaryName = "";
var only_use_suffix = ['php'];
if (args.includes("--binary")){

    let binNameLoc = args.indexOf("--binary") +1;

    if (binNameLoc < args.length) {
        binaryName = args[binNameLoc];
    }
}

let inputpath = "";
if (args.includes("--inputpath")){
    let inputpathLoc = args.indexOf("--inputpath") + 1;
    if (inputpathLoc < args.length){
        inputpath = args[inputpathLoc];
    }
}

function makeid(length) {
    var result           = '';
    var characters       = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    var charactersLength = characters.length;
    for ( var i = 0; i < length; i++ ) {
        result += characters.charAt(Math.floor(Math.random() * charactersLength));
    }
    return result;
}

function keysin(firsturl, securl){
    let firstkeys=[], seckeys=[]
    for (let key of firsturl.searchParams.keys()) {
        firstkeys.push(key);
    }
    for (let key of securl.searchParams.keys()) {
        seckeys.push(key);
    }
    if (firstkeys.length > seckeys.length){
        return false;
    }
    firstkeys.sort();
    seckeys.sort();
    for (let i=0; i < firstkeys.length; i++){
        if (firstkeys[i] !== seckeys[i]){
            return false;
        }
    }
    let firstout = "", secout="";
    for (let key of firstkeys){
        firstout += `${key}, `;
    }
    for (let key of seckeys){
        secout += `${key}, `;
    }
    console.log(firstout);
    console.log(secout);
    return true;

}

const sifterdata = fs.readFileSync("sifter_data.json","utf-8");
const jdata = JSON.parse(sifterdata);

let inputSet = new Set(jdata["inputSet"]);
if (binaryName.length > 0){
    //console.log("doing thing to binname  ", jdata);
    let binaryInputSet = new Set(jdata["inputSet-"+binaryName]);
    inputSet = new Set([...inputSet, ...binaryInputSet]);
    //console.log('inputSet=', inputSet);
}

var afldict = "";
if (fs.existsSync("dict.txt")){
    afldict = fs.readFileSync("dict.txt","ascii");
}


let temprf = jdata["requestsFound"];
let outpages = "", outPathSet = new Set();
for (key of Object.keys(temprf)){
    let req = temprf[key];
    //console.log(req._url);
    let url = new URL(req._url);
    let pagepath = path.join("/app",url.pathname);
    if (USE_URL){
        outPathSet.add(req.url);
    } else {
        let re = /(?:\.([^.]+))?$/;
        let match = re.exec(pagepath);
        if (match.length > 0){
            if (only_use_suffix.includes(match[1]) ) {
                 if (fs.existsSync(pagepath) ){
                    outPathSet.add(pagepath)
                }
            }
        }

    }

}

let outPathArr = Array.from(outPathSet);
outPathArr.sort();
for (let apath of outPathArr){
    outpages += `${apath}\n`;
}

//console.log(outpages);
if (FILE_WRITE){
    fs.writeFileSync("files.dat", outpages);
}


// for (let line of afldict.split("\n")){
//     inputSet.add(line);
// }

//let inputSet  = new Set(['show_all=no&form_inactive=1','show_all=no&sortby=pnotes.message_status&sortorder=asc&form_inactive=1', 'show_all=no&sortby=pnotes.message_status&form_inactive=1']);
// let inputSet = new Set(['jumpdate=&viewtype=day&',
//     'jumpdate=&viewtype=day&pc_username%5B%5D=admin&',
//     'auth=login&site=default&']);
let longerInputSet = new Set();
let shorterInputSet = new Set();
let shortOutStr ="", longOut = [];
for (let inp of inputSet){
    if (inp.indexOf("&") > -1 && inp.indexOf("&") !== (inp.length -1)){
        longerInputSet.add(inp);
    } else {
        if (inp.length < 128){
            shortOutStr += `${inp}&\n`;
            shortOutStr += `${inp}'(&\n`;
        } else {
            longOut.push(inp);
        }

    }
}
console.log(`LONGER INPUT SET SIZE = ${longerInputSet.size}`);
let outset = new Set(), outkeys = new Set();


for (let firstinp of longerInputSet){
    let firsturl = new URL(`http://example.com?${firstinp}`);
    let firstkeys = [];
    let firstkeysstr = "";
    for (let key of firsturl.searchParams.keys()) {
        firstkeys.push(key);
    }
    firstkeys.sort();
    for (let key of firstkeys){
        firstkeysstr += `${key}_`;
    }
    if (outset.has(firstkeysstr)){
        continue
    }

    let bestMatchKeyStr = "", bestMatchQS = "";
    for (let secinp of longerInputSet){
        let securl = new URL(`http://example.com?${secinp}`);
        let seckeys = [];
        let seckeysstr = "";
        for (let key of securl.searchParams.keys()) {
            seckeys.push(key);
        }
        if (firstkeys.length > seckeys.length){
            continue;
        }
        seckeys.sort();
        for (let key of seckeys){
            seckeysstr += `${key}_`;
        }
        let keyMatch = true;
        for (let i=0; i < firstkeys.length; i++){
            if (seckeysstr.indexOf(firstkeys[i] ) === -1){
                keyMatch=false;
            }
        }
        if (keyMatch){
            if (seckeysstr.length > bestMatchKeyStr.length){
                bestMatchKeyStr = seckeysstr;
                bestMatchQS = secinp;
            }
        }
    }
    if (bestMatchKeyStr.length > 0){
        if (outkeys.has(bestMatchKeyStr)){
            // don't do nufin
        } else {
            outset.add(bestMatchQS);
            outkeys.add(bestMatchKeyStr);
            if (bestMatchQS.length < 126){
                shortOutStr += `${bestMatchQS}\n`;
            } else {
                longOut.push(bestMatchQS);
            }
        }
    }
}

fs.writeFileSync("dict.txt", shortOutStr + "&\n=\n");
console.log("LONG OUT",longOut.length);
for (const lo of longOut){
    let outstr = `Cooky=1\x00${lo}\x00${lo}`;
    let filename = `xtracted_seed_${makeid(6)}`;
    fs.writeFileSync(path.join(inputpath, "input", filename), outstr);
}

console.log("DONE");







