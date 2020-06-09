<?php
	require('misc.php');
	require('sql.php');
	//mysession();
	$sess = new session;
	$sess->init_session();
	$_SESSION['upload'] = 0;
	echo "<div class='text-center'><h1>Bienvenue " . $_SESSION["Prenom"] . " " . $_SESSION["Nom"] . "</h1></div>";
?>
<html>
	<head>
		<title>Portfolio</title>
		 <meta name="viewport" content="width=device-width, initial-scale=1.0">
		 <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
	</head>
	<body id="reponse" style="margin-right:5%;margin-left:5%;">
<!-- Button trigger modal -->
<div id="buttons" style="height:150px;width:175px;float:right;">
	<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModalLong" style='float:right;'>Upload/Suppression de projet(s)</button>
	<button type='button' class='btn btn-primary btn-warning' data-toggle='modal' data-target='#wiki' style='float:right;width:100%;'>wiki</button>
	<a href=<?php $url = "?&ID=" . $_SESSION['id'] . "&SID=" . $_SESSION['ticket']; echo "../index.php$url";?>><button type='button' class='btn btn-primary btn-secondary' data-toggle='modal' data-target='.bd-example-modal-lg' style='float:right;width:100%;'>d&eacute;connection</button></a>
</div>
<!-- Modal -->
<div class="modal fade" id="exampleModalLong" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">Uploader/Supprimer vos projets ici:</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
              <div id='txt' class="p-3">
		  Nom du projet: <input type='text' id='project_name'/><br>
		  <div class="input-group">
		  
  <div class="input-group-prepend">
    <span class="input-group-text">Description<br>du projet:</span>
  </div>
  <textarea id='desc' class="form-control" aria-label="With textarea"></textarea>
</div>

		  <input type="file" id="filepicker" name="repertoire[]"  webkitdirectory />
		  <form action="./delete.php?ID=<?php echo $_SESSION['id'] . "&SID=" . $_SESSION['ticket']?>" method="POST">
	    <input type="submit" id="delete_file" name="delete" value="suppression de projet">
	    </form>
	</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!-- Modal -->
<div class="modal fade" id="wiki" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle2">wiki</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
	  <?php
		$root_url="http://" . $host . $path . "/upload/";
		$db = "Portfolio";
		$email = $_SESSION['email'];
		$conn = new mysqli($server,$user,$password,$db);
		if($conn->connect_error){
			die("connection failed: " . $conn->error);
		}
		$sql = "SELECT url FROM users where Email = '$email'";
		$result = $conn->query($sql);
		$myurl = $result->fetch_assoc();
		echo "<p>l'url par laquelle vous pouvez accéder &agrave; vos projet est:<br><strong>$root_url" . $myurl['url'] . "/Project_Name</strong></p>";
		echo "<p>Vous avez acc&egrave;s &agrave; la base de donn&eacute;e par <strong>" . $myurl['url'] ."@localhost</strong> et le même mot de passe que pour votre login.</p>";
		$conn->close();
	  ?>
	  </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!--div id='reponse'></div-->
	<div class='container-fluid'>
	  <div class="float-left">
	  	<h3>Vos projets:</h3>
<?php
	//require('misc.php');
	$db = "Portfolio";
	$email = $_SESSION['email'];
	$conn = new mysqli($server,$user,$password,$db);
	$id = $_SESSION["id"];
	$ticket = $_SESSION["ticket"];
	$inc = 0;
	$z = 0;
	if($conn->connect_error){
		die("connection failed: " . $conn->error);
	}
	$sql = "SELECT url FROM users where Email = '$email'";
	$result = $conn->query($sql);
	$myurl = $result->fetch_assoc();
	$sql = "SELECT * FROM " . $myurl['url'];
	$result = $conn->query($sql);
	$search = (isset($_POST['search']) && $_POST['search'] != "") ? 1 : 0;
	echo "<form id='post' method='POST' action='./getfiles.php?ID=$id&SID=$ticket'>Recheche:<input type='text' name='search'><input type='submit' value='recheche'></form>";
	if($search == 1){
		$s = new Elements;
		$s->s_recherche($conn,$_POST['search'],$myurl['url']);
		$s->s_and_print($host,$path,$charsetin,$charsetout);
	}else{
		echo "<table class='table'><tbody>";
		if(printdb($myurl['url'],$conn,FALSE,$path,$host,$path,$charsetin,$charsetout) == 0 && empty($_POST['entry'])) {
			echo "Vous n'avez pas encore poster de projets";
		}
		echo "</tbody></table>";
	}
	$conn->close();
?>
	  </div>
	</div>
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <script type='text/javascript'>
	document.getElementById("filepicker").addEventListener("change", function(event) {
	let files = event.target.files;
	var d = 1;
    for (let i=0; i<files.length; i++) {
	var xhttp = new XMLHttpRequest();
	var f;
	let file = files[i];
	let formData = new FormData();
	//xhttp.responseType = 'text';
	xhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			document.getElementById('reponse').innerHTML = this.responseText;
			d = 1;
		}
	};
	formData.append("file", files[i]);
	let n = files.length - 1;
	var project = document.getElementById('project_name').value;
	var desc = document.getElementById('desc').value;	
	function waiting(){
		if(d == 0){
			setTimeout(function(){waiting();},3000);
			//alert("ok");
		}else{
			d = 0;
			xhttp.open("POST", './select_file.php?<?php echo "ID=" . $_SESSION['id'] . "&SID=" . $_SESSION['ticket'];?>' + "&Project_Name=" + project + "&desc=" + desc + "&path=" + files[i].webkitRelativePath + "&FILE=" + i + "&END=" + n);
			xhttp.send(formData);
			//xhttp.send(formData);
		}
	}
	waiting();    
	};
}, false);
	</script>
	</body>
</html>
