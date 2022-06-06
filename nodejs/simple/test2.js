console.log("\033[1;35m[WC][test1.js] \tStarting test1.js \033[0m");
const express = require('express');
var bodyParser = require('body-parser');
console.log("\033[1;35m[WC][test1.js]\t\tBefore app = express()\033[0m");
var app = express();
console.log("\033[1;35m[WC][test1.js]\t\tAfter app = express()\033[0m");

app.use(bodyParser.json()); // support json encoded bodies
app.use(bodyParser.urlencoded({ extended: true })); // support encoded bodies

const server = require('http').Server(app);

const port = 5143;

function nada(){
    console.log("nana bobo");
}

app.all('/narn/:id', function (req, res, next) {
  console.log('ID:', req.params.id);
  console.log('req: ', req.query.url_p);
  console.log('req2: ', req.query.url_not_really_there);
  if (req.hasOwnProperty("body")){
      console.log("\tpost_var=", req.body.post_var);
  } else {
      console.log("No body recv'd");
  }

  res.send("<html>hi</html>");
  res.end()
  next()
});

console.log("[WC][test1.js]\tAfter app.all()");

function do_request(){
    console.log("[WC][test1.js] Starting do_request");
    var readline = require('readline');
    console.log("    set readline");
    var rl = readline.createInterface({
      input: process.stdin,
      output: process.stdout,
      terminal: false
    });
    function process_request (line){
        console.log("    in  process_request");
        http = require("http");
        var items = line.split("\x00");
        var cookies = "";
        var url_params = "";
        var post_variables = "";

        if (items.length > 0){
            cookies = items[0];
        }
        if (items.length > 1){
            url_params = items[1];
        }
        if (items.length > 2){
            post_variables = items[2];
        }
        console.log("[WC][test1.js]\tCOOKIES   :", cookies);
        console.log("[WC][test1.js]\tURL PARAMS:", url_params);
        console.log("[WC][test1.js]\tPOST VARS :", post_variables);
        http.get({hostname:"localhost", port:port, path:"/node/1?" + url_params, headers:{
                "HTTP_COOKIE": cookies,
            }}
            , (res)=> {
            // using this so that only one request processed for AFL.
                console.log("    \033[1;35mFINISHING test1.js \033[0m");
                process.exit();
        });
    }

    rl.on('line', process_request);

}

function test_request(){
    console.log("\033[1;35mINSIDE TEST REQUEST!!!\033[0m");
}

console.log("\033[1;36m[WC][WC][test1.js]\tBefore app.listen()");
//app.listen(port, () => console.log(`Example app listening on port ${port}!`));

//app.listen(port);

server.listen(port, () => {
    console.log(`Server listening on port ${port}`)
    //require('./lib/startup/registerWebsocketEvents')(server)
    console.log("RTG");
});

console.log("[WC][test1.js]\tAFTER app.listen()\033[0m");


