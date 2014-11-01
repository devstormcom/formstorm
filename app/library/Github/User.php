<?php
namespace devStorm\Library\Github;
/**
 * Userobject
 *
 * @author Flavio Kleiber <flavio.kleiber@gentleman-informatik.ch>
 * @copyright (c) 2014 Flavio Kleiber, Gentleman Informatik
 * @package devstorm.library.github
 */
 
 class User{
 	
 	/**
 	 * Github API URL
 	 */
 	private $url = 'https://api.github.com';

 	private $accessToken = null;

 	private $response = null;

 	public function __construct($accessToken) {
 		$this->accessToken = $accessToken;
 		//$this->response = 
 	}

 	public function request($method) {
 		try {
 			$client = new HttpClient();
            return json_decode((string)$client->get($this->url . $method . '?access_token=' . $this->accessToken)->send()->getBody(),true);
 		} catch (\Exception $e) {
 			return false;
 		}
 	}

 	public function isValid() {
 		return is_array($this->response);
 	}
 }
 
?>