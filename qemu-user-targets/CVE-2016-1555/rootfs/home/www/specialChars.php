<?php 
			$str = str_replace('\\:',':',$str);
			$str = str_replace('\\ ',' ',$str);
			$str = str_replace('\\\\','\\',$str);
			$str = str_replace("\\\n","\n", $str);
			$str= str_replace("\\'","'",$str);
			$str= str_replace('\\"','"',$str);
?>