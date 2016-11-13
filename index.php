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
			
		
		</div>
		
		
		<div id="contderecho">
			
			
			<?php

             //guia strftime   http://php.net/manual/es/function.strftime.php

			date_default_timezone_set("America/Bogota"); 
			setlocale(LC_TIME, 'spanish'); 

			$start_day = isset($_GET['start_day']) ? $_GET['start_day'] : date('Y-m-d'); 

			//date('Y-m-d') 
			$current_day = strtotime($start_day); 
			$arr_dates = array(); 


			$prev_week_day = date('Y-m-d', strtotime('-6 day', $current_day)); 
			$next_week_day = date('Y-m-d', strtotime('+6 day', $current_day)); 

			?> 
			
	<table>

		<tr valign="top">
			<td class="thleft">Doctores</td>
            <td class="thright">
				<table cellspacing="0" cellpadding="0"> 
					<tr> 
						<td class="go_prev"> 
							<?php 
							
							if($prev_week_day >= date('Y-m-d')) 
							{ 
							
							
							echo " <a href=" . $_SERVER["PHP_SELF"] . "?start_day=" . $prev_week_day. "><img src='./img/but_prev_active.png'> </a>"; 
							
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
						                       'dia_mes_año' => strftime(" %b %d %Y ", $current_date)

						 	                  );


						}

						$days_count = 0;

						foreach ($arr_dates as $key ) {
						 	
						 	 echo '<td class="th_week_day'.(($days_count++ % 2 == 0) ? ' wd_colored' : ' wd_white').'">'.$key['dia'].'<br>'. $key['dia_mes_año'] .'</td>';

						 	
						 } 

						?>


						</td>


					<td class="go_next"> 
					<?php 

					echo " <a href=" . $_SERVER["PHP_SELF"] . "?start_day=" . $next_week_day. "> <img src='./img/but_next_active.png'> </a>"; 
											



					?> 


					</td> 

				</tr> 

				</table> 

			</td>
	 </tr>

	</table>
					

		
</div>
	
</div>

	
	</div>
	


</body>
</html>