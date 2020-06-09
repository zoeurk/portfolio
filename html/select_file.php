<?php
//var_dump($_SERVER['REQUEST_URI']);
//echo "<br>";
	$var = explode("?",$_SERVER['REQUEST_URI']);
	$param = explode("&",$var[1]);
	$z = $param;
	$end = 0;
	for($i = 0; isset($z[$i]); $i++)
	{	$temp = $z[$i];
		//echo $temp . "<br>";
		$myparam = explode("=",$temp);
		switch($myparam[0]){
			case "ID":	$id = $myparam[1];
					break;
			case "SID":	$sid = $myparam[1];
					break;
			case "path":	$newpath = $myparam[1];
					break;
			case "Project_Name": $project = $myparam[1];
					break;
			case "desc": 	$desc = $myparam[1];
					break;
			case "FILE":	$f = intval($myparam[1]);
					break;
			case "END": 	$end = intval($myparam[1]);
					//echo $end . "=========================================================================<br>";
					break;
		}
	}
	session_id($id);
	session_start();
	$ticket = session_id().microtime().rand(0,9999999999);
	$ticket = hash('sha512',$ticket);
	$_SESSION['id'] = $id;
	$_SESSION['ticket'] = $ticket;
	$_SESSION['path'] = $newpath;
	$_SESSION['projet'] = $project;
	$_SESSION['desc'] = $desc;
	//if(empty($_SESSION['END']) && isset($end) && $end != 0){
		$_SESSION['END'] = $end;
		//$_SESSION['FILE'] = 0;
		/*if(empty($_SESSION['FILE'])){
			$_SESSION['FILE'] = 0;
		}else{
			$_SESSION['FILE']++;
		}*/
	//}
	/*else{
		$_SESSION['FILE']++;
	}*/
	if(isset($_SESSION['FILE'])){
		$_SESSION['FILE']++;
	}else{
		$_SESSION['FILE'] = 0;
	}
	if(empty($_SESSION['session'])){
		$_SESSION['session'] = array();
	}
	//var_dump($_SESSION);
	//echo urldecode($desc);
	//echo "<div class='text-center'><h1>Bienvenue " . $_SESSION['Prenom'] . " " . $_SESSION['Nom'] . "</h1></div>"
?>
<html>
	<head>
		<title>select a file</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link href='https://fonts.googleapis.com/css?family=Sofia' rel='stylesheet'>
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
	</head>
	<body style="margin-left:5%;margin-right:5%;">
<?php
	require('misc.php');
	require('sql.php');
	$db = "Portfolio";
	$url = $_SESSION['url'];
	$targetdir = $upload . "/" . $url ."/";
	$targetfile = $targetdir;
	$conn = new mysqli($server,$user,$password,$db);
	$filename = $_FILES['file']['name'];
	$tmp_name = $_FILES['file']['tmp_name'];
	$rep = $_SESSION['path'];
	$projet = $_SESSION['projet'];
	$desc = $_SESSION['desc'];
	$subrep = explode("/",$rep);
	$err = 0;
	$retour = "./getfiles.php?" . "ID=" . $_SESSION['id'] . "&SID=" . $_SESSION['ticket'];
	if($conn->connect_error){
		die("connection failed: " . $conn->error);
	}
	if(empty($_SESSION['projet'])){
		echo "<strong>Vous devez renseigner un nom de projet et une description</strong><br><i>cliker sur le lien pour recommencer.</i><br><a href='" . $retour . "'>retour</a>";
	}else{
		$sql = "SELECT Projet FROM `$url` WHERE Projet = '$projet'";
		$result = $conn->query($sql);
		if($result->num_rows > 0 && $_SESSION['upload'] == 0){
			echo "<strong>Vous avez deja un projet de ce nom</strong><br>";
			echo "<a href='" . $retour . "'>retour &agrave la page d'accueil</a>";
			$conn->close();
			exit(1);
		}else{
			$_SESSION['upload'] = 1;
		}
		$newpath = $upload . "/" . $url . '/';
		$i = 0;
		while(isset($subrep[$i]) && isset($subrep[$i+1])){
			if($i == 0){
				$newpath = $newpath . $_SESSION['projet'] . '/';
			}else{
				$newpath = $newpath . $subrep[$i] . '/';
			}
			$i++;
		}
		if(!is_dir($newpath)){
			mkdir($newpath,0775,TRUE);
		}
		$newpath = $newpath . $filename;
		$desc = $desc;
		$porjet = $projet;
		$sql = "INSERT IGNORE INTO `$url` (`Projet`, `Description`) VALUES ('$projet','$desc')";
		if(move_uploaded_file($tmp_name, $newpath)){
			if(!$conn->query($sql)){
				echo "erreur: " . $conn->errno . ":" . $conn->error;
				$conn->close();
				exit(0);	
			}
			$conn->close();
		}else{
			/*if($_FILES['file']['error'] != 3){
				echo "failed to import project<br><a href='" . $retour . "'>retour &agrave la page d'accueil</a>";
				remove("/home/public/Portfolio/upload/" . $url . "/" . $projet);
				$sql = "DELETE FROM `$url` WHERE Projet = '$projet'";
				$conn->query($sql);
				$conn->close();
				var_dump($_FILES);
				exit(0);
		}else{*/
			echo "Erreur:" . $filename . " " . $_SESSION['FILE'] . "/" . $_SESSION['END'] . "<br>";
			$err = 1;
				//$_SESSION['FILE']--;
				//var_dump($_FILES);
			//}
		}
		//var_dump($_SESSION);
		$_SESSION['session'][$_SESSION['FILE']] = 1;
		//var_dump($_SESSION['session']);
		$c = 0;
		for($j = 0;$j <= $_SESSION['END'];$j++){
			if(empty($_SESSION['session'][$j])){
				$c = 1;
				break;
			}
		}
		if($err != 1 || $c == 0){
			if($c == 0){
				echo "Projet Import&eacute; avec succ&egrave;s<br><a href='" . $retour . "'>retour &agrave la page d'accueil</a>";
				echo $_SESSION['END'] . " " . $_SESSION['FILE'] . "<br>";
				unset($_SESSION['END']);
				unset($_SESSION['FILE']);
				unset($_SESSION['session']);
			}else{
				echo "importation du projet:" . $filename . "...<br>file:" . $_SESSION['FILE'] ."/" . $_SESSION['END'] . "<br>";
			}
		}
	}
?>
	</body>
</html>
