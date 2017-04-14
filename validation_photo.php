<?php

		// On verifie si le champ est rempli
		if( !empty($_FILES[$photo_id]['name']) ) {
			// Recuperation de l'extension du fichier
			$extension  = pathinfo($_FILES[$photo_id]['name'], PATHINFO_EXTENSION);
			// On verifie l'extension du fichier
			if(!in_array(strtolower($extension),$tabExt)) {
				$erreurs_photo[] = 'Type de fichier non autorisé.';
			}
			// On verifie la taille de l'image
			if($_FILES[$photo_id]['size'] >= 1048576) {
				$erreurs_photo[] = 'Fichier trop gros.';
			}
			// Parcours du tableau d'erreurs
			if(isset($_FILES[$photo_id]['error']) && UPLOAD_ERR_OK === $_FILES[$photo_id]['error']) {
				#RAS
			} else {
				$erreurs_photo[] = 'ERREUR';
			}
			// On renomme le fichier
			$nomImage = $photo_id .'.'. $extension;

		} else {
			$erreurs_photo[] = 'Aucun fichier transmis';
		}

?>

	