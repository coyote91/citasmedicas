<?php


/**
 *
 */
class Direccionmedico
{

  public static function obtenerdireccionpordefecto($doctor_id = '')
	{
		$output = '';

		$sql = 'SELECT id, address, latitude, longitude
				FROM doctor_addresses
				WHERE doctor_id = '.(int)$doctor_id.' AND is_active = 1
				ORDER BY is_default DESC, priority_order ASC';

		$result = database_query($sql, DATA_AND_ROWS, FIRST_ROW_ONLY);
		if($result[1] > 0){
			$output = $result[0];
		}

		return $output;
	}

}// end class Direccionesmedicos


?>
