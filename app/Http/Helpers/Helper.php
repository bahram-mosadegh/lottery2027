<?php

class Helper {

	public static function asset_versioned($path)
    {
        $public_path = public_path($path);
        $versioned_path = $path . '?' . sha1_file($public_path);
        
        return asset($versioned_path);
    }

	public static function gregorian_to_jalali($gy, $gm, $gd, $mod = '') {
		list($gy, $gm, $gd) = explode('_', self::tr_num($gy . '_' . $gm . '_' . $gd));
		$g_d_m = array(0, 31, 59, 90, 120, 151, 181, 212, 243, 273, 304, 334);
		$gy2 = ($gm > 2) ? ($gy + 1) : $gy;
		$days = 355666 + (365 * $gy) + ((int) (($gy2 + 3) / 4)) - ((int) (($gy2 + 99) / 100)) + ((int) (($gy2 + 399) / 400)) + $gd + $g_d_m[$gm - 1];
		$jy = -1595 + (33 * ((int) ($days / 12053)));
		$days %= 12053;
		$jy += 4 * ((int) ($days / 1461));
		$days %= 1461;
		if ($days > 365) {
			$jy += (int) (($days - 1) / 365);
			$days = ($days - 1) % 365;
		}
		if ($days < 186) {
			$jm = 1 + (int) ($days / 31);
			$jd = 1 + ($days % 31);
		} else {
			$jm = 7 + (int) (($days - 186) / 30);
			$jd = 1 + (($days - 186) % 30);
		}
		return ($mod == '') ? array($jy, $jm, $jd) : $jy . $mod . sprintf("%02d", $jm) . $mod . sprintf("%02d", $jd);
	}

	public static function jalali_to_gregorian($jy, $jm, $jd, $mod = '') {
		list($jy, $jm, $jd) = explode('_', self::tr_num($jy . '_' . $jm . '_' . $jd));
		$jy += 1595;
		$days = -355668 + (365 * $jy) + (((int) ($jy / 33)) * 8) + ((int) ((($jy % 33) + 3) / 4)) + $jd + (($jm < 7) ? ($jm - 1) * 31 : (($jm - 7) * 30) + 186);
		$gy = 400 * ((int) ($days / 146097));
		$days %= 146097;
		if ($days > 36524) {
			$gy += 100 * ((int) (--$days / 36524));
			$days %= 36524;
			if ($days >= 365) $days++;
		}
		$gy += 4 * ((int) ($days / 1461));
		$days %= 1461;
		if ($days > 365) {
			$gy += (int) (($days - 1) / 365);
			$days = ($days - 1) % 365;
		}
		$gd = $days + 1;
		$sal_a = array(0, 31, (($gy % 4 == 0 and $gy % 100 != 0) or ($gy % 400 == 0)) ? 29 : 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
		for ($gm = 0; $gm < 13 and $gd > $sal_a[$gm]; $gm++) $gd -= $sal_a[$gm];
		return ($mod == '') ? array($gy, $gm, $gd) : $gy . $mod . sprintf("%02d", $gm) . $mod . sprintf("%02d", $gd);
	}

	public static function tr_num($str, $mod = 'en', $mf = '٫') {
		$num_a = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '.');
		$key_a = array('۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹', $mf);
		return ($mod == 'fa') ? str_replace($num_a, $key_a, $str) : str_replace($key_a, $num_a, $str);
	}

	public static function is_json($string){
	   return is_string($string) && is_array(json_decode($string, true)) && (json_last_error() == JSON_ERROR_NONE) ? true : false;
	}

	public static function get_countries(): array {
		return [
			'Iran' => 'ایران / Iran',
			'Afghanistan' => 'افغانستان / Afghanistan',
			'Albania' => 'آلبانی / Albania',
			'Algeria' => 'الجزایر / Algeria',
			'American Samoa' => 'جزایر ساموآ / American Samoa',
			'Andorra' => 'آندورا / Andorra',
			'Angola' => 'آنگولا / Angola',
			'Anguilla' => 'آنگویلا / Anguilla',
			'Antigua and Barbuda' => 'آنتیگوا و باربودا / Antigua and Barbuda',
			'Argentina' => 'آرژانتین / Argentina',
			'Armenia' => 'ارمنستان / Armenia',
			'Aruba' => 'آروبا / Aruba',
			'Australia' => 'استرالیا / Australia',
			'Austria' => 'اتریش / Austria',
			'Azerbaijan' => 'آذربایجان / Azerbaijan',
			'Bahrain' => 'بحرین / Bahrain',
			'Bangladesh' => 'بنگلادش / Bangladesh',
			'Barbados' => 'باربادوس / Barbados',
			'Belarus' => 'بلاروس / Belarus',
			'Belgium' => 'بلژیک / Belgium',
			'Belize' => 'بلیز / Belize',
			'Benin' => 'بنین / Benin',
			'Bermuda' => 'برمودا / Bermuda',
			'Bhutan' => 'بوتان / Bhutan',
			'Bolivia' => 'بولیوی / Bolivia',
			'Bosnia and Herzegovina' => 'بوسنی و هرزگوین / Bosnia and Herzegovina',
			'Botswana' => 'بوتساوا / Botswana',
			'Bouvet Island' => 'جزیره بووه / Bouvet Island',
			'Brazil' => 'برزیل / Brazil',
			'British Indian Ocean Territory' => 'قلمرو اقیانوس هند بریتانیا / British Indian Ocean Territory',
			'British Virgin Islands' => 'جزایر ویرجین بریتانیا / British Virgin Islands',
			'Brunei' => 'برونئی / Brunei',
			'Bulgaria' => 'بلغارستان / Bulgaria',
			'Burkina Faso' => 'بورکینافاسو / Burkina Faso',
			'Burundi' => 'بوروندی / Burundi',
			'Côte d’Ivoire' => 'ساحل عاج / Côte d’Ivoire',
			'Cambodia' => 'کامبوج / Cambodia',
			'Cameroon' => 'کامرون / Cameroon',
			'Canada' => 'کانادا / Canada',
			'Cape Verde' => 'کیپ ورد / Cape Verde',
			'Cayman Islands' => 'جزایر کیمن / Cayman Islands',
			'Central African Republic' => 'جمهوری آفریقای مرکزی / Central African Republic',
			'Chad' => 'چاد / Chad',
			'Chile' => 'شیلی / Chile',
			'China' => 'چین / China',
			'Christmas Island' => 'جزیره کریسمس / Christmas Island',
			'Cocos (Keeling) Islands' => 'جرایز کوکوس ( کیلینگ) / Cocos (Keeling) Islands',
			'Colombia' => 'کلمبیا / Colombia',
			'Comoros' => 'کومور / Comoros',
			'Congo' => 'کنگو / Congo',
			'Cook Islands' => 'جزایر کوک / Cook Islands',
			'Costa Rica' => 'کاستاریکا / Costa Rica',
			'Croatia' => 'کرواسی / Croatia',
			'Cuba' => 'کوبا / Cuba',
			'Cyprus' => 'قبرس / Cyprus',
			'Czech Republic' => 'جمهوری چک / Czech Republic',
			'Democratic Republic of the Congo' => 'جمهوری دموکراتیک کنگو / Democratic Republic of the Congo',
			'Denmark' => 'دانمارک / Denmark',
			'Djibouti' => 'جیبوتی / Djibouti',
			'Dominica' => 'دومینیکا / Dominica',
			'Dominican Republic' => 'جمهوری دومینیکن / Dominican Republic',
			'East Timor' => 'تیمور شرقی / East Timor',
			'Ecuador' => 'اکوادور / Ecuador',
			'Egypt' => 'مصر / Egypt',
			'El Salvador' => 'السالوادور / El Salvador',
			'Equatorial Guinea' => 'گینه استوایی / Equatorial Guinea',
			'Eritrea' => 'اریتره / Eritrea',
			'Estonia' => 'استونی / Estonia',
			'Ethiopia' => 'اتیوپی / Ethiopia',
			'Faeroe Islands' => 'جزایر فارو / Faeroe Islands',
			'Falkland Islands' => 'جزایر فالکلند / Falkland Islands',
			'Fiji' => 'فیجی / Fiji',
			'Finland' => 'فنلاند / Finland',
			'Former Yugoslav Republic of Macedonia' => 'جمهوری مقدونیه یوگسلاوی سابق / Former Yugoslav Republic of Macedonia',
			'France' => 'فرانسه / France',
			'French Guiana' => 'گویای فرانسه / French Guiana',
			'French Polynesia' => 'پلینزی فرانسه / French Polynesia',
			'French Southern Territories' => 'سرزمین های جنوبی فرانسه / French Southern Territories',
			'Gabon' => 'گابن / Gabon',
			'Georgia' => 'گرجستان / Georgia',
			'Germany' => 'آلمان / Germany',
			'Ghana' => 'غنا / Ghana',
			'Gibraltar' => 'جبل الطارق / Gibraltar',
			'Greece' => 'یونان / Greece',
			'Greenland' => 'گرینلند / Greenland',
			'Grenada' => 'گرانادا / Grenada',
			'Guadeloupe' => 'گوادلوپ / Guadeloupe',
			'Guam' => 'گوام / Guam',
			'Guatemala' => 'گواتمالا / Guatemala',
			'Guinea' => 'گینه / Guinea',
			'Guinea-Bissau' => 'گینه بیسائو / Guinea-Bissau',
			'Guyana' => 'گویان / Guyana',
			'Haiti' => 'هائیتی / Haiti',
			'Heard Island and McDonald Islands' => 'جزیه هرد و جزایر مک دونالد / Heard Island and McDonald Islands',
			'Honduras' => 'هندوراس / Honduras',
			'Hong Kong' => 'هنگ کنگ / Hong Kong',
			'Hungary' => 'مجارستان / Hungary',
			'Iceland' => 'ایسلند / Iceland',
			'India' => 'هند / India',
			'Indonesia' => 'اندونزی / Indonesia',
			'Iran' => 'ایران / Iran',
			'Iraq' => 'عراق / Iraq',
			'Ireland' => 'ایرلند / Ireland',
			'Israel' => 'اسرائیل / Israel',
			'Italy' => 'ایتالیا / Italy',
			'Jamaica' => 'جامائیکا / Jamaica',
			'Japan' => 'ژاپن / Japan',
			'Jordan' => 'اردن / Jordan',
			'Kazakhstan' => 'قزاقستان / Kazakhstan',
			'Kenya' => 'کنیا / Kenya',
			'Kiribati' => 'کیریباتی / Kiribati',
			'Kuwait' => 'کویت / Kuwait',
			'Kyrgyzstan' => 'قرقیزستان / Kyrgyzstan',
			'Laos' => 'لائوس / Laos',
			'Latvia' => 'لتونی / Latvia',
			'Lebanon' => 'لبنان / Lebanon',
			'Lesotho' => 'لسوتو / Lesotho',
			'Liberia' => 'لیبریا / Liberia',
			'Libya' => 'لیبی / Libya',
			'Liechtenstein' => 'لیختن اشتاین / Liechtenstein',
			'Lithuania' => 'لیتوانی / Lithuania',
			'Luxembourg' => 'لوکزامبورگ / Luxembourg',
			'Madagascar' => 'ماداگاسکار / Madagascar',
			'Malawi' => 'مالاوی / Malawi',
			'Malaysia' => 'مالزی / Malaysia',
			'Maldives' => 'مالدیو / Maldives',
			'Mali' => 'مالی / Mali',
			'Malta' => 'مالت / Malta',
			'Marshall Islands' => 'جزایر مارشال / Marshall Islands',
			'Martinique' => 'مارتینیک / Martinique',
			'Mauritania' => 'موریتانی / Mauritania',
			'Mauritius' => 'موریس / Mauritius',
			'Mayotte' => 'مایوت / Mayotte',
			'Mexico' => 'مکزیک / Mexico',
			'Micronesia' => 'میکرونزی / Micronesia',
			'Moldova' => 'مولداوی / Moldova',
			'Monaco' => 'موناکو / Monaco',
			'Mongolia' => 'مغولستان / Mongolia',
			'Montserrat' => 'مونتسرات / Montserrat',
			'Morocco' => 'مراکش / Morocco',
			'Mozambique' => 'موزامبیک / Mozambique',
			'Myanmar' => 'میانمار / Myanmar',
			'Namibia' => 'نامیبیا / Namibia',
			'Nauru' => 'نائورو / Nauru',
			'Nepal' => 'نپال / Nepal',
			'Netherlands' => 'هلند / Netherlands',
			'New Caledonia' => 'کالدونیای جدید / New Caledonia',
			'New Zealand' => 'نیوزیلند / New Zealand',
			'Nicaragua' => 'نیکاروگوئه / Nicaragua',
			'Niger' => 'نبجر / Niger',
			'Nigeria' => 'نیجریه / Nigeria',
			'Niue' => 'نیوئه / Niue',
			'Norfolk Island' => 'جزیره نورفولک / Norfolk Island',
			'North Korea' => 'کره شمالی / North Korea',
			'Northern Marianas' => 'ماریانای شمالی / Northern Marianas',
			'Norway' => 'نروژ / Norway',
			'Oman' => 'عمان / Oman',
			'Pakistan' => 'پاکستان / Pakistan',
			'Palau' => 'پالائو / Palau',
			'Panama' => 'پاناما / Panama',
			'Papua New Guinea' => 'پاپوآ گینه نو / Papua New Guinea',
			'Paraguay' => 'پاراگوئه / Paraguay',
			'Peru' => 'پرو / Peru',
			'Philippines' => 'فیلیپین / Philippines',
			'Pitcairn Islands' => 'جزایر پیتکرن / Pitcairn Islands',
			'Poland' => 'لهستان / Poland',
			'Portugal' => 'کشور پرتغال / Portugal',
			'Puerto Rico' => 'پورتوریکو / Puerto Rico',
			'Qatar' => 'قطر / Qatar',
			'Réunion' => 'رئونیون / Réunion',
			'Romania' => 'رومانی / Romania',
			'Russia' => 'روسیه / Russia',
			'Rwanda' => 'رواندا / Rwanda',
			'São Tomé and Príncipe' => 'سائوتومه و پرنسیپ / São Tomé and Príncipe',
			'Saint Helena' => 'سنت هلنا / Saint Helena',
			'Saint Kitts and Nevis' => 'سنت کیتس و نویس / Saint Kitts and Nevis',
			'Saint Lucia' => 'سنت لوسیا / Saint Lucia',
			'Saint Pierre and Miquelon' => 'سنت پیر و میکلون / Saint Pierre and Miquelon',
			'Saint Vincent and the Grenadines' => 'سنت وینسنت و گرنادین ها / Saint Vincent and the Grenadines',
			'Samoa' => 'ساموآ / Samoa',
			'San Marino' => 'سن مارینو / San Marino',
			'Saudi Arabia' => 'عربستان سعودی / Saudi Arabia',
			'Senegal' => 'سنگال / Senegal',
			'Seychelles' => 'سیشل / Seychelles',
			'Sierra Leone' => 'سیرا لئون / Sierra Leone',
			'Singapore' => 'سنگاپور / Singapore',
			'Slovakia' => 'اسلواکی / Slovakia',
			'Slovenia' => 'اسلوونی / Slovenia',
			'Solomon Islands' => 'جزایر سلیمان / Solomon Islands',
			'Somalia' => 'سومالی / Somalia',
			'South Africa' => 'آفریقای جنوبی / South Africa',
			'South Georgia and the South Sandwich Islands' => 'جورجیا جنوبی و جزایر ساندویج جنوبی / South Georgia and the South Sandwich Islands',
			'South Korea' => 'کره جنوبی / South Korea',
			'Spain' => 'اسپانیا / Spain',
			'Sri Lanka' => 'سریلانکا / Sri Lanka',
			'Sudan' => 'سودان / Sudan',
			'Suriname' => 'سورینام / Suriname',
			'Svalbard and Jan Mayen' => 'سوالبارد و یان ماین / Svalbard and Jan Mayen',
			'Swaziland' => 'سوازیلند / Swaziland',
			'Sweden' => 'سوئد / Sweden',
			'Switzerland' => 'سوئیس / Switzerland',
			'Syria' => 'سوریه / Syria',
			'Taiwan' => 'تایوان / Taiwan',
			'Tajikistan' => 'تاجیکستان / Tajikistan',
			'Tanzania' => 'تانزانیا / Tanzania',
			'Thailand' => 'تایلند / Thailand',
			'The Bahamas' => 'باهاماس / The Bahamas',
			'The Gambia' => 'گامبیا / The Gambia',
			'Togo' => 'توگو / Togo',
			'Tokelau' => 'توکلائو / Tokelau',
			'Tonga' => 'تونگا / Tonga',
			'Trinidad and Tobago' => 'ترینیداد و توباگو / Trinidad and Tobago',
			'Tunisia' => 'تونس / Tunisia',
			'Turkey' => 'ترکیه / Turkey',
			'Turkmenistan' => 'ترکمنستان / Turkmenistan',
			'Turks and Caicos Islands' => 'جزایر تورکس و کایکوس / Turks and Caicos Islands',
			'Tuvalu' => 'تووالو / Tuvalu',
			'US Virgin Islands' => 'جزایر ویرجین ایالات متحده / US Virgin Islands',
			'Uganda' => 'اوگاندا / Uganda',
			'Ukraine' => 'اوکراین / Ukraine',
			'United Arab Emirates' => 'امارات متحده عربی / United Arab Emirates',
			'United kingdom' => 'انگستان / United kingdom',
			'United States' => 'ایالات متحده آمریکا / United States',
			'United States Minor Outlying Islands' => 'جزایر کوچک حاشیه ای ایالات متحده / United States Minor Outlying Islands',
			'Uruguay' => 'اروگوئه / Uruguay',
			'Uzbekistan' => 'ازبکستان / Uzbekistan',
			'Vanuatu' => 'وانواتو / Vanuatu',
			'Vatican City' => 'شهر واتیکان / Vatican City',
			'Venezuela' => 'ونزوئلا / Venezuela',
			'Veitnam' => 'ویتنام / Veitnam',
			'Wallis and Futuna' => 'والیس و فوتونا / Wallis and Futuna',
			'Western Sahara' => 'صحرای غربی / Western Sahara',
			'Yemen' => 'یمن / Yemen',
			'Zimbabwe' => 'زیمبابوه / Zimbabwe',
			'Serbia' => 'صربستان / Serbia',
			'Montenegro' => 'مونته نگرو / Montenegro'
		];
	}

	public static function get_monthes(): array {
		return [
			'01' => 'فروردین',
			'02' => 'اردیبهشت',
			'03' => 'خرداد',
			'04' => 'تیر',
			'05' => 'مرداد',
			'06' => 'شهریور',
			'07' => 'مهر',
			'08' => 'آبان',
			'09' => 'آذر',
			'10' => 'دی',
			'11' => 'بهمن',
			'12' => 'اسفند',
		];
	}

	public static function price(): array {
		return [
			'online' => [
				'single' => 1900000,
				'married' => 2900000,
				'double_register' => 2000000,
				'adult_child' => 500000,
				'child' => 500000,
				'independent_register' => 1500000,
			],
			'onsite' => [
				'single' => 3000000,
				'married' => 4500000,
				'double_register' => 2000000,
				'adult_child' => 1000000,
				'child' => 1000000,
				'independent_register' => 1500000,
			],
			'agent' => [
				'single' => 1500000,
				'married' => 2500000,
				'double_register' => 2000000,
				'adult_child' => 500000,
				'child' => 500000,
				'independent_register' => 1000000,
			],
		];
	}
}

?>