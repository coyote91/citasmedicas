<?php

/**
 *
 */
class Busquedadoctores
{

  public static function busquedaporparametros($parametros_de_busqueda)
  {
      /* recibo parametros de la pagina index.php */

      $resultado = array();

      $total_doctores = self::consultarmedicosporparametros($parametros_de_busqueda);
      foreach ($total_doctores[0]  as $key => $val)
      {
           $resultado[$val['doctor_id']] = array(
             'first_name'         => $val['first_name'],
     				'middle_name'        => $val['middle_name'],
     				'last_name'          => $val['last_name'],
     				'title'              => $val['title'],
     				'gender'             => $val['gender'],
     				'medical_degree'     => $val['medical_degree'],
     				'doctor_photo'       => $val['doctor_photo'],
     				'doctor_photo_thumb' => $val['doctor_photo_thumb'],
     				'speciality_id'      => $val['speciality_id'],
     				'speciality_name'    => $val['speciality_name'],
     				'address_id'         => $val['address_id'],
     				'address'            => $val['address'],
           'membership_addresses_count' => $val['membership_addresses_count'],
           'membership_images_count' => $val['membership_images_count']

           );


      }

      return $resultado;

  }  // end function busquedaporparametros

  public static function consultarmedicosporparametros($parametros)
  {
          define('TABLE_DOCTORS', 'doctors');
          define('TABLE_DOCTOR_SPECIALITIES', 'doctor_specialities');
          define('TABLE_SPECIALITIES_DESCRIPTION', 'specialities_description');
          define('TABLE_DOCTOR_ADDRESSES', 'doctor_addresses');

    $speciality = isset($parametros['speciality']) ? $parametros['speciality'] : '';
    $name = isset($parametros['name']) ? trim($parametros['name']) : '';

    $name_parts = explode(' ', $name);
    $name_part1 = isset($name_parts[0]) ? $name_parts[0] : '';
    $name_part2 = isset($name_parts[1]) ? $name_parts[1] : '';

    $location = isset($parametros['location']) ? $parametros['location'] : '';
    $doctor_id = isset($parametros['doctor_id']) ? $parametros['doctor_id'] : '';  /* parametro al parecer sin uso*/


    $sql = 'SELECT
          d.id as doctor_id,
          d.first_name,
          d.middle_name,
          d.last_name,
          d.gender,
          d.title,
          d.medical_degree,
          d.doctor_photo,
          d.doctor_photo_thumb,
                    d.membership_plan_id,
                    d.membership_images_count,
                    d.membership_addresses_count,
                    d.membership_show_in_search,
          sd.id as speciality_id,
          sd.name as speciality_name
          '.(!empty($location) ? ', da.address' : ', "" as address').'
          '.(!empty($location) ? ', da.id as address_id' : ', "" as address_id').'
        FROM '.TABLE_DOCTORS.' d
          INNER JOIN '.TABLE_DOCTOR_SPECIALITIES.' ds ON d.id = ds.doctor_id
          INNER JOIN '.TABLE_SPECIALITIES_DESCRIPTION.' sd ON ds.speciality_id = sd.speciality_id AND sd.language_id = \''.Aplicacion::Get('lang').'\'
          '.(!empty($location) ? ' INNER JOIN '.TABLE_DOCTOR_ADDRESSES.' da ON d.id = da.doctor_id' : '').'
        WHERE
                    d.membership_show_in_search = 1 AND
                    d.is_active = 1
          '.(($doctor_id != '') ? ' AND d.id = '.(int)$doctor_id : '').'
            '.(!empty($speciality)  ? ' AND ds.speciality_id = '.(int)$speciality : '').'
          '.(!empty($name) ? ' AND (
            d.last_name LIKE \'%'.$name_part1.'%\'
            '.($name_part2 != '' ? ' OR d.last_name LIKE \'%'.$name_part2.'%\'' : '').'
            OR d.first_name LIKE \'%'.$name_part1.'%\'
            '.($name_part2 != '' ? ' OR d.first_name LIKE \'%'.$name_part2.'%\'' : '').'
          ) ' : '').'
          '.(!empty($location)  ? ' AND da.address LIKE \'%'.$location.'%\'' : '');

    return database_query($sql, DATA_AND_ROWS);



  } //end function consultarmedicosporparametros


  public static function resultadosbusqueda($resultado, $parametros_de_busqueda)
  {

    define("_MORE","más");

                  //guia strftime   http://php.net/manual/es/function.strftime.php

         date_default_timezone_set("America/Bogota");
         setlocale(LC_TIME, 'spanish');

         	$output = '';

         $start_day = isset($_POST['start_day']) ? $_POST['start_day'] : date('Y-m-d');

          	$is_active = 'yes';

              $search_page_size = 20;
              $doctors_total = count($resultado);

              //-------- pagination
          		$current_page = isset($_REQUEST['p']) ? (int)$_REQUEST['p'] : '1';
          		$total_pages = (int)($doctors_total / $search_page_size);
          		if($current_page > ($total_pages+1))
                 $current_page = 1;

              if(($doctors_total % $search_page_size) != 0)
              $total_pages++;

              if(!is_numeric($current_page) || (int)$current_page <= 0)
              $current_page = 1;
          		// --------

      if(empty($resultado)){
   			if($parametros_de_busqueda['speciality'] != '' && empty($parametros_de_busqueda['name']) && empty($parametros_de_busqueda['location']))
        {
   				 echo "Los médicos no se encuentran en esta especialidad en este momento! ";
   			}else{

   				 echo "Ningún médico en nuestra base de datos coincide con los criterios de búsqueda";
   			}
   		}else{
                     /* Preparacion array con los proximos 7 dias de la semana */
        //date('Y-m-d')
        $current_day = strtotime($start_day);
        $arr_dates = array();


                       for($i=0; $i<7; $i++){
                       $current_date = ($i == 0) ? $current_day : strtotime('+'.$i.' day', $current_day);


                        $arr_dates[] = array(
                                              'date'=>date('Y-m-d', $current_date),
                                              'dia' => strftime(" %A ", $current_date),
                                              'dia_mes_año' => strftime(" %b %d %Y ", $current_date),
                                              'week_day_num'=>(date('w', $current_date)+1) // w	Representación numérica del día de la semana
                                                                                           // 0 (para domingo) hasta 6 (para sábado)
                                           );


                       }

        $prev_week_day = date('Y-m-d', strtotime('-6 day', $current_day));
        $next_week_day = date('Y-m-d', strtotime('+6 day', $current_day));

        $but_prev = (Aplicacion::Get('lang_dir') == 'ltr') ? 'but_prev' : 'but_next';
        $but_next = (Aplicacion::Get('lang_dir') == 'ltr') ? 'but_next' : 'but_prev';
        $docid_param = isset($parametros_de_busqueda['doctor_id']) ? '&docid='.(int)$parametros_de_busqueda['doctor_id'] : '';

         ?>

     <table class="doctors_result">

       <tr valign="top">
         <td class="thleft">Doctores</td>
          <?php

          if ($is_active == "yes")
          {

          ?>

        <td class="thright">
                   <table cellspacing="0" cellpadding="0">
                     <tr>
                         <td class="go_prev">
                               <?php

                               if($prev_week_day >= date('Y-m-d'))
                               {


  echo  $output = '<a href="javascript:void(\'week|previous\');"  onclick="appFormSubmit(\'frmFindDoctors\',\'start_day='.$prev_week_day.'&page_number='.$current_page.$docid_param.'\')"> <img src="./img/but_prev_active.png"></a>';

//echo  $output = '<a href="javascript:void(\'week|previous\');"  onclick="appFormSubmit(\'frmFindDoctors\',\'start_day='.$prev_week_day.'&page_number='.$current_page.$docid_param.'\')">  <img src="templates/'.Aplicacion::Get('template').'/images/'.$but_prev.'_active.png"></a>';



                               }
                               else{

                               ?>

                                                     <img src="./templates/default/images/but_prev.png" >

                               <?php
                                  }
                               ?>

                       </td>


                       <td>

               <?php


               $days_count = 0;

               foreach ($arr_dates as $dkey => $dval ) {

                  echo '<td class="th_week_day'.(($days_count++ % 2 == 0) ? ' wd_colored' : ' wd_white').'">'.$dval['dia'].'<br>'. $dval['dia_mes_año'] .'</td>';
                  echo $dval['week_day_num'];

                }

               ?>


                     </td>


             <td class="go_next">


<?php

echo $output = '<a href="javascript:void(\'week|next\');" onclick="appFormSubmit(\'frmFindDoctors\',\'start_day='.$next_week_day.'&page_number='.$current_page.$docid_param.'\')"><img src="./img/but_next_active.png"> </a>';

//echo $output = '<a href="javascript:void(\'week|next\');" onclick="appFormSubmit(\'frmFindDoctors\',\'start_day='.$next_week_day.'&page_number='.$current_page.$docid_param.'\')"><img src="templates/'.Aplicacion::Get('template').'/images/'.$but_next.'_active.png"> </a>';


?>

             </td>

           </tr>

           </table>

         </td>

         <?php
           }
           ?>

      </tr>  <!-- END PRIMERA FILA DONDE SALEN LOS 7 DIAS DE LA SEMANA  -->

       <?php

             $doctors_count = 0;
             foreach($resultado as $key => $val)
             {
                 $doctors_count++;

                 if($doctors_count <= ($search_page_size * ($current_page - 1)))
                 {
                   continue;
                 }else if($doctors_count > ($search_page_size * ($current_page - 1)) + $search_page_size)
                 {
                   break;
                 }

                 if($parametros_de_busqueda['speciality'] != ''){
                   $speciality_id = isset($val['speciality_id']) ? (int)$val['speciality_id'] : '';
                   $speciality_name = isset($val['speciality_name']) ? $val['speciality_name'] : '';
                 }else{
                    $doc_speciality_info = self::listarespecialidadesmedicas($key);
                    $speciality_id = isset($doc_speciality_info[0][0]['speciality_id']) ? (int)$doc_speciality_info[0][0]['speciality_id'] : '';
                    $speciality_name = isset($doc_speciality_info[0][0]['speciality_name']) ? $doc_speciality_info[0][0]['speciality_name'] : '';
                 }

                 $doctor_def_address_id = '0';
                 if($val['membership_addresses_count'] > 0){
                     if(!empty($val['address'])){
                         ///$doctor_def_address = $val['address'];
                         $doctor_def_address_id = (int)$val['address_id'];
                     }else{
                         // draws 1st non-default address
                         $doctor_default_address = Direccionmedico::obtenerdireccionpordefecto($key);
                         ///$doctor_def_address = isset($doctor_default_address['address']) ? $doctor_default_address['address'] : '';
                         $doctor_def_address_id = isset($doctor_default_address['id']) ? (int)$doctor_default_address['id'] : '0';
                     }
                 }


                 $photo = ($val['doctor_photo_thumb'] != '') ? $val['doctor_photo_thumb'] : 'doctor_'.$val['gender'].'.png';
                 $doc_name = $val['title'].' '.$val['first_name'].' '.$val['middle_name'].' '.$val['last_name'];
              ?>
                   <tr valign="top">   <!---  SEGUNDA FILA DONDE VAN LAS FOTOS DEL DOCTOR    --->
                     <td colspan="3">
                       <div class="doctor_profile">
                                          <img class="doctor_small_photo" src=./templates/doctors/<?php echo $photo ?> > <br>
                         <?php

              echo " <a  href=\" " . $_SERVER["PHP_SELF"] . "?page=doctors&docid=".(int)$key. "\"> $doc_name </a> ";
              echo $val['medical_degree']."<br />". $speciality_name;

                      ?>

                       </div>

           <?php

                     /// '.$doctor_def_address.'
                       if($is_active == 'yes')
                       {
                             $non_empty_days = 0;
                ?>
                        <table cellspacing="0" cellpadding="0">
                          <tr>
                             <td class="go_prev"></td>

                  <?php

                         $days_count = 0;
                         $first_day = '';

                         foreach($arr_dates as $dkey => $dval)
                         {
                                 if($first_day == '')
                                   $first_day = $dval['dia_mes_año'];

 																//get time slots for day  :  obtener franjas horarias para el dia
 																// time slots for day : horarios para el dia
                                                                                        // dia_mes_año
             								$time_slots = Bloquesdetiempo::GetTimeSlotsForDay($key, $dval['date'], $dval['week_day_num']);
             								$slots = 0;
             								echo '<td class="td_week_day'.(($days_count++ % 2 == 0) ? ' wd_colored' : ' wd_white').'">';
                            foreach($time_slots as $ts_key => $ts_val)
                            {
  									              $doctor_address_id = (!empty($ts_val['doctor_address_id']) ? $ts_val['doctor_address_id'] : $doctor_def_address_id);
  									              $param = base64_encode('docid='.$key.'&dspecid='.$speciality_id.'&schid='.$ts_val['schedule_id'].'&daddid='.$doctor_address_id.'&date='.$dval['date'].'&start_time='.$ts_val['time'].'&duration='.$ts_val['duration']);
  		/*	OJO		*/				//$output_inner .= prepare_permanent_link('index.php?page=appointment_details&prm='.$param, $ts_val['time_view']).'<br>'."\n";

                                  echo " <a  href='index.php?page=appointment_details&prm='.$param.'>".$ts_val['time_view']."</a> <br />  ";

                									if($slots == 5)
                                  {
                										 echo  '<div class="hidden_slots'.$key.'" style="display:none;">';
                									}
                									$slots++;
                						}

                                            if($slots > 0) $non_empty_days++;
                              if($slots > 6)
                              {

                                ?>

                                 </div>

                               <?php

                                 echo '<a class="more_links'.$key.'" href="javascript:void(0)" onclick="appShowElement(\'.hidden_slots'.$key.'\');appHideElement(\'.more_links'.$key.'\');">'._MORE.'...</a>';
                              }

                                ?>
                                </td>

                              <?php
                          }

                              ?>

                                <td class="go_next"></td>
                            </tr>
                          </table>
                        <?php

                           echo $o = ($non_empty_days == 0) ? self::FindNextAppointment($key, $first_day) : $u ="" ;
                  }

                 ?>
                  </td>
               </tr>  <!-- END SEGUNDA FILA DONE VAN LAS FOTOS DEL DOCTOR   ---->

                <tr valign="top">   <!-- TERCER FILA  ---->
                   <td colspan="3">
                     <hr>
                   </td>
                </tr>

              </table>


              <?php


  $output .= '<div class="paging">';
  for($page_ind = 1; $page_ind <= $total_pages; $page_ind++){
    $output .= '<a class="paging_link" href="javascript:void(\'page|'.$page_ind.'\');" onclick="javascript:appFormSubmit(\'frmFindDoctors\',\'page_number='.$page_ind.$docid_param.'\')">'.(($page_ind == $current_page) ? '<b>['.$page_ind.']</b>' : $page_ind).'</a> ';
  }
  $output .= '</div>';


             }//end foreach

        ?>

     </table>
<?php
      }



  } //end resultados busqueda

  public static function listarespecialidadesmedicas($doctor_id = 0)
  {
    $sql = 'SELECT
          d.id as doctor_id,
          sd.id as speciality_id,
          sd.name as speciality_name
        FROM doctor_specialities ds
          INNER JOIN doctors d ON ds.doctor_id = d.id
                    INNER JOIN specialities_description sd ON ds.speciality_id = sd.speciality_id AND sd.language_id = "en"
        WHERE ds.doctor_id = '.(int)$doctor_id.'
        ORDER BY ds.priority_order ASC';
    return database_query($sql, DATA_AND_ROWS);
  }


  /**
  	 * Find next appointment for given doctor
  	 * 		@param $doc_id
  	 * 		@param $date_from
  	 */
  	private static function FindNextAppointment($doc_id, $date_from = '')
  	{

       define("_NEXT_APPOINTMENT_AT","Next appointment at");

          $msg = '';
          $date_from = ($date_from != '') ? $date_from : date('Y-m-d');
          $arr_weekdays_en = array('1'=>'Sunday','2'=>'Monday','3'=>'Tuesday','4'=>'Wednesday','5'=>'Thursday','6'=>'Friday','7'=>'Saturday');
  		$arr_weekdays_loc = array('1'=>_SUNDAY, '2'=>_MONDAY, '3'=>_TUESDAY, '4'=>_WEDNESDAY, '5'=>_THURSDAY, '6'=>_FRIDAY, '7'=>_SATURDAY);

          // check if there are schedules at all
  		$sql = 'SELECT * FROM '.TABLE_SCHEDULES.' WHERE date_to >= \''.$date_from.'\' AND doctor_id = '.(int)$doc_id.' ORDER BY date_from ASC LIMIT 0, 1';
          $result = database_query($sql, DATA_AND_ROWS, FIRST_ROW_ONLY);
          if($result[1] > 0){
              // check if there is schedule in the nearest 2 months
              $datediff = round(time_diff($result[0]['date_from'], $date_from) / 86400);
              if($datediff < 60){
                  $sql = 'SELECT * FROM '.TABLE_SCHEDULE_TIMEBLOCKS.' WHERE schedule_id = '.(int)$result[0]['id'].' ORDER BY week_day ASC LIMIT 0, 1';
                  $result_day = database_query($sql, DATA_AND_ROWS, FIRST_ROW_ONLY);
                  if($result_day[1] > 0){
                      $next_date = date('Y-m-d', strtotime('next '.$arr_weekdays_en[(int)$result_day[0]['week_day']], strtotime($result[0]['date_from'])));
                      $msg = draw_message(_NEXT_APPOINTMENT_AT.' '.format_date($next_date, '', '', true).' ('.$arr_weekdays_loc[(int)$result_day[0]['week_day']].')', false);
                  }else{
                      echo "Next appointment at ".$result[0]['date_from'];
                  }
              }else{

                   echo "Next appointment more than in 2 months";
              }
          }else{
                  echo "No appointments available at this time";
          }

          return $msg;
      }





} //end class Busquedadoctores



























?>
