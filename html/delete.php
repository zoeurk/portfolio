<?php
	require('misc.php');
	$sess = new session;
	$sess->init_session();
	echo "<div class='text-center'><h1>Bienvenue " . $_SESSION["Prenom"] . " " . $_SESSION["Nom"] . "</h1></div>";
?>

<html>
	<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
 	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
		<title>delete files</title>
	</head>
	<body style='margin:0 0 5% 5%;'>
<?php
	require('sql.php');
	$db = "Portfolio";
	$url = $_SESSION['url'];
	$conn = new mysqli($server,$user,$password,$db);
	$inc = 0;
	$delete = "http://" . $host . $path . "/html/delete.php?ID=" . $_SESSION['id'] ."&SID=" . $_SESSION['ticket'];
	$getfiles = "http://" . $host . $path ."/html/getfiles.php?ID=" . $_SESSION['id'] . "&SID=" . $_SESSION['ticket'];
	if($conn->connect_error){
		die("connection failed: " . $conn->error);
	}
	/*###############*/
	if(isset($_POST['choix'])){
		foreach($_POST['choix'] as $val){
			remove($upload . "/" . $url . "/" . $val);
			rmdir($upload . "/" . $url . "/" . $val);
			$sql="DELETE FROM `$url` WHERE Projet = '$val'";
			$conn->query($sql);
		}
		echo "Projet(s) supprimer.<br>";
		echo "<br><a href='http://". $host . $path . "/html/getfiles.php?ID=" . $_SESSION['id'] . "&SID=" . $_SESSION['ticket'] . "'>retour &agrave; ma page personnel</a>";
		exit(0);
	}
	/*###############*/
	$sql = "SELECT * FROM `$url`";
	$result = $conn->query($sql);
	echo "<a href='" . $getfiles . "' style='float:right;'><button type='button' class='btn btn-primary btn-primary' data-toggle='modal' data-target='.bd-example-modal-lg' style='float:right'>retour &agrave; ma page personnel</button></a>";
	if($result->num_rows > 0){
		echo "<form method='POST' action='" . $delete . "'>Recherche: <input type='text' name='entry'><input type='submit' value='recheche'><br><input type=checkbox name='reg'>Utiliser les expressions r&eacute;guli&egrave;res php</input><form><hr>";
		echo "<form method='POST' action='" . $delete . "'>";
		echo "<input type='checkbox' id='select' onclick='selected()' name='select' value='tout selectionner'>Tout selectionner</input><hr>";
    	while($row = $result->fetch_assoc()) {
    		if(isset($_POST['entry']) && $_POST['entry'] != ""){
				if(isset($_POST['reg'])){
					$regex=$_POST['entry'];
				}else{
					$regex= "#" . $_POST['entry'] . "#";
				}
				$title = $row["Projet"];
    				if(preg_match($regex, $title)) {
    					echo "<input type='checkbox' class='music' name='choix[]' value='$title'></input>" . $row["Projet"] . "<br>";
    				}
			}else{
    			$title = $row["Projet"];
        		echo "<input type='checkbox' class='music' name='choix[]' value='$title'></input>" . $row["Projet"] . "<br>";
        		$inc++;
    		}
    	}
    }
	echo "<input type='submit'/></form>";
?>
<script type='text/javascript'>
	function selected()
	{	var s = document.getElementsByClassName('music');
		if(document.getElementById('select').checked == true){
			//alert(s.length);
			for(var i = 0; i < s.length; i++){
				s[i].checked = true;
			}
		}else{
			for(var i = 0; i < s.length; i++){
				s[i].checked = false;
			}
		}
	}
</script>
</body>

