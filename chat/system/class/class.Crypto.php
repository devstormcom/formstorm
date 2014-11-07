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

class Crypto {
	
	const CRYPTO_VERSION = 0700;
	
	private $key, $iv;
	
	/**
	 * Initialize Crypto with secret key and iv
	 * @param String $key password used for enryption
	 * @param String $iv Optional: INIT_VECTOR
	 * @return void
	 */
	public function Crypto( $key = '', $iv = '0000000000000000' ){
	
		if( $key != NULL ){
    		$this->key = $key;
    	}
    	elseif( ($key = $this->readKeyFile()) != "" ){
    		$this->key = $key;
    	}
    	else{
    		throw new InvalidKeyException("No key entered in contructor Crypto( \$key ).");
    	}
   		$this->iv = $iv;
 	}
	
	
	/**
	 * Encrypt a string with Rijndael-128
	 * @param String $text
	 * @param int $mode (0=binary, 1=base64, 2=hexadecimal)
	 * @return String
	 */
	public function encrypt( $text, $mode = 0 ){
		
		$size = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, 'cbc');
		$cipher = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', 'cbc', '');
      
		// Add padding to String
		$text = $this->pkcs5Pad("CONTENT=".$text, $size);
		
		mcrypt_generic_init($cipher, $this->key, $this->iv);
		$text = mcrypt_generic($cipher,$text);
		
		mcrypt_generic_deinit($cipher);

		// Base64
		if( $mode == 1 ){
			$text = base64_encode($text);
		}
		// Hexadezimal
		elseif( $mode == 2 ){
			$text = bin2hex($text);
		}
		
		return $text;
	}
	
	
	/**
	 * Decrypt a string with Rijndael-128
	 * @param String $encrypted
	 * @param int $mode (0=binary, 1=base64, 2=hexadecimal)
	 * @return String
	 */
	public function decrypt( $encrypted, $mode = 0 ){
		
		if( $encrypted != NULL ){		
			$size = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, 'cbc');
			$cipher = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', 'cbc', '');
	      
			mcrypt_generic_init($cipher, $this->key, $this->iv);
	
			// Base64
			if ( $mode == 1 ) {
				$encrypted = base64_decode($encrypted);
			}
			// Hexadezimal
			elseif( $mode == 2 ){
				// pack() is used to convert hex string to binary
				$encrypted = pack('H*', $encrypted);
			}		
		  
			$decrypted = mdecrypt_generic($cipher, $encrypted);
			mcrypt_generic_deinit($cipher);
			
			// Check valid string
			if( substr($this->pkcs5Unpad($decrypted),0,8) == "CONTENT=" ){
				return substr($this->pkcs5Unpad($decrypted),8);
			}
			else{
				throw new InvalidKeyException("Invalid password entered for decrypt operation");
			}
		}
		return "";
	}
	
	
	/**
     * Adds pkcs5 padding
     * @return Given text with pkcs5 padding
     * @param string $data
     *   String to pad
     * @param integer $blocksize
     *   Blocksize used by encryption
     */
	private function pkcs5Pad($data, $blocksize){
        
        $pad = $blocksize - (strlen($data) % $blocksize);
        $returnValue = $data . str_repeat(chr($pad), $pad);
        
        return $returnValue;
    }
    
    
    /**
     * Removes padding
     * @return Given text with removed padding characters
     * @param string $data
     *   String to unpad
     */
    private function pkcs5Unpad($data) {
      
		$pad = ord($data{strlen($data)-1});
		if ($pad > strlen($data)) return false;
		if (strspn($data, chr($pad), strlen($data) - $pad) != $pad) return false;

		return substr($data, 0, -1 * $pad);
    }
    
    
    /**
     * Auslesen der .crypto Keyfile
     * @return String
     */
    private function readKeyFile(){
    	
    	global $config;
    	// Versuche die Keyfile zu lesen
    	$filename = $config["system_path"]."/config/.crypto";
    	if( file_exists($filename) )
    		$key = file_get_contents($filename);
    	else{
    		$this->writeKeyFile();
    		return $this->readKeyFile();
    	}
    	
    	// Schreibe das Passwort in $this->key
    	if( trim($key) != "" ){ 
    		return trim($key);
    	}
    	return "";
    }
    
    
    /**
     * Schreiben einer .crypto Keyfile
     * @return void
     */
    private function writeKeyFile(){
    	
    	global $config;
    	// Versuche eine neue Keyfile zu schreiben
   		$file = $config["system_path"]."/config/.crypto";
    	
    	if( !file_exists($file) ){
	    	$key = $this->randomString(16,true);
	    	file_put_contents($file,$key);
	    	return;
    	}else{
    		throw new Exception("Error: Key file already exists!");
    		return;
    	}
    }
    
    
	/**
	 * Generiert einen Random-String
	 * @param $len Integer String-Length
	 * @return String
	 */
	private function randomString( $len, $complexity = false ){ 		
		// Der String $possible enthält alle Zeichen, die verwendet werden sollen
		if( $complexity == true ){
			$possible = "ABCDEFGHJKLMNPRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789.-!$";
		}
		else{ 
			$possible = "ABCDEFGHJKLMNPRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789";
		} 
		$str=""; 
		
		while( strlen($str) < $len ) { 
		  $str.=substr($possible,(rand()%(strlen($possible))),1);
		}
		 
		return $str;
	}
}


class InvalidKeyException extends Exception {	
	public function InvalidKeyException( $message, $code = 0 ){
		new Exception( $message, $code );
	}
}
?>