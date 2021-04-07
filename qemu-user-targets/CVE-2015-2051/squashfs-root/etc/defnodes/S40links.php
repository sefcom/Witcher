<?
/* For BWC, BWC-2's flag and enable will use BWC-1's config */
setattr("/bwc/entry:2/flag" ,	"link","/bwc/entry:1/flag");
setattr("/bwc/entry:2/enable" ,	"link","/bwc/entry:1/enable");
setattr("/bwc/entry:2/rules" ,	"link","/bwc/entry:1/rules");
?>
