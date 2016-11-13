use medicalappointment

SET lc_time_names = 'es_CO';

/*
 * 
 * Fecha de finalizacion debe ser posterior a la fecha de inicio!, por favor, vuelva a entrar.
 * 
 * nombre: invierno 
 * valido desde fecha :  diciembre 21 2016
 * valido para : marzo 20 2017   => no 2016      
 *  al mostrar los siete dias de la semana me muestra a partir del dia siguiente osea desde el 21 de marzo y no muestra horarios desde diciembre de 2016 
 *  ni los meses siguientes sino hasta marzo de 2017
 * 
 * La razon por la que no mostraria horarios en diciembre 2016 es que si estoy en octubre meses antes de diciembre 2016 verifica si ya estoy en diciembre si no no muestra.
 * 
 * muestra horarios en todo el año hasta diciembre 20 del 2017 y no hasta el dia 21 fecha limite
 *  
 * */

select *
from schedules

select *, DATE_FORMAT(date_from,'%W %d %M %Y') as fecha_desde, DATE_FORMAT(date_to,'%W %d %M %Y') as fecha_hasta
from schedules
where doctor_id=1

select *
from schedule_timeblocks
order by week_day asc limit 0,1

select *
from timeoffs


select sh.id, sh.name, sh.date_from, sh.date_to, stm.doctor_address_id, stm.week_day, stm.time_from, stm.time_to, stm.time_slots
from schedules sh
inner join schedule_timeblocks stm  ON  sh.id = stm.schedule_id
where sh.doctor_id = 1 




/*Direccion doctor */

select *
from doctors

show create table doctors

INSERT INTO doctors 
(first_name,
 middle_name,
 last_name,
 gender,  #enum
 birth_date,
 title,   #enum
 b_address,
 b_address_2,
 b_city,
 b_state,
 b_country,
 b_zipcode,
 email,
 user_name,
 user_password,
 preferred_language,
 date_created,   #datetime
 date_lastlogin, #datetime
 registered_from_ip,
 last_logged_ip,
 email_notifications, #tinyint 1
 notification_status_changed, #datetime
 medical_degree,
 additional_degree,
 license_number,
 education,
 experience_years,
 residency_training,
 hospital_affiliations,
 board_certifications,
 awards_and_publications,
 languages_spoken,
 insurances_accepted,
 doctor_photo,
 doctor_photo_thumb,
 work_phone,
 work_mobile_phone,
 phone,
 fax,
 default_visit_price,  #decimal 10,2
 default_visit_duration, #tinyint 3
 membership_plan_id,  #int
 membership_images_count,   #tinyint 1 
 membership_addresses_count, #tinyint 1
 membership_show_in_search, #tinyint 1
 membership_expires,  #date
 is_active,      #tinyint 1
 is_removed,     #tinyint 1
 comments,   #text
 registration_code
)
values('Martin','David','Aristizabal','m','1989-04-18','doctor4@email.me','doctor4','123','MBBS','80','15','4','1','1','0')

select *
from doctor_specialities

select *
from membership_plans

select *
from membership_plans_description
where language_id = 'es'

select *
from specialities

select *
from specialities_description
where language_id='es'

SELECT
      s.id,
      sd.name,
      sd.description
  FROM specialities s
  LEFT OUTER JOIN specialities_description sd ON s.id = sd.speciality_id AND sd.language_id = 'es'
  ORDER BY name ASC

/*************************/


/* Citas medicas */

select *
from visit_reasons

select *
from visit_reasons_description
where language_id='es'

select *
from appointments


select ap.id, 
ap.appointment_number, 
ap.appointment_description,
ap.appointment_date,
ap.appointment_time,
ap.visit_duration,
ap.first_visit,
pt.user_name as paciente, 
CONCAT_WS(' ', dc.first_name, dc.last_name) as doctor, 
spd.name
from appointments ap
inner join patients pt on pt.id = ap.patient_id
inner join doctors dc on ap.doctor_id = dc.id
inner join doctor_specialities dp on dp.doctor_id = dc.id
inner join specialities_description spd on spd.speciality_id  = dp.speciality_id
where spd.language_id in('es')
group by ap.id

/*******************/


select *
from vocabulary
where language_id='es'


select *
from vocabulary
where key_value='_NO_SEARCH_CRITERIA_SELECTED' and language_id in('es','en')


  SELECT
		ms.id,
		ms.module_name,
		ms.settings_key,
		ms.settings_value
	FROM modules m
		INNER JOIN modules_settings ms ON m.name = ms.module_name
	WHERE m.is_installed = 1
	
	
	

select first_name, last_name, user_name, user_password, email
from patients
	
select *
from accounts




select *
from doctor_specialities 

select *
from specialities_description

SELECT
d.id as doctor_id,
sd.id as speciality_id,
sd.name as speciality_name
FROM doctor_specialities ds
INNER JOIN doctors d ON ds.doctor_id = d.id
INNER JOIN specialities_description sd ON ds.speciality_id = sd.speciality_id AND sd.language_id = 'en'


select *
from doctor_addresses 


SELECT * 
FROM modules 
WHERE is_installed = 1 AND is_system = 0 
ORDER BY priority_order ASC


SELECT
ms.id,
ms.module_name,
ms.settings_key,
ms.settings_value
FROM modules m
INNER JOIN modules_settings ms ON m.name = ms.module_name
WHERE m.is_installed = 1


select patients .*, first_name, last_name, user_name
from patients 















