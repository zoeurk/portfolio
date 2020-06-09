<?php
	//require("sql.php");
	//$charin = "ISO-8859-1";
	//$charout="UTF-8";
	class session{
		private $id;
		private $ticket;
		private $projet;
		public function init_session(){
			$var = explode("?",$_SERVER['REQUEST_URI']);
			$param = explode("&",$var[1]);
			$___param___ = $param;
			$this->id = 0;
			for($i = 0; isset($___param___[$i]); $i++)
			{	$temp = $___param___[$i];
				$myparam = explode("=",$temp);
				switch($myparam[0]){
					case "ID":	$this->id = $myparam[1];
							break;
					case "SID":	$this->ticket = $myparam[1];
							break;
					case "Projet":	$this->projet = $myparam[1];
							break;
			
				}
			}
			session_id($this->id);
			session_start();
			if($this->ticket != $_SESSION['ticket']){
				$_SESSION = array();
				session_destroy();
				header('location:../index.php');
			}
			if(empty($this->projet)){
				$_SESSION['projet'] = NULL;
			}else{
				$_SESSION['Projet'] = $this->projet;
			}
			$this->ticket = session_id().microtime().rand(0,9999999999);
			$this->ticket = hash('sha512',$this->ticket);
			$_SESSION["id"] = $this->id;
			$_SESSION["ticket"] = $this->ticket;
		}
	}
class Elements{
		private $user_directory;
		private $Nom;
		private $Prenom;
		private $about;
		private $description;
		private $projet;
		private $score;
		public function user($sql,$upload){
			$files = scandir($upload);
			$i = 0;
			foreach($files as $f => $myfile){
				if($myfile != ".." && $myfile != "."){
					$this->user_directory[$i] = $myfile;
					$req = "SELECT Nom,Prenom,about FROM users WHERE url = '$myfile'";
					$result = $sql->query($req);
					if($result->num_rows > 0){
						while($utilisateur = $result->fetch_assoc()){
							$this->Prenom[$i]	= $utilisateur['Prenom'];
							$this->Nom[$i]		= $utilisateur['Nom'];
							$this->about[$i]	= $utilisateur['about'];
							$this->url		= $myfile;
						}
					}
					$i++;
				}
			}
		}
		public function recherche($sql,$keyword){
			foreach($this->user_directory as $d => $u){
				$req = "SELECT * FROM $u";
				$result = $sql->query($req);
				if($result->num_rows>0){
					$word = explode(" ",$keyword);
					$x_word = count($word);
					$z = 0;
					$this->score[$d][$z] = 0;
					while($projet = $result->fetch_assoc()){
						$this->projet[$d][$z] = $projet['Projet'];
						$this->description[$d][$z] = $projet['Description'];
						$this->score[$d][$z] = match($keyword,$this->description[$d][$z],$this->projet[$d][$z]);
						$z++;
					}
				}
			}
		}
		public function and_print($host,$path,$from,$to){
			//$from = "iso-8859-1";
			//$to = "UTF-8";
			$max = 0;
			$c = TRUE;
			echo "<table class='table'><tbody>";
			while($c == TRUE){
				foreach($this->user_directory as $z => $d){
					foreach($this->score[$z] as $y => $r){
						$max = ($max < $r) ? $r : $max;
					}
				}
				foreach($this->user_directory as $z => $d){
					foreach($this->score[$z] as $y => $r){
						if($r !=  NULL && $r == $max){
							$projet = $this->projet[$z][$y];
							$description = urldecode($this->description[$z][$y]);
							$Nom = $this->Nom[$z];
							$Prenom = $this->Prenom[$z];
							$about = urldecode($this->about[$z]);
							$url = $this->user_directory;
							//$about = str_replace("'",'&apos;',$about);
							//$about = str_replace('"','&quot;',$about);
							$this->score[$z][$y] = NULL;
							echo "<tr><td><a href='http://$host$path/upload/$url/$projet' target='_blank'>" . iconv($from,$to,$projet) . "</a></td><td>" . iconv($from,$to,$description) . "</td><td><a href='#' data-toggle='popover' data-placement='top' title='about' data-content='" . iconv($from,$to,$about) . "'>$Prenom $Nom</a><td></tr>";
						}
					}
				}
				$max = 0;
				$c = FALSE;
				foreach($this->user_directory as $z => $d){
					foreach($this->score[$z] as $y => $r){
						if($r !=  NULL){
							$c = TRUE;
							break;
						}
					}
					if($c == TRUE){
						break;
					}
				}
			}
			echo "</tbody></table>";
		}
		public function s_recherche($sql,$keyword,$url){
				$this->user_directory = $url;
				$req = "SELECT * FROM $url";
				$result = $sql->query($req);
				if($result->num_rows>0){
					$z = 0;
					while($projet = $result->fetch_assoc()){
						$this->projet[$z] = $projet['Projet'];
						$this->description[$z] = $projet['Description'];
						$this->score[$z] = match($keyword,$this->description[$z],$this->projet[$z]);
						$desc = $this->description[$z];
						$p = $this->projet[$z];
						$s = $this->score[$z];
						$z++;
					}
				}
		}
		public function s_and_print($host,$path,$from,$to){
			//$from = "ISO-8859-1";
			//$to = "UTF-8";
			$max = 0;
			$c = TRUE;
			echo "<table class='table'><tbody>";
			while($c == TRUE){
					foreach($this->score as $y => $r){
						$max = ($max < $r) ? $r :$max;
					}
					foreach($this->score as $y => $r){
						if($r !=  NULL && $r == $max){
							$projet = $this->projet[$y];
							$description = urldecode($this->description[$y]);
							$Nom = $this->Nom;
							$Prenom = $this->Prenom;
							$about = urldecode($this->about);
							$url = $this->user_directory;
							//$about = str_replace("'",'&apos;',$about);
							//$about = str_replace('"','&quot;',$about);
							$this->score[$y] = NULL;
							$max = 0;
							echo "<tr><td><strong><a href='http://$host$path/upload/$url/$projet' target='_blank'>" . iconv($from,$to,$projet) . "</a></strong></td><td>" . iconv($from,$to,$description) . "</td></tr>";
						}
					}
				$max = 0;
				$c = FALSE;
				foreach($this->score as $y => $r){
						if($r !=  NULL){
							$c = TRUE;
							break;
						}
					}
				}
			echo "</tbody></table>";
		}
	}
	function remove($path){
		$files = scandir($path);
		foreach($files as $f => $myfiles){
			if(!is_dir($path . "/" . $myfiles)){
				unlink($path . "/" . $myfiles);
			}else{
				if($myfiles != "." && $myfiles != ".."){
					remove($path . '/' . $myfiles);
					rmdir($path . '/' . $myfiles);
				}
			}
		}
	}
	function printdb($which,$sql,$user,$upload,$host,$path,$charin,$charout){
		//$charin = "ISO-8859-1";
		//$charout="UTF-8";
		$i = 0;
		switch($which){
			case NULL:
				$files = scandir($upload);
				foreach($files as $f => $myfile){
					if($myfile != ".." && $myfile != "."){
						$i += printdb($myfile,$sql,$user,$upload,$host,$path,$charin,$charout);
					}
				}
				break;
			default:
				if($user == TRUE){
					$req = "SELECT Nom,Prenom,about FROM users WHERE url = '$which'";
					$result = $sql->query($req);
					if($result->num_rows > 0){
						while($utilisateur = $result->fetch_assoc()){
							$Prenom = $utilisateur['Prenom'];
							$Nom = $utilisateur['Nom'];
							$about = $utilisateur['about'];
						}
					}
				}
				$req = "SELECT * FROM $which WHERE 1";
				$result = $sql->query($req);
				$i = $result->num_rows;
				if($i > 0){
					while($projet = $result->fetch_assoc()){
						$___projet___ = $projet['Projet'];
						$___desc___ = urldecode($projet['Description']);
						echo "<tr><td><strong><a href='http://$host/$path/upload/$which/$___projet___/' target='_blank'>$___projet___</a></strong></td><td>" . $___desc___ . "</td>";
						if($user == TRUE){
							$about = urldecode($about);
							//$about = str_replace("'",'&apos;',$about);
							//$about = str_replace('"','&quot;',$about);
							echo "<td><a href='#' data-toggle='popover' data-placement='top' title='About' data-content='" . iconv($charin,$charout,$about) . "'>$Prenom $Nom</a></td></tr>";
						}else{
							echo "</tr>";
						}
					}
				}
				break;
		}
		return $i;
	}
	function match($keyword,$projet,$description){
		$word = explode(" ",$keyword);
		$i = 0;
		if(preg_match("#" . $keyword . "#i", $projet)){
			$i+=7;
		}
		if(preg_match("#" . $keyword . "#i", $description)){
			$i+=5;
		}
		foreach($word as $k => $w){
			if(preg_match("#" . $w . "#i",$projet)){
				$i++;
			}
		}
		foreach($word as $k => $w){
			if(preg_match("#" . $w . "#i",$description)){
				$i++;
			}
		}
		return $i;
	}

?>

