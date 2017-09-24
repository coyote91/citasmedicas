<?php
include'./include/Especialidadmedico.class.php';
include'./include/Busquedadoctores.class.php';
include'./include/Aplicacion.class.php';
include'./include/Direccionmedico.class.php';
include'./include/Bloquesdetiempo.class.php';
include'./include/otrasfunciones.php';
include'./include/funcioneshtml.php';

define("_SUNDAY","Sunday");
define("_MONDAY","Monday");
define("_TUESDAY","Tuesday");
define("_WEDNESDAY","Wednesday");
define("_THURSDAY","Thursday");
define("_FRIDAY","Friday");
define("_SATURDAY","Saturday");
define("TABLE_SCHEDULES", "schedules");
define('TABLE_SCHEDULE_TIMEBLOCKS', 'schedule_timeblocks');
define('TABLE_APPOINTMENTS', 'appointments');
define('TABLE_TIMEOFFS', 'timeoffs');

$doctor_speciality = isset($_POST['doctor_speciality']) ? $_POST['doctor_speciality'] : '';
$doctor_name = isset($_POST['doctor_name']) ? $_POST['doctor_name'] : '';
$doctor_location = isset($_POST['doctor_location']) ? $_POST['doctor_location'] : '';
$doctor_id         = isset($_REQUEST['docid']) ? (int)$_REQUEST['docid'] : '';

$parametros_de_busqueda  = array('speciality' => $doctor_speciality,
                           'name' => $doctor_name,
                           'location' => $doctor_location,
                           'doctor_id' => $doctor_id

                          );

echo "Especialidad del doctor es ". $doctor_speciality;
echo $doctor_name;
echo $doctor_location;



?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Document</title>

<link rel="stylesheet" href="./css/style.css">


</head>
<body>


	<div id="contglobal">

	<div id="continfo">

	   <div id="contizquierdo">

			<form action="index.php?page=find_doctors" id="frmFindDoctors" name="frmFindDoctors" method="post" >

			   <input type="hidden" name="p" id="page_number" value="1" />
				 <input type="hidden" name="start_day" id="start_day" value=<?php echo date('Y-m-d') ?> />
				 <input type="hidden" name="docid" id="docid" value="" /> <!--- parece que no se esta usando -->


				   <table class="tblSideBlock" cellspacing="0">

					 <?php $total_specialities = Especialidadmedico::especialidadesactivas();
					 		if($total_specialities[1] == 1)
							{
						?>
								<tr>
									 <td>
								      <input type="hidden" name="doctor_speciality" id="doctor_speciality" value=<?php $total_specialities[0][0]['id'] ?> />
									</td>

								</tr>
            <?php
							 }
							 else
							 {
             ?>

						 <tr valign="middle">
							  <td>Encuentre un Doctor por Especialidad</td>
						</tr>

						 <tr>
							  <td>
						       <select name="doctor_speciality" id="doctor_speciality">
						          <option value="">-- Seleccione --</option>
											  <?php
													 foreach($total_specialities[0] as $val)
													 {
														 $selected = (($val['id'] == $doctor_speciality) ? ' selected="selected" ' : '');
												?>
															<option value=<?php echo $val['id']. $selected ?> > <?php echo $val['name'] ?> </option>
                       <?php
										      }
												?>

						 			</select>
            <?php
					    }
						 ?>
					    </td>
						</tr>

					<tr>
						<td></td>
					</tr>

					<tr valign="middle">
						 <td>Búsqueda por Nombre</td>
					</tr>

					<tr>
						 <td><input type="text" name="doctor_name" maxlength="64" value=<?php echo $doctor_name ?> > </td>
					</tr>


				  <tr>
						<td></td>
					</tr>

					<tr valign="middle">
						 <td>Buscar por ubicación</td>
					</tr>

					<tr>
						<td> <input type="text" name="doctor_location" maxlength="64" value=<?php echo $doctor_location ?> > </td>
					</tr>


					<tr>
						 <td nowrap height="3px"> </td>
				  </tr>

					<tr>
						 <td> <input class="button" type="submit"  value="Encontrar médicos" /> </td>
					</tr>

			</table>

    	<form/>

		</div>



		<div id="contderecho">

   <?php

       if(empty($doctor_speciality) && empty($doctor_name) && empty($doctor_location) && empty($doctor_id))
       {
           echo "No hay criterios de búsqueda seleccionados! Por favor seleccione o introduzca los criterios de búsqueda y vuelve a intentarlo.";
        }
        else{

           busquedadoctores::resultadosbusqueda(Busquedadoctores::busquedaporparametros($parametros_de_busqueda), $parametros_de_busqueda);

           }

  ?>

   <?php


   ?>


		</div>

</div>


	</div>


  <script src="./js/main.js" charset="utf-8"></script>
  <script type="text/javascript" src="./js/jquery-1.6.3.min.js"></script>


</body>
</html>
