<?php
/*
	SHChat
	(C) 2006-2012 by Scripthosting.net
	http://www.shchat.net

	Free for non-commercial use:
	Licensed under the "Creative Commons 3.0 BY-NC-SA"
	http://creativecommons.org/licenses/by-nc-sa/3.0/
	
	Support-Forum: http://board.scripthosting.net/viewforum.php?f=18
	Don't send emails asking for support!!
*/

class Format {	
	
	const FORMAT_VERSION = 0400;

	
	/**
	 * Returns a readable timestamp for given seconds
	 * @param $seconds
	 * @return String
	 */
	public function getTime($seconds)
	{
	    $units = array( "Tag|e"=>86400, 
	                    "Stunde|n"=>3600,
	                    "Minute|n"=>60,
	                    "Sekunde|n"=>1
	                    );
	
	    if($seconds < 1) {
	        return "< 1 Sekunde";
	    }
	
		else	
		{
	        $show = false;
	        $ausgabe = "";
	        foreach($units as $key=>$value) 
	        {
	            $t = floor($seconds/$value);
	            $seconds = $seconds%$value;
	            list($s, $pl) = explode("|", $key);
	            if($t > 0 || $show) {
	                if($t == 1) {
	                    $ausgabe .= $t." ".$s.", ";
					} 
	
					else {
		                    $ausgabe .= $t." ".$s.$pl.", ";
					}
					$show = true;
	            }
	        }
	        $ausgabe = mb_substr($ausgabe, 0, mb_strlen($ausgabe)-2);
	        return $ausgabe;
	    }
	}	
		
	
	/**
	 * Returns formatted Time
	 * @param $seconds
	 * @return String
	 */	
	public function getFormatTime( $seconds ){
		
	    $units = array("Stunde|n"=>3600,
	                    "Minute|n"=>60,
	                    "Sekunde|n"=>1
	                    );
	
	    if($seconds < 1) {
	        return "00:00:00";
	    }
	
		else	
		{
	        $show = false;
	        $ausgabe = "";
	        foreach( $units as $key=>$value ) 
	        {
	            $t = floor( $seconds/$value );
	            $seconds = $seconds%$value;
	            	
				if($t<10){$t = "0".$t;}
				$ausgabe .= $t.":";
			} 
	
	        $show = true;
	        $ausgabe = mb_substr($ausgabe, 0, mb_strlen($ausgabe)-1);
	        
	        return $ausgabe;
	    }
	}
}
?>