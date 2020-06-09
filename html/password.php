<html>
	<head>
		<title>password</title>
		<link href='https://fonts.googleapis.com/css?family=Sofia' rel='stylesheet'>
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
	</head>
	<body>
	<div class="container-fluid">
<?php
//echo $_SERVER['REQUEST_URI'];
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
$url = substr($_SERVER['REQUEST_URI'], -25);
$db = "Portfolio";
$conn = new mysqli($server,$user,$password,$db);
if($conn->connect_error){
	die("connection failed: " . $conn->error);
}
//$sql = "SELECT * FROM `users` WHERE `url` = '$url'";
if(isset($_POST['checkbox'])){
	$prenom = $_POST['fprenom'];
	$nom = $_POST['fnom'];
	$pass = $_POST['fpasswd'];
	$pass_ = $_POST['fpasswd_'];
	$email = $_POST['femail'];
	$sql = "SELECT * From `users` WHERE `Prenom` = '$prenom' AND `Nom` = '$nom' AND `Email` = '$email' AND `passwd_url` = '$url'";
	$result = $conn->query($sql);
	if($result->num_rows == 1 && strcmp($pass, $pass_) === 0){
		$sql = "SELECT * FROM `users` WHERE `passwd_url` = '$url'";
		while($reslut = $conn->query($sql)){
			if($reslut->num_rows > 0)
			{
				$url = generateRandomString();
				$sql = "SELECT * FROM `users` WHERE `passwd_url` = '$url'";
			}else{
				break;
			}
		}
		$pass = password_hash($pass, PASSWORD_DEFAULT);
		$sql = "UPDATE `users` SET `password` = '$pass', `passwd_url` = '$url' WHERE `Prenom` = '$prenom' AND `Nom` = '$nom' AND `Email` = '$email'";
		$result = $conn->query($sql);
		echo "<br><a href='http://$host$path/html/accueil.php'>retour &agrave; l'accueil</a>";
	}else{
		if(strcmp($pass, $pass_)){
			echo "<h1 style='color:red;'>Les mots de passes diffèrent.</h1>";
		}
	}
}else{	echo "<form method='post'>";
		echo "<div class='form-group'><label>Pr&eacute;nom:</label><input type='text' class='form-control' class='form-text' id='fprenom' name='fprenom' required></div>";
		echo "<div class='form-group'><label>Nom:</label><input type='text' class='form-control' class='form-text' id='fnom' name='fnom' required><br>";
		echo "<div class='form-group'><label>Email:</label><input type='email' class='form-control' id='femail' name='femail' required><br>";
		echo "<div class='form-group'><label>Password:</label><input type='password' class='form-control' id='fpasswd' name='fpasswd' required><br>";
		echo "<div class='form-group'><label>Re-Enter:</label><input type='password' class='form-control' id='fpasswd_' name='fpasswd_' required><br>";
		echo "<div class='form-check'><input type='checkbox' class='form-check-input' name='checkbox'><label>Change my password</label></div>";
	    echo "<button type='submit' class='btn btn-primary'>envoyer</button>";
		echo "</form>";
	}
?></div>
	<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
	</body>
</html>
