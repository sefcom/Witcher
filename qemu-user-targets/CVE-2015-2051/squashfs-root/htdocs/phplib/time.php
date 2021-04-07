<?
function TIME_i18n_tzname($name)
{
/*  1 */if      ($name=="(GMT-12:00) International Date Line West")	return "(GMT-12:00) ".i18n("International Date Line (West)");
/*  2 */else if ($name=="(GMT-11:00) Midway Island, Samoa")			return "(GMT-11:00) ".i18n("Midway Island, Samoa");
/*  3 */else if ($name=="(GMT-10:00) Hawaii")						return "(GMT-10:00) ".i18n("Hawaii");
/*  4 */else if ($name=="(GMT-09:00) Alaska")						return "(GMT-09:00) ".i18n("Alaska");
/*  5 */else if ($name=="(GMT-08:00) Pacific Time (US & Canada); Tijuana") return "(GMT-08:00) ".i18n("Pacific Time (US & Canada); Tijuana");
/*  6 */else if ($name=="(GMT-07:00) Arizona")						return "(GMT-07:00) ".i18n("Arizona");
/*  7 */else if ($name=="(GMT-07:00) Chihuahua, La Paz, Mazatlan")	return "(GMT-07:00) ".i18n("Chihuahua, La Paz, Mazatlan");
/*  8 */else if ($name=="(GMT-07:00) Mountain Time (US & Canada)")	return "(GMT-07:00) ".i18n("Mountain Time (US & Canada)");
/*  9 */else if ($name=="(GMT-06:00) Central America")				return "(GMT-06:00) ".i18n("Central America");
/* 10 */else if ($name=="(GMT-06:00) Central Time (US & Canada)")	return "(GMT-06:00) ".i18n("Central Time (US & Canada)");
/* 11 */else if ($name=="(GMT-06:00) Guadalajara, Mexico City, Monterrey") return "(GMT-06:00) ".i18n("Guadalajara, Mexico City, Monterrey");
/* 12 */else if ($name=="(GMT-06:00) Saskatchewan")					return "(GMT-06:00) ".i18n("Saskatchewan");
/* 13 */else if ($name=="(GMT-05:00) Bogota, Lima, Quito")			return "(GMT-05:00) ".i18n("Bogota, Lima, Quito");
/* 14 */else if ($name=="(GMT-05:00) Eastern Time (US & Canada)")	return "(GMT-05:00) ".i18n("Eastern Time (US & Canada)");
/* 15 */else if ($name=="(GMT-05:00) Indiana (East)")				return "(GMT-05:00) ".i18n("Indiana (East)");
/* 16 */else if ($name=="(GMT-04:30) Caracas")				return "(GMT-04:30) ".i18n("Caracas");
/* 17 */else if ($name=="(GMT-04:00) Atlantic Time (Canada)")		return "(GMT-04:00) ".i18n("Atlantic Time (Canada)");
/* 18 */else if ($name=="(GMT-04:00) Santiago, La Paz")						return "(GMT-04:00) ".i18n("Santiago, La Paz");
/* 19 */else if ($name=="(GMT-03:30) Newfoundland")					return "(GMT-03:30) ".i18n("Newfoundland");
/* 20 */else if ($name=="(GMT-03:00) Brasilia")						return "(GMT-03:00) ".i18n("Brasilia");
/* 21 */else if ($name=="(GMT-03:00) Buenos Aires, Georgetown")		return "(GMT-03:00) ".i18n("Buenos Aires, Georgetown");
/* 22 */else if ($name=="(GMT-03:00) Greenland")					return "(GMT-03:00) ".i18n("Greenland");
/* 23 */else if ($name=="(GMT-02:00) Mid-Atlantic")					return "(GMT-02:00) ".i18n("Mid-Atlantic");
/* 24 */else if ($name=="(GMT-01:00) Azores")						return "(GMT-01:00) ".i18n("Azores");
/* 25 */else if ($name=="(GMT-01:00) Cape Verde Is.")				return "(GMT-01:00) ".i18n("Cape Verde Is.");
/* 26 */else if ($name=="(GMT) Casablanca, Monrovia")				return "(GMT) ".i18n("Casablanca, Monrovia");
/* 27 */else if ($name=="(GMT) Greenwich Mean Time : Dublin, Edinburgh, Lisbon, London")
			return "(GMT) ".i18n("Greenwich Mean Time : Dublin, Edinburgh, Lisbon, London");
/* 28 */else if ($name=="(GMT+01:00) Amsterdam, Berlin, Bern, Rome, Stockholm, Vienna")
			return "(GMT+01:00) ".i18n("Amsterdam, Berlin, Bern, Rome, Stockholm, Vienna");
/* 29 */else if ($name=="(GMT+01:00) Belgrade, Bratislava, Budapest, Ljubljana, Prague")
			return "(GMT+01:00) ".i18n("Belgrade, Bratislava, Budapest, Ljubljana, Prague");
/* 30 */else if ($name=="(GMT+01:00) Brussels, Copenhagen, Madrid, Paris") return "(GMT+01:00) ".i18n("Brussels, Copenhagen, Madrid, Paris");
/* 31 */else if ($name=="(GMT+01:00) Sarajevo, Skopje, Warsaw, Zagreb") return "(GMT+01:00) ".i18n("Sarajevo, Skopje, Warsaw, Zagreb");
/* 32 */else if ($name=="(GMT+01:00) West Central Africa")			return "(GMT+01:00) ".i18n("West Central Africa");
/* 33 */else if ($name=="(GMT+02:00) Athens, Istanbul, Minsk")		return "(GMT+02:00) ".i18n("Athens, Istanbul, Minsk");
/* 34 */else if ($name=="(GMT+02:00) Bucharest")					return "(GMT+02:00) ".i18n("Bucharest");
/* 35 */else if ($name=="(GMT+02:00) Cairo")						return "(GMT+02:00) ".i18n("Cairo");
/* 36 */else if ($name=="(GMT+02:00) Harare, Pretoria")				return "(GMT+02:00) ".i18n("Harare, Pretoria");
/* 37 */else if ($name=="(GMT+02:00) Helsinki, Kyiv, Riga, Sofia, Tallinn, Vilnius")
			return "(GMT+02:00) ".i18n("Helsinki, Kyiv, Riga, Sofia, Tallinn, Vilnius");
/* 38 */else if ($name=="(GMT+02:00) Jerusalem")					return "(GMT+02:00) ".i18n("Jerusalem");
/* 39 */else if ($name=="(GMT+03:00) Baghdad")						return "(GMT+03:00) ".i18n("Baghdad");
/* 40 */else if ($name=="(GMT+03:00) Kuwait, Riyadh")				return "(GMT+03:00) ".i18n("Kuwait, Riyadh");
/* 41 */else if ($name=="(GMT+03:00) Moscow, St. Petersburg, Volgograd") return "(GMT+03:00) ".i18n("Moscow, St. Petersburg, Volgograd");
/* 42 */else if ($name=="(GMT+03:00) Nairobi")						return "(GMT+03:00) ".i18n("Nairobi");
/* 43 */else if ($name=="(GMT+03:30) Tehran")						return "(GMT+03:30) ".i18n("Tehran");
/* 44 */else if ($name=="(GMT+04:00) Abu Dhabi, Muscat")			return "(GMT+04:00) ".i18n("Abu Dhabi, Muscat");
/* 45 */else if ($name=="(GMT+04:00) Baku, Tbilisi, Yerevan")		return "(GMT+04:00) ".i18n("Baku, Tbilisi, Yerevan");
/* 46 */else if ($name=="(GMT+04:30) Kabul")						return "(GMT+04:30) ".i18n("Kabul");
/* 47 */else if ($name=="(GMT+05:00) Ekaterinburg")					return "(GMT+05:00) ".i18n("Ekaterinburg");
/* 48 */else if ($name=="(GMT+05:00) Islamabad, Karachi, Tashkent")	return "(GMT+05:00) ".i18n("Islamabad, Karachi, Tashkent");
/* 49 */else if ($name=="(GMT+05:30) Chennai, Kolkata, Mumbai, New Delhi") return "(GMT+05:30) ".i18n("Chennai, Kolkata, Mumbai, New Delhi");
/* 50 */else if ($name=="(GMT+05:45) Kathmandu")					return "(GMT+05:45) ".i18n("Kathmandu");
/* 51 */else if ($name=="(GMT+06:00) Almaty, Novosibirsk")			return "(GMT+06:00) ".i18n("Almaty, Novosibirsk");
/* 52 */else if ($name=="(GMT+06:00) Astana, Dhaka")				return "(GMT+06:00) ".i18n("Astana, Dhaka");
/* 53 */else if ($name=="(GMT+06:00) Sri Jayawardenepura")			return "(GMT+06:00) ".i18n("Sri Jayawardenepura");
/* 54 */else if ($name=="(GMT+06:30) Rangoon")						return "(GMT+06:30) ".i18n("Rangoon");
/* 55 */else if ($name=="(GMT+07:00) Bangkok, Hanoi, Jakarta")		return "(GMT+07:00) ".i18n("Bangkok, Hanoi, Jakarta");
/* 56 */else if ($name=="(GMT+07:00) Krasnoyarsk")					return "(GMT+07:00) ".i18n("Krasnoyarsk");
/* 57 */else if ($name=="(GMT+08:00) Beijing, Chongqing, Hong Kong, Urumqi") return "(GMT+08:00) ".i18n("Beijing, Chongqing, Hong Kong, Urumqi");
/* 58 */else if ($name=="(GMT+08:00) Irkutsk, Ulaan Bataar")		return "(GMT+08:00) ".i18n("Irkutsk, Ulaan Bataar");
/* 59 */else if ($name=="(GMT+08:00) Kuala Lumpur, Singapore")		return "(GMT+08:00) ".i18n("Kuala Lumpur, Singapore");
/* 60 */else if ($name=="(GMT+08:00) Perth")						return "(GMT+08:00) ".i18n("Perth");
/* 61 */else if ($name=="(GMT+08:00) Taipei")						return "(GMT+08:00) ".i18n("Taipei");
/* 62 */else if ($name=="(GMT+09:00) Osaka, Sapporo, Tokyo")		return "(GMT+09:00) ".i18n("Osaka, Sapporo, Tokyo");
/* 63 */else if ($name=="(GMT+09:00) Seoul")						return "(GMT+09:00) ".i18n("Seoul");
/* 64 */else if ($name=="(GMT+09:00) Yakutsk")						return "(GMT+09:00) ".i18n("Yakutsk");
/* 65 */else if ($name=="(GMT+09:30) Adelaide")						return "(GMT+09:30) ".i18n("Adelaide");
/* 66 */else if ($name=="(GMT+09:30) Darwin")						return "(GMT+09:30) ".i18n("Darwin");
/* 67 */else if ($name=="(GMT+10:00) Brisbane")						return "(GMT+10:00) ".i18n("Brisbane");
/* 68 */else if ($name=="(GMT+10:00) Canberra, Melbourne, Sydney")	return "(GMT+10:00) ".i18n("Canberra, Melbourne, Sydney");
/* 69 */else if ($name=="(GMT+10:00) Guam, Port Moresby")			return "(GMT+10:00) ".i18n("Guam, Port Moresby");
/* 70 */else if ($name=="(GMT+10:00) Hobart")						return "(GMT+10:00) ".i18n("Hobart");
/* 71 */else if ($name=="(GMT+10:00) Vladivostok")					return "(GMT+10:00) ".i18n("Vladivostok");
/* 72 */else if ($name=="(GMT+11:00) Magadan, Solomon Is., New Caledonia") return "(GMT+11:00) ".i18n("Magadan, Solomon Is., New Caledonia");
/* 73 */else if ($name=="(GMT+12:00) Auckland, Wellington")			return "(GMT+12:00) ".i18n("Auckland, Wellington");
/* 74 */else if ($name=="(GMT+12:00) Fiji, Kamchatka, Marshall Is.") return "(GMT+12:00) ".i18n("Fiji, Kamchatka, Marshall Is.");
/* 75 */else if ($name=="(GMT+13:00) Nuku'alofa")					return "(GMT+13:00) ".i18n("Nuku'alofa");

	/* Not found, return the original */
	return $name;
}
?>
