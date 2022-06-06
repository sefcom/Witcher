
function isDefined(val) {
    return !(typeof val === 'undefined' || val === null);
}

export class FoundRequest  {
    static requestObjectFactory(request, site_url="http://localhost/"){
        return new FoundRequest(request.url(), request.method(), request.postData(), request.headers(), request.resourceType(), site_url);
    }
    static requestParamFactory(url="", method="GET", postData="", headers={}, resourceType="UNK TYPE",site_url="http://localhost/"){
        return new FoundRequest(url, method, postData, headers, resourceType, site_url);
    }
    constructor(urlstr="", method="GET", postData="", headers={}, resourceType="UNK TYPE", site_url="http://localhost/") {
        this._id = -1;
        this._urlstr = urlstr;
        if (!this._urlstr.startsWith("http://")) {
            this._urlstr = site_url + this._urlstr;
        }
        this.multipleParamKeys = new Set();
        this._url = new URL(this._urlstr);
        this._resourceType = resourceType;
        this._method = method.toUpperCase();
        if (this._method === ""){
            this._method = "GET";
        }
        this._postData = postData;
        this._headers = headers;
        this.attempts = 0;
        this.processed = 0;
        this.from = "";
        this.cleanURLParamRepeats();
        this.cleanPostDataRepeats();
    }
    incTimesProcessed(){
        this.processed++;
    }
    getTimesProcessed(){
        return this.processed;
    }
    setId(newid){
        this._id = newid;
    }
    addParams(params){
        if (this._method === "POST"){
            this._postData = params ;
            this.cleanPostDataRepeats();
        } else {
            if (this._urlstr.indexOf("?") > -1) {
                this._urlstr += `&${params}`;
            } else {
                this._urlstr += `?${params}`;
            }
            this._url = new URL(this._urlstr);
            this.cleanURLParamRepeats();
        }
    }
    urlstr(){
        return this._urlstr;
    }
    getURL(){
        if (typeof this._url === "string"){
            this._url = new URL(this._urlstr);
        }
        return this._url;
    }
    toString(){
        return `${this.from} -- ${this._method} ${this._urlstr} urllen=${this._urlstr.length} postlen=${this._postData.length} -- `
    }
    getRequestKey(){
        return `${this._method} ${this.getURL().href} ${this._postData}`;
    }
    cookieData(){
        var cookie = this._headers["cookie"];
        if (isDefined(cookie)){
            return cookie;
        }
        cookie = this._headers["COOKIE"];
        if (isDefined(cookie)){
            return cookie;
        }
        return "";
    }
    isSaveable(){
        return this._urlstr.length > 3;
    }
    getContentType(){
        let content_type = "";
        if (typeof this._headers === "object"){
            if (isDefined(this._headers["content-type"])){
                content_type = this._headers["content-type"]
            }
        }
        return content_type;
    }
    /**
     * @return {string}
     */
    url() {
        return this._urlstr;
    }

    /**
     * @return {string}
     */
    resourceType() {
        return this._resourceType;
    }

    /**
     * @return {string}
     */
    method() {
        return this._method;
    }

    /**
     * @return {string|undefined}
     */
    postData() {
        return this._postData;
    }

    /**
     * @return {!Object}
     */
    headers() {
        return this._headers;
    }

     getPathname(){
        let pathname = this.getURL().pathname;
        // if (this._url.href.indexOf("/#/") > -1){
        //     let hashIndex = this._url.href.indexOf("#");
        //     pathname = `/${this._url.href.slice(hashIndex)}`
        // }
        return pathname;
    }

    cleanURLParamRepeats(){
        var newvars = "";
        if (!this._url.search && this._urlstr.search("#.*[?]") > -1 ){
            this._urlstr = this._urlstr.replace(/([Q2])[Q2]+/g,"$1")
            this._url = new URL(this._urlstr);
            console.log(`NEW SEARCH ${this._urlstr}`);
        } else {
            this._url.search = this._url.search.replace(/([Q2])[Q2]+/g,"$1")
            this._urlstr = this._url.href;
            if (this._url.search){
                //console.log(`Cleaned up VARS => ${this._url.search}  ---> ${this._urlstr}`);
            }
        }
        // this.getURL().searchParams.forEach(function (value, key, parent){
        //     value = value.replace(/([Q2])[Q2]+/g,"$1")
        //     newvars += `&${key}=${value}`;
        // });
        // this._url.search = newvars;
        //
        // this._urlstr = this._url.href;
        //
        // // if (this._urlstr.search(/.*?.*=[Q2][Q2]+/) > -1){
        // //     this._urlstr = this._urlstr.replace(/[Q2][Q2]+/,"Q");
        // //     this._url = new URL(this._urlstr);
        // // }
        //
        // this._url = new URL(this._urlstr);


    }

    cleanPostDataRepeats(){
        if (this._postData === "" || this._postData.startsWith('<?xml')){
            return;
        }
        const boundryIndex = this.getContentType().indexOf("----WebKitFormBoundary");
        if (boundryIndex > -1){
            console.log("[WC] WebKitFormBound index = ",boundryIndex, this.getContentType());
            const WEBKIT_BOUNDRY = "----WebKitFormBoundary0123456789ABCDEF";
            const targetBoundary = this.getContentType().slice(boundryIndex);
            this._headers["content-type"] = this._headers["content-type"].replace(targetBoundary, WEBKIT_BOUNDRY);
            this._postData = this._postData.replace(targetBoundary, WEBKIT_BOUNDRY);
            //console.log("[WC] DOING REPLACE IN FOUNDREQUESTS::::: ", this._postData);
        }
        var postArray = [];
        if (this.getContentType().indexOf("application/json") > -1){
            let jdata = JSON.parse(this._postData)
            for (const [key, value] of Object.entries(jdata)) {
                postArray.push(`${key}=${value}`);
            }
        } else {
            if (this._postData){
                postArray = this._postData.split("&");
            }

        }
        let newPostData = "";
        for (let p of postArray){
            let {key,value} = this.extractKeyValue(p);
            value = value.replace(/([Q2])[Q2]+/g,"$1")
            newPostData += `&${key}=${value}`;
        }

        newPostData = newPostData.slice(1);
        if (newPostData){
            //console.log(`post data => ${newPostData}`);
            this._postData = newPostData;
        }

    }

    getQueryString(){
        return this.getURL().search.substring(1);
    }
    extractKeyValue(p){
        let key = p;
        let value = "";
        if (p.indexOf("=") > -1){
            let temparr = p.split("=");
            key = temparr[0];
            value = temparr[1];
        }
        return {key:key, value:value};
    }

    getAllParams(){
        let queryString = this.getQueryString();
        let postData = this._postData;
        let builtParams = {};
        let plist = [];
        let bf = this.multipleParamKeys;
        if (!this.multipleParamKeys){
            this.multipleParamKeys = new Set();
        } else if (!(this.multipleParamKeys instanceof Set)){
            //console.log(`[WC] taking ${this.multipleParamKeys} and turning into Set`);
            if (this.multipleParamKeys instanceof Array){
                this.multipleParamKeys = new Set(this.multipleParamKeys);
            } else if (this.multipleParamKeys instanceof Object){
                this.multipleParamKeys = new Set(Object.keys(this.multipleParamKeys));
            }
        }
        //console.log(bf, bf instanceof Set,"after", this.multipleParamKeys, this.multipleParamKeys instanceof Set );
        
        if (isDefined(queryString) && queryString.length > 0) {
            plist = queryString.split("&");
        }
        if (isDefined(postData) && postData.length > 0 ) {
            plist = [...plist, ...postData.split("&")];
        }
        for (let p of plist){
            let {key,value} = this.extractKeyValue(p);
            if (p.length > 0){
                // if the string is filled with repeating Q's or 2's then truncate string to 1 character
                if (key in builtParams){
                    builtParams[key].add(value);
                    this.multipleParamKeys.add(key)
                } else {
                    builtParams[key] = new Set([value]);
                }
            }
        }

        return builtParams;
    }
}

//module.exports = FoundRequest;

