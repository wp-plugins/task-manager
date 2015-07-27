<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class wpeo_tasks_utils_ctr {	
	/**
	 * convert_to_hour_minute_from_minute - Convert minute to hour and minute / Converti les minutes en heures et minutes
	 * @param int $time - Time as minute - Temps en minute
	 * @param string format - The format result - Le foramt du resultat
	 * @return string
	 */
	public static function convert_to_hour_minute_from_minute($time, $format = '%02d:%02d') {
		settype($time, 'integer');
		if ($time < 1) {
			return;
		}
		$hours = floor($time / 60);
		$minutes = ($time % 60);
		// return the format
		return sprintf($format, $hours, $minutes);
	}
	
	/**
	 * convert_to_hour_minute_from_minute - Convert minute to hour and minute / Converti les minutes en heures et minutes
	 * @param int $time - Time as minute - Temps en minute
	 * @param string format - The format result - Le foramt du resultat
	 * @return string
	 */
	public static function convert_to_minute_from_hour_minute($hour, $minute, $format = '%02d:%02d') {
		$minutes = ($hour * 60);
		$minutes += $minute;
	
		return $minutes;
	}

}