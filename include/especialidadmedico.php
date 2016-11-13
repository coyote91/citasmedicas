<?php
include'./bd/conexion.php';

/**
 *
 */
class Especialidadmedico
{
  /**
   *	Retorna array de todas las especialidades que estan activas

   *		@param $where_clause
   */

  public static function especialidadesactivas($where_clause = '')
  {
      $sql = " SELECT
                  s.id,
                  sd.name,
                  sd.description
               FROM specialities s
               LEFT OUTER JOIN specialities_description sd ON s.id = sd.speciality_id AND sd.language_id = '".Aplicacion::Get('lang')."'
               ORDER BY name ASC
          ";


    return database_query($sql, DATA_AND_ROWS);
  }


} //End Especialidadmedico


?>
