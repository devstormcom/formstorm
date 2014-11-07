/*
	SHChat
	(C) 2006-2013 by Scripthosting.net
	http://www.shchat.net

	Free for non-commercial use:
	Licensed under the "Creative Commons 3.0 BY-NC-SA"
	http://creativecommons.org/licenses/by-nc-sa/3.0/
	
	Support-Forum: http://board.scripthosting.net/viewforum.php?f=18
	Don't send emails asking for support!!
*/


/************************************
 * AJAX Funktionalität mit MooTools
 * Requires 'mootools-core.js'
 ***********************************/

/**
 * AJAX Request mit MooTools absenden
 * @param String url / URL, die aufgerufen werden soll
 * @param String id / HTML Element
 * @param int periodical / Optional: Falls der Aufruf periodisch wiederholt werden soll
 * @param Boolean refresh / Wird die Seite bei Erfolg nachgeladen?
 * @return
 */
function mooRequest( url, id, periodical, refresh ){
	
	var request = new Request({
		url: url,
		async: true,
		method: 'get',
		update: 'refresh-me',
		evalScripts: false,
		evalResponse: false,
		onComplete: function(response) {
			if( id != null && id != "dummy" ){ 
//				$(id).set('html',response);
				$(id).innerHTML = response;
			}
			if( response != "" && response != null && response != "undefined" ){
				window.response = response;
				Slimbox.scanPage(); // Slimbox nach Grafiken suchen lassen
				UnTip(); // Tooltip entfernen, falls er noch an der Maus hängt!
			}			
			if( refresh == true ){ document.location.href='./'; }
		}
	});

	// request absenden und maximal drei Versuche tätigen
	var i=0;
	do {
		request.cancel();
		request.send();
		i++;
	} while( window.response == null && id != "dummy" && i < 3 );
	
	// Optional: Wiederhole die Anforderung in diesem Zeitabstand
	if( periodical != null && parseInt(periodical) != 0 ){
		var doMany = function() {
			request.cancel();
			request.send();
		};		
		doMany.periodical(periodical);
	}
	
	return request;
}


/**
 * Startmethode wird beim Login ausgeführt
 * @return
 */
function onStart(){	
	if( $('chat') != null && $('online') != null && $('channel') != null && $('chat_content') != null ){
		if( !Browser.ie6 && !Browser.ie7 && !Browser.ie8 ) $('chat').fade("hide");			
		var t_scrollDown = window.setInterval( function() { if( $('autoscroll').checked ){ scrollDown("chat_content"); } }, 200 );
		var t_online = mooRequest('xml/xml.online.php','online', 9789);
		var t_channel = mooRequest('xml/xml.channel.php','channel', 59889);
		var t_chatContent = mooRequest('xml/xml.content.php','chat_content', 3089);
		if( $("text") != null ) {
			$("text").focus();
		}
		if( !Browser.ie6 && !Browser.ie7 && !Browser.ie8 ) $('chat').fade("in");
	}
}


/**
 * Senden von Chatbefehlen
 * @returns {Boolean}
 */
function SendData(){
	
    url="xml/xml.chatinput.php?";    
    var input = $('text').value;
    var string = "text="+translate_charset(input);

    // Textfeld leeren und neu fokussieren
    $('text').value="";
    $('text').focus();
    
    // Request senden
    mooRequest(url + string,'dummy');

    // JOIN Befehl
    if( input.substring(0,6) == "/join " || input.substring(0,3) == "/j "  || input.substring(0,5) == "/sep " ){
    	window.setTimeout("document.location.reload()",100);
    	return false;
	}
    // QUIT-Befehl
    if( input.substring(0,5) == "/quit" ){
    	$clear(t_online);
    	$clear(t_channel);
    	$clear(t_chatContent);
    	if( $('autoscroll').checked ){
    		window.clearInterval(t_scrollDown);
    	}
    	document.location.href='system/logout.php';
    	return false;
    }    
    // Chatinhalt nachladen
    mooRequest('xml/xml.content.php','chat_content');
    return false;
}


/**
 * Smiley einfügen
 */
function setsmiley( what ){
	$("text").value = $("text").value+" "+what;
	$("text").focus();
	smileys(0);
}


/**
 * Text flüstern
 * @param name
 */
function whisper( name ){
	var getText = $("text").value;
	if( name.indexOf(' ') == -1 )
		$("text").value='/w ' + name + " " + getText;
	else
		$("text").value='/w <' + name + "> " + getText;
	$("text").focus();
}


/**
 * WHOIS Abfrage
 * @param who
 */
function whois( who ){
	var whois = window.open ("index.php?site=whois&user=" + who,"whois",
	"width=600,height=500,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no");
}


/**
 * Profil aufrufen
 */
function profil(){
	var profil = window.open ("index.php?site=profil","profil",
	"width=600,height=500,left=50,top=50,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no");
}


/**
 * WHOISONLINE aufrufen
 */
function whoisonline(){
	var whoisonline = window.open ("index.php?site=whoisonline","whoisonline",
	"width=600,height=500,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no");
}


/**
 * Anzeigen der Userlist
 */
function userlist(){
	var userlist = window.open ("index.php?site=userlist","userlist",
	"width=600,height=500,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no");
}


/**
 * Anzeigen der TOP10
 */
function top10(){
	var top10 = window.open ("index.php?site=top10","top10",
	"width=600,height=500,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no");
}


/**
 * Anzeigen der HILFE
 */
function help(){
	var help = window.open ("index.php?site=help","help",
	"width=600,height=520,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no");
}


/**
 * Smileyliste anzeigen
 * @param toggle
 */
function smileys(toggle){
	
	if( toggle == 0 ){
		$("smileys").style.display = "none";
	}
	else if( toggle == 1 ){
		$("smileys").style.display = "block";
	}
	
}


/**
 * Adminpanel aufrufen
 */
function admin(){
	var help = window.open ("admin.php","admin",
	"width=600,height=500,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no");
}


/**
 * Einen Kanal betreten
 * @param channel
 */
function join(channel){
	$("text").value='/join '+ channel;
	SendData();
	$("chat").fade("out");
}


/**
 * Ein Element nach unten scrollen
 * @param id
 */
function scrollDown( id ){
	if ( $(id).scrollTop != $(id).scrollHeight )
		$(id).scrollTop=$(id).scrollHeight;
}

/**
 * Ein Element nach oben scrollen
 * @param id
 */
function scrollUp( id ){
	$(id).scrollTop=0;
}


/**
 * Vom Chat abmelden
 */
function logout(){
	$('chat').fade("out");
	document.location.href='system/logout.php';
}