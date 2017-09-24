<?php

/**
 * 	Returns time difference
 * 		@param first_time
 * 		@param last_time
 **/
function time_diff($last_time, $first_time)
{
	// convert to unix timestamps
	$time_diff=strtotime($last_time)-strtotime($first_time);
	return $time_diff;
}


/**
 *  Format date
 *  	@param $date
 *  	@param $format
 *  	@param $empty_text
 *  	@param $locale
 */
function format_date($date, $format = '', $empty_text = '', $locale = false)
{
	$format = ($format == '') ? get_date_format() : $format;

	if($date != '' && $date != '0000-00-00'){
		$date_new = mktime(0, 0, 0, substr($date, 5, 2), substr($date, 8, 2), substr($date, 0, 4));

		// convert date according to local settings
		if($locale && Aplicacion::Get('lang') != 'en'){
			$format = str_replace('%b', get_month_local(@strftime('%m', $date_new)), get_date_format('', false, true));
			return @strftime($format, $date_new);
		}

		return @date($format, $date_new);
	}else{
		return $empty_text;
	}
}

/**
 * Get date format
 * 		@param $format
 * 		@param $settings_format
 * 		@param $locale
 */
function get_date_format($format = 'view', $settings_format = false, $locale = false)
{
	//global $objSettings;

	$date_format = 'M d, Y';
/*
	if($objSettings->GetParameter('date_format') == 'mm/dd/yyyy'){
		if($locale)
		$date_format = '%b %d, %Y';
		else if($settings_format) $date_format = 'mm/dd/yyyy';
		else $date_format = ($format == 'edit') ? 'm-d-y' : 'M d, Y';
	}else{
		if($locale) $date_format = '%d %b, %Y';
		else if($settings_format) $date_format = 'dd/mm/yyyy';
		else $date_format = ($format == 'edit') ? 'd-m-y' : 'd M, Y';
	}*/
	return $date_format;
}

?>
