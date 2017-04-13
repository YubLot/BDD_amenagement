<?php

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

			

			?>