//import puppeteer from 'puppeteer';

const puppeteer = require('puppeteer');
const fs = require('fs');
const path = require('path');
const http = require('http');
const process = require('process');
const fuzzySet = require('fuzzyset');
//const {JSHandle} = require('puppeteer/lib');
const FoundRequest = require('./FoundRequest');

const GREEN="\x1b[38;5;2m";
const ENDCOLOR="\x1b[0m";

const MAX_NUM_ROUNDS = 3;

// var requestsFound = {}; // { <method+url>: {url:"", method:"", postData:"", attempts:0 } }
function sleepg(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}
class AppData{

    constructor(initializeWithBase, base_appdir, base_site, headless) {
        this.requestsFound = {};

        this.site_url = new URL(base_site);
        this.headless = headless;
        this.inputSet = new Set();
        this.currentURLRound = 1;
        this.collectedURL = 0;
        this.base_appdir = base_appdir;
        this.usingFuzzingDir = false;
        this.maxKeyMatches = 2;
        this.fuzzyMatchEquivPercent = .70;
        this.ignoreValues = new Set();
        this.urlUniqueIfValueUnique = new Set();
        this.minFuzzyScore = .80;


        this.site_ip = this.site_url.host
        //this.site_ip = base_site.

        if (!this.loadDataFromJSON()){
            if (this.site_url.href.endsWith("/")){
                this.addRequest(FoundRequest.requestParamFactory(`${this.site_url.href}/admin`, "GET", "",{},"initial",this.site_url.href))
            }
            this.addRequest(FoundRequest.requestParamFactory(`${this.site_url.href}`, "GET", "",{},"initial",this.site_url.href))
            if (initializeWithBase){

                //console.log("Adding / to requests found");
                //this.addRequest(BASE_SITE,"GET","","initial","");
            }
        }
    }

    loadDataFromJSON() {
        let json_fn = path.join(this.base_appdir, "request_data.json");

        if (fs.existsSync(json_fn)) {
            console.log(" ************************* LOADING INCOMING ******************************");
            let jstrdata = fs.readFileSync(json_fn);
            let jdata = JSON.parse(jstrdata);
            this.inputSet = new Set(jdata["inputSet"]);
            let temprf = jdata["requestsFound"];
            let itemcnt =0;
            for (let key of Object.keys(temprf)){
                itemcnt++;

                let req = temprf[key];
                this.currentURLRound = Math.min(this.currentURLRound, req["attempts"]);

                this.requestsFound[key] = Object.assign(new FoundRequest(), req);
                console.log(this.requestsFound[key].toString());
                // if (!("id" in this.requestsFound[key])){
                //     this.requestsFound[key]["id"] = itemcnt;
                // }

                //this.requestsFound[key]["attempts"] = req["attempts"];
            }
            return true
            //console.log(requestsFound);
        }
        console.log("***************** No saved data found **************************");
        return false;

    }

    setIgnoreValues(exclusions){
        if (isDefined(exclusions)){
            this.ignoreValues = new Set(exclusions);
        } else {
            this.ignoreValues = new Set();
        }
    }

    setUrlUniqueIfValueUnique(inclusions){
        if (isDefined(inclusions)){
            this.urlUniqueIfValueUnique = new Set(inclusions);
        } else {
            this.urlUniqueIfValueUnique = new Set();
        }
    }

    resetRequestsAttempts(key){
        console.log(`Trying to reset for ${key}`);
        this.requestsFound[key]["attempts"] = this.currentURLRound - 1;
        console.log(`RESET attempts to ${this.requestsFound[key]["attempts"]} for ${key}`)
    }

    getRequestInfo(){
        let outstr = "";
        for (let value of Object.values(this.requestsFound)){
            outstr += `\x1b[38;5;28m${value.url()}, \x1b[38;5;11m${value.attempts}\x1b[0m\n`
        }
        return outstr;
    }

    usingFuzzingDir(){
        this.usingFuzzingDir = true;
    }

    fuzzyValueMatch(soughtValue, testValues){
        let fuzset = fuzzySet([...testValues]);
        let results = fuzset.get(soughtValue,false, this.minFuzzyScore);
        if (results === false){
            return false;
        } else {
            //console.log("Fuzzy Match = ", results[0][0]);
            return true;
        }
    }

    /**
     * Looks for an equivalnt match where fullMatchEquiv of the params or more match one another in the query strings.
     * @param soughtParams
     * @param testParams
     * @param fullMatchEquiv the percent of key/values in the query string that are equivalent to an exact match
     * @returns {boolean}
     */
    equivParameters(soughtParams, testParams, fullMatchEquiv){
        // if target has no query params
        if (testParams.length === 0){
            return false;
        }
        let paramValueMatchCnt=0;
        // excluded
        //0.3533549273542278&_=1617038119579
        //0.8389814703576484&_=1617038119586
        //0.5336236531483045
        let timeVarRegex = /[0-9.]+[0-9]{6,50}/; // e.g., 0.3533549273542278
        // All keys must be the same for a match
        for (let [skey,svalues] of Object.entries(soughtParams)){
            // add as a match when a variable matches the format for timestamp nanoseconds
            // this might be too lax, should maybe find match for both
            if (timeVarRegex.exec(skey)){
                paramValueMatchCnt++;
                continue;
            }
            if (skey in testParams){
                if (this.ignoreValues.has(skey)){
                    paramValueMatchCnt++;
                } else {
                    for (const svalue of svalues.values()){
                        if (testParams[skey].has(svalue)){
                            paramValueMatchCnt++;
                            break;
                        } else {
                            if (this.urlUniqueIfValueUnique.has(skey)){
                                return false;
                            }
                            if (svalue.length > 5 && this.fuzzyValueMatch(svalue, testParams[skey])){
                                paramValueMatchCnt++;
                                break;
                            }
                        }
                    }
                }
            // } else {
            //     return false;
            }
        }
        //console.log(`Equiv found ${paramValueMatchCnt} of ${fullMatchEquiv} ${(paramValueMatchCnt >= fullMatchEquiv)}`);
        return (paramValueMatchCnt >= fullMatchEquiv);

    }


    keyMatch(soughtParams, testParams){

        if (Object.keys(soughtParams).length !== Object.keys(testParams).length){
            return false;
        }

        for (let param of Object.keys(soughtParams)){
            // want to disable keyMatch equivalence when the key value is required
            if (this.urlUniqueIfValueUnique.has(param)){
                return false;
            }
            if (!(param in testParams)){
                return false;
            }
        }

        return true;
    }

    /**
     * Search the requestData to determine whether a sufficient match exists between urls.
     * An equivalent querystring matches for 100% of the keys and 75% of the values.
     * @param soughtRequest {FoundRequest} - the Request object that contains the query string in question
     * @returns {boolean}
     */
    containsEquivURL(soughtRequest){

        let soughtURL = new URL(soughtRequest.url());
        let queryString = soughtURL.search.substring(1);
        let postData = soughtRequest.postData();
        let soughtParamsArr = soughtRequest.getAllParams();
        // let trimmedSoughtParams = [];
        // for (let sp of )
        //soughtParamsArr = [...new Set(soughtParamsArr)];
        let nbrParams = Object.keys(soughtParamsArr).length;
        // if nbrParams*matchPercent is more than nbrParams-1, it's requires a 100% parameter match
        let fullMatchEquiv = nbrParams * this.fuzzyMatchEquivPercent;
        let soughtPathname = soughtRequest.getPathname();

        let keyMatch = 0;

        for (let savedReq of Object.values(this.requestsFound)){
            let prevURL = savedReq.getURL();
            let prevPathname = savedReq.getPathname();

            if (prevURL.href === soughtURL.href && savedReq.postData === soughtRequest.postData() && savedReq.hash === soughtURL.hash){
                return true;
            }

            if (prevPathname === soughtPathname && (!soughtURL.hash || savedReq.hash === soughtURL.hash)){

                if (postData.startsWith("<?xml")){
                    let testPostData = savedReq.postData();
                    let re = new RegExp(/<soap:Body>(.*)<\/soap:Body>/);
                    if (re.test(postData) && re.test(testPostData)){
                        let pd_match = re.exec(postData)
                        let test_pd_match = re.exec(testPostData);

                        let matchVal = this.fuzzyValueMatch(pd_match[1], test_pd_match[1])
                        return matchVal;
                    }
                }

                let testParamsArr = savedReq.getAllParams();

                if (this.equivParameters(soughtParamsArr, testParamsArr , fullMatchEquiv)){
                    return true;
                } else if ((nbrParams-1) < fullMatchEquiv){
                    // for situations where the reduced number of parameters forces 100%, also do a keyMatch
                    if (this.keyMatch(soughtParamsArr, testParamsArr) &&  this.fuzzyMatchEquivPercent < .99){
                        keyMatch++;
                    }
                }
            } else {
                // if (savedReq.hash !== soughtURL.hash){
                //     console.log(`Pathnames => ${prevPathname} == ${soughtPathname} hashes=> ${savedReq.hash}\n${soughtURL.hash}`)
                // } else {
                //     console.log(`Pathnames => ${prevPathname} == ${soughtPathname}`)
                // }
            }
        }
        /*since the */
        return (nbrParams <= 3 && keyMatch >= this.maxKeyMatches);
    }

    getValidURL(urlstr, parenturl) {
        let lowerus = urlstr.toLowerCase();
        if (lowerus.startsWith("javascript")) {
            return "";
        }
        //console.log("\x1b[38;5;3mTESTING", urlstr, "\x1b[0m", ` for parent ${parenturl.origin}`);

        //if (lowerus.search(/.php($|\?)/) > -1 || lowerus.search(/.html($|\?)/) > -1 || lowerus.search("#") > -1 ) {

        if (lowerus.startsWith(parenturl.origin)) {
            //console.log("\x1b[38;5;3mValidated ", urlstr, "\x1b[0m");
            return urlstr;
        } else if (lowerus.startsWith("http")) {
            //console.log("\x1b[38;5;3mFAILED TO validate ", urlstr, "\x1b[0m");
            return "";
        }

        if (lowerus.startsWith("/")) { // absolute path
            console.log("\x1b[38;5;3mValidated from /", parenturl.origin + urlstr, "\x1b[0m");
            return parenturl.origin + urlstr;
        } else { // relative path
            let lastPathOut = ""
            try {
                //console.log("\x1b[38;5;3mLast choice trying to add origin and pathname to lowerus ", parenturl.origin + path.dirname(parenturl.pathname) + urlstr, "\x1b[0m");
                lastPathOut = parenturl.origin + path.dirname(parenturl.pathname) + urlstr;
            } catch (Exception) {
                //console.log("\x1b[38;5;3mInvalid path WITH Last choice trying to add origin and pathname to lowerus parenturl=", parenturl, "urlstr=", urlstr, "\x1b[0m");
                return ""
            }
            return parenturl.origin + path.dirname(parenturl.pathname) + urlstr;
        }

    }

    addValidURLS(links, parenturl, origin){
        let requestsAdded = 0;
        for (let link of links){
            let validURLStr = this.getValidURL(link, parenturl);

            if (validURLStr.length > 0){
                let foundRequest = FoundRequest.requestParamFactory(validURLStr, "GET", "",{},origin,this.site_url.href);

                if (!this.containsEquivURL(foundRequest)){
                    foundRequest.from = origin;
                    let addResult = this.addRequest(foundRequest);
                    if (addResult){
                        requestsAdded++;
                        console.log(`\x1b[38;5;2m[WC] ADDED ${foundRequest.toString()} ${ENDCOLOR}`);
                    }
                }
            }
        }
        return requestsAdded;
    }

    interestingURL(url) {
        if (url.pathname.endsWith(".php") || url.pathname.search(/php\?/) > -1) {
            return true;
        } else if (url.pathname.endsWith(".css")){
            return false
        }
        return false;

    }

    /**
     *
     * @param foundRequest {FoundRequest}
     * @returns {number}
     */
    addInterestingRequest(foundRequest){
        let requestsAdded =0 ;
        let tempURL = foundRequest.url();
        if (tempURL.endsWith('.css') || tempURL.endsWith('.jpg') || tempURL.endsWith('.gif') || tempURL.endsWith('.png') || tempURL.endsWith(".js")  || tempURL.endsWith(".ico")){
            return requestsAdded;
        }
        if (this.containsEquivURL(foundRequest) ) { //|| this.containsMaxNbrSameKeys(tempURL)
            //do nothing for now
            //console.log("[WC] Could have been added, ",req.url(), req.method(), req.postData());
        } else {

            let wasAdded = this.addRequest(foundRequest);
            if (wasAdded){
                let postlen ="";
                if (isDefined(foundRequest.postData())){
                    postlen = foundRequest.postData().length
                }
                requestsAdded++;
                console.log(`${GREEN}[WC] ADDED -- ${foundRequest.toString()} ${ENDCOLOR}`);
            }
        }
        return requestsAdded;
    }
    nextRequestId(){
        return Object.keys(this.requestsFound).length + 1
    }

    //addRequest(urlstr, method, postData, headers, from="interceptedRequest", cookieData="") {
    /**
     * Adds the supplied request to the list of requests
     * @param fRequest:FoundRequest
     * @returns {boolean}
     */
    addRequest(fRequest) {

        // let requestInfo = {
        //     id: this.nextRequestId(), url:urlstr, method: method, postData: postData,
        //     attempts:0, from:from, cookieData:cookieData,
        //     usedFuzzingDir: this.usingFuzzingDir,
        //     content_type: content_type,
        //     processed:0
        // };
        //console.log(requestInfo);
        let reqkey = fRequest.getRequestKey();

        if (reqkey in this.requestsFound) {
            return false;
        } else {
            fRequest.setId(this.nextRequestId());
            this.collectedURL += 1;
            this.requestsFound[fRequest.getRequestKey()] = fRequest;
            return true;
        }

    }

    addQueryParam(key, value){
        var keycnt = 0;

        this.inputSet.forEach(function(setkey){
            if (setkey.startsWith(key+"=")){
                keycnt++;
            }
        });
        if (keycnt < 4){
            if (value.search(/[Q2][Q2]+/) > -1){
                value = value.substring(0,1);
            }
            if (this.inputSet.has(`${key}=`) && value.length > 0){
                this.inputSet.delete(`${key}=`);
            }
            if (value.length ===0 && keycnt ===0 || value.length > 0){
                this.inputSet.add(`${key}=${value}`);
            }
        }

    }
    numInputsFound(){
        return this.inputSet.size;
    }
    hasRequests(){
        return Object.keys(this.requestsFound).length === 0
    }
    numRequestsFound(){
        return Object.keys(this.requestsFound).length
    }
    getNextRequest() {

        // console.log(inputSet);
        while (this.currentURLRound <= MAX_NUM_ROUNDS) {
            let randomKeys = Object.keys(this.requestsFound);
            for (const key of randomKeys) {
                let req = this.requestsFound[key];
                if (req["attempts"] < this.currentURLRound) {
                    console.log(`\x1b[38;5;4mStarting exploration of:\n\t${key}\n\t${req.url()}\x1b[0m`);
                    req["attempts"] += 1;
                    this.save();
                    req["key"] = key;
                    this.currentRequest = req;
                    return req;
                }
            }

            this.currentURLRound++;

            console.log("CURRENT ROUND VALUE HAS INCREASED TO ", this.currentURLRound);
        }
        return null;
    }

    save() {
        //await exerciseTarget(page, new URL(key));
        let randomKeys = Object.keys(this.requestsFound);
        for (const key of randomKeys) {
            let req = this.requestsFound[key];
            if (req["_method"] === "POST"){
                req["response_status"] = 200;
            }
        }

        let jdata = JSON.stringify({requestsFound: this.requestsFound, inputSet: Array.from(this.inputSet)});
        fs.writeFileSync(path.join(this.base_appdir, "request_data.json"), jdata);
    }
}

// var inputSet = new Set();
//var formsData = {}; // {<method+url>:{action:"", method:"", elems:{<parameter>:""} }

process.on('uncaughtException', function(err) {
    console.log('Caught exception: ' + err);
    console.log(err.stack);
});


/**
 * Attempts to parse the text, if there's a syntax error then returns false
 * @param response
 * @param responseText
 * @returns {boolean}
 */
function isInteractivePage(response, responseText){

    try {
        JSON.parse(responseText);
        return false;
    } catch (SyntaxException){
        //check out other types
    }

    if (response.headers().hasOwnProperty("content-type")){

        let contentType = response.headers()['content-type'];

        if (contentType === "application/javascript" || contentType === "text/css" || contentType.startsWith("image/") || contentType === "application/json"){
            console.log("Content type ${contentType} is considered non-interactive (e.g., JavaScript, CSS, json, or image/* )")
            return false;
        }
    }

    console.log(responseText.slice(0,500))
    if (responseText.search(/<body[ >]/) > -1 || responseText.search(/<form[ >]/) > -1){
        return true;
    } else {
        console.log("\n\nNO HTML tag FOUND anywhere\n\n")
        return false;
    }

}
function logdata(msg){
    console.log("\x1b[38;5;6m[DATA] ", msg, "\x1b[0m")
}
function isDefined(val) {
    return !(typeof val === 'undefined' || val === null);
}

/**
 *
 *
 *
 *
 */
class RequestExplorer {

    constructor(appData, workernum, base_appdir, currentRequest ) {
        this.appData= appData;
        this.base_appdir = base_appdir;
        this.loopcnt=0;
        this.cookies = [];
        this.bearer = "";
        this.isLoading = false;
        this.reinitPage= false;
        this.loadedURLs = [];
        if (appData.numRequestsFound() > 0){
            this.currentRequestKey = currentRequest.getRequestKey();
            this.url = currentRequest.getURL();
            this.method = currentRequest.method();
            this.postData = currentRequest.postData()
            this.cookieData = currentRequest.cookieData();


            if (this.appData.requestsFound.hasOwnProperty(this.currentRequestKey))
                this.appData.requestsFound[this.currentRequestKey]["processed"]++;
            else{
                // this.appData.requestsFound[this.currentRequestKey] = currentRequest;
                // this.appData.requestsFound[this.currentRequestKey]["processed"] = 1;
                console.log(`\x1b[31mWE SHOULD ME ADDING currentRequest to requestsFound ${this.currentRequestKey}\x1b[0m`);
            }
        } else {
            this.currentRequestKey = "GET";
            this.url = "";
            this.method = "GET";
            this.postData = "";
            this.cookieData = "";
        }
        this.requestsAdded = 0;
        this.timeoutLoops = 45;
        this.timeoutValue = 3;
        this.workernum = workernum;
        this.gremCounter = {};
        this.shownMessages = {};
        this.maxLevel = 10;
        this.browser;
        this.page;
        this.gremlins_error = false;
        this.lamehord_done = false
        this.getConfigData();
    }

    getConfigData(){
        let json_fn = path.join(this.base_appdir,"witcher_config.json");
        if (fs.existsSync(json_fn)){
            let jstrdata = fs.readFileSync(json_fn);
            this.loginData = JSON.parse(jstrdata)["request_crawler"];
            this.appData.setIgnoreValues(this.loginData["ignoreValues"]);
            this.appData.setUrlUniqueIfValueUnique(this.loginData["urlUniqueIfValueUnique"]);
        }
    }

    async searchForURLSelector(page, tag, attribute){
        let elements = [];

        try {
            const links = await page.$$(tag);
            for (var i=0; i < links.length; i++) {
                let valueHandle = await links[i].getProperty(attribute);
                let val = await valueHandle.jsonValue();
                if (isDefined(val)){
                    elements.push(val);
                }
            }

        } catch (e){
            console.log("[WC] error encountered while trying to search for tag");
        }
        return elements;
    }


    async getAttribute(node, attribute, defaultval=""){
        let valueHandle = await node.getProperty(attribute);
        let val = await valueHandle.jsonValue();
        if (isDefined(val)){
            //logdata(attribute + val);
            //elements.push(val);
            return val;
        }
        return defaultval;
    }

    async searchForInputs(node){
        let requestsAdded = 0;
        let requestInfo = {}; //{action:"", method:"", elems:{"attributename":"value"}
        let nodeaction = await this.getAttribute(node, "action");
        let method = await this.getAttribute(node, "method");

        let foundRequest = FoundRequest.requestParamFactory(nodeaction, method, "",{},"PageForms",this.appData.site_url.href);

        const buttontags = await node.$$('button');
        let formdata = await this.searchTags(buttontags);

        const inputtags = await node.$$('input');
        formdata += await this.searchTags(inputtags);

        const selectags = await node.$$('select');
        formdata += await this.searchTags(selectags);

        const textareatags = await node.$$('textarea');
        formdata += await this.searchTags(textareatags);
        if (formdata.length === 0){
            return requestsAdded;
        }

        foundRequest.addParams(formdata);

        //console.log("[WC] ",requestInfo);
        if (foundRequest.isSaveable() ){ // && this.appData.containsMaxNbrSameKeys(tempurl) === false

            if (this.appData.containsEquivURL(foundRequest) ) {
                // do nothing yet
                //console.log("[WC] Could have been added, ",requestInfo["url"], requestInfo["method"], requestInfo["postData"]);
            } else {
                if (foundRequest.urlstr().startsWith(`${this.appData.site_url.origin}`)){

                    foundRequest.from = "PageForms";
                    foundRequest.cleanURLParamRepeats()
                    foundRequest.cleanPostDataRepeats()
                    let wasAdded = this.appData.addRequest(foundRequest);
                    if (wasAdded){
                        requestsAdded++;
                        if (foundRequest.postData()){
                            console.log(`\x1b[38;5;2m[WC] ADDED ${foundRequest.toString()} postData=${foundRequest.postData()} ${ENDCOLOR}`);
                        } else {
                            console.log(`\x1b[38;5;2m[WC] ADDED ${foundRequest.toString()} \n ${ENDCOLOR}`);
                        }

                    }
                } else {
                    console.log(`\x1b[38;5;3m[WC] IGNORED b/c not correct ${foundRequest.toString()} does not start with ${this.appData.site_url.origin} -- ${ENDCOLOR}`);
                }

            }
        }

        return requestsAdded;
    }

    async searchTags(tags) {
        let formdata = "";
        for (let j = 0; j < tags.length; j++) {
            let tagname = encodeURIComponent(await this.getAttribute(tags[j], "name"));
            let tagval = encodeURIComponent(await this.getAttribute(tags[j], "value"));
            formdata += `${tagname}=${tagval}&`;
            this.appData.addQueryParam(tagname, tagval);
        }
        return formdata;
    }

    async addURLsFromPage(page, parenturl){
        let requestsAdded = 0;
        try {
            // these are always GETs
            const anchorlinks = await this.searchForURLSelector(page, 'a', 'href');
            //console.log("LINKS ", anchorlinks);
            requestsAdded += this.appData.addValidURLS(anchorlinks, parenturl, "OnPageAnchor");
            const iframelinks = await this.searchForURLSelector(page, 'iframe', 'src');
            requestsAdded += this.appData.addValidURLS(iframelinks, parenturl, "OnPageIFrame");

        } catch (ex){
            console.log(`[WC] Error ${ex}`)
        }
        return requestsAdded;
    }

    async addFormData(page) {
        let requestsAdded = 0;
        try{
            const forms = await page.$$('form').catch(reason => {
                console.log(`received error in page. ${reason} `);
            });
            if (isDefined(forms)){
                for (let i = 0; i < forms.length; i++) {
                    let faction = await this.getAttribute(forms[i], "action", "");
                    let fmethod = await this.getAttribute(forms[i], "method", "GET");
                    console.log("[WC] second form ACTION=", faction, fmethod);
                    requestsAdded += await this.searchForInputs(forms[i]);
                }
            }

        } catch (ex){
            console.log(`[WC] Error ${ex}`)
        }
        return requestsAdded;
    }

    async addDataFromBrowser(page, parenturl){
//        console.log("Starting formdatafrompage");
        let requestsAdded = 0;
        let childFrames = this.page.mainFrame().childFrames();

        if (typeof childFrames !== 'undefined' && childFrames.length > 0){
            for (const frame of this.page.mainFrame().childFrames()){
                console.log("ADDING form data from FRAMES.")
                requestsAdded += await this.addFormData(page);
                requestsAdded += await this.addURLsFromPage(page);
            }

        } else {
            requestsAdded = await this.addFormData(page);
            requestsAdded += await this.addURLsFromPage(page, parenturl);
        }

        //const bodynode = await page.$('html');
        //requestsAdded += await this.searchForInputs(bodynode);
        return requestsAdded;
    }


    async addCodeExercisersToPage(gremlinsHaveStarted){
        if (gremlinsHaveStarted){
            console.log("[WC] Using alternative launcher (horde only)");
            await this.page.evaluate(()=>{
                function randr(a) {
                    return function() {
                        var t = a += 0x6D2B79F5;
                        t = Math.imul(t ^ t >>> 15, t | 1);
                        t ^= t + Math.imul(t ^ t >>> 7, t | 61);
                        return ((t ^ t >>> 14) >>> 0) / 4294967296;
                    }
                }
                function sleep(ms) {
                    return new Promise(resolve => setTimeout(resolve, ms));
                }
                async function triggerHorde(){
                    $("select").trigger("change");
                    await sleep(100);
                    $("select").prop("selectedIndex",1)

                }
                async function checkHordeLoad(){
                    if (typeof window.gremlins === 'undefined') {
                        console.log("cannot find gremlins, attempting to load on the fly");
                        (function (d, script) {
                            script = d.createElement('script');
                            script.type = 'text/javascript';
                            script.async = true;
                            script.onload = function () {
                                // remote script has loaded
                            };
                            script.src = 'https://trickel.com/gremlins.min.js';
                            //script.src = 'https://unpkg.com/gremlins.js';
                            d.getElementsByTagName('head')[0].appendChild(script);
                        }(document));
                    }
                }
                async function repeativeHorde(){


                    let pforms = document.getElementsByTagName("form");

                    for(let i = 0; i < pforms.length; i++) {
                        let f = pforms[i];
                        if(typeof f.submit === 'function') {
                            console.log("[WC] repeativeHord: submitting form")
                            f.submit();
                        } else if (typeof f.submit === 'undefined') {
                            console.log("[WC] repeativeHord: The method submit of ", f, "is undefined");
                        } else {
                            //console.log("[WC] repeativeHord: It's neither undefined nor a function. It's a " + typeof f.submit, f);
                        }

                    }

                }
                async function coolHorde(){

                    console.log("[WC] Setting fillTextElement to new function");
                    // ff.prototype.fillTextElement = function(element) {
                    //     console.log("[WC] happily filling in a form");
                    //     const character = randomizer.character();
                    //     const newValue = element.value + character;
                    //     triggerSimulatedOnChange(element, newValue, window.HTMLInputElement.prototype);
                    //     return character;
                    // };

                    var noChance = new gremlins.Chance();
                    //noChance.prototype.bool = function(options) {return true;};
                    noChance.character = function(options) {
                        if (options != null){
                            return "2";
                        } else {
                            let rnd =  Math.random()
                            if (rnd > 0.7){
                                return "Witcher";
                            } else if (rnd > 0.3){
                                return "127.0.0.1";
                            } else {
                                return "2"
                            }
                        }
                    };
                    console.log("[WC] UNLEASHING Reloaded Horde!!!");

                    let ff = window.gremlins.species.formFiller(console.log, noChance, window);
                    const distributionStrategy = gremlins.strategies.distribution({
                        distribution: [0.53, 0.23, 0.23,0.01], // the first three gremlins have more chances to be executed than the last
                        delay: 20,
                    });
                    for (let i =0; i < 5; i ++){
                        await gremlins.createHorde({
                            species: [gremlins.species.clicker(),ff,gremlins.species.typer(), gremlins.species.scroller()],
                            mogwais: [gremlins.mogwais.alert(),gremlins.mogwais.gizmo()],
                            strategies: [distributionStrategy],
                            randomizer: noChance
                        }).unleash();

                    }

                    console.log("[WC] Done with the unleashing");

                    clearInterval(repeativeHorde);
                    clearInterval(triggerHorde);
                }

                console.log("[WC] setting timeout for coolhorde")
                setTimeout(checkHordeLoad, 3500);
                setTimeout(coolHorde, 4000);
                setTimeout(function(){setInterval(repeativeHorde, 5000)}, 20000);
                setTimeout(function(){setInterval(triggerHorde, 500)}, 5000);
            });
        } else {
            // ##############################################################################
            //                         START Injected Exercise Code
            // ##############################################################################
            console.log("[WC] running code injection evaluation");
            await this.page.evaluate(()=>{
                const CLICK_ELE_SELECTOR = "div,li,span,input,p,button";
                //const CLICK_ELE_SELECTOR = "button";
                var usedText = new Set();
                const STARTPAGE = window.location.href;
                const MAX_LEVEL = 10;
                function shuffle(array) {
                    var currentIndex = array.length, temporaryValue, randomIndex;

                    // While there remain elements to shuffle...
                    while (0 !== currentIndex) {

                        // Pick a remaining element...
                        randomIndex = Math.floor(Math.random() * currentIndex);
                        currentIndex -= 1;

                        // And swap it with the current element.
                        temporaryValue = array[currentIndex];
                        array[currentIndex] = array[randomIndex];
                        array[randomIndex] = temporaryValue;
                    }

                    return array;
                }
                function sleep(ms) {
                    return new Promise(resolve => setTimeout(resolve, ms));
                }
                function getChangedDOM(domBefore, domAfter){
                    let changedDOM = {};
                    let index = 0;
                    for (let dbIndex of Object.keys(domBefore)){
                        let db = domBefore[dbIndex];
                        let found = false;
                        for (let da of domAfter){
                            if (db === da){
                                found = true;
                                break;
                            }
                        }
                        if (!found){
                            changedDOM[index] = db;
                            index++;
                        }

                    }
                    // if domAfter larger, then add entries if not in domBefore
                    for (let daIndex=Object.keys(domBefore).length;daIndex < Object.keys(domAfter).length; daIndex++){
                        let da = domAfter[daIndex];
                        let found = false;
                        for (let db of domBefore){
                            if (db === da){
                                found = true;
                                break;
                            }
                        }
                        if (!found){
                            changedDOM[index] = da;
                            index++;
                        }
                    }
                    return changedDOM;
                }
                function indent(cnt){
                    let out = ""
                    for (let x =0;x<cnt;x++){
                        out += "  ";
                    }
                    return out;
                }
                async function clickSpam(elements, level=0, parentClicks=[]){
                    if (level >= MAX_LEVEL){
                        console.log(`[WC] ${indent(level)} L${level} too high, skipping`);
                        return;
                    }
                    //let randomArr = shuffle(Array.from(Object.values(elements)));
                    let randomArr = Array.from(Object.values(elements));

                    let mouseEvents = ["click","mousedown","mouseup"];
                    let eleIndex = 0;
                    let startingURL = location.href;
                    let startingDOM = document.querySelectorAll(CLICK_ELE_SELECTOR);

                    console.log(`[WC] ${indent(level)} L${level} Starting DOM selected=${startingDOM.length} Nodes toExplore=${randomArr.length} `);
                    //console.log(`[WC] ${indent(level)} L${level} number of elements initially `, startingDOM.length);
                    // startingDOM.filter(function (e) {
                    //     return e.hasOwnProperty("hasClicker");
                    // });
                    // console.log("[WC] number of elements is now ", startingDOM.length);
                    for (let eleIndex =0; eleIndex < randomArr.length; eleIndex++){
                        let ele = randomArr[eleIndex];
                        //console.log(`[WC] ${indent(level)} L${level} attempt to click on  ${ele.textContent}`);
                        try {
                            if (ele.href != null){
                                console.log(`FOUND URL of ${ele.href}`)
                                if (ele.href.indexOf("support.dlink.com") !== -1){
                                    console.log(`[WC] IGNORING url of FOUND URL of ${ele.href}`)
                                    continue;
                                }
                            }
                            let searchText="";
                            if (ele.outerHTML != null) {
                                searchText += ele.outerHTML;
                            }
                            if (ele.innerHTML != null) {
                                searchText += ele.innerHTML;
                            }
                            if (ele.textContent != null) {
                                searchText += ele.textContent;
                            }
                            console.log(`ele id=${ele.id} name=${ele.name}`)
                            if (usedText.has(ele.textContent) ){
                                return;
                            }
                            let pos = searchText.indexOf("Logout");
                            if (pos > -1 ){
                                console.log("[WC] SKIPPING B/C IT's a logout, ", ele.textContent);
                                continue;
                            }

                            try {
                                ele.disabled = false;
                            } catch (ex){
                                //pass
                                console.log("[WC] ERROR WITH THE ELEMENTS CLICKING", e);
                            }

                            try {

                                function triggerMouseEvent (node, eventType) {
                                    console.log("usedText=", usedText, "node=", node);
                                    // if
                                    // if (node.textContent.indexOf("Order History") === -1 && node.textContent.indexOf("account_circle") === -1 && node.textContent.indexOf("check_circle_outline") === -1 ){
                                    //     return;
                                    // }
                                    if (level > 1){
                                        console.log(`[WC] ${indent(level)} L${level} triggering on ${node.textContent}`)
                                    }
                                    console.log("usedText=", usedText, "node=", node);
                                    // if (usedText.has(node.textContent) ){
                                    //     return;
                                    // }

                                    usedText.add(node.textContent);
                                    let clickEvent = document.createEvent ('MouseEvents');
                                    clickEvent.initEvent (eventType, true, true);
                                    node.dispatchEvent (clickEvent);
                                    console.log(`[WC] DONE-TRIGGERED ${indent(level)} L${level} triggering on ${clickEvent} ${node} ${node.id} ${node.name}`)
                                }
                                for (let ev of mouseEvents){
                                    console.log("mouse event = ", ev);
                                    let temp = window.location.href;
                                    triggerMouseEvent (ele, ev);
                                    await sleep(50);
                                    if (temp !== window.location.href){
                                        console.log("[WC] RESET EM, ", temp, window.location.href);
                                        // this sends it back out to puppeteer
                                        console.log(`[WC-URL]${window.location.href}`);
                                        await window.location.replace(temp);
                                        for (let pc of parentClicks){
                                            //console.log (`[WC] ${indent(level)} retriggering ${pc.textContent}`);
                                            triggerMouseEvent(pc, "click");
                                        }
                                    }
                                    let curDOM = document.querySelectorAll(CLICK_ELE_SELECTOR);
                                    if (Object.keys(curDOM).length !== Object.keys(startingDOM).length && Object.keys(curDOM).length > 0){
                                        var changedDOM = getChangedDOM(startingDOM, curDOM);
                                        console.log(`[WC] ${indent(level)} ${level} starting len = ${Object.keys(elements).length} cur len = ${Object.keys(curDOM).length} changed len=${Object.keys(changedDOM).length}`);
                                        /*for (let cd of Object.keys(changedDOM)){
                                            console.log(`[WC] ${indent(level+1)} changedDOM #${cd} ${changedDOM[cd].textContent}`);
                                        }*/
                                        parentClicks.push(ele);
                                        console.log(`[WC] ${indent(level)} L${level} recursing into the next level of ${ele.textContent}`);
                                        await clickSpam(changedDOM, level+1, parentClicks);
                                        // this resets DOM??
                                        location.href = startingURL;
                                        //startingDOM = document.querySelectorAll("div,li,span,a,input,p,button");

                                        // can break by assuming that DOM change means event was heard.
                                        break;
                                    } else {
                                        console.log(`[WC] ${indent(level)} ${level} ${Object.keys(startingDOM).length} ${Object.keys(curDOM).length}`)
                                    }

                                }
                                await sleep(50);


                            } catch(e2){
                                console.log("[WC] NO CLICK, ERROR ", e2.message);

                            }

                            // if (typeof ele.click === 'function') {
                            //
                            //     console.log("\tLOG gremlin click all_clicker ", cnt );
                            //     console.log("\tLOG gremlin click all_clicker ", cnt );
                            //     //ele.click();
                            //     //await sleep(100);
                            // } else {
                            //     console.log("\tNO CLICK ");
                            // }

                        } catch (e){
                            console.log("[WC] ERROR WITH THE ELEMENTS CLICKING", e.message);
                        }

                    }

                }
                async function repeativeHorde(){
                    let pforms = document.getElementsByTagName("form");
                    for(let i = 0; i < pforms.length; i++) {
                        let f = pforms[i];
                        if(typeof f.submit === 'function') {
                            console.log("[WC] repeativeHord: submitting form")
                            f.submit();
                        } else if (typeof f.submit === 'undefined') {
                            //console.log("[WC] repeativeHord: The method submit of ", f, "is undefined");
                        } else {
                            //console.log("[WC] repeativeHord: It's neither undefined nor a function. It's a " + typeof f.submit, f);
                        }

                    }

                }
                async function lameHorde(){

                    console.log("Searching and clicking.");
                    window.alert = function(message) {/*console.log(`Intercepted alert with '${message}' `)*/};

                    let all_elements = document.querySelectorAll( CLICK_ELE_SELECTOR);
                    console.log(`\t FOUND ${all_elements.length} elements to attempt to click in main `);
                    for (let ele of document.querySelectorAll("iframe")){
                        all_elements = [...all_elements, ...ele.contentWindow.document.querySelectorAll(CLICK_ELE_SELECTOR) ];
                    }
                    console.log(`\t FOUND ${all_elements.length} elements to attempt to click in main and frames`);
                    function hashChangeEncountered(){
                        alert('got hashchange');
                    }
                    window.addEventListener("hashchange", hashChangeEncountered);
                    var filter   = Array.prototype.filter;
                    var clickableElements = filter.call( all_elements, function( node ) {
                        if (node.hasOwnProperty("href") && node.href.startsWith("http")){
                            return false;
                        }
                        return node.hasOwnProperty('hasClicker');
                    });
                    console.log("[WC] clicky  DOM elements count = ", clickableElements.length);

                    //await clickSpam(clickableElements);
                    await clickSpam(all_elements);

                    let pforms = document.getElementsByTagName("form");
                    for(let i = 0; i < pforms.length; i++) {
                        let frm = pforms[i];
                        if(typeof frm.submit === 'function') {
                            console.log("Submitting a form");
                            frm.submit();
                        } else if (typeof frm.submit === 'undefined') {
                            console.log("[WC] lameHorde: The method submit of ", frm, "is undefined");
                        } else {
                            //console.log("[WC] lameHorde: It's neither undefined nor a function. It's a " + typeof frm.submit, frm);
                        }

                    }

                    //
                    console.log(`[WC] lamehorde is done.`);

                }
                function randr(a) {
                    return function() {
                        var t = a += 0x6D2B79F5;
                        t = Math.imul(t ^ t >>> 15, t | 1);
                        t ^= t + Math.imul(t ^ t >>> 7, t | 61);
                        return ((t ^ t >>> 14) >>> 0) / 4294967296;
                    }
                }
                async function checkHordeLoad(){
                    if (typeof window.gremlins === 'undefined') {
                        console.log("cannot find gremlins, attempting to load on the fly");
                        (function (d, script) {
                            script = d.createElement('script');
                            script.type = 'text/javascript';
                            script.async = true;
                            script.onload = function () {
                                // remote script has loaded
                            };
                            script.src = 'https://trickel.com/gremlins.min.js';
                            //script.src = 'https://unpkg.com/gremlins.js';
                            d.getElementsByTagName('head')[0].appendChild(script);
                        }(document));
                    }
                }
                async function triggerHorde(){
                    $("select").trigger("change");
                    await sleep(100);
                    $("select").prop("selectedIndex",1)

                }
                async function coolHorde(){
                    var noChance = new gremlins.Chance();
                    //noChance.prototype.bool = function(options) {return true;};
                    noChance.character = function(options) {
                        if (options != null){
                            return "2";
                        } else {
                            let rnd =  Math.random()
                            if (rnd > 0.7){
                                return "Witcher";
                            } else if (rnd > 0.3){
                                return "127.0.0.1";
                            } else {
                                return "2"
                            }
                        }
                    };
                    console.log("[WC] UNLEASHING Reloaded Horde!!!");

                    let ff = window.gremlins.species.formFiller(console.log, noChance, window);
                    const distributionStrategy = gremlins.strategies.distribution({
                        distribution: [0.5, 0.2, 0.2,0.1], // the first three gremlins have more chances to be executed than the last
                        delay: 20,
                    });
                    await gremlins.createHorde({
                        species: [gremlins.species.clicker(),ff,gremlins.species.typer(), gremlins.species.scroller()],
                        mogwais: [gremlins.mogwais.alert(),gremlins.mogwais.gizmo()],
                        strategies: [distributionStrategy],
                        randomizer: noChance
                    }).unleash({nb:100000});



                    clearInterval(repeativeHorde);
                    clearInterval(triggerHorde);

                    clearInterval(repeativeHorde);
                }

                console.log("[WC] setting timeout for lamehorde")
                setTimeout(lameHorde, 2000);
                setTimeout(function(){setInterval(repeativeHorde, 150)}, 3000);
                setTimeout(function(){setInterval(triggerHorde, 500)}, 5000);
                console.log("[WC] setting timeout for coolhorde")
                setTimeout(checkHordeLoad, 19000);
                setTimeout(coolHorde, 20000);

                function hc(){
                    console.log(`[WC] Detected HASH CHANGE, replacing ${window.location.href} with ${STARTPAGE}`);
                    window.location.replace(STARTPAGE);
                }
                window.onhashchange = hc

            });
            // ##############################################################################
            //                         END Injected Exercise Code
            // ##############################################################################
        }
    }

    async exerciseTarget(page){
        this.requestsAdded = 0;
        let errorThrown = false;
        let clearURL = false;

        if (this.url === ""){

            var urlstr = `/login.php`
            if (this.loginData !== undefined && 'form_url' in this.loginData){
                clearURL = true;
                urlstr = await page.url();
                console.log("page.url = ", urlstr );
            } else {
                console.log("pre chosen url string = ", urlstr);
            }

            let foundRequest = FoundRequest.requestParamFactory(urlstr, "GET", "",{}, "LoginPage", this.appData.site_url.href)

            this.url = foundRequest.getURL();

            this.currentRequestKey = foundRequest.getRequestKey();
            this.method = foundRequest.method();

            if (this.appData.containsEquivURL(foundRequest)) {
                // do nothing
            } else {
                foundRequest.from="startup";
                let addresult = this.appData.addRequest(foundRequest);
                if (addresult) {
                    this.appData.requestsFound[this.currentRequestKey]["processed"] = 1;
                } else {
                    console.log(this.appData.requestsFound);
                    console.log(this.currentRequestKey);
                    process.exit(3);
                }
            }
            //console.log("CREATING NEW PAGE for new pagedness");
            //this.page = await this.browser.newPage();
        }

        let url = this.url;
        let shortname = "";
        console.log("\x1b[38;5;5mexerciseTarget, URL = ", url.href, "\x1b[0m");
        if (url.href.indexOf("/") > -1) {
            shortname = path.basename(url.pathname);
        }
        let options = {timeout: 10000, waituntil: "networkidle0"};
        //let options = {timeout: 10000, waituntil: "domcontentloaded"};
        let madeConnection = false;

        for (let i=0;i<3;i++){
            try {
                let response = "";
                this.isLoading = true;

                if (clearURL){
                    response = await page.reload(options);
                    let turl = await page.url();
                    console.log("Reloading page ", turl);
                } else {
                    let request_page =url.origin + url.pathname
                    console.log("GOING TO requested page =", request_page , "hash =", url.hash);
                    //response =
                    //let p1 = page.waitForResponse(url.origin + url.pathname);
                    //let p1 = page.waitForResponse(request => {console.log(`INSIDE request_page= ${request_page} ==> ${request.url()}`);return request.url().startsWith(url.origin);}, {timeout:10000});

                    response = await page.goto(url.href, options);

                    //response = await p1
                    //console.log("DONE WAITING FOR RESPONSE!!!!! ", url)
                    //console.log(test);
                    //response = await page.waitForResponse(() => true, {timeout:10000});
                    // //response = await page.waitForResponse(request => {console.log(`INSIDE requst.url() = ${request.url()}`);return request.url() === url.href;}, {timeout:10000})
                }
                // TODO:  a bug seems to exist when a hash is used in the url, the response will be returned as null from goto
                // This is attempt 1 to resolve, by skipping response actions when resoponse is null
                // This problem appears to be tied to setIncerpetRequest(true)
                // https://github.com/puppeteer/puppeteer/issues/5492

                //response = await page.goto(url.href, options);

                let response_good = await this.checkResponse(response, page.url());

                if (response_good){
                    madeConnection = await this.initpage(page, url);
                }
                break; // connection successful
            } catch (e) {
                console.log(`Error: Browser cannot connect to '${url.href}' RETRYING`);
                console.log(e.stack);
            }
        }
        if (!madeConnection){
            console.log(`Error: LAST ATTEMPT, giving up, browser cannot connect to '${url.href}'`);
            return;
        }

        let lastGT=0, lastGTCnt=0, gremCounterStr="";
        try {
            //console.log("Performing timeout and element search");

            for (var cnt=0; cnt < this.timeoutLoops;cnt++){

                let roundResults = this.getRoundResults();
                if (page.url().indexOf("/") > -1) {
                    shortname = path.basename(page.url());
                }
                let processedCnt = 0;
                if (this.currentRequestKey in this.appData.requestsFound){
                    processedCnt = this.appData.requestsFound[this.currentRequestKey]["processed"];
                }
                if (typeof this.requestsAdded === "string"){
                    this.requestsAdded = parseInt(this.requestsAdded);
                }
                let startingReqAdded = this.requestsAdded;
                this.requestsAdded += await this.addDataFromBrowser(page, url);

                console.log(`\tW#${this.workernum} ${shortname} Count ${cnt} Round ${this.appData.currentURLRound} loopcnt ${processedCnt}, added ${this.requestsAdded} reqs : Inputs: ${roundResults.totalInputs}, (${roundResults.equaltoRequests}/${roundResults.totalRequests}) reqs left to process ${gremCounterStr}`);
                let pinfo = this.browser.process();
                if (isDefined(pinfo) && pinfo.killed){
                    console.log("Breaking out from test loop b/c BROWSER IS DEAD....")
                    break;
                }
                // if new requests added on last passs, then keep going
                if (startingReqAdded < this.requestsAdded){
                    cnt = (cnt > 3) ? cnt-3: 0;
                }

                const now_url = await page.url();
                const this_url = this.url.href
                if (this.reinitPage){
                    madeConnection = await this.initpage(page, url, true);
                    this.reinitPage = false;
                }
                if (now_url !== this_url){
                    console.log(`[WC] Attempting to reload target page b/c browser changed urls ${this_url !== now_url} '${this.url}' != '${now_url}'`)
                    this.isLoading = true;
                    let response = await page.goto(this.url, options);

                    let response_good = await this.checkResponse(response, page.url());

                    if (response_good){
                        madeConnection = await this.initpage(page, url, true);
                    }
                    this.isLoading = false;
                }

                await page.waitFor(this.timeoutValue*1000);
                // eval for iframes, a, forms
                if (this.workernum === 0 && cnt % 3 === 1){
                    //page.screenshot({path: `/p/webcam/screenshot-${this.workernum}-${cnt}.png`, type:"png"}).catch(function(error){console.log("no save")});
                }
                //page.screenshot({path: `/p/tmp/screenshot-${this.workernum}-${cnt}.png`, type:"png"}).catch(function(error){console.log("no save")});
                //console.log("After content scan =>",cnt );

                if (this.hasGremlinResults()) {
                    if (lastGT === this.gremCounter["grandTotal"]){
                        lastGTCnt++;
                    } else {
                        lastGTCnt = 0;
                    }
                    gremCounterStr = `Grems total = ${this.gremCounter["grandTotal"]}`;
                    lastGT = this.gremCounter["grandTotal"];
                    if (lastGTCnt > 3){
                        console.log("Grand Total the same too many times, exiting.");
                        break
                    }
                }
            }
        } catch (e) {
            console.log(`Error: Browser cannot connect to ${url.href}`);
            console.log(e.stack);
            errorThrown = true;

        }
        // Will reset :
        //   If added more than 10 requests (whether error or not), this catches the situation when
        //     we added so many requests it caused a timeout.
        //   OR IF only a few urls were added but no error was thrown
        if (this.requestsAdded > 10 || (errorThrown===false && this.requestsAdded > 0)){
            this.appData.resetRequestsAttempts(this.currentRequestKey);
        }

    }

    async initpage(page, url, doingReload=false) {
        if (fs.existsSync("/app/gremlins.min.js")) {
            const gremscript = fs.readFileSync("/app/gremlins.min.js", 'utf8');
            console.log(`\nread in gremscript ${gremscript.length}\n`);
            await page.evaluate(gremscript);
        } else {
            console.log(`loading gremscript from remote location`);
            await page.addScriptTag({url: 'https://trickel.com/gremlins.min.js'});
            //await page.addScriptTag({url: 'https://unpkg.com/gremlins.js'});

        }

        this.isLoading = false;

        await page.screenshot({path: '/p/tmp/screenshot-pre.png', type: "png"});

        console.log("Waited for goto and response and div");
        this.requestsAdded += this.addDataFromBrowser(page, url);

        //console.log(this.appData.requestsFound[this.currentRequestKey]["processed"]% 2 === 0);

        // JSHandle.prototype.getEventListeners = function () {
        //     return this._client.send('DOMDebugger.getEventListeners', { objectId: this._remoteObject.objectId });
        // };

        //await this.submitForms(page);

        const elementHandles = await page.$$('div,li,span,a,input,p,button');
        for (let ele of elementHandles) {
            if (!doingReload){
                await ele.evaluate(node => node["hasClicker"] = "true");
            }
        }

        await this.addCodeExercisersToPage(doingReload);
        //await this.startCodeExercisers();
        return true;
    }

    async checkResponse(response, cururl) {
        if(isDefined(response)) {
            console.log("[WC] status = ", response.status(), response.statusText(), response.url());
            // only update status if current value is not 200
            if (this.appData.requestsFound[this.currentRequestKey].hasOwnProperty("response_status")) {
                if (this.appData.requestsFound[this.currentRequestKey]["response_status"] !== 200) {
                    this.appData.requestsFound[this.currentRequestKey]["response_status"] = response.status();
                }
            } else {
                this.appData.requestsFound[this.currentRequestKey]["response_status"] = response.status();
            }

            if (response.headers().hasOwnProperty("content-type")) {
                this.appData.requestsFound[this.currentRequestKey]["response_content-type"] = response.headers()["content-type"];
            } else {
                if (!this.appData.requestsFound[this.currentRequestKey].hasOwnProperty("response_content-type")) {
                    this.appData.requestsFound[this.currentRequestKey]["response_content-type"] = response.headers()["content-type"];
                }
            }
            if (response.status() >= 400) {
                console.log(`[WC] Received response error (${response.status()}) for ${cururl} `);
                return false;
            }
            //console.log(response);

            console.log(await response.headers());

            if (response.status() !== 200) {
                //console.log("[WC] ERROR status = ", response.status(), response.statusText(), response.url())
            }
            let responseText = await response.text();
            if (!isInteractivePage(response, responseText)) {
                console.log(`[WC] ${cururl} is not an interactive page, skipping`);
                return false;
            }
            if (responseText.length < 20) {
                console.log(`[WC] ${cururl} is too short of a page at ${responseText.length}, skipping`);
                return false;
            }
            if (responseText.toUpperCase().search(/<TITLE> INDEX OF /) > -1) {
                console.log("Index page, should disaable for fuzzing")
                this.appData.requestsFound[this.currentRequestKey]["response_status"] = 999;
                this.appData.requestsFound[this.currentRequestKey]["response_content-type"] = "application/dirlist";
            }
        } else {
            return false;
        }
        return true;
    }

    async do_login(page){
        //curl -i -s -k -X $'POST' --data-binary $'ipamusername=admin&ipampassword=password&phpipamredirect=%2F' $'http://10.90.90.90:9797/app/login/login_check.php'
        var loginData = this.loginData;
        console.log(loginData["form_url"])
        var gotourl = new URL(loginData["form_url"]);
        var data = loginData["post_data"];
        var method = loginData["method"];
        if (this.url === ""){
            let foundRequest = FoundRequest.requestParamFactory(loginData["form_url"], method, data, {}, "LoginPage", this.appData.site_url.href);
            foundRequest.from = "LoginPage";
            let addResult = this.appData.addRequest(foundRequest);
            if (addResult){
                console.log(`\x1b[38;5;2mADDED ${foundRequest.toString()}  ${ENDCOLOR}`);
            }
        }

        var self = this;
        function interceptLoginRequest(req){
            // let pdata = {
            //     'method': method,
            //     'postData': data,
            //     headers: {
            //         ...interceptedReq.headers(),
            //         "Content-Type": "application/x-www-form-urlencoded"
            //     }
            // };
            if (req.url().startsWith(`${self.appData.site_url.href}`)){
                let basename = path.basename(req.url());
                if (basename.indexOf("?") > -1) {
                    basename = basename.slice(0,basename.indexOf("?"));
                }

                let foundRequest = FoundRequest.requestObjectFactory(req);
                foundRequest.from = "LoginInterceptedRequest";
                self.requestsAdded += self.appData.addInterestingRequest(foundRequest );
            }
            req.continue();
        }
        page.on('request', interceptLoginRequest);

        console.log("[Login] REQUESTING URL ", gotourl);

        const response = await page.goto(gotourl, {waitUntil:"load"});

        console.log(`[Login] URL GOTO'ed `);

        try {
            if (loginData["usernameSelector"] || loginData["passwordSelector"]){

                await page.keyboard.press("Escape");
                await page.keyboard.press("Escape");
                if (loginData["loginStartSelector"]){
                    let p = await page.$(loginData["loginStartSelector"])
                    await p.click();
                    await(sleepg(100));
                }
                if (loginData["usernameSelector"]) {
                    await page.focus(loginData["usernameSelector"]);
                    await page.keyboard.type(loginData["usernameValue"], {delay:100});
                }
                await page.focus(loginData["passwordSelector"]);
                await page.keyboard.type( loginData["passwordValue"], {delay:100});
                const element = await page.$(loginData["passwordSelector"]);
                const text = await (await element.getProperty('value')).jsonValue();
                console.log("PW TEXT VALUE IS = ", text,  loginData["passwordValue"]);
                await page.screenshot({path: '/p/tmp/screenshot-pre-login.png', type:"png"});

                let submitType = loginData["submitType"].toLowerCase();
                let navwait =  page.waitForNavigation({waitUntil:"load"});
                if (submitType === "submit"){
                    const inputElement = await page.$('input[type=submit]');
                    await inputElement.click();
                } else if (submitType === "enter"){
                    console.log("\nPRESSING ENTERE\n");
                    await Promise.all([page.keyboard.type("\n"), page.waitForNavigation({timeout: 5000, waitUntil:'networkidle2'})])

                } else if (submitType === "click") {
                    //await page.keyboard.type("");
                    console.log("submitting form");
                    const formElement = await page.$(loginData["form_selector"]);
                    const inputElement = await formElement.$(loginData["form_submit_selector"]);
                    inputElement.disabled = false
                    console.log("input element = ", inputElement),
                    await Promise.all([page.evaluate("$('#loginButton').disabled = false;$('#loginButton').click()"),
                        await inputElement.click(),
                        page.waitForNavigation({timeout: 5000, waitUntil:'networkidle2'})]);

                }
            } else {
                 console.log(`No login b/c usernameSelector config value is empty`);
            }



        } catch (err){
            console.log(await page.content());
            console.log("CRITICAL ERROR: login failed");
            console.log(err);
            console.log(err.stack);
            process.exit(39);
        }


        const bodyResponse = await page.content();

        const responseStatusCode = response.statusCode;
        if (responseStatusCode >= 400){
            console.log(response);
            console.log("\nERROR ERROR ERROR ERROR  LOGIN FAILED TO COMPLETE ERROR ERROR ERROR ");
            process.exit(39);
        }

        //console.log(bodyResponse);
        console.log("POSI IS ", loginData["positiveLoginMessage"]);
        if (bodyResponse.indexOf(loginData["positiveLoginMessage"]) === -1){
            console.log(bodyResponse);
            console.log("\nERROR ERROR ERROR ERROR  LOGIN FAILED TO COMPLETE, didn't find expected message ERROR ERROR ERROR ");
            process.exit(38);
        }
        page.removeListener('request', interceptLoginRequest);
        let cookies = await page.cookies();
        console.log(cookies);


        return cookies
    }

    async addCookiesToPage(loginCookies, cookiestr, page) {

        var cookiesarr = cookiestr.split(";");
        var cookies_in = [];
        for (let cooky of loginCookies) {
            cookies_in.push(cooky); //["name"] + "=" + cooktest[cooky]["value"] + ";";
        }

        cookiesarr.forEach(function (cv) {
            if (cv.length > 2 && cv.search("=") > -1) {
                var cvarr = cv.split("=");
                var cv_name = `${cvarr[0].trim()}`;
                var cv_value = `${cvarr[1].trim()}`;
                cookies_in.push({"name": cv_name, "value": cv_value, url: `${this.appData.site_url.origin}`});

            }
        });
        //console.log("COOKIES", cookies_in);
        for (let cooky of cookies_in) {
            console.log("\t\x1b[38;5;5m" + cooky["name"] + "=" + cooky["value"] + "\x1b[0m");
            if (cooky["name"] === "token"){
                page.setExtraHTTPHeaders({Authorization:`Bearer ${cooky["value"]}`});
                this.bearer = `Bearer ${cooky["value"]}`;
            }
            this.cookies.push({"name": cooky["name"], "value": cooky["value"]});
            //console.log("COOKIES = ",this.cookies);
        }

        await page.setCookie(...cookies_in);
    }
    hasGremlinResults(){
        return ("grandTotal" in this.gremCounter);
    }
    gremTracker(ltext){

        try {
            this.gremCounter["grandTotal"] = ("grandTotal" in this.gremCounter) ? this.gremCounter["grandTotal"] + 1: 0;
            const { groups: { primaryKey, secKey } } = /gremlin (?<primaryKey>[a-z]*)[ ]*(?<secKey>[a-z]*)/.exec(ltext);
            this.gremCounter[primaryKey] = (primaryKey in this.gremCounter) ? this.gremCounter[primaryKey]: {total:0};
            this.gremCounter[primaryKey]["total"] += 1;
            let combinedKey = `${primaryKey} ${secKey}`;
            this.gremCounter[primaryKey][secKey] = (secKey in this.gremCounter[primaryKey]) ? this.gremCounter[primaryKey][secKey] + 1 : 1;

        } catch (err){
            // skip if no match
        }

    }
    getRoundResults(){
        let total = 0, above = 0, below = 0, equalto = 0;
        for (const [key, val] of Object.entries(this.appData.requestsFound)) {
            total++;
            equalto += val["attempts"] === this.appData.currentURLRound ? 1 : 0;
            above += val["attempts"] === this.appData.currentURLRound ? 0 : 1;
        }
        return {totalInputs:this.appData.numInputsFound(), totalRequests: total, equaltoRequests: equalto, aboveRequests:above}
    }
    reportResults(){
        console.log("XxXxXxXxXxXxXxXxXxXxXxXxXxXxXxXxXxXxXxXxXxXxXxXxXxXxXxXxXxXxXxXxXxXx");
        console.log("XxXxXxXxXxXxXxXxXxXxXxXxXxXxXxXxXxXxXxXxXxXxXxXxXxXxXxXxXxXxXxXxXxXx");
        console.log("XxXxXxXxXxXxXxXxXxXxXxXxXxXxXxXxXxXxXxXxXxXxXxXxXxXxXxXxXxXxXxXxXxXx");
        if (Object.entries(this.shownMessages).length > 0) {
            console.log("ERRORS:");
            for (const [key, val] of Object.entries(this.shownMessages)) {
                let strindex = key.indexOf("\n");
                strindex = strindex === -1 ? key.length : strindex;
                console.log(`\tERROR msg '${key.substring(0, strindex)}' seen ${val} times`);
            }
        }
        if (this.hasGremlinResults()) {
            console.log(this.gremCounter);
        }

        let roundResults = this.getRoundResults();
        console.log(`Round Results for round ${this.appData.currentURLRound} of ${MAX_NUM_ROUNDS}: `);
        console.log(`\tTotal Inputs :  ${roundResults.totalInputs}`);
        console.log(`\tTotal Requests: ${roundResults.equaltoRequests} of ${roundResults.totalRequests} processed so far`);

        console.log("XxXxXxXxXxXxXxXxXxXxXxXxXxXxXxXxXxXxXxXxXxXxXxXxXxXxXxXxXxXxXxXxXxXx");
        console.log("XxXxXxXxXxXxXxXxXxXxXxXxXxXxXxXxXxXxXxXxXxXxXxXxXxXxXxXxXxXxXxXxXxXx");
        console.log("XxXxXxXxXxXxXxXxXxXxXxXxXxXxXxXxXxXxXxXxXxXxXxXxXxXxXxXxXxXxXxXxXxXx");
    }

    async start() {
        var self = this;

        async function targetChanged(target){

            try {
                const newPage = await target.page();
                var newurl = newPage.target().url();

                if (target.url() !== self.url.href && target.url().startsWith(`${self.appData.site_url.origin}`)) {

                    //console.log(`TARGETED CHANGED from ${self.url.href} to ${target.url()} `);
                    //console.log(target);
                    let foundRequest = FoundRequest.requestParamFactory(target.url(),"GET", "",{},"targetChanged", self.appData.site_url.href);
                    foundRequest.from = "targetChanged";
                    self.requestsAdded += self.appData.addInterestingRequest(foundRequest);

                    //var tempurl = new URL(newurl);
                    //console.log("target changed -----------------------> ", tempurl.pathname);
                    // tempurl.searchParams.forEach(function (value, key, parent) {
                    //     self.appData.addQueryParam(key, value);
                    //     //console.log("PARAM NAME :::> ", key, value);
                    // });
                } else {  // target is foreign or same url
                    console.log(`TARGETED CHANGED to SAME ${self.url.href}`);
                    var tempurl = new URL(newurl);
                    console.log("target changed -----------------------> ", tempurl.pathname);
                    tempurl.searchParams.forEach(function (value, key, parent) {
                        //self.appData.addQueryParam(key, value);
                        console.log("PARAM NAME :::> ", key, value);
                    });

                    // self.page = await self.browser.newPage();
                    // await self.page.goto(newurl,{waitUntil:"load"});
                    // await self.addCodeExercisersToPage(self.hasGremlinResults());

                }


                //self.page = newPage;
            } catch (e) {
                console.log(`TARGET CHANGED Error: target changed encountered an error`);
                console.log(e.stack);
                //await browser.close();
            }

        }

        function pageError (error) {
            let msg = error.message;
            if (msg.length> 50){
                msg = msg.substring(0, 50);
            }
            if (msg in self.shownMessages) {
                if (self.shownMessages[msg] % 1000 === 0) {
                    console.log(msg, ` seen for the ${self.shownMessages[msg]} time`);
                }
                self.shownMessages[msg] += 1;
            } else if (error.message.indexOf("TypeError: Cannot read property 'species' of undefined") > -1) {
                console.log("\x1b[38;5;136mGREMLINS JS Error:\n\t", error.message, "\x1b[0m");
                self.gremlins_error = true;
            } else {
                self.shownMessages[msg] = 1;
                console.log("\x1b[38;5;136mBrowser JS Error:\n\t", error.message, "\x1b[0m");
            }

        }

        function consoleLog (message) {

            if (message.text().indexOf("[WC]") > -1) {
                if (message.text().indexOf("lamehorde is done") > -1){
                    console.log("\x1b[38;5;136mLamehorde completion detected\x1b[0m");
                    self.lamehord_done = true;
                } else {
                    console.log(message.text());
                }
            } else if (message.text().search("[WC-URL]") > - 1){
                let urlstr = message.text().slice("[WC-URL]".length);
                //console.log("urlstr=", urlstr);
                self.appData.addValidURLS([urlstr], `${self.appData.site_url.href}`,"ConsleRecvd");
            } else if (message.text().search("CW DOCUMENT") === -1 && message.text() !== "JSHandle@node") {
                if (message.text().indexOf("gremlin") > -1){
                    self.gremTracker(message.text());
                } else if (message.text().indexOf("mogwai") > -1){
                    self.gremTracker(message.text());
                } else {
                    if (message.text().startsWith("jQuery") || message.text().startsWith("disabled") || message.text().startsWith("__ko__")) {
                        // do nothing
                    } else {
                        console.log(message.text())
                    }
                }
            }
        }

        /**
         * Two phases, in the first, we record and save any relevant request information on local requests.
         * In the second, we attempt to determine if the request should be aborted.
         * @param req
         */
        function processRequest(req){
            // interception does not fire for /#/XXXX changes

            // Save Request info if we can
            if (req.method() !== "GET" || req.postData() || req.resourceType() === "xhr"){
                //console.log("NONGET: ", req.url(), "method=",req.method(), "restype=", req.resourceType(), "data=", req.postData());
            }
            if (req.url().search(/.*HNAP1/) > -1){
                let re = new RegExp(/<soap:Body>(.*)<\/soap:Body>/);
                if (re.test(req.postData())){
                    let pd_match = re.exec(req.postData());
                    console.log(`${GREEN}${req.url()} ${pd_match[1]}${ENDCOLOR}`);
                } else {
                    console.log(`${GREEN}${req.url()} NO SOAP MATCH ${req.postData()} ${ENDCOLOR}`);
                }
            }
            if (self.url.href === req.url()) {
                //not sure why reforming request data for continue here.

                var pdata = {
                    'method': self.method,
                    'postData': self.postData,
                    headers: {
                        ...req.headers(),
                        "Content-Type": "application/x-www-form-urlencoded"
                    }
                };

                let foundRequest = FoundRequest.requestObjectFactory(req, self.appData.site_url.href);
                foundRequest.from="InterceptedRequestSelf";

                for (let [pkey, pvalue] of Object.entries(foundRequest.getAllParams())){
                    if (typeof pvalue === "object"){
                        pvalue = pvalue.values().next().value;
                    }
                    self.appData.addQueryParam(pkey, pvalue);
                }

                if (self.appData.addInterestingRequest(foundRequest) > 0){
                    self.requestsAdded++;
                    console.log("[WC] req.url() = ", req.url());
                }

                console.log("processRequest caught to add method and data ");
                console.log("\t", self.method, pdata.method)
                console.log("\t", self.postData, pdata.postData)
                if (!self.isLoading){
                    self.reinitPage = true;
                }
                req.continue(pdata);

            } else {

                let tempurl = new URL(req.url());

                //self.appData.addInterestingRequest(req );

                tempurl.searchParams.forEach(function (value, key, parent) {
                    self.appData.addQueryParam(key, value);
                });
                if (req.url().startsWith(self.appData.site_url.origin)){
                    //console.log("Intercepted in processRequest ", req.url(), req.method());
                    let basename = path.basename(tempurl.pathname);
                    if (req.url().indexOf("rest") > -1 && (req.method() === "POST" || req.method() === "PUT")){
                        //console.log(basename, req.method(), req.headers(), req.resourceType());
                    }

                    let foundRequest = FoundRequest.requestObjectFactory(req, self.appData.site_url.href);
                    foundRequest.from="InterceptedRequest";

                    for (let [pkey, pvalue] of Object.entries(foundRequest.getAllParams())){
                        if (typeof pvalue === "object"){
                            pvalue = pvalue.values().next().value;
                        }
                        self.appData.addQueryParam(pkey, pvalue);
                    }

                    if (self.appData.addInterestingRequest(foundRequest) > 0){
                        self.requestsAdded++;
                        console.log("[WC] ADDED intercepted request req.url() = ", req.url());
                    }
                    // skip if it has a period for nodejs apps

                    // let result = self.appData.addRequest(req.url(), req.method(), req.postData(), "interceptedRequest");
                    // if (result){
                    //     console.log(`\x1b[38;5;2mINTERCEPTED REQUEST and ADDED  #${self.appData.collectedURL} ${req.url()} RF size = ${self.appData.numRequestsFound()}\x1b[0m`);
                    // } else {
                    //     //console.log(`INTERCEPTED and ABORTED repeat URL ${req.url()}`);
                    // }
                } else {
                    if (req.url().indexOf("gremlins") > -1){
                        console.log("[WC] CONTINUING with getting some gremlins in here.");
                        req.continue();
                    } else {
                        console.log(`Aborting request for ${req.url().substr(0,200)}`)
                        req.abort();
                    }
                    return;
                }
                //console.log("PROCESSED ", req.url(), req.isNavigationRequest());
                if (false && req.frame() === self.page.mainFrame()){
                    req.abort('aborted');
                } else {
                    if (req.isNavigationRequest() && req.frame() === self.page.mainFrame() ) {
                        if (typeof self.last_nav_request !== "undefined" && self.last_nav_request === req.url()){
                            console.log("[WC] this is the same as last nav request, ignoring");
                            req.abort()
                            self.last_nav_request = req.url();
                            return;
                        }
                        self.last_nav_request = req.url();
                        if (req.url().indexOf("gremlins") > -1){
                            console.log("[WC] CONTINUING with getting some gremlins in here.");
                            req.continue();
                            return;
                        }
                        if (self.isLoading){
                            console.log(`[WC] \tRequest granted while still in loading phase ${req.resourceType()} ${req.url()} `);

                            req.continue();
                        } else {

                                console.log(`[WC] \tNavigation Request in mainFrame denied ${req.url()}`);
                                req.respond(req.redirectChain().length
                                  ? { body: '' } // prevent 301/302 redirect
                                  : { status: 204 } // prevent navigation by js
                                )
                            //req.abort();
                        }

                    } else {

                        // NON-mainFrame or not a navigation reque, shouldn't change page navigation

                        // var pdata = {
                        //     headers: {
                        //         ...req.headers(),
                        //         "Content-Type": "application/x-www-form-urlencoded"
                        //     }
                        // };
                        // if (!("Authorization" in pdata.headers)){
                        //     pdata.headers["Authorization"] = self.bearer;
                        // }
                        // let cookiestr = "";
                        // for (let cookie of self.cookies){
                        //     cookiestr += `${cookie.name}=${cookie.value}; `
                        // }
                        // pdata.headers["Cookie"] = cookiestr;
                        // console.log("\nprocessRequest REFORMED continue --- > nav req = ", req.isNavigationRequest(),
                        //     "is main frame = ", req.frame() === self.page.mainFrame(),
                        //     "is loading = ", self.isLoading,
                        //     "url = ", req.url(), "\n");
                        if (req.frame() === self.page.mainFrame()){
                            if (self.isLoading){

                                self.loadedURLs.push(tempurl.origin + tempurl.pathname);
                                req.continue();
                            } else {
                                req.continue();
                                // if (self.loadedURLs.includes(tempurl.origin + tempurl.pathname)){
                                //     console.log(`[WC] \tAllowing reload of frame ${req.url()}`);
                                //     req.continue();
                                // } else {
                                //     req.abort();
                                // }
                            }
                        } else {
                            req.continue()
                        }

                    }
                }

            }
        }

        console.log("WE ARE STARTING THE BROWSER!!!!! ", this.url.href);
        console.log("HEADLESS = ", this.appData.headless);
        try {
            try{
                this.browser = await puppeteer.launch({headless:this.appData.headless, args:["--disable-features=site-per-process", "--window-size=1920,1040"], "defaultViewport": null }); //
                console.log("OPENED BROWSER!");
                this.browser_up = true;
            } catch (xerror) {
                console.log("UNABLE TO OPEN X DISPLAY");
                if (xerror.message.indexOf("Unable to open X display") > -1){
                    this.browser = await puppeteer.launch({headless:this.appData.headless, args:["--disable-features=site-per-process"] });
                    this.browser_up = true;
                } else {
                    this.browser_up = false;
                    // noinspection ExceptionCaughtLocallyJS
                    throw(xerror);
                }
            }
            let pagetimeout = setTimeout(function(){
                    console.log("I think we are STUCKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKK");
                    try{
                        this.browser_up = false;
                        self.browser.close();
                    } catch (err){
                        console.log("\tProblem closing browser after timeout\n");
                    }

                }, this.timeoutLoops*this.timeoutValue*1000 + 6000);

            let gremlinsErrorTest = setInterval(function(){
                if (self.gremlins_error && self.lamehord_done){
                    console.log("Ohh no, they killed Gizmo!, and the lamhord completed.  Aborting!!!");
                    try{
                        this.browser_up = false;
                        self.browser.close();
                    } catch (err){
                        console.log("\tProblem closing browser after timeout\n");
                    }
                    self.gremlins_error = false;
                }
            }, 10*1000);

            this.page = await this.browser.newPage();

            try {
                await this.page.evaluate(() => console.log(`url is ${location.href}`));

                await this.page.setRequestInterception(true);

                if (this.loginData !== undefined && 'form_url' in this.loginData){
                    let loginCookies = await this.do_login(this.page);
                    await this.addCookiesToPage(loginCookies, this.cookieData, this.page).catch(function (error) {
                        console.log("COOKIE ERROR:!!!", error)
                    });

                }

                this.page.on('request', processRequest);

                this.page.on('console', consoleLog);
                this.page.on('pageerror', pageError);

                this.browser.on('targetchanged', targetChanged);

                await this.page.setCacheEnabled(false);
                await this.page.setDefaultNavigationTimeout(0);

                await this.exerciseTarget(this.page);

                this.reportResults();

            } catch (e) {
                console.log(`Error: cannot start browser `);
                console.log(e.stack);
            } finally {
                clearTimeout(pagetimeout);
                clearInterval(gremlinsErrorTest);
                console.log(`current request = ${this.appData.requestsFound[this.currentRequestKey]}`)
                await this.browser.close();
            }

        } catch (browsererr) {
            console.log(`Error: with Starting browser or creating new page `);
            console.log(browsererr.stack);
        }

    }

}

module.exports = {AppData:AppData, RequestExplorer:RequestExplorer};


/*
 *
 *
 *
 *
 *
 *
 */
