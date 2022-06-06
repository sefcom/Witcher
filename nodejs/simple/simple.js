
const test = require("test-request");
const express = require('express');

console.log("    \033[1;35mStarting simple.js \033[0m");

test.test();

// var app = express();
// const port = 5143;
//console.log(require.resolve('express'));

function doStuff(){

    var readline = require('readline');
    var rl = readline.createInterface({
      input: process.stdin,
      output: process.stdout,
      terminal: false
    });

    rl.on('line', function(line){
	var a = parseInt(line, 10); //Math.round(Math.random() * 10);
	console.log('\t\033[1;33mWelcome to My Console,\033[0m');
	
	var b = 8;
	a++;

        console.log(line);
	if (a >= b){
	a--;
	for (var x=0; x < 3; x++){
	    console.log("\t\033[1;33m\tit was greater\033[0m", x);
	}
    } else {
	b++;
	for (var x=0; x < 3; x++){
            console.log("\t\033[1;33m\tit was lesser\033[0m");
	}
    }

    var c = 5;
    if (a >= c){
	console.log("\t\033[1;33m\tsecond test, a was greater than or equal to c\033[0m");
    } else {
	console.log("\t\033[1;33m\tsecond test, a was less than c\033[0m");
    }

    if (a >= 9){
	var d = 9;
	if (d > 9){
            console.log('\t\033[1;33mnevermore\033[0m');
	} 
    }

    switch(a){
	
    case 5:
        console.log('\t\033[1;33mjackpot\033[0m');
        break;
    case 6:
    case 7:
    case 8:
    case 9:
    case 10:
        console.log('\t\033[1;33ma is too great\033[0m');
        break;
    default:
        console.log('\t\033[1;33mthey loose!\033[0m');

    }

    // setTimeout(function() {
    //     console.log('Blah blah blah blah extra-blah\033[0m');
    // }, 30000);

    console.log("\t\033[1;33mprintme\033[0m", a, b, c);

//         process.exit();
    })
    //console.log("Exiting on my own");
} // end doStuff()


// function doit() {
//     const { HTTPParser } = process.binding("http_parser");
//     const REQUEST = HTTPParser.REQUEST;
//     const kOnHeaders = HTTPParser.kOnHeaders | 0;
//     const kOnHeadersComplete = HTTPParser.kOnHeadersComplete | 0;
//     const kOnBody = HTTPParser.kOnBody | 0;
//     const kOnMessageComplete = HTTPParser.kOnMessageComplete | 0;
//     const CRLF = '\r\n';
//
//     function processHeader(header, n) {
//         const parser = newParser(REQUEST);
//
//         for (let i = 0; i < n; i++) {
//           parser.execute(header, 0, header.length);
//     //      parser.initialize(REQUEST, {});
//             console.log(parser);
//         }
//         return parser;
//     }
//
//     function newParser(type) {
//         const parser = new HTTPParser();
//         parser.initialize(type, {});
//
//         parser.headers = [];
//
//         parser[kOnHeaders] = function() { };
//         parser[kOnHeadersComplete] = function() { };
//         parser[kOnBody] = function() { };
//         parser[kOnMessageComplete] = function() { };
//
//         return parser;
//     }
//     console.log("here");
//     let header = `GET /hello HTTP/1.1${CRLF}Content-Type: text/plain${CRLF}`;
//     len = 3
//     for (let i = 0; i < len; i++) {
//         header += `X-Filler${i}: ${Math.random().toString(36).substr(2)}${CRLF}`;
//     }
//     header += CRLF;
//
//     var parser = processHeader(Buffer.from(header), len);
//     console.log(header);
//     console.log(parser.getCurrentBuffer());
//     // console.log(parser.getCurrentBuffer());
//     //var req = parser.execute(header, 0, header.length);
//     // console.log(parser.REQUEST);
//     // res = {};
//     //app.handle(express.req, express.res, function(){console.log("handling request...")});
//
// }

//doStuff();
//doit();

// var readline = require('readline');
// var rl = readline.createInterface({
//   input: process.stdin,
//   output: process.stdout,
//   terminal: false
// });
//
// rl.on('line', function(line){
//
//    app.handle(req, res, )
//
// });
// console.timeEnd('doStuff');
// console.log("    \033[1;35mFinishing simple.js \033[0m\n\n");

//app.get('/', (req, res) => res.send('Hello World!'))

// app.all('/node/:id', function (req, res, next) {
//   console.log('ID:', req.params.id);
//   console.log('req: ', req.query.url_p);
//   console.log('req2: ', req.query.url_not_really_there);
//   res.send("<html>hi</html>");
//   res.end()
//   next()
// });

//var router = require('express').Router();
// module.exports = function() {
//   router.get('/node', function(req, res, next){
//     res.send("this is the internal one with Router");
//   });
//   return router;
// };
// function do_request(){
//
//     var readline = require('readline');
//     var rl = readline.createInterface({
//       input: process.stdin,
//       output: process.stdout,
//       terminal: false
//     });
//     function process_request (line){
//         http = require("http");
//         var items = line.split("\x00");
//         var cookies = "";
//         var url_params = "";
//         var post_variables = "";
//
//         if (items.length > 0){
//             cookies = items[0];
//         }
//         if (items.length > 1){
//             url_params = items[1];
//         }
//         if (items.length > 2){
//             post_variables = items[2];
//         }
//         console.log("\tCOOKIES   :", cookies);
//         console.log("\tURL PARAMS:", url_params);
//         console.log("\tPOST VARS :", post_variables);
//         http.get({hostname:"localhost", port:port, path:"/node/1?" + url_params, headers:{
//                 "HTTP_COOKIE": cookies,
//
//             }}
//             , (res)=> {
//             // using this so that only one request processed for AFL.
//             process.exit();
//         });
//     }
//     rl.on('line', process_request);
//
// }
function nada(){
    console.log("nana bobo");
}

//app.listen(port, nada);



