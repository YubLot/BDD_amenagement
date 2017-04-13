		<?php

				  // On verifie si le champ est rempli
				  if( !empty($_FILES['fichier']['name']) ) {
				    // Recuperation de l'extension du fichier
				    $extension  = pathinfo($_FILES['fichier']['name'], PATHINFO_EXTENSION);

				    // On verifie l'extension du fichier
				    if(in_array(strtolower($extension),$tabExt)) {
				      // On verifie le type de l'image
				      if($infosImg[2] >= 1 && $infosImg[2] <= 14) {
				        // On verifie la taille de l'image
				        if(((filesize($_FILES['fichier']['tmp_name']) <= MAX_SIZE))) {
				          // Parcours du tableau d'erreurs
				          if(isset($_FILES['fichier']['error']) && UPLOAD_ERR_OK === $_FILES['fichier']['error']) {
				            // On renomme le fichier
				            $nomImage = $section_id .'.'. $extension;
				 
				            // Si c'est OK, on teste l'upload
				            if(move_uploaded_file($_FILES['fichier']['tmp_name'], TARGET.$nomImage)) {
				              $message = 'Upload réussi !';
				            } else {
				              // Sinon on affiche une erreur systeme
				              $message = 'Problème lors de l\'upload !';
				            }
				          } else {
				            $message = 'Une erreur interne a empêché l\'uplaod de l\'image';
				          }
				        } else {
				          // Sinon erreur sur les dimensions et taille de l'image
				          $message = 'Erreur dans les dimensions de l\'image !';
				        }
				      } else {
				        // Sinon erreur sur le type de l'image
				        $message = 'Le fichier à uploader n\'est pas une image !';
				      }
				    } else {
				      // Sinon on affiche une erreur pour l'extension
				      $message = 'L\'extension du fichier est incorrecte !';
				    }
				  } else {
				    // Sinon on affiche une erreur pour le champ vide
				    $message = 'Veuillez remplir le formulaire svp !';
				  }

	?>