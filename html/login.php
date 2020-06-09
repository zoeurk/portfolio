<?php
	session_start();
	$_SESSION['id'] = session_id();
	$ticket = session_id().microtime().rand(0,9999999999);
	$_SESSION['ticket'] = hash('sha512',$ticket);
?>
<?php
require('sql.php');
function session_off(){
	$_SESSION = array();
	session_destroy();
	header("Location:../index.php");
}
$db = "Portfolio";
$email = (isset($_POST['lemail']))? $_POST['lemail'] : NULL;
$passwd = (isset($_POST['lpasswd'])) ? $_POST['lpasswd'] : NULL;
$test = 0;
$conn = new mysqli($server,$user,$password,$db);
if($conn->connect_error){
	die("connection failed: " . $conn->error);
}
$sql = "SELECT * FROM users";
if(($result = $conn->query($sql)) === FALSE){
	echo "Error: " . $sql . "<br>" . $conn->error;
}else{
	if($result->num_rows>0){
		while($row = $result->fetch_assoc()){
			if($email === $row['Email']){
				$test++;
				if(password_verify($passwd, $row['Password']) === FALSE){
					session_off();
				}else{
					$_SESSION['email'] = $row['Email'];
					$_SESSION['Nom'] = $row['Nom'];
					$_SESSION['Prenom'] = $row['Prenom'];
					$_SESSION['url'] = $row['url'];
					$path = "Location:getfiles.php?ID=" . $_SESSION['id'] . "&SID=" . $_SESSION['ticket'];
					header($path);
									}
			}
		}
	}
}
if($test == 0){
	session_off();
}
if($conn){
	$conn->close();
}
?>
