<?php
@include('sessionCheck.inc'); 
if ((time()-@filemtime('/tmp/sessionid'))>300)
{
echo "<script>window.opener.location.href = window.opener.location.href</script>";  
echo "<script>window.location='redirect.html'</script>";  
}
?>
<html><head><meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1'/>
<link href='include/css/default.css' rel='stylesheet' media='screen'/>
<link href='include/css/style.css' rel='stylesheet' media='screen'/>
<link href='include/css/layout.css' rel='stylesheet' media='screen'/>
<script src="include/scripts/copyright.js" type="text/javascript"></script>
<style type="text/css">
<!--
.style2 {
	font-size: 90%
}
#tabstyle td
{
padding:5px;
}
-->
</style>
</head>
<title>NetGear</title>
<body height="100%">
<table class="tableStyle" height="100%" width="100%">
	<tr class="topAlign">
		<td valign="top" class="leftBodyNotch topAlign">&nbsp;</td>
		<td>
			<table class="tableStyle">
				<tr class="topAlign">
					<td class="leftNextBodyNotch"><img src="../images/clear.gif" width="11" height="16"/></td>
					<td class="middleBodyNotch spacer100Percent">&nbsp;</td>
					<td class="rightNextBodyNotch"><img src="../images/clear.gif" width="11"/></td>
				</tr>
			</table>
		</td>
		<td class="rightBodyNotch">&nbsp;</td>
	</tr>
	<tr>
		<td rowspan="2" class="leftEdge">&nbsp;</td>
		<td height="100%" align="center" valign="top" >
		<br>
		<table >
          <tbody>
		  <tr><td colspan="3" style="text-align:center"><p class="font15Bold">Enter your Product ID</p><br><br></td></tr>
            <tr> 
              <td class="DatablockLabel" style="width:100px">Product Id:&nbsp;</td>
              <td style="text-align:center" class="DatablockContent">
			<input type='text' id="model_no" onkeypress='return checkInput(event);' onchange='UpdateBoardData(this.value,product)'  value='' name='product' class='mainText' size='30'>
            </tr>
			<tr><td colspan="3" style="text-align:center">
			<br>
			<label>
			<BUTTON name="btBack" type="button" class="orange11" id="btBack" onClick="WriteData()" >Update</BUTTON>
			</label>
</td></tr>
          </tbody>
        </table>
		<br>
		<br>
		
	</form>
		</td>
		<td rowspan="2" class="rightEdge">&nbsp;</td>
	</tr>
	<tr>
		<td>
			<table class="tableStyle">
				<tr>
					<td colspan="3" class="topBottomDivider" id="cloneTd"><img src="../images/clear.gif" height="3"/></td>
				</tr>
				<tr>
					<td colspan="3" class="footerBody">
						<table >
              <tr> 
                <td >&nbsp;
				</td>
              </tr>
            </table>
		  </td>
		</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td class="leftEdgeFooter"><img src="../images/clear.gif" width="11" height="9"/></td>
		<td>
			<table class="tableStyle">
				<tr>
					<td class="leftBottomDivider"><img src="../images/clear.gif" width="11" height="9"/></td>
					<td class="middleBottomDivider spacer100Percent"><img src="../images/clear.gif" height="9"/></td>
					<td class="rightBottomDivider spacer1Percent"><img src="../images/clear.gif" height="9"/></td>
				</tr>
			</table>
		</td>
		<td class="rightEdgeFooter"><img src="../images/clear.gif" width="11" height="9"/></td>
	</tr>
	<tr>
		<td class="leftCopyrightFooter"><img src="../images/clear.gif" width="11" height="9"/></td>
		<td class="middleCopyrightDivider">
			<table class="blue10 tableStyle">
				<tr class="topAlign">
					<td>
					<script type='text/javascript'>getCopyright();</script>
				</tr>
			</table>
		</td>
		<td class="rightCopyrightFooter"><img src="../images/clear.gif" width="11" height="9"/></td>
	</tr>
</table>
</body>
</html>
<script type='text/javascript'>

WritemfgData={
  doxhr:function(url){
    var request;
    try{
      request = new XMLHttpRequest();
    }catch(error){
      try{
        request = new ActiveXObject("Microsoft.XMLHTTP");
      }catch(error){
        return true;
      }
    }
    request.open('get',url,true);
    request.onreadystatechange=function(){
      if(request.readyState == 1){
      }
      if(request.readyState == 4){
	  alert("Product ID Updated")
      }
    }
    request.send(null);
    return false;
  }
}

function WriteData()
{
	var ModelNumber=document.getElementById('model_no').value;
	if(ModelNumber!="")
		{
		var url="/mfgwrite.php?product="+ModelNumber+"&id="+Math.random(10000,99999);
		WritemfgData.doxhr(url)	
		}
}
</script>