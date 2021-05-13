// Ticker Messages ( HTML Tags supported)
var qiksearch_ticker_text = new Array ("Realtek", "WLAN Access Point");

// Ticker Message URLs
var qiksearch_ticker_URL = new Array ("http://www.realtek.com", "http://www.realtek.com");

// Ticker Message URLs' Target (1 for NEW WINDOW, 0 for SAME WINDOW)
var qiksearch_ticker_target = new Array ("1", "1");

var timeOutVal=200; // Delay in milliseconds


//--------------------------------------------------------------------------------------------

//-----------------------------DO-NOT-MODIFY-BELOW-THIS---------------------------------------

var qiksearch_tickerObj;

// Setting qiksearch_tickerObj depending on Browser
function setTickerObj()
{ 
  qiksearch_tickerObj=document.getElementById("topHeaderId"); 
}

var def_10='A',def_11='B',def_12='C',def_13='D',def_14='E',def_15='F';
var colorVal=15;
var div_count=0;

// Fading Color code Generating function
function qiksearch_fade_desat(getColorIntVal)
{
 var returnVal;
 if(getColorIntVal>=10)
 {
  for(var i=0; i<=15; i++)
  {
   if((getColorIntVal==i))
   {
    returnVal = eval('def_' + i);
   }
  }
 }
 else
 {
  returnVal=getColorIntVal;
 }
 return(returnVal);
} 

// Main
function writeDiv()
{
	setTickerObj();
	if(!qiksearch_tickerObj)
		return;
  qiksearch_tickerObj.innerHTML= '<font face="verdana,arial,helvetica" size="2" color="#' +  joinColor(qiksearch_fade_desat(colorVal)) + '"><b>' + qiksearch_ticker_text[div_count] +  '</b>' ;
 
 if((colorVal>0)   &&  (colorVal!=0))
 {
  colorVal--;
 }
 else
 {
  colorVal=15;
  if(div_count<qiksearch_ticker_text.length)
  {
   div_count++;
  }
  if(div_count==qiksearch_ticker_text.length)
  {
   setTimeout("resetAll()",timeOutVal);
   setTimeout("writeDiv()",timeOutVal);
  }
 }

 if(div_count<qiksearch_ticker_text.length)
 {
  setTimeout("writeDiv()",timeOutVal);
 }
}

// Generating Final Hex Color
function joinColor(getColor)
{
 return (getColor + '0' + getColor + '0' + getColor + '0');
}

// Reset
function resetAll()
{
 div_count=0;
 colorVal=15;
}

// URL Navigation function
function goURL()
{
 if(qiksearch_ticker_target[div_count]=="0")
 {
  location.href=qiksearch_ticker_URL[div_count];
 }
 else
 {
  if(qiksearch_ticker_target[div_count]=="1")
  {
   window.open(qiksearch_ticker_URL[div_count]);
  }
 }
}

// Setting Delay on MouseOver and MouseOut
var temp_timeOutVal=timeOutVal;
function delay_timeOutVal()
{
 timeOutVal=100000000000000;
 setTimeout("writeDiv()",timeOutVal);
}

function resume_timeOutVal()
{
 timeOutVal=temp_timeOutVal;
 setTimeout("writeDiv()",timeOutVal);
}

window.onload=writeDiv;
