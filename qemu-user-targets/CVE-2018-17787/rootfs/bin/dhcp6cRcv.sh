#!/bin/sh
#When dhcp6c reveive call the script

echo "=============dhcpv6c get====================="
optionStr=
test "$new_sip_servers" && optionStr="new_sip_servers=$new_sip_servers	"
test "$new_sip_name" && optionStr=$optionStr"new_sip_name=$new_sip_name	"
test "$new_domain_name_servers" && optionStr=$optionStr"new_domain_name_servers=$new_domain_name_servers	"
test "$new_domain_name" && optionStr=$optionStr"new_domain_name=$new_domain_name	"
test "$new_ntp_servers" && optionStr=$optionStr"new_ntp_servers=$new_ntp_servers	"
test "$new_nis_servers" && optionStr=$optionStr"new_nis_servers=$new_nis_servers	"
test "$new_nis_name" && optionStr=$optionStr"new_nis_name=$new_nis_name	"
test "$new_nisp_servers" && optionStr=$optionStr"new_nisp_servers=$new_nisp_servers	"
test "$new_nisp_name" && optionStr=$optionStr"new_nisp_name=$new_nisp_name	"
test "$new_bcmcs_servers" && optionStr=$optionStr"new_bcmcs_servers=$new_bcmcs_servers	"
test "$new_bcmcs_name" && optionStr=$optionStr"new_bcmcs_name=$new_bcmcs_name	"
echo "optionStr=$optionStr"
sysconf dhcp6c_get $optionStr 
