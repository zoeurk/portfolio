<?php
require('sql.php');
$db = "Portfolio";
$conn = new mysqli($server,$user,$password,$db);
if($conn->connect_error){
	die("connection failed: " . $conn->error);
}
$prenom = $_POST['fprenom'];
$nom = $_POST['fnom'];
$email = $_POST['femail'];
$sql = "SELECT `passwd_url` FROM `users` WHERE `Prenom` = '$prenom' AND `Nom` = '$nom' AND `Email` = '$email'";
if(($result = $conn->query($sql)) === FALSE){
	echo "Error: " . $sql . "<br>" . $conn->error;
}else{
	$row = $result->fetch_assoc();
	$url = $row['passwd_url'];
	$message = wordwrap("Follow this link for reset password\r\nhttp://$host$path/html/password.php?" . $url, 70, "\r\n");
	if(mail($email,"Change Your Password",$message) == True){
		echo "<p>Un email pour changer votre mot de passe vient de vous &ecirc;tre envoy&eacute;</p>";
	}else{
		echo "<p>Un echec est survenue lors de l'envoye de l'email</p>";
	}
}
$conn->close();
?>
