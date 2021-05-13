#!/bin/sh

#Functions whole startup scripts need.
##function for bold and no new line printing.

ncecho()
{
	echo ""
	echo -e -n "\033[1;29m$*\033[1;0m"
}

#will echo with new line and supports colours.
cecho()
{
	case "$1" in
		red)
			shift
			echo -e "\033[1;31m$*\033[1;0m"
			;;
		green)
			shift
			echo -e "\033[1;32m$*\033[1;0m"
			;;
		yellow)
			shift
			echo -e "\033[1;33m$*\033[1;0m"
			;;
		bblink)
			shift
			echo -e "\033[5m\033[1;29m$*\033[0m"
			;;
		rblink)
			shift
			echo -e "\033[5m\033[1;31m$*\033[0m"
			;;
		gblink)
			shift
			echo -e "\033[5m\033[1;32m$*\033[0m"
			;;
		yblink)
			shift
			echo -e "\033[5m\033[1;33m$*\033[0m"
			;;
		*)
			shift
			echo $*
			;;
	esac
}
