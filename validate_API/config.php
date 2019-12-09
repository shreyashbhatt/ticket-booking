<?php
    define("DB_HOST", 'localhost');
    define("DB_USER", 'root');
    define("DB_PASSWORD", '');
    define("DB_DATABASE", 'ticketbooking');
  
    if(!function_exists ( "clean" )) {
        function clean($str) {
            $str = @trim($str);
            if(get_magic_quotes_gpc()) {
                $str = stripslashes($str);
            }
            return mysql_real_escape_string($str);
        }
    }

	function clean_input($str,$type)
	{
		$str = @trim($str);
		if(get_magic_quotes_gpc()) {
			$str = stripslashes($str);
		}
		$str = mysql_real_escape_string($str);
		$str = str_replace("'",'"',$str);
		if($type == "K_EMAIL") {
			
			if(!eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $str)) { 
				redirect();
			}
		}
		else if($type == "K_INT") {
			if (!(filter_var($str, FILTER_VALIDATE_INT) === 0) AND filter_var($str, FILTER_VALIDATE_INT) === false) {
				redirect();
			}
		}
		else if($type == "K_TEXT") {
			
		}
		else if($type == "K_BOOL") {
			if (!is_bool($str) === true) {
				redirect();
			}
		}
		else if($type == "K_VARCHAR") {
			if (!ctype_alnum($str)) 
				redirect();
		}
		else if($type == "K_STRING") {
			if (ctype_alpha($str))
				redirect();
		
		}
		return $str;
	}
?>
