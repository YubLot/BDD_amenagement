<?php
$bdd = new PDO('mysql:host=localhost;dbname=amenagements;charset=utf8','root','',array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));


?>

<!DOCTYPE html>
<html>
<head>
	<title>ajouter une section</title>
	<link rel="stylesheet" href="styles.css">
</head>
<body>

<aside>
	<a href="bddSections.php">
		Retour à la liste
	</a>
</aside>

<article>

	<?php

	#petite fonction pour sécuriser les inputs :
	function nettoyage($input) {
		$input = htmlspecialchars($input);
		$input = stripslashes($input);
		$input = addslashes($input);
		return $input;
	}


	// Variables
	$extension = '';
	$message = '';
	$nomImage = '';

	function validation_photo ($nom_champ_photo) {

		global $nomImage;
		global $pas_de_photo;

		if (!empty($_FILES[$nom_champ_photo]['name'])) {

			$tabExt = array('jpg','jpeg');    // Extensions autorisees

				// Recuperation de l'extension du fichier
				$extension  = pathinfo($_FILES[$nom_champ_photo]['name'], PATHINFO_EXTENSION);
				// On verifie l'extension du fichier
				if(!in_array(strtolower($extension), $tabExt)) {
					$erreurs_photo[] = 'Type de fichier non autorisé.';
				}
				// On verifie la taille de l'image
				if($_FILES[$nom_champ_photo]['size'] >= 10/*1048576*/) {
					$erreurs_photo[] = 'Fichier trop gros.';
				}
				// Parcours du tableau d'erreurs
				if(isset($_FILES[$nom_champ_photo]['error']) && UPLOAD_ERR_OK === $_FILES[$nom_champ_photo]['error']) {
					#RAS
				} else {
					$erreurs_photo[] = 'ERREUR';
				}
				// On renomme le fichier
				$nomImage = $nom_champ_photo .'.'. $extension;

		}
		#Si on est en train d'ajouter une section, alors l'absence de photo est considérée comme une erreur :
		elseif ($_GET['action'] == 'ajouter') {
			$erreurs_photo[] = 'Aucune photo sélectionnée !';
		} 
		#Si on est en train de modifier une section, alors l'absence de photo n'est pas une erreur mais doit être signalée :
		elseif ($_GET['action'] == 'modifier') {
			if (empty($_FILES[$nom_champ_photo]['name'])) {
				$pas_de_photo = true;
			} else {
				echo "pas de photo (fonction) = ";
			}
		}

			if (!empty($erreurs_photo)) {
				return $erreurs_photo;
			} else {
				return "";
			}
		
	}


	$nom_erreur = $ville_erreur = $topographie_erreur = $contexte_erreur = "";

	#/// MODIFIER /// MODIFIER /// MODIFIER /// MODIFIER /// MODIFIER /// MODIFIER /// 

	if (isset($_GET['action']) AND $_GET['action'] == 'modifier') {
		
		#Récupère l'identifiant de la fiche en cours de modification :
		if (isset($_GET['id'])) {
			$section_id = $_GET['id'];
		}

		#On indique au formulaire qu'en cas d'erreur, nous sommes dans une modification :
		$action_suffixe = '?action=modifier&id=' . $section_id;

		#Charge les valeurs stockées et confirme la procédure à l'utilisateur :
		echo "<h1>" . "Modifier la section" . "</h1>";
		$section_id = $_GET['id'];
		$SQL_infoSection = "SELECT * FROM sections WHERE id = $section_id";
		$requete_infoSection = $bdd->query($SQL_infoSection);
		$requete_infoSection->execute();
		$infoSection = $requete_infoSection->fetch();
		$nom = $infoSection['nom'];
		$ville = $infoSection['ville'];
		$pays = $infoSection['pays'];
		$largeur_max = $infoSection['largeur'];
		$topographie = $infoSection['topographie'];
		$type = $infoSection['type'];
		$contexte = $infoSection['contexte'];
		$vitesse = $infoSection['vitesse'];
		$descriptif = $infoSection['descriptif'];

		#Vérifier les valeurs saisies à l'envoi du formulaire :
		if (isset($_POST['enregistrer'])) {

			#Invoque la procédure de validation depuis le fichier validation.php :
			require('validation.php');

			#On compose la requête avec ces données :
			$SQL = "UPDATE sections SET nom = '$nom', ville = '$ville', pays = '$pays', largeur = $largeur_max, topographie = '$topographie', type = '$type', contexte = '$contexte', vitesse = $vitesse, descriptif = '$descriptif' WHERE id = $section_id";

			$functions_MUF = array();
			
			$champs_photos = array(1,2,3);
			foreach ($champs_photos as $numPhoto) {

				#Pour chaque photo téléversée, on vérifie qu'il n'y a pas d'erreur :
				$nom_champ_photo = 'photo' . $numPhoto;
				$erreurs_photo = validation_photo($nom_champ_photo);

				#On regarde dans la base s'il existe une photo pour cette section et ce numéro de photo (de champ) :
				$SELECT_photo = $bdd->query("SELECT * FROM photos WHERE refSection = $section_id AND numPhoto = $numPhoto");
				$photo_bdd = $SELECT_photo->fetch();
				if (!empty($photo_bdd['id'])) {
					#S'il y a déjà une photo dans la bdd, on fixe l'$idPhoto égal à celui-ci pour que la procédure passe à UPDATE (et non INSERT) :
					$idPhoto = $photo_bdd['id'];
				} else {
					#S'il n'y a pas de photo dans la bdd, on fixe l'$idPhoto à NULL pour que la procédure passe à INSERT (et non UPDATE) :
					$idPhoto = 'NULL';
				}

				#On génère un nom de photo avec l'identifiant de la section et le nom du champ photo1, photo2 ou photo3, que l'on a récupéré de la fonction validation_photo :
				$nomImage_complet = $section_id . "_" . $nomImage;

				#Si aucune photo n'est téléversée, on ne fait rien, sinon, on prépare les requêtes et la fonction :
				if ($pas_de_photo) {
					$SQL_photos[] = "";
					$functions_MUF[] = "";
				} else {
					#On prépare la requête (insérée dans une chaine de requêtes du coup) : INSERST s'il n'y a pas de doublon, UDPATE sinon :
					$SQL_photos[] = "INSERT INTO photos (id, refSection, numPhoto, nom) VALUES ($idPhoto, $section_id, $numPhoto, '$nomImage_complet') ON DUPLICATE KEY UPDATE nom = '$nomImage_complet'";
					#Et on prépare la fonction move_uploaded_file qui va créer la photo dans le server :
					$functions_MUF[] = move_uploaded_file($_FILES[$nom_champ_photo]['tmp_name'], "photos/" . $nomImage_complet);
				}

			}

			
			#Si tous les champs obligatoires ont été remplis, alors on peut modifier la section et rediriger vers celle-ci :
			if (!empty($_POST['nom']) AND !empty($_POST['ville']) AND !empty($_POST['topographie']) AND !empty($_POST['contexte']) AND empty($erreurs_photo)) {

				$bdd->query($SQL);

				#Pour chaque champ de photo, on exécute la requête préparée :
				foreach ($SQL_photos as $UPDATE_INSERT_photo) {
					if (!empty($UPDATE_INSERT_photo)) {
						$bdd->query($UPDATE_INSERT_photo);
					}	
				}

				#Crées les photos sur le serveur :
				foreach ($functions_MUF as $MUF) {
					if (!empty($MUF)) {
						$MUF;
					}
				}

				#header("Refresh:0; url=section.php?id=$section_id");

			} else {
				foreach ($erreurs_photo as $erreur) {
					echo $erreur . "<br>" ;
				}
			}

		}

	} 

	#/// AJOUTER /// AJOUTER /// AJOUTER /// AJOUTER /// AJOUTER /// AJOUTER ///  
	
	elseif (isset($_GET['action']) AND $_GET['action'] == 'ajouter') {

		#Indique la procédure à l'utilisateur :
		echo "<h1>" . "Nouvelle section" . "</h1>";

		$nom = $ville = $pays = $largeur_max = $topographie = $type = $contexte = $vitesse = $descriptif = "";
		

		#On indique au formulaire qu'en cas d'erreur, nous sommes dans un ajout :
		$action_suffixe = '?action=ajouter';

		#Vérifier les valeurs saisies :
		if (isset($_POST['enregistrer'])) {

			#Invoque la procédure de validation depuis le fichier validation.php :
			require('validation.php');

			#On compose la requête avec ces données :
			$SQL = "INSERT INTO sections (nom, ville, pays, largeur, topographie, type, contexte, vitesse, descriptif) VALUES ('$nom', '$ville', '$pays', $largeur_max, '$topographie', '$type', '$contexte', $vitesse, '$descriptif')";	

			#On invoque la procédure de validation des photos :
			$erreurs_photo = validation_photo('photo1');
			$erreurs_photo = validation_photo('photo2');

			#Si tous les champs obligatoires ont été remplis, alors on peut créer la fiche et rediriger vers la dernière fiche créée :
			if (!empty($_POST['nom']) AND !empty($_POST['ville']) AND !empty($_POST['topographie']) AND !empty($_POST['contexte']) AND !empty($_FILES['photo1']['name']) AND empty($erreurs_photo)) {

				$bdd->query($SQL);

				$last_section = $bdd->query("SELECT MAX(id) AS maxId FROM sections");
				$fetch_last_section = $last_section->fetch();
				$section_id = $fetch_last_section['maxId'];

				#On génère un nom de photo avec l'identifiant de la section et du champ 1, 2 ou 3 :
				$nomImage_complet = $section_id . "_" . $nomImage;

				#On crée les métadonnées de la photo dans la BDD :
				$SQL_insertPhotos = "INSERT INTO photos (refSection, nom) VALUES ($section_id, '$nomImage_complet')";
				$bdd->query($SQL_insertPhotos);
				#On stocke la photo sur le server :
				move_uploaded_file($_FILES['photo1']['tmp_name'], "photos/" . $nomImage_complet);

				
				#On invoque ici la procédure de validation de la photo car on a besoin du $lastId pour nommer la photo :
				header("Refresh:0; url=section.php?id=$section_id");

			} elseif (!empty($erreurs_photo)) {
				foreach ($erreurs_photo as $erreur_photo) {
					echo $erreur_photo . "<br>";
				}
			}
		} 		

	}

		#Une fonction pour automatiser les menus déroulants :
		function set_default_select($options, $current_default){
			foreach ($options as $option_value) {
				#Si l'option est égale à la valeur actuelle, alors on la sélectionne :
				if ($option_value == $current_default) {
					$default = 'selected';
				} else {
					#Sinon, on sélectionne l'option "vide" :
					$default = '';
				}
				echo "<option $default value='$option_value'> $option_value </option>";
			}
		}

	?>

	<form method="post" id="formulaire_section" enctype="multipart/form-data" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) . $action_suffixe ?>">

		<label>Photo 1 (couverture) : <input name="photo1" type="file"/></label><br>
		<label>Photo 2 : <input name="photo2" type="file"/></label><br>
		<label>Photo 3 : <input name="photo3" type="file"/></label><br>

		<label>nom de la rue : <input type="text" name="nom" value="<?php echo $nom ?>"></label>
		<span class="erreur"> * <?php echo $nom_erreur;?></span><br>

		<label>ville : <input type="text" name="ville" value="<?php echo $ville ?>"></label>
		<span class="erreur"> * <?php echo $ville_erreur;?></span><br>

		<label>pays : <input type="text" name="pays" value="<?php echo $pays ?>"></label><br>

		<label>largeur max : <input type="number" name="largeur_max" value="<?php echo $largeur_max ?>"></label><br>

		<label>topographie : <select name="topographie">
		<?php set_default_select(array('', 'plat', 'faible pente', 'forte pente'), $topographie); ?>
		</select></label>	<span class="erreur"> * <?php echo $topographie_erreur;?></span><br>

		<label>type : <select name="type">
		<?php set_default_select(array('', 'voie communale', 'voie verte', 'route départementale', 'route nationale', 'autoroute'), $type); ?>
		</select></label><br>

		<label>contexte : <select name="contexte">
		<?php set_default_select(array('', 'urbain', 'périurbain', 'rural'), $contexte); ?>
		</select></label><span class="erreur"> * <?php echo $contexte_erreur;?></span><br>

		<label>vitesse : <input type="number" name="vitesse" value="<?php echo $vitesse ?>"></label><br>

		<label>descriptif : <textarea id="descriptif" form="formulaire_section" name="descriptif"><?php echo $descriptif ?></textarea></label><br>

		<input type='submit' name='enregistrer'>
		
	</form>

</article>

</body>
</html>