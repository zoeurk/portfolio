<?php
require('sql.php');
function generateRandomString($length = 25) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}
$db = "Portfolio";
$nom = $_POST['inom'];
$prenom = $_POST['iprenom'];
$email = $_POST['iemail'];
$about = str_replace("'",'&apos;',$_POST['about']);
$about = str_replace('"','&quot;',$about);
$passwd = $_POST['ipasswd'];
$passwd_ = $_POST['ipasswd_'];
if(strcmp($passwd,$passwd_)!=0){
	die("Les mots de passe diff&egrave;rent");
}
$pass = password_hash($passwd, PASSWORD_DEFAULT);
$conn = new mysqli($server,$user,$password,$db);
if($conn->connect_error){
	die("connection failed: " . $conn->error);
}
$url = generateRandomString();
$sql = "SELECT * FROM `users` WHERE `url` = '$url'";
while($reslut = $conn->query($sql)){
	if($reslut->num_rows > 0)
	{
		$url = generateRandomString();
		$sql = "SELECT * FROM `users` WHERE `url` = '$url'";
	}else{
		break;
	}
}
$passwd_url = generateRandomString();
$sql = "SELECT * FROM `users` WHERE `passwd_url` = '$passwd_url'";
while($reslut = $conn->query($sql)){
	if($reslut->num_rows > 0)
	{
		$passwd_url = generateRandomString();
		$sql = "SELECT * FROM `users` WHERE `passwd_url` = '$passwd_url'";
	}else{
		break;
	}
}
if($email != ""){
	//$about = urldecode($about);
	$sql = "INSERT INTO users (Nom,Prenom,Email,about,Password,passwd_url,url,aime) VALUES ('$nom','$prenom','$email', '$about','$pass','$passwd_url', '$url',0)";
	if($conn->query($sql) === TRUE){
		echo("New user created successfully<br>");
		$sql = "CREATE TABLE `Portfolio`.`$url` (Projet VARCHAR(255) NULL DEFAULT NULL , UNIQUE `$url` (`Projet` (15)), Description VARCHAR(255) NULL DEFAULT NULL, size INT UNSIGNED) ENGINE = InnoDB";
		if($conn->query($sql) === TRUE){
			$pathname = "../upload/" . $url;
			mkdir($pathname);
			echo("Une nouvelle table vient d'&ecirc;tre cr&eacute;e.<br>");
		}else{
			echo "Error: " . $sql . "<br>" . $conn->error;
		}
	}else{
		echo "Error: " . $sql . "<br>" . $conn->error;
	}
	echo "<br><a href='http://$host$path/index.php'>retour &agrave; l'accueil</a>";
}
$conn->close();
?>
