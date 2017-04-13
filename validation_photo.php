<?php
#validationPhoto



// On verifie si le champ est rempli
if( !empty($_FILES['fichier']['name']) ) {

	// Recuperation de l'extension du fichier
	$extension  = pathinfo($_FILES['fichier']['name'], PATHINFO_EXTENSION);

	// On verifie l'extension du fichier
	if(!in_array(strtolower($extension),$tabExt)) {
		$messages_erreur[] = 'Type de fichier non autorisé.';
	}

	// On verifie la taille de l'image
	if(((filesize($_FILES['fichier']['tmp_name']) >= MAX_SIZE))) {
		$messages_erreur[] = 'Fichier trop gros.';
	}

	// Parcours du tableau d'erreurs
	if(isset($_FILES['fichier']['error']) && UPLOAD_ERR_OK === $_FILES['fichier']['error']) {
		#RAS
	} else {
		$messages_erreur[] = 'ERREUR';
	}

	// On renomme le fichier
	$nomImage = $section_id .'.'. $extension;

	if (empty($messages_erreur)) {
		move_uploaded_file($_FILES['fichier']['tmp_name'], TARGET.$nomImage);
	}


} else {

	$messages_erreur[] = 'Aucun fichier transmis';

}


?>

	