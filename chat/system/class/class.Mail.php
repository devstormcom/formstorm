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

class Mail {
	
	const MAIL_VERSION = 0916;
	
	/**
	 * Sendet eine E-Mail über Sendmail 
	 * @param String $sender
	 * @param String $receiver
	 * @param String $subject
	 * @param String $msg
	 * @return Boolean
	 */
	public function sendMail( $sender, $receiver, $subject, $msg, $f = false ){
		
		global $config;
		
		if( isset($config["smtp"]["isActive"]) && $config["smtp"]["isActive"] == true ){
			return $this->sendPHPMail($receiver,$subject,nl2br($msg));
		}
		else{
			(Boolean) $mail = false;
		
			if( $this->validate($receiver) && $this->validate($sender) ){
			
				$headers  = "From: {$sender}" . "\r\n";
				$headers .= "Content-type: text/plain; charset=UTF-8\r\n";
				$headers .= "Reply-To: {$sender}" . "\r\n";
				$headers .= "X-Mailer: PHP/" . phpversion();
			
				/* Verschicken der Mail */
				if(!$f){
					$mail = @mail($receiver, $subject, $msg, $headers);
				}else{
					$mail = @mail($receiver, $subject, $msg, $headers, "-f ". $sender);
				}
			}
			return $mail;
		}		
	}
	
	
	/**
	 * Sendet eine HTML E-Mail über Sendmail
	 * @param String $sender
	 * @param String $receiver
	 * @param String $subject
	 * @param String $msg
	 * @param Boolean $addHeader - HTML-Header hinzufügen
	 * @param String $backgroundColor
	 * @return Boolean
	 */
	public function sendHtmlMail( $sender, $receiver, $subject, $msg, $addHeader = true, $backgroundColor="#FFFFFF" ){
		
		global $config;
		
		if( isset($config["smtp"]["isActive"]) && $config["smtp"]["isActive"] == true ){
			return $this->sendPHPMail($receiver,$subject,$msg);
		}
		else{
			(Boolean) $mail = false;
			(String) $msg = trim($msg);
			
			if( $this->validate($receiver) && $this->validate($sender) ){
				// Text in ein gültiges HTML-Dokument umwandeln
				if( $addHeader == true ){
					/* Message */
					$msg = "<!doctype html>
					<html>
					<head>
						<title>{$subject}</title>
					</head>
					<body>
						<style type='text/css'> background-color:{$backgroundColor}; font-family:Arial,Tahoma,sans-serif;</style>
						<div>{$msg}</div>
					</body>
					</html>";
				}else{
					// Wenn die Nachricht bereits HTML-Code beinhaltet, müssen _keine_ Header hinzugefügt werden ! 
					$msg = trim($msg);
					$msg = str_replace("ï»¿","",$msg); // Elbresidenz FIX
				}
	
				/* Mail-Headers */
				$headers  = "MIME-Version: 1.0\r\n";
				$headers .= "Content-type: text/html; charset=UTF-8\r\n";
				$headers .= "From: {$sender}" . "\r\n";
				$headers .= "Reply-To: {$sender}" . "\r\n";
				$headers .= "X-Mailer: PHP/" . phpversion();
			
				/* Send Mail */
				$mail = @mail($receiver, $subject, $msg, $headers);
			}
			return $mail;
		}		
	}
	
	
	/**
	 * Sendet eine E-Mail über den PHPMailer
	 * @param String $receiver Empfänger
	 * @param String $subject Betreffzeile
	 * @param String $msg Nachrichtentext
	 * @param String $altSender Von der Config abweichender Absender
	 * @return Boolean
	 */
	public function sendPHPMail( $receiver, $subject, $msg, $altSender = '' ){
		
		global $config;
		
		// Mail Objekt initialisieren
		$mail = $this->initializePHPMailer();
		
		// E-Mail Daten füllen
		if( $altSender == "" ){
			$mail->SetFrom($config["smtp"]["sender"]);
		}else{
			if( $this->validate($altSender) ){
				$mail->SetFrom($altSender);
			}
			else{
				throw new MailException("Invalid E-Mail Address: " . $altSender);
			}
		}
		//$mail->AddReplyTo($sender);
		$mail->Subject = $subject;
		//$mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test
		$mail->MsgHTML($msg);
		$mail->AddAddress($receiver);
		
		return $mail->Send();
	}	
	
		
	/**
	 * Validates an E-Mail Address
	 * @param $email
	 * @return Boolean
	 */
	public function validate( $email ){
		if( filter_var($email, FILTER_VALIDATE_EMAIL) != false ) {
		    return true;
		}
		return false;
	}
		
	
	/** 
	 * Checks if an E-Mail Adress domain exists
	 * @param $email
	 * @return Boolean
	 */
	public function checkEmail( $email ){
		// checks proper syntax
		if( !$this->validate($email) ){
			return false;
		}
	
		// gets domain name
		list($username,$domain)=split('@',$email);
		// checks for if MX records in the DNS
		$mxhosts = array();
		if(!getmxrr($domain, $mxhosts)){
			// no mx records, ok to check domain
			if (!@fsockopen($domain,25,$errno,$errstr,2)){
				return false;
			}else{
				return true;
			}
		}
		else {
			// mx records found
		    foreach ($mxhosts as $host){
				if (@fsockopen($host,25,$errno,$errstr,2)){
					return true;
				}
			}
			return false;
		}
	}
	
	
	/**
	 * Initializiert einen SMTP-Versand über PHPMailer
	 * @return PHPMailer
	 */
	private function initializePHPMailer(){
		
		global $config;
		
		$crypto = new Crypto();
		$mail = new PHPMailer();
		
		$mail->IsSMTP(); // telling the class to use SMTP
		$mail->SMTPDebug  = 0;                     // enables SMTP debug information (for testing)
		                                           // 1 = errors and messages
		                                           // 2 = messages only
		$mail->SMTPAuth   = true;                  // enable SMTP authentication
		$mail->Host       = $config["smtp"]["hostname"]; // sets the SMTP server
		$mail->Port       = $config["smtp"]["port"];                    // set the SMTP port for the GMAIL server
		$mail->Username   = $config["smtp"]["username"]; // SMTP account username
		$mail->Password   = $crypto->decrypt($config["smtp"]["password"],2);    // SMTP account password
		
		return $mail;
	}
}


class MailException {
	public function MailException($message,$code=0){
		new Exception($message,$code);
	}
}
?>