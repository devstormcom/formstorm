<?php
namespace devStorm\Library\Error;
/**
 * Error msg's
 *
 * @author Flavio Kleiber <flavio.kleiber@gentleman-informatik.ch>
 * @copyright (c) 2014 Flavio Kleiber, Gentleman Informatik
 * @package devstorm.library.error
 */
 
 class Notification {
 	// BASIC MSG'S //
 	const ERROR_GITHUB_PROBLEM = "Anscheined git es gerade ein Problem mit github.com, wir versuchen den Fehler zu finden.";
 	const ERROR_USER_ALLREADY_EXISTS = "Der User mit dem Username ###username### existiert schon.";
    const ERROR_EAMIL_ALLREADY_EXISTS = "Der User mit dem Username ###email### existiert schon.";
    const ERROR_OOPS = "I think he is dead gim! Da ging was schief, bitte versuche es doch nochmal";
 }
 
?>