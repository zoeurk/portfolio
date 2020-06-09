<html>
	<head>
		<title>supression</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
	</head>
	<body>
<?php
	require('sql.php');
	require('misc.php');
	ini_set('memory_limit', '2048M');
	function session_off(){
		header("Location:../index.php");
	}
#	function remove($path){
#		$files = scandir($path);
#		foreach($files as $f => $myfiles){
#			if(!is_dir($path . '/' . $myfiles)){
#				unlink($path . '/' . $myfiles);
#			}else{
#				if($myfiles != "." && $myfiles != ".."){
#					remove($path . '/' . $myfiles);
#					rmdir($path . '/' . $myfiles);
#				}
#			}
#		}
#	}
	$db = "Portfolio";
	$email = $_POST['remail'];
	$passwd = $_POST['rpasswd'];
	$name = $_POST['rprenom'];
	$firstname = $_POST['rnom'];
	$test = 0;
	$conn = new mysqli($server,$user,$password,$db);
	if($conn->connect_error){
		die("connection failed: " . $conn->error);
	}
	$sql = "SELECT Email,Password,url from users";
	$result = $conn->query($sql);
	if($result->num_rows>0){
		while($row = $result->fetch_assoc()){
			if($email === $row['Email']){
				$test++;
				//echo "email: " . $row['Email'] . " password: " . $row['Password'] . "<br>";
				if(password_verify($passwd, $row['Password']) == FALSE){
					session_off();
				}
				$url = $row['url'];
			}
		}
	}
	$sql = "DROP TABLE " . $url;
	$conn->query($sql);
	$sql = "SELECT * FROM `users` WHERE `Email` = '$email' AND `Prenom` = '$name' AND `Nom` = '$firstname'";
	$result = $conn->query($sql);
	if($result->num_rows == 1 ){
			$sql = "DELETE FROM `users` WHERE Email = '$email'";
			if($conn->query($sql) === TRUE){
				echo "Votre compte a &eacute;t&eacute; supprimer avec succ&egrave;s";
				$sql = "DROP TABLE IF EXISTS `$url`";
				if($conn->query($sql) === TRUE){
					$path = "../upload/" . $url;
					remove($path);
					rmdir($path);
					echo("Toutes vos donn&eacute;es ont &eacute;t&eacute; supprim&eacute;es.<br>");
				}else{
					echo "Error: " . $sql . "<br>" . $conn->error;
				}
			}else{
				echo "Error: " . $sql . "<br>" . $conn->error;
			}
		}else{
			echo "Error: " . $sql . "<br>" . $conn->error;
		}
		echo "<a href='../index.php'>retour &agrave; l'accueil</a>";
	?>
	</body>
</html>
