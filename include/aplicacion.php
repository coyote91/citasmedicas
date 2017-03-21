<?php

/**
 *
 */
class Aplicacion
{
  private static $params = array(
		                                'lang' => 'en',
                                    'lang_dir'          => 'ltr',
                                    	'template'        	=> 'default',

                              	);


  public static function Get($param = '', $val = '')
	{
		if(isset(self::$params[$param])){
			if(is_array(self::$params[$param])){
				return isset(self::$params[$param][$val]) ? self::$params[$param][$val] : '';
			}else{
				return isset(self::$params[$param]) ? self::$params[$param] : '';
			}
		}else{
			return '';
		}
	}

}


?>
