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

	#Constantes :
	define('TARGET', 'photos/');   
	define('MAX_SIZE', 10000000);    

	// Variables
	$extension = '';
	$message = '';
	$nomImage = '';

	#Tableau de données :
	$tabExt = array('jpg','gif','png','jpeg','JPG','PNG');    // Extensions autorisees
	$extensions_autorisees = array();


	$nom_erreur = $ville_erreur = $topographie_erreur = $contexte_erreur = "";

	#1. Déterminer si l'on est en train de créer ou de modifier une fiche :
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

			#/!\ MODIFIER validation_photo avec une variable commune à MODIFIER ET AJOUTER
			require('validation_photo.php');

			#On compose la requête avec ces données :
			$SQL = "UPDATE sections SET nom = '$nom', ville = '$ville', pays = '$pays', largeur = $largeur_max, topographie = '$topographie', type = '$type', contexte = '$contexte', vitesse = $vitesse, descriptif = '$descriptif' WHERE id = $section_id";

			#Si tous les champs obligatoires ont été remplis, alors on peut créer la fiche et rediriger vers celle-ci :
			if (!empty($_POST['nom']) AND !empty($_POST['ville']) AND !empty($_POST['topographie']) AND !empty($_POST['contexte']) AND empty($messages_erreur)) {
				$bdd->query($SQL);
				header("Refresh:0; url=section.php?id=$section_id");
			} else {
				foreach ($messages_erreur as $erreur) {
					echo $erreur . "<br>" ;
				}
			}

		}

	} elseif (isset($_GET['action']) AND $_GET['action'] == 'ajouter') {

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

			#Si tous les champs obligatoires ont été remplis, alors on peut créer la fiche et rediriger vers la dernière fiche créée :
			if (!empty($_POST['nom']) AND !empty($_POST['ville']) AND !empty($_POST['topographie']) AND !empty($_POST['contexte']) AND empty($messages_erreur)) {

				$bdd->query($SQL);

				$last_section = $bdd->query("SELECT MAX(id) AS maxId FROM sections");
				$fetch_last_section = $last_section->fetch();
				$section_id = $fetch_last_section['maxId'];

				#On invoque ici la procédure de validation de la photo car on a besoin du $lastId pour nommer la photo :
				require('validation_photo.php');
				header("Refresh:0; url=section.php?id=$section_id");

			} 
		} 		

	}

		#Une fonction pour automatiser les menus déroulants :
		function set_default_select($options, $current_default){
			foreach ($options as $option_value) {
				if ($option_value == $current_default) {
					$default = 'selected';
				} else {
					$default = '';
				}
				echo "<option $default value='$option_value'> $option_value </option>";
			}
		}

	?>

	<form method="post" id="formulaire_section" enctype="multipart/form-data" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) . $action_suffixe ?>">

		<label for="fichier_a_uploader" title="Recherchez le fichier à uploader !">Envoyer le fichier :</label>
            <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo MAX_SIZE; ?>" />
            <input name="fichier" type="file" id="fichier_a_uploader" /><br>


		<label>nom de la rue : <input type="text" name="nom" value="<?php echo $nom ?>"></label>
		<span class="erreur"> * <?php echo $nom_erreur;?></span><br>

		<label>ville : <input type="text" name="ville" value="<?php echo $ville ?>"></label>
		<span class="erreur"> * <?php echo $ville_erreur;?></span><br>

		<label>pays : <input type="text" name="pays" value="<?php echo $pays ?>"></label><br>

		<label>largeur max : <input type="number" name="largeur_max" value="<?php echo $largeur_max ?>"></label><br>

		<label>topographie : <select name="topographie">
		<?php set_default_select(array('plat', 'faible pente', 'forte pente'), $topographie); ?>
		</select></label>	<span class="erreur"> * <?php echo $topographie_erreur;?></span><br>

		<label>type : <select name="type">
		<?php set_default_select(array('voie communale', 'voie verte', 'route départementale', 'route nationale', 'autoroute'), $type); ?>
		</select></label><br>

		<label>contexte : <select name="contexte">
		<?php set_default_select(array('urbain', 'périurbain', 'rural'), $contexte); ?>
		</select></label><span class="erreur"> * <?php echo $contexte_erreur;?></span><br>

		<label>vitesse : <input type="number" name="vitesse" value="<?php echo $vitesse ?>"></label><br>

		<label>descriptif : <textarea id="descriptif" form="formulaire_section" name="descriptif"><?php echo $descriptif ?></textarea></label><br>

		<input type='submit' name='enregistrer'>
		
	</form>

</article>

</body>
</html>