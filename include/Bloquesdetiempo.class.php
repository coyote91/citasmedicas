<?php

/**
 *
 */
class Bloquesdetiempo
{

  //==========================================================================
      // Static Methods
  	//==========================================================================
  	/**
  	 * Returns a list of slots for certain doctor for a specific day
  	 */
  	public static function GetTimeSlotsForDay($doctor_id, $date, $week_day_num)
  	{

  		$result = array();
  		$minimum_time_slots = "2";  //ranuras de retardo  2
  		$today = date('Y-m-d');
  		$time_format = 'H:i:s';

  		// prepare real timeslots
  		$sql = 'SELECT
  					'.TABLE_SCHEDULES.'.id,
  					'.TABLE_SCHEDULES.'.name,
  					'.TABLE_SCHEDULES.'.date_from,
  					'.TABLE_SCHEDULES.'.date_to,
  					'.TABLE_SCHEDULE_TIMEBLOCKS.'.doctor_address_id,
  					'.TABLE_SCHEDULE_TIMEBLOCKS.'.week_day,
  					'.TABLE_SCHEDULE_TIMEBLOCKS.'.time_from,
  					'.TABLE_SCHEDULE_TIMEBLOCKS.'.time_to,
  					'.TABLE_SCHEDULE_TIMEBLOCKS.'.time_slots
  				FROM '.TABLE_SCHEDULES.'
  					INNER JOIN '.TABLE_SCHEDULE_TIMEBLOCKS.' ON '.TABLE_SCHEDULES.'.id = '.TABLE_SCHEDULE_TIMEBLOCKS.'.schedule_id
  				WHERE
  					('.TABLE_SCHEDULES.'.date_from <= \''.$date.'\' AND \''.$date.'\' <= '.TABLE_SCHEDULES.'.date_to) AND
  					'.TABLE_SCHEDULES.'.doctor_id = '.(int)$doctor_id.' AND
  					'.TABLE_SCHEDULE_TIMEBLOCKS.'.week_day = '.(int)$week_day_num.'
  				ORDER BY '.TABLE_SCHEDULES.'.id ASC, '.TABLE_SCHEDULE_TIMEBLOCKS.'.time_from ASC';
  		$res = database_query($sql, DATA_AND_ROWS, ALL_ROWS);
  		for($i=0; $i < $res[1]; $i++){
  			$time_slot   = (int)$res[0][$i]['time_slots'];
  			$schedile_id = $res[0][$i]['id'];
  			$doctor_address_id = $res[0][$i]['doctor_address_id'];
  			$start_time  = strtotime($res[0][$i]['time_from']);

  			if($date == $today){
  				$actual_time = date('H:i:s', strtotime('+'.($time_slot*$minimum_time_slots).' minute'));
  			}else{
  				$actual_time = $res[0][$i]['time_from'];
  			}

  			$current_time = $res[0][$i]['time_from'];
  			$end_time     = $res[0][$i]['time_to'];
  			$counter = 0;
  			while($current_time < $end_time){
  				$current_time_shift = strtotime('+'.($counter * $time_slot).' minute', $start_time);
  				$current_time = date('H:i:s', $current_time_shift);
  				$current_time_1 = date('H-i', $current_time_shift);
  				$current_time_2 = date($time_format, $current_time_shift);

  				$counter++;
  				if($counter > 300) break;

  				if($current_time < $actual_time){
  					continue;
  				}else if($current_time < $end_time){
  					$result[] = array('date'=>$date, 'schedule_id'=>$schedile_id, 'doctor_address_id'=>$doctor_address_id, 'time_real'=>$current_time, 'time'=>$current_time_1, 'time_view'=>$current_time_2, 'duration'=>$time_slot);
  				}
  			}
  		}
  		$result_total = count($result);

  		// subtruct booked timeslots
  		$sql = 'SELECT id, doctor_id, patient_id, appointment_date, appointment_time, visit_duration
  				FROM '.TABLE_APPOINTMENTS.'
  				WHERE appointment_date = "'.$date.'" AND
  					  doctor_id = '.(int)$doctor_id.' AND
  					  (status = 0 OR status = 1)';
  		// exclude canceled
  		$res = database_query($sql, DATA_AND_ROWS, ALL_ROWS);
  		for($i=0; $i < $res[1]; $i++){
  			for($j=0; $j < $result_total; $j++){
  				$time_real = isset($result[$j]['time_real']) ? $result[$j]['time_real'] : false;
  				if($time_real == $res[0][$i]['appointment_time']){
  					unset($result[$j]);
  					break;
  				}
  			}
  		}

  		//echo '<pre>';
  		//print_r($result);
  		//echo '</pre>';

  		// subtruct timeoff timeslots
  		$sql = 'SELECT id, doctor_id, date_from, time_from, date_to, time_to
  				FROM '.TABLE_TIMEOFFS.'
  				WHERE
  					(
  					  ("'.$date.'" > date_from AND "'.$date.'" < date_to) OR
  					  ("'.$date.'" = date_from AND "'.$date.'" <= date_to) OR
  					  ("'.$date.'" >= date_from AND "'.$date.'" = date_to)
  					) AND
  					doctor_id = '.(int)$doctor_id;
  		$res = database_query($sql, DATA_AND_ROWS, FIRST_ROW_ONLY);
  		if($res[1] > 0){
  			if($date == $res[0]['date_from'] && $date == $res[0]['date_to']){
  				// this day doctor works partially
  				for($j=0; $j < $result_total; $j++){
  					$time_real = isset($result[$j]['time_real']) ? $result[$j]['time_real'] : false;
  					if($time_real >= $res[0]['time_from'] && $time_real < $res[0]['time_to']){
  						unset($result[$j]);
  					}
  				}
  			}else if($date == $res[0]['date_to']){
  				// this day doctor works partially
  				for($j=0; $j < $result_total; $j++){
  					$time_real = isset($result[$j]['time_real']) ? $result[$j]['time_real'] : false;
  					if($time_real < $res[0]['time_to']){
  						unset($result[$j]);
  					}
  				}
  			}else if($date == $res[0]['date_from']){
  				// this day doctor works partially
  				for($j=0; $j < $result_total; $j++){
  					$time_real = isset($result[$j]['time_real']) ? $result[$j]['time_real'] : false;
  					if($time_real >= $res[0]['time_from']){
  						unset($result[$j]);
  					}
  				}
  			}else{
  				// this day doctor doesn't work at all
  				$result = array();
  			}
  		}

  		return $result;
  	}

}  /* END CLASS BLOQUES DE TIEMPO*/




 ?>
