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
      foreach ($total_doctores[0] as $val)
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

                  //guia strftime   http://php.net/manual/es/function.strftime.php

         date_default_timezone_set("America/Bogota");
         setlocale(LC_TIME, 'spanish');

         $start_day = isset($_GET['start_day']) ? $_GET['start_day'] : date('Y-m-d');

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

        $prev_week_day = date('Y-m-d', strtotime('-6 day', $current_day));
        $next_week_day = date('Y-m-d', strtotime('+6 day', $current_day));

        //$docid_param = isset($parametros_de_busqueda['doctor_id']) ? '&docid='.(int)$parametros_de_busqueda['doctor_id'] : '';

         ?>

     <table>

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
                  ?>

                  <a href=<?php echo $_SERVER["PHP_SELF"]."?start_day=". $prev_week_day ?> > <img src='./img/but_prev_active.png'> </a>


                  <?php
                 }
                 else{

                 ?>

                                       <img src="./img/but_prev.png" >

                 <?php
                    }
                 ?>

               </td>


               <td>

               <?php

               for($i=0; $i<7; $i++){
               $current_date = ($i == 0) ? $current_day : strtotime('+'.$i.' day', $current_day);


                $arr_dates[] = array(

                                      'dia' => strftime(" %A ", $current_date),
                                      'dia_mes_año' => strftime(" %b %d %Y ", $current_date),
                                      'week_day_num'=>(date('w', $current_date)+1) // w	Representación numérica del día de la semana
                                                                                   // 0 (para domingo) hasta 6 (para sábado)
                                   );


               }

               $days_count = 0;

               foreach ($arr_dates as $key ) {

                  echo '<td class="th_week_day'.(($days_count++ % 2 == 0) ? ' wd_colored' : ' wd_white').'">'.$key['dia'].'<br>'. $key['dia_mes_año'] .'</td>';
                  echo $key['week_day_num'];

                }

               ?>


               </td>


             <td class="go_next">

                 <a href=<?php echo $_SERVER["PHP_SELF"]."?start_day=". $next_week_day ?> > <img src='./img/but_next_active.png'> </a>



             </td>

           </tr>

           </table>

         </td>

         <?php
           }
           ?>

      </tr>

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
                   <tr valign="top">
                     <td colspan="3">
                       <div class="doctor_profile">
                                          <img class="doctor_small_photo" src=./img/doctors/<?php echo $photo ?> > <br>
                         <?php

              echo " <a  href=\" " . $_SERVER["PHP_SELF"] . "?page=doctors&docid=".(int)$key. "\"> $doc_name </a> ";
              echo $val['medical_degree']."<br />". $speciality_name;

                      ?>

                       </div>

           <?php

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





} //end class Busquedadoctores



























?>
