<?php
	require('./html/sql.php');
	require('./html/misc.php');
	//$path = session_save_path();
	$var = explode("?",$_SERVER['REQUEST_URI']);
	if(isset($var[1])){
		$param = explode("&",$var[1]);
		$___param___ = $param;
		$id = 0;
		for($i = 0; isset($___param___[$i]); $i++)
		{	$temp = $___param___[$i];
			$myparam = explode("=",$temp);
			switch($myparam[0]){
				case "ID":	$id = $myparam[1];
						break;
				case "SID":	$sid = $myparam[1];
						break;
			}
		}
		session_id($id);
		session_start();
		$_SESSION = array();
		session_destroy();
		header('location:./index.php');
	}
?>
<html>
 <head>
 	<meta name="viewport" content="width=device-width, initial-scale=1.0">
 	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
   <title>Accueil</title>
 </head>
  <body>
  	<div class='container'>
  	<div class="text-center"><h1>Portfolio</h1></div>
	<div>
	 <div class="float-right">
	 <a href='http://<?php echo $host . $path;?>/html/inscription.html'>inscription</a><br>
	 <a href='http://<?php echo $host . $path;?>/html/resiliation.html'>r&eacute;siliation</a><br>
	 <a href='http://<?php echo $host . $path;?>/html/oublie.html'>mot de passe oubli&eacute;</a><br>
	  </div>
  	 <div class="float-left">
  	  <h3>login</h3>
  	   <form method="post" id="login" action="./html/login.php">
  	   	<div style="display:'flex';flex-direction: 'row';">
	    	<table>
	    		<tr>
	    			<th><label>addresse mail: </label></th>
	    			<th><input type="email" id="lemail" name="lemail" size="25" maxlenght="25" required></th>
	    		</tr>
	    		<tr>
	    			<th><lable>Mot de passe: </lable></th>
	    			<th><input type="password" id="lpasswd" name="lpasswd" size="25" maxlenght="25" required></th>
	    		</tr>
	    	</table>
	    		<button type='submit' class="btn btn-primary" onclick="setCookie()">envoyer</button>
		</div>
	   </form>
	<div >
<?php
$conn = new mysqli($server,$user,$password);
if($conn->connect_error){
	die("connexion failed " . $conn->error);
}
$sql = "CREATE DATABASE IF NOT EXISTS Portfolio;";
$sql .= "USE Portfolio;";
$sql .= "CREATE TABLE IF NOT EXISTS users (Nom VARCHAR(56),Prenom VARCHAR(56),Email VARCHAR(112) PRIMARY KEY,about VARCHAR(255),Password VARCHAR(255),passwd_url VARCHAR(25) UNIQUE,url VARCHAR(25) UNIQUE,aime INT)";
if($conn->multi_query($sql) === FALSE){
	die("multi_query failed:(" . $conn->errno . ")" . $conn->error);
}
$conn->close();
	if(! is_dir($upload)){
		mkdir($upload,0775,TRUE);
	}
	$db = "Portfolio";
	$conn = new mysqli($server,$user,$password,$db);
	if($conn->error){
		die("connexion failed:" . $conn->error);
	}
	$search = (isset($_POST['search']) && $_POST['search'] != "") ? 1 : 0;
	echo "<form method='POST' action=''>Recheche:<input type='text' name='search'><input type='submit' value='recheche'></form><hr>";
	if($search == 1){
		$directory = new Elements;
		$directory->user($conn);
		$directory->recherche($conn,$_POST['search']);
		$directory->and_print($host,$path,$charsetin,$charsetout);
	}else{
		echo "<table class='table'><tbody>";
		printdb(NULL,$conn,TRUE,$upload,$host,$path,$charsetin,$charsetout);
		echo "</tbody></table>";
	}
	$conn->close();
?>
</div>


	  </div>
	 </div>
	</div>

<!-- Modal -->
<div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">Sorry :(</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" data-toggle="collapse" data-target="#info">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        Sorry you can't have a good experience.
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal" data-toggle="collapse" data-target="#info">Close</button>
      </div>
    </div>
  </div>
</div>
<div id="info" class="collapse show" style="position:fixed;bottom: 0px;left:0px;right:0px;width:auto;background-color:blue;color: white;border-radius: 5px 5px 0px 0px;">
  <p style="text-align: center;">ce site fonctionne avec un cookie, sans cookie le service ne fonctionne pas. En vous logant ou vous inscrivant vous accept&eacute; la politique des cookie du site.<br>Merci de l'accepter
  <button type="button" id="yes" class="btn btn-primary" data-toggle="collapse" data-target="#info" aria-expanded="true" onclick="cookietest()">Ok</button>
  <button type="button" id="no" class="btn btn-success" data-toggle="modal" data-target="#exampleModalCenter">No thanks</button>
</p>
  </div>
</div>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <script>
    	window.onload = function(){
    		if(getCookie("service") == "audio"){
    			document.getElementById("info").style.display = "none";
    		}
    	};
    	function cookietest(){
    		var x = navigator.cookieEnabled;
    		if( x != true){
    			alert("Vous devez accepter les cookies. Votre navigateur n'acc&egrave;pte pas les cookies.");
    		}else{
    			document.cookie = "service=audio; expires=Fri, 31 Dec 9999 23:59:59 GMT";
    		}
    	}
    	function setCookie(){
    		document.cookie = "service=audio; expires=Fri, 31 Dec 9999 23:59:59 GMT";
    	}
    	function getCookie(cname) {
    		var name = cname + "=";
    		var decodedCookie = decodeURIComponent(document.cookie);
    		var ca = decodedCookie.split(';');
    		for(var i = 0; i <ca.length; i++) {
    			var c = ca[i];
    			while (c.charAt(0) == ' ') {
    				c = c.substring(1);
    			}
    			if (c.indexOf(name) == 0) {
    				return c.substring(name.length, c.length);
    			}
    		}
    		return "";
	}
	$(document).ready(function(){
		$("[data-toggle='popover']").popover();
	});
    </script>
  </body>
</html>
