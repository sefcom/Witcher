var assert = require('assert');

const iser = require("./input_sifter2");
const FoundRequest = require("./FoundRequest");

BASE_APPDIR="/tmp";

function gremTracker_test() {
    console.log("================ TESTING gremTracker");
    let appData = new iser.AppData(false, BASE_APPDIR);
    let re = new iser.RequestExplorer(appData, 0, BASE_APPDIR);

    re.gremTracker("LOG gremlin clicker    mousedown at 339 184");
    re.gremTracker("LOG gremlin clicker    click at 339 184");
    re.gremTracker("LOG gremlin toucher    gesture at 115 447 JSHandle@object");
    re.gremTracker("LOG gremlin toucher    gesture at 115 447 JSHandle@object");
    re.gremTracker("LOG gremlin toucher    tap at 115 447 JSHandle@object");
    re.gremTracker("LOG gremli    gesture at 115 447 JSHandle@object");
    assert(re.gremCounter["clicker"]["total"]===2, "Found the wrong number of clicks");
    assert(re.gremCounter["toucher"]["total"]===3, "Foudn the wrong number of touches.");
}

function createTestAppData(){
    let appData = new iser.AppData(false, BASE_APPDIR);
    appData.addQueryParam("module","PostCalendar");
    appData.addQueryParam("module","1111");
    appData.addQueryParam("module","2222");
    appData.addQueryParam("module","3333");
    // appData.inputSet.add('module=PostCalendar&viewtype=day&func=view&&pc_username[]=admin&framewidth=797');
    // appData.inputSet.add('module=1111&viewtype=111&func=111&&pc_username[]=111&framewidth=111');
    // appData.inputSet.add('module=222&viewtype=222&func=222&&pc_username[]=222&framewidth=222');
    // appData.inputSet.add('module=333&viewtype=333&func=333&&pc_username[]=333&framewidth=333');
    // appData.inputSet.add('module=444&viewtype=444&func=444&&pc_username[]=444&framewidth=444');
    // appData.inputSet.add('taname=VALUE%20FOR%20TEXTAREAUx3QE!G1i');
    // appData.inputSet.add('taname=VALUE FOR TEXTAREA6J7)TwCL%[ce[dd6rV2mKl%%#7vrt*eMQw8TeW41BdSH!GBAe#SaiEkPZu(p2wNu)3tPJEqZ(HOugZrs')
    // appData.inputSet.add('taname=VALUE FOR TEXTAREAp6LGbn)AxCSp6wsUl(YAXZNvarG%17pW3a@x([b^OO!ekqWYq8yfz6%1N]Y#&OtsUdyD6FirUQy')
    // appData.inputSet.add('taname=VALUE FOR TEXTAREAF^5H3Qt2VI^)r*70v3hbemJ%&obC(ucUHvyP[C*riTwK5VWOFO@No!lu*KLxuaOtTeRfsd@4Fgx3lfD&)1f')
    return appData;
}

function addQueryParam_test () {
    let appData = createTestAppData();

    assert(appData.inputSet.has("module=3333"),"could not find expected param");
    appData.addQueryParam("module","4444");
    assert(appData.inputSet.has("module=4444")===false,"did not expect to find parameter in inputSet.");
}
function addRequestFromRequest(appData, requests) {

}
function createTestRequestDataDates(){
    let appData = new iser.AppData(false, BASE_APPDIR);
    let requests = [{
        "url": "http://localhost/interface/main/calendar/index.php?module=PostCalendar&func=view&tplview=&viewtype=day&Date=20200606&pc_username=&pc_category=&pc_topic=",
            "method": "GET",
            "attempts": 0,
            "from": "interceptedRequest",
            "cookieData": ""
    },
        {
        "url": "http://localhost/interface/main/calendar/index.php?module=PostCalendar&func=view&tplview=&viewtype=day&Date=20200613&pc_username=&pc_category=&pc_topic=",
            "method": "GET",
            "attempts": 0,
            "from": "interceptedRequest",
            "cookieData": ""
    },
    {
        "url": "http://localhost/interface/main/calendar/index.php?module=PostCalendar&func=view&tplview=&viewtype=day&Date=20200701&pc_username=&pc_category=&pc_topic=",
            "method": "GET",
            "attempts": 0,
            "from": "interceptedRequest",
            "cookieData": ""
    },
    {
        "url": "http://localhost/interface/main/calendar/index.php?module=PostCalendar&func=view&tplview=&viewtype=day&Date=20200614&pc_username=&pc_category=&pc_topic=",
            "method": "GET",
            "attempts": 0,
            "from": "interceptedRequest",
            "cookieData": ""
    },
    {
        "url": "http://localhost/interface/main/calendar/index.php?module=PostCalendar&func=view&tplview=&viewtype=day&Date=20200608&pc_username=&pc_category=&pc_topic=",
            "method": "GET",
            "attempts": 0,
            "from": "interceptedRequest",
            "cookieData": ""
    },
    {
        "url": "http://localhost/interface/main/calendar/index.php?module=PostCalendar&func=view&tplview=&viewtype=day&Date=20200525&pc_username=&pc_category=&pc_topic=",
            "method": "GET",
            "attempts": 0,
            "from": "interceptedRequest",
            "cookieData": ""
    },
    {
        "url": "http://localhost/interface/main/calendar/index.php?module=PostCalendar&func=view&tplview=&viewtype=day&Date=20200419&pc_username=&pc_category=&pc_topic=",
            "method": "GET",
            "attempts": 0,
            "from": "interceptedRequest",
            "cookieData": ""
    }
    ];
    for (req of requests) {
        let foundRequest = FoundRequest.requestParamFactory(req["url"], "", "", {"Cookie": req["cookieData"]});
        foundRequest.from= req["from"];
        appData.addRequest(foundRequest);
    }
    return appData;
}

function containsEquivURLHASH_test(){
    console.log("================ TESTING containsEquivURL   NoName");
    let appData = new iser.AppData(false, BASE_APPDIR);
    appData.addRequest(FoundRequest.requestParamFactory("http://localhost/?=", "GET", ""));
    console.log(appData.requestsFound);
    // appData.addRequest(FoundRequest.requestParamFactory("http://localhost/#/privacy-security/erasure-request?=QQQQ&", "GET", ""));
    // appData.addRequest(FoundRequest.requestParamFactory("http://localhost/#/privacy-security/erasure-request?=QQQQQ&", "GET", ""));
    // appData.addRequest(FoundRequest.requestParamFactory("http://localhost/#/privacy-security/erasure-request?=QQQQQQ&", "GET", ""));
    // let request1 =     FoundRequest.requestParamFactory("http://localhost/#/privacy-security/erasure-request?=QQQQQQQ&","GET");
    // assert(appData.containsEquivURL(request1), "failed to find unknown single var");
}
function containsEquivURLNoname_test(){
    console.log("================ TESTING containsEquivURL   NoName");
    let appData = new iser.AppData(false, BASE_APPDIR);
    appData.addRequest(FoundRequest.requestParamFactory("http://localhost/#/privacy-security/erasure-request?=QQQ&", "GET", ""));
    appData.addRequest(FoundRequest.requestParamFactory("http://localhost/#/privacy-security/erasure-request?=QQQQ&", "GET", ""));
    appData.addRequest(FoundRequest.requestParamFactory("http://localhost/#/privacy-security/erasure-request?=QQQQQ&", "GET", ""));
    appData.addRequest(FoundRequest.requestParamFactory("http://localhost/#/privacy-security/erasure-request?=QQQQQQ&", "GET", ""));
    let request1 =     FoundRequest.requestParamFactory("http://localhost/#/privacy-security/erasure-request?=QQQQQQQ&","GET");
    assert(appData.containsEquivURL(request1), "failed to find unknown single var");
}
function containsEquivURL_test() {
    console.log("================ TESTING containsEquivURL");
    let appData = new iser.AppData(false, BASE_APPDIR);
    let re = new iser.RequestExplorer(appData, 0, BASE_APPDIR);
    appData.addRequest(FoundRequest.requestParamFactory("http://localhost/interface/main/about_page.php?taname=AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA", "POST", ""));
    appData.addRequest(FoundRequest.requestParamFactory("http://localhost/interface/main/about_page.php?module=PostCalendar&viewtype=day&func=view&&pc_username[]=admin&framewidth=797", "POST", ""));
    appData.addRequest(FoundRequest.requestParamFactory("http://localhost/blank.html", "GET", ""));
    appData.addRequest(FoundRequest.requestParamFactory("http://localhost/testpost.php", "GET", "var1=val1&var2=val2&var3=val3&var4=val4&var5=val5"));
    appData.addRequest(FoundRequest.requestParamFactory("http://localhost/testdups.php?var1=1&var1=1", "POST", "var1=1&var1=1&var1=1&var1=1&var1=1&var1=1"));
    let request1 = FoundRequest.requestParamFactory("http://localhost/interface/main/about_page.php?taname=AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA","");
    assert(appData.containsEquivURL(request1), "failed to find unknown single var");

    let request21 = FoundRequest.requestParamFactory("http://localhost/interface/main/about_page.php?module=PostCalendar&viewtype=day&func=view&&pc_username[]=admin&framewidth=797","POST");
    assert(appData.containsEquivURL(request21)===true, "failed to find exactly the same QS");

    appData.fuzzyMatchEquivPercent = .75;
    let request2 = FoundRequest.requestParamFactory("http://localhost/interface/main/about_page.php?module=PostCalendar&viewtype=day&func=view&&pc_username[]=admin&framewidth=300","POST");
    assert(appData.containsEquivURL(request2), "failed to find mostly the same QS");

    let request3 = FoundRequest.requestParamFactory("http://localhost/interface/main/about_page.php?module=PostCalendar&viewtype=DIFF&func=view&&pc_username[]=DIFF&framewidth=DIFF","GET");
    assert(appData.containsEquivURL(request3) === false, "found match even though too different");

    let request4 = FoundRequest.requestParamFactory("http://localhost/boom.html","GET");
    assert(appData.containsEquivURL(request4) === false, "found match even though completely different url without QS different");


    let request5 = FoundRequest.requestParamFactory("http://localhost/testpost.php","POST", "var1=val1&var2=val2&var3=val3&var4=val4&var5=val5");
    assert(appData.containsEquivURL(request5), "failed to find exactly the same postData");

    let request6 = FoundRequest.requestParamFactory("http://localhost/testpost.php","POST","var1=val1&var2=val2&var3=val3&var4=val4&var5=new_val5");
    assert(appData.containsEquivURL(request6), "failed to find mostly the same postData");

    let request7 = FoundRequest.requestParamFactory( "http://localhost/testpost.php","POST","var1=DIFF&var2=new_val2&var3=DIFF&var4=val4&var5=DIFF");
    assert(appData.containsEquivURL(request7) === false, "found a postData match even tough too many different values");
}
function duplicateParams_tests(){
    let appData = new iser.AppData(false, BASE_APPDIR);
    let re = new iser.RequestExplorer(appData, 0, BASE_APPDIR);
    appData.addRequest(FoundRequest.requestParamFactory("http://localhost/testdups.php?var1=1&var1=1", "POST", "var1=1&var1=1&var1=1&var1=1&var1=1&var1=1"));

    //dups test
    let request8 = FoundRequest.requestParamFactory("http://localhost/testdups.php?var1=1&var1=1","POST","var1=1&var1=1");

    assert(appData.containsEquivURL(request8) === true, "Dups detection failed, should have matched vars already in a request.");

    //dups test
    let request9 = FoundRequest.requestParamFactory("http://localhost/testdups.php?var1=1&var1=2","POST", "var1=3&var1=4");

    assert(appData.containsEquivURL(request8) === true, "Dups detection failed for same key different vals, should have matched vars already in a request.");



}
function containsEquivURL_date_test(){
    console.log("================ TESTING containsEquivURL with DATE differences");
    let dateAppData = createTestRequestDataDates();

    let requestDate = FoundRequest.requestParamFactory("http://localhost/interface/main/calendar/index.php?module=PostCalendar&func=view&tplview=&viewtype=day&Date=99999999&pc_username=&pc_category=&pc_topic=","GET");

    assert (dateAppData.containsEquivURL(requestDate), "Did not find a match even though GET DATE has only DATE as diff");

}

function addValidURLS_test(){
    let appData = createTestAppData();

    appData.addValidURLS(["./test.php"], new URL("http://localhost"), "OnPageAnchor");

    console.log(appData);

}
function keyMatch_test(){
    console.log("================ TESTING Key Match ");
    let appData = createTestAppData();
    let sought1 = {"var1":new Set("val1"),"var2": new Set("val2")};
    let test1 = {"var1":new Set("XXXX"),"var2": new Set("YYYYY")};

    assert(appData.keyMatch(sought1, test1), "It didn't find a keyMatch but should have for the keys.")

    sought1["var3"] =new Set("val3");
    assert(appData.keyMatch(sought1, test1)===false, "Extra key should have prevented match")
    test1["var33"] =new Set("ZZZZ");
    assert(appData.keyMatch(sought1, test1)===false, "Tests key should prevent a match");

    let re = new iser.RequestExplorer(appData, 0, BASE_APPDIR);
    appData.addRequest(FoundRequest.requestParamFactory("http://localhost/interface/main/about_page.php?id=1&method=2", "GET", ""));
    appData.addRequest(FoundRequest.requestParamFactory("http://localhost/interface/main/about_page.php?id=2&method=2", "GET", ""));
    //appData.addRequest("http://localhost/interface/main/about_page.php?id=3&method=2", "GET", "");
    //appData.addRequest("http://localhost/interface/main/about_page.php?id=4&method=2", "GET", "");
    appData.maxKeyMatches=4;
    let request1 = FoundRequest.requestParamFactory("http://localhost/interface/main/about_page.php?id=99&method=2", "GET");
    assert(appData.containsEquivURL(request1)===false, "Should fail b/c only 2 already exist");

    appData.addRequest(FoundRequest.requestParamFactory("http://localhost/interface/main/about_page.php?id=3&method=2", "GET", ""));
    assert(appData.containsEquivURL(request1)===false, "Should fail b/c only 3 already exist");
    for (let i=appData.numRequestsFound();i<appData.maxKeyMatches;i++){
        appData.addRequest(FoundRequest.requestParamFactory(`http://localhost/interface/main/about_page.php?id=${i}&method=3`, "GET", ""));
    }
    assert(appData.containsEquivURL(request1)===true, `Should be found inside b/c ${this.maxKeyMatches} already exist`);

}

function fuzzyValueMatch_test(){

    console.log("================ TESTING fuzzyValueMatch");
    let appData = new iser.AppData(false, BASE_APPDIR);
    appData.addRequest(FoundRequest.requestParamFactory("http://localhost/interface/main/about_page.php?taname=AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA", "POST", ""));
    appData.addRequest(FoundRequest.requestParamFactory("http://localhost/interface/main/about_page.php?module=PostCalendar&viewtype=day&func=view&&pc_username[]=admin&framewidth=797", "POST", ""));
    appData.addRequest(FoundRequest.requestParamFactory("http://localhost/blank.html", "GET", ""));
    appData.addRequest(FoundRequest.requestParamFactory("http://localhost/testpost.php", "GET", "var1=val1&var2=val2&var3=val3&var4=val4&var5=val5"));
    appData.addRequest(FoundRequest.requestParamFactory("http://localhost/testdups.php?var1=1&var1=1", "POST", "var1=1&var1=1&var1=1&var1=1&var1=1&var1=1"));
    let request1 = FoundRequest.requestParamFactory("http://localhost/interface/main/about_page.php?taname=BAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA","GET");
    appData.minFuzzyScore=.70;
    assert(appData.containsEquivURL(request1)===true, `Should be found inside b/c most of the value is the same`);


}


function ignoreValues_test(){

    console.log("================ TESTING ignoreValues");
    let appData = new iser.AppData(false, BASE_APPDIR);
    appData.setIgnoreValues(["var1","timestamp"]);
    appData.addRequest(FoundRequest.requestParamFactory("http://localhost/interface/main/about_page.php?timestamp=1584278906&var1=1&var2=1&var3=1&var4=1", "POST", ""));
    appData.addRequest(FoundRequest.requestParamFactory("http://localhost/interface/main/about_page.php?timestamp=1584278800&var1=2&var2=2&var3=2&var4=2", "POST", ""));
    appData.addRequest(FoundRequest.requestParamFactory("http://localhost/interface/main/about_page.php?timestamp=1584278822&var1=3&var2=3&var3=3&var4=3", "POST", ""));
    appData.addRequest(FoundRequest.requestParamFactory("http://localhost/interface/main/about_page.php?timestamp=1584278844&var1=4&var2=4&var3=4&var4=4", "POST", ""));
    appData.addRequest(FoundRequest.requestParamFactory("http://localhost/testdups.php?var1=1&var1=1", "POST", "var1=1&var1=1&var1=1&var1=1&var1=1&var1=1"));

    appData.fuzzyMatchEquivPercent = .99;
    appData.minFuzzyScore = .50;
    // ignoreValue (e.g., a timestamp, so a keymatch is the same has the values matching)
    let request1 = FoundRequest.requestParamFactory("http://localhost/interface/main/about_page.php?timestamp=1584278905&var1=1&var2=1&var3=1&var4=1","GET");
    assert(appData.containsEquivURL(request1)===true, `Should be EQUIV (i.e., true)  b/c value should be ignored and keymatch treats the values as equal `);

    // this matches a value already entered
    let request2 = FoundRequest.requestParamFactory("http://localhost/interface/main/about_page.php?timestamp=9999999999&var1=1&var2=1&var3=1&var4=1","GET");
    assert(appData.containsEquivURL(request2)===true, `Should be EQUIV (i.e., true) b/c value should be ignored and keymatch treats the values as equal `);

    // var1 is different from 2nd entry above, but var1 is ignored
    let request3 = FoundRequest.requestParamFactory("http://localhost/interface/main/about_page.php?timestamp=1584278800&var1=9&var2=2&var3=2&var4=2","GET");
    assert(appData.containsEquivURL(request3)===true , `Should be EQUIV (i.e., true) b/c value should be ignored and keymatch treats the values as equal `);

    // var1 is different from 2nd entry above and so is var2, but var1 is ignored, thus this should be NOT EQUIV
    let request4 = FoundRequest.requestParamFactory("http://localhost/interface/main/about_page.php?timestamp=1584278800&var1=9&var2=9&var3=2&var4=2","GET");
    assert(appData.containsEquivURL(request4)===false, `Should NOT be EQUIV (i.e., false) b/c var2 value does not match and it is not an ignored value `);

}


function requiredValues_test(){

    console.log("================ TESTING ignoreValues");
    let appData = new iser.AppData(false, BASE_APPDIR);
    appData.setUrlUniqueIfValueUnique(["mode","mode2","mode3"]);
    appData.addRequest(FoundRequest.requestParamFactory("http://localhost/interface/main/about_page.php?mode=login&var1=1&var2=1&var3=1&var4=1", "POST", ""));
    appData.addRequest(FoundRequest.requestParamFactory("http://localhost/interface/main/about_page.php?mode=admin&var1=2&var2=2&var3=2&var4=2", "POST", ""));
    appData.addRequest(FoundRequest.requestParamFactory("http://localhost/interface/main/about_page.php?mode=wreckit&var1=3&var2=3&var3=3&var4=3", "POST", ""));
    appData.addRequest(FoundRequest.requestParamFactory("http://localhost/interface/main/about_page.php?mode=diamond&var1=4&var2=4&var3=4&var4=4", "POST", ""));
    appData.addRequest(FoundRequest.requestParamFactory("http://localhost/interface/main/page2.php?mode1=diamond1", "POST", ""));
    appData.addRequest(FoundRequest.requestParamFactory("http://localhost/interface/main/page2.php?mode1=diamond2", "POST", ""));
    appData.addRequest(FoundRequest.requestParamFactory("http://localhost/interface/main/page2.php?mode1=diamond3", "POST", ""));
    appData.addRequest(FoundRequest.requestParamFactory("http://localhost/interface/main/page2.php?mode1=diamond4", "POST", ""));
    appData.addRequest(FoundRequest.requestParamFactory("http://localhost/interface/main/page3.php?mode2=gold1", "POST", ""));
    appData.addRequest(FoundRequest.requestParamFactory("http://localhost/interface/main/page3.php?mode2=gold2", "POST", ""));
    appData.addRequest(FoundRequest.requestParamFactory("http://localhost/interface/main/page3.php?mode2=gold3", "POST", ""));
    appData.addRequest(FoundRequest.requestParamFactory("http://localhost/interface/main/page3.php?mode2=gold4", "POST", ""));

    appData.fuzzyMatchEquivPercent = .50;
    appData.minFuzzyScore = .99;
    appData.maxKeyMatches = 3;
    // ignoreValue (e.g., a timestamp, so a keymatch is the same has the values matching)
    let request1 =FoundRequest.requestParamFactory( "http://localhost/interface/main/about_page.php?mode=monstercar&var1=1&var2=1&var3=1&var4=1","GET");
    assert(appData.containsEquivURL(request1)===false, `Should NOT be EQUIV (i.e., true)  b/c mode is not currently a found request`);

    request1 =FoundRequest.requestParamFactory( "http://localhost/interface/main/about_page.php?mode=login&var1=1&var2=1&var3=2&var4=1","GET");
    assert(appData.containsEquivURL(request1)===true, `Should be EQUIV (i.e., true)  b/c mode IS in a found request`);

    // this matches a value already entered
    let request2 = FoundRequest.requestParamFactory("http://localhost/interface/main/about_page.php?timestamp=9999999999&var1=1&var2=1&var3=1&var4=1","GET");
    assert(appData.containsEquivURL(request2)===true, `Should be EQUIV (i.e., true) b/c value should be ignored and keymatch treats the values as equal `);

    // mode1 is different from all entries above, but the keyMatch will ignore this value and consider it equiv
    let request3 = FoundRequest.requestParamFactory("http://localhost/interface/main/page2.php?mode1=diamond5","GET");
    assert(appData.containsEquivURL(request3)===true , `Should be EQUIV (i.e., true) b/c value should be ignored and keymatch treats the values as equal `);



    // mode2 is different from all prior entires, even though 4 others use mode 2 it will still be not equiv b/c mode2 is important key and just it's single difference in a URL makes
    // the entire URL unique
    let request4 = FoundRequest.requestParamFactory("http://localhost/interface/main/page3.php?mode2=gold5","GET");
    assert(appData.containsEquivURL(request4)===false, `Should NOT be EQUIV (i.e., false) b/c a url with mode2 is unique if mode2's value is unique `);

    // mode2 is same as value above and entire url matches
    // the entire URL unique
    let request5 = FoundRequest.requestParamFactory("http://localhost/interface/main/page3.php?mode2=gold4" ,"GET");
    assert(appData.containsEquivURL(request5)===true, `Should be EQUIV (i.e., true) b/c this exact URL exists`);
    let cnt = 1;
    appData.addRequest(FoundRequest.requestParamFactory(`http://localhost/interface/main/page4.php?mode3=gold1&v1=${cnt}&v2=${cnt}&v3=${cnt}&v4=${cnt}&v5=${cnt}&v6=${cnt}&v7=${cnt}&v8=${cnt}`, "POST", ""));
    appData.addRequest(FoundRequest.requestParamFactory(`http://localhost/interface/main/page4.php?mode3=gold2&v1=${cnt}&v2=${cnt}&v3=${cnt}&v4=${cnt}&v5=${cnt}&v6=${cnt}&v7=${cnt}&v8=${cnt}`, "POST", ""));
    appData.addRequest(FoundRequest.requestParamFactory(`http://localhost/interface/main/page4.php?mode3=gold3&v1=${cnt}&v2=${cnt}&v3=${cnt}&v4=${cnt}&v5=${cnt}&v6=${cnt}&v7=${cnt}&v8=${cnt}`, "POST", ""));
    appData.addRequest(FoundRequest.requestParamFactory(`http://localhost/interface/main/page4.php?mode3=gold4&v1=${cnt}&v2=${cnt}&v3=${cnt}&v4=${cnt}&v5=${cnt}&v6=${cnt}&v7=${cnt}&v8=${cnt}`, "POST", ""));
    // mode3 is different but all the other vars in the URL are the same
    // the entire URL unique
    cnt=1;
    let request6 = FoundRequest.requestParamFactory(`http://localhost/interface/main/page4.php?mode3=gold4&v1=${cnt}&v2=9&v3=${cnt}&v4=${cnt}&v5=${cnt}&v6=${cnt}&v7=${cnt}&v8=${cnt}`,"GET");
    assert(appData.containsEquivURL(request6)===true, `Should be EQUIV (i.e., true) b/c gold4 is a match to a prior and the 8 out of 9 match`);

    cnt=1;
    let request7 =FoundRequest.requestParamFactory(`http://localhost/interface/main/page4.php?mode3=goldUNIQUE&v1=${cnt}&v2=${cnt}&v3=${cnt}&v4=${cnt}&v5=${cnt}&v6=${cnt}&v7=${cnt}&v8=${cnt}`,"GET");
    assert(appData.containsEquivURL(request7)===false, `Should not be EQUIV (i.e., false) b/c mode3s is different which makes the entire URL different `);

}

function requiredValuesReal_test(){
    //http://localhost/ucp.php?mode=login&sid=cead403a87de26c57b1be92d1178da83
    console.log("================ TESTING urlUniqueIfValueUnique");
    let appData = new iser.AppData(false, BASE_APPDIR);
    appData.setIgnoreValues(["sid","_","confirm_key"]);
    appData.setUrlUniqueIfValueUnique(["mode"]);
    appData.addRequest(FoundRequest.requestParamFactory("http://localhost/search.php?sid=31bef860bd3992e2a61b0dcec4917e75&keywords=&fid[0]=2&sid=31bef860bd3992e2a61b0dcec4917e75", "GET", ""));
    // appData.addRequest("http://localhost/interface/main/about_page.php?mode=login&var1=2&var2=2&var3=2&var4=2", "POST", "");
    // appData.addRequest("http://localhost/interface/main/about_page.php?mode=wreckit&var1=3&var2=3&var3=3&var4=3", "POST", "");
    // appData.addRequest("http://localhost/interface/main/about_page.php?mode=diamond&var1=4&var2=4&var3=4&var4=4", "POST", "");
    console.log(appData.requestsFound);
    let request7 = FoundRequest.requestParamFactory(`http://localhost/search.php?sid=eee827ac9e105bc47fa8f5acc056396a&keywords=&fid[0]=2&sid=eee827ac9e105bc47fa8f5acc056396a&`, "GET");
    assert(appData.containsEquivURL(request7)===true, `Should be EQUIV (i.e., true) b/c mode=login and sid is an ignored value `);

    request7 = FoundRequest.requestParamFactory(`http://localhost/search.php?sid=f7c14fe1eb7fd0d47f344778ba414547&keywords=&fid[0]=2&sid=f7c14fe1eb7fd0d47f344778ba414547&`,"GET");
    assert(appData.containsEquivURL(request7)===true, `Should be EQUIV (i.e., true) b/c mode=login and sid is an ignored value `);
}

// gremTracker_test();
// addQueryParam_test();
containsEquivURLHASH_test();

// containsEquivURLNoname_test();
// containsEquivURL_test();
// containsEquivURL_date_test();
// duplicateParams_tests();
// addValidURLS_test();
// keyMatch_test();
//
// fuzzyValueMatch_test();
// ignoreValues_test();
// requiredValues_test();
// requiredValuesReal_test();
//
