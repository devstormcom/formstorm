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

$user = new User();

// Benutzerliste holen
$output = "";
$i=1;
$result = $user->result("SELECT * FROM {$db["user"]} u LEFT JOIN {$db["userdetails"]} d ON u.id=d.id LEFT JOIN {$db["groups"]} g ON u.level=g.level ORDER BY u.level DESC, u.username");
$count = $result->num_rows;

while( $row = $result->fetch_assoc() ){
	
	$mod = $i%2;
	$reg_date = date("d.m.Y",$row["reg_date"]);
	
	if( $row["status"] == 0 ){
		$status = "<i>Offline</i>";
	}
	elseif( $row["status"] == 1 ){
		$status = "<b>Online</b>";
	}
	elseif( $row["status"] == 2 ){
		$status = "<span style=\"color:#FF0000;\"><i>Offline</i></span>";
	}
	elseif( $row["status"] == 3 ){
		$status = "<span style=\"color:#FF0000;\"><i>unbestätigt</i></span>";
	}
	
	$output .= "<tr>\r\n\t";
	$output .= "<td width='20%' class='admin_menu{$mod}' style='text-align:left;'><a href='?action=user_edit&amp;id={$row["id"]}'>{$row["username"]}</a></td>";	// Table Data
	$output .= "<td width='20%' class='admin_menu{$mod}' style='text-align:left;'>{$row["rang"]}</td>";	// Table Data
	$output .= "<td width='20%' class='admin_menu{$mod}' style='text-align:left;'>{$reg_date}</td>";	// Table Data
	$output .= "<td width='20%' class='admin_menu{$mod}' style='text-align:left;'>{$status}</td>";	// Table Data
	$output .= "</tr>";
	$output .= "\r\n\t\t";
	$i++;
}
// Ende Benutzerliste holen

$vars = array(
					"{template_name}" => $config["template_name"],
					"{output}"	=> $output,
					"{count}" => $count,

);

################################################################
#### AB HIER NICHTS ÄNDERN !!! 
#### Teamplate einbinden und definierte Variablen ersetzen
################################################################

$open = file_get_contents($config["template_path"]. "/admin_user.html");
      
foreach($vars as $key => $value)
{
   $open = str_replace($key,$value,$open);
}

echo $open;

?>