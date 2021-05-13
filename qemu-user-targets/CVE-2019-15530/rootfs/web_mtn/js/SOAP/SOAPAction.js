/*!
 * SOAPAction core v1.0.0
 *
 * Copyright 2013 jumi
 * Released under the GPL license
 */
if (!Object.keys) Object.keys = function(o) {
    if (o !== Object(o))
        throw new TypeError('Object.keys called on a non-object');
    var k=[],p;
    for (p in o) if (Object.prototype.hasOwnProperty.call(o,p)) k.push(p);
    return k;
}
/*var DONT_ENUM =  "propertyIsEnumerable,isPrototypeOf,hasOwnProperty,toLocaleString,toString,valueOf,constructor".split(","),
    hasOwn = ({}).hasOwnProperty;
for (var i in {
    toString: 1
}){
    DONT_ENUM = false;
}


Object.keys = Object.keys || function(obj){//ecma262v5 15.2.3.14
        var result = [];
        for(var key in obj ) if(hasOwn.call(obj,key)){
            result.push(key) ;
        }
        if(DONT_ENUM && obj){
            for(var i = 0 ;key = DONT_ENUM[i++]; ){
                if(hasOwn.call(obj,key)){
                    result.push(key);
                }
            }
        }
        return result;
    };*/
var SOAP_NAMESPACE="http://purenetworks.com/HNAP1/";function SOAPAction(){this.list=[]}SOAPAction.prototype={timeout:0,list:null,multipleSoap:"",SOAPResponse:function(c){this.name=c;this.outputObj=[]},AddSOAP:function(c,b){var a=null,d;for(d in this.list)if(this.list[d].name==c){a=this.list[d];break}null==a?(a=new this.SOAPResponse(c),a.outputObj.push(b),this.list.push(a)):a.outputObj.push(b)}};
SOAPAction.prototype.parseHNAP=function(c,b,a){var d=b.find(c+"Result").text().toUpperCase();if("ERROR"==d)return!1;if(null!=a){var g=function(a,c){if(0==Object.keys(a).length)return c.children().end(),a=[],c.children().each(function(){a.push($(this).text())}),a;for(var b in a)if(1==$.isArray(a[b]))if(0==c.find(b).length)a[b].splice(0,1);else{var d=!1;c.find(b).each(function(){var c=$(this),e={};0<a[b].length&&(e=a[b].slice(0,1),e=JSON.parse(JSON.stringify(e[0])));e=g(e,c);0==a[b].length?(d=!0,a[b]=
e):a[b].push(e)});0==d&&a[b].splice(0,1)}else c.children().each(function(){var c=$(this);if(c[0].tagName.toLowerCase()==b.toLowerCase()){if("object"==$.type(a[b])){if(1==$.isArray(a[b]))return!0;a[b]=g(a[b],c)}else a[b]=c.text();return!1}});return a};a=g(a,b)}else a={};a[c+"Result"]=d;return a};SOAPAction.prototype.SetMultipleSOAP=function(c,b,a){this.multipleSoap+=this.createActionBody(c,b);this.AddSOAP(c,a)};
SOAPAction.prototype.SendMultipleSOAPAction=function(c){var b=$.Deferred(),a=this,d;d='<?xml version="1.0" encoding="utf-8"?><soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/"><soap:Body>'+("<"+c+' xmlns="http://purenetworks.com/HNAP1/">')+this.multipleSoap;d=d+("</"+c+">")+"</soap:Body></soap:Envelope>";var g='"'+SOAP_NAMESPACE+c+'"',f=$.cookie("PrivateKey");null==f&&
(f="withoutloginkey");var e= $.cookie("Cookie");$.cookie("uid",e,{expires:1,path:"/"});e=(new Date).getTime();e=Math.floor(e/1E3)%2E9;e=e.toString();f=hex_hmac_md5(f,e+g);f=f.toUpperCase()+" "+e;$.ajax({url:"/HNAP1/",type:"POST",contentType:"text/xml; charset=utf-8",headers:{SOAPAction:g,HNAP_AUTH:f},timeout:a.timeout,data:d,success:function(e){
    if($.browser.msie&&(parseInt($.browser.version)<9)){var xml = new ActiveXObject("Microsoft.XMLDOM");xml.async = false;xml.loadXML(e);e = xml;}if("ERROR"==$(e).find(c+"Result").text().toUpperCase())return b.reject();for(var d in a.list)$(e).find(a.list[d].name+"Response").each(function(b){a.parseHNAP(a.list[d].name,
$(this),a.list[d].outputObj[b])});b.resolve(a.list,"OK")},error:function(a,c,d){b.reject()}});this.multipleSoap="";return b.promise()};
SOAPAction.prototype.createValueBody=function(c){var b="",a;for(a in c)if("_"!=a.charAt(0)&&"push"!=a)if(1==$.isArray(c[a])){var d=!1,g="",f;for(f in c[a]){var e=c[a][f];"object"==typeof e?(b+="<"+a+">",b+=this.createValueBody(e),b+="</"+a+">"):(d=!0,g+="<string>"+e+"</string>")}d&&(b+="<"+a+">"+g+"</"+a+">")}else"string"!=typeof a||0<a.length?(b+="<"+a+">",b="object"==$.type(c[a])&&null!=c[a]?b+this.createValueBody(c[a]):b+c[a],b+="</"+a+">"):b+="<"+a+"/>";return b};
SOAPAction.prototype.createActionBody=function(c,b){var a="";"object"==typeof b&&null!=b?(a+="<"+c+' xmlns="'+SOAP_NAMESPACE+'">',a+=this.createValueBody(b),a+="</"+c+">"):a+="<"+c+' xmlns="'+SOAP_NAMESPACE+'" />';return a};SOAPAction.prototype.createSOAP=function(c){return'<?xml version="1.0" encoding="utf-8"?><soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/"><soap:Body>'+c+"</soap:Body></soap:Envelope>"};
SOAPAction.prototype.sendSOAPAction=function(c,b,a){var d=$.Deferred(),g=this,f=g.createActionBody(c,b),f=g.createSOAP(f),e='"'+SOAP_NAMESPACE+c+'"';b={SOAPAction:e};var k=$.cookie("PrivateKey");if(null!=k){var h=$.cookie("Cookie");$.cookie("uid",h,{expires:1,path:"/"});h=(new Date).getTime();h=Math.floor(h/1E3)%2E9;h=h.toString();e=hex_hmac_md5(k,h+e);e=e.toUpperCase()+" "+h;b.HNAP_AUTH=e}$.ajax({url:"/HNAP1/",type:"POST",contentType:"text/xml; charset=utf-8",headers:b,
timeout:g.timeout,data:f,success:function(b){if($.browser.msie&&(parseInt($.browser.version)<9)){var xml = new ActiveXObject("Microsoft.XMLDOM");xml.async = false;xml.loadXML(b);b = xml;}b=g.parseHNAP(c,$(b).find(c+"Response"),a);0==b?d.reject():d.resolve(b)},error:function(a,b,c){d.reject()}});return d.promise()};SOAPAction.prototype.copyObject=function(c,b){for(var a in c)for(var d in b)if(a==d)if("object"==$.type(c[a]))this.copyObject(c[a],b[d]);else{c[a]=b[d];break}};