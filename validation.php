<?php

# POUR LA SECTION :

#petite fonction pour sécuriser les inputs :
function nettoyage($input) {
	$input = htmlspecialchars($input);
	$input = stripslashes($input);
	$input = addslashes($input);
	return $input;
}

$nom_erreur = $ville_erreur = $topographie_erreur = $contexte_erreur = "";

if (isset($_POST['enregistrer'])) {

	if (empty($_POST['nom'])) {
		$nom_erreur = "Renseigner le nom de la rue.";
	} else {
		$nom = nettoyage($_POST['nom']);
	}

	if (empty($_POST['ville'])) {
		$ville_erreur = "Renseigner la ville.";
	} else {
		$ville = nettoyage($_POST['ville']);
	}

	if (empty($_POST['pays'])) {
		$pays = "";
	} else {
		$pays = nettoyage($_POST['pays']);
	}

	if (empty($_POST['largeur_max'])) {
		$largeur_max = "NULL";
	} else {
		$largeur_max = nettoyage($_POST['largeur_max']);
	}

	if (empty($_POST['topographie'])) {
		$topographie_erreur = "Renseigner la pente.";
	} else {
		$topographie = nettoyage($_POST['topographie']);
	}

	if (empty($_POST['type'])) {
		$type = "";
	} else {
		$type = nettoyage($_POST['type']);
	}

	if (empty($_POST['contexte'])) {
		$contexte_erreur = "Renseigner le contexte du quartier.";
	} else {
		$contexte = nettoyage($_POST['contexte']);
	}

	if (empty($_POST['vitesse'])) {
		$vitesse = "NULL";
	} else {
		$vitesse = nettoyage($_POST['vitesse']);
	}

	if (empty($_POST['descriptif'])) {
		$descriptif = "";
	} else {
		$descriptif = nettoyage($_POST['descriptif']);
	}
}

# POUR LES PHOTOS :

$tabExt = array('jpg','jpeg');    // Extensions autorisees


$file_inputs_index = array(1,2,3);

foreach ($file_inputs_index as $file_input_number) {

	$file_input_name = 'photo'.$file_input_number;

	if (!empty($_FILES["$file_input_name"]['name'])) {

		$tabExt = array('jpg','jpeg');    // Extensions autorisees

		// Recuperation de l'extension du fichier
		$extension  = pathinfo($_FILES["$file_input_name"]['name'], PATHINFO_EXTENSION);

		// On verifie l'extension du fichier
		if(!in_array(strtolower($extension), $tabExt)) {
			$erreurs_photo[] = 'Type de fichier non autorisé.';
		}
		// On verifie la taille de l'image
		if($_FILES["$file_input_name"]['size'] >= 1048576) {
			$erreurs_photo[] = 'Fichier trop gros.';
		}
		// Parcours du tableau d'erreurs
		if(isset($_FILES["$file_input_name"]['error']) && UPLOAD_ERR_OK === $_FILES["$file_input_name"]['error']) {
			#RAS
		} else {
			$erreurs_photo[] = 'ERREUR';
		}
		// On renomme le fichier
		$nomImage = $file_input_name .'.'. $extension;

	}

	#L'absence de photo n'est pas une erreur mais doit être signalée :
	if (empty($_FILES["$file_input_name"]['name'])) {
		$pas_de_photo = true;
	} else {
		$pas_de_photo = false;
	}

	#On regarde dans la base s'il existe une photo pour cette section et ce numéro de photo (de champ) :
	$SELECT_photo = $bdd->query("SELECT * FROM photos WHERE refSection = $idSection AND numPhoto = $file_input_number");
	$photo_bdd = $SELECT_photo->fetch();
	if (!empty($photo_bdd['id'])) {
		#S'il y a déjà une photo dans la bdd, on fixe l'$idPhoto égal à celui-ci pour que la procédure passe à UPDATE (et non INSERT) :
		$idPhoto = $photo_bdd['id'];
		#Récupère également le nom de la photo actuellement stockée :
		$old_nomImage = $photo_bdd['nom'];
	} else {
		#S'il n'y a pas de photo dans la bdd, on fixe l'$idPhoto à NULL pour que la procédure passe à INSERT (et non UPDATE) :
		$idPhoto = 'NULL';
	}

	#Si aucune photo n'est téléversée, on ne fait rien, sinon, on prépare les requêtes et la fonction :
	if ($pas_de_photo) {

		$SQL_photos[] = "";
		$functions_MUF[] = "";

	} else {

		if (empty($erreurs_photo)) {

			#On prépare la requête (insérée dans une chaine de requêtes du coup) : INSERST s'il n'y a pas de doublon, UDPATE sinon :
			$SQL_photos[] = "INSERT INTO photos (id, refSection, numPhoto, nom) VALUES ($idPhoto, $idSection, $file_input_number, '$nomImage') ON DUPLICATE KEY UPDATE nom = '$nomImage'";
			
			#On crée un répertoire pour les photos de cette section, s'il n'existe pas déjà :
			$repertoire_photos_section = "photos/$idSection/";
			if (!is_dir($repertoire_photos_section)) {
				mkdir($repertoire_photos_section);
			}

			#Et on prépare la fonction move_uploaded_file qui va créer la photo dans le server :
			if (file_exists('photos/$idSection/$nomImage')) {
				$unlink_previous_photo = unlink($repertoire_photos_section.$old_nomImage);
			}
			$functions_MUF[] = move_uploaded_file($_FILES[$file_input_name]['tmp_name'], $repertoire_photos_section.$nomImage);

		}

	}

}


?>