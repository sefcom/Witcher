
<html>
  <head>
    <link rel="stylesheet" href="/css/blueprint/screen.css" type="text/css" media="screen, projection">
    <link rel="stylesheet" href="/css/blueprint/print.css" type="text/css" media="print">
    <!--[if IE]><link rel="stylesheet" href="/css/blueprint/ie.css" type="text/css" media="screen, projection"><![endif]-->
    <link rel="stylesheet" href="/css/stylings.css" type="text/css" media="screen">
    <title>WackoPicko.com</title>
  </head>
  <body>
    <div class="container " style="border: 2px solid #5c95cf;">
      <div class="column span-24 first last">
	<h1 id="title"><a href="/">WackoPicko.com</a></h1>
      </div>
      <div id="menu">
	<div class="column prepend-1 span-14 first">
	  <ul class="menu">
	    <li class="current"><a href="/users/home.php"><span>Home</span></a></li>
	    <li class=""><a href="/pictures/upload.php"><span>Upload</span></a></li>
	    <li class=""><a href="/pictures/recent.php"><span>Recent</span></a></li>
            <li class=""><a href="/guestbook.php"><span>Guestbook</span></a></li>

      	  </ul>
	</div>
	<div class="column prepend-1 span-7 first last">
	  <ul class="menu top_login" >
      	    <li><a href="/users/login.php"><Span>Login</span></a></li>
      	  </ul>
	</div>
      </div>



      <div class="column span-24 first last" id="search_bar_blue">
	<div class="column prepend-17 span-7 first last" id="search_box">
	  <form action="/pictures/search.php" method="get" style="display:inline;">
	    <input id="query2" name="query" size="15" style="padding: 2px; font-size: 16px; text-decoration:none;border:none;vertical-align:middle;" type="text" value=""/>
	    <input src="/images/search_button_white.gif" type="image" style="border: 0pt none ; position: relative; top: 0px;vertical-align:middle;margin-left: 1em;" />
	  </form>
	</div>
      </div>
   

<div class="column prepend-1 span-24 first last">
  <h2>Welcome to WackoPicko</h2>
  <p>
    On WackoPicko, you can share all your crazy pics with your friends. <br />
    But that's not all, you can also buy the rights to the high quality <br />
    version of someone's pictures. WackoPicko is fun for the whole family.
  </p>

  <h3>New Here?</h3>
  <p>
    <h4><a href="/users/register.php">Create an account</a></h4>
  </p>
  <p>
    <h4><a href="/users/sample.php?userid=1">Check out a sample user!</a></h4>
  </p>
  <p>
    <h4><a href="/calendar.php">What is going on today?</a></h4>
  </p>
  <p>
    <h4>Or you can test to see if WackoPicko can handle a file:</h4> <br />
  <script>
    document.write('<form enctype="multipart/form-data" action="/pic' + 'check' + '.php" method="POST"><input type="hidden" name="MAX_FILE_SIZE" value="30000" />Check this file: <input name="userfile" type="file" /> <br />With this name: <input name="name" type="text" /> <br /> <br /><input type="submit" value="Send File" /><br /> </form>');
  </script>
  </p>
</div>


       <div class="column span-24 first last" id="footer" >
	<ul>
	  <li><a href="/">Home</a> |</li>
          <li><a href="/admin/index.php?page=login">Admin</a> |</li>
	  <li><a href="mailto:contact@wackopicko.com">Contact</a> |</li>
	  <li><a href="/tos.php">Terms of Service</a></li>
	</ul>
      </div>
    </div>
  </body>
</html>
   