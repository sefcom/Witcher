#
# main idea:
# for wlan0 and wlan1 
#   for all paths
#     rx_gain = flash get HW_WLAN0/1_TX_POWER_5G_HT4_1S_A/B/C/D
#     iwpriv wlan0/1 set_mib pwrlevel5GHT40_1S_A/B/C/D=rx_gain
#

GETMIB="flash get $1"

for wlan in 0 1;do
	for path in  A B C D;do
		#
		# flash get HW_WLAN0/1_TX_POWER_5G_HT40_1S_A/B/C/D
		#
		eval `\$GETMIB HW_WLAN${wlan}_TX_POWER_5G_HT40_1S_$path`
		# debug eval echo HW_WLAN${wlan}_TX_POWER_5G_HT40_1S_$path=\$HW_WLAN${wlan}_TX_POWER_5G_HT40_1S_$path
		#
		# iwpriv wlan0/1 set_mib pwrlevel5GHT40_1S_A/B/C/D=xxx
		#
		eval iwpriv wlan$wlan set_mib pwrlevel5GHT40_1S_$path=\$HW_WLAN${wlan}_TX_POWER_5G_HT40_1S_$path
	done
done

#
# rx_gain_value_A/B/C/D = first 14 digits for 7 channel groups(2G and 5G group1~6)
#eval rx_gain_value_$path=\${HW_WLAN${wlan}_TX_POWER_5G_HT40_1S_$path:0:14}
#eval echo mp_rx_gain_value_$wlan=\$mp_rx_gain_value_$wlan
# mp_rx_gain_value combine all paths' rx_gain_value_A/B/C/D
#eval mp_rx_gain_value_$wlan=\$mp_rx_gain_value_$wlan\$rx_gain_value_$path
# iwpriv wlan0/1 set_mib mp_rx_gain=mp_rx_gain_vlaue_0/1
#eval iwpriv wlan$wlan set_mib mp_rx_gain=\$mp_rx_gain_value_$wlan
#eval iwpriv wlan$wlan set_mib pwrlevel5GHT40_1S_$path=\$mp_rx_gain_value_$wlan
#iwpriv wlan$wlan get_mib mp_rx_gain
#
