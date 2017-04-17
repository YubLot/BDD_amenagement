<?php
$bdd = new PDO('mysql:host=localhost;dbname=amenagements;charset=utf8','root','',array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
?>

<!DOCTYPE html>
<html>

<head>
	<title>Édition en cours</title>
	<link rel="stylesheet" href="styles.css">
</head>

<body>

<?php

# ÉTAPE 1 : CHARGER LES VALEURS PAR DÉFAUT

#Récupère l'identifiant de la section en cours d'édition :
if (isset($_GET['id'])) {
	$idSection = $_GET['id'];
}

$action_suffixe = '?id='.$idSection;

#Récupère toutes les données sur cette section :
$SELECT_section = $bdd->query("SELECT * FROM sections WHERE id = $idSection");
$section = $SELECT_section->fetch();

$nom = $section['nom'];
$ville = $section['ville'];
$pays = $section['pays'];
$largeur_max = $section['largeur'];
$topographie = $section['topographie'];
$type = $section['type'];
$contexte = $section['contexte'];
$vitesse = $section['vitesse'];
$descriptif = $section['descriptif'];

#Récupère les données photos :
$SELECT_photos = $bdd->query("SELECT * FROM photos WHERE refSection = $idSection ORDER BY numPhoto");

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

# ÉTAPE 2 : REMPLIR ET VALIDER LE FORMULAIRE

#Les procédures de validation :
require('validation.php');

# ÉTAPE 3 : Accepter les modifications ou afficher les messages d'erreur :

#On compose la requête avec ces données :
$UPDATE_section = "UPDATE sections SET nom = '$nom', ville = '$ville', pays = '$pays', largeur = $largeur_max, topographie = '$topographie', type = '$type', contexte = '$contexte', vitesse = $vitesse, descriptif = '$descriptif' WHERE id = $idSection";

#Si tous les champs obligatoires ont été remplis, alors on peut modifier la section et rediriger vers celle-ci :
if (!empty($_POST['nom']) AND !empty($_POST['ville']) AND !empty($_POST['topographie']) AND !empty($_POST['contexte'])) {

	$bdd->query($UPDATE_section);

}

if (empty($erreurs_photo)) {
	
	#Pour chaque champ de photo, on exécute la requête préparée :
	if (!empty($SQL_photos)) {
		foreach ($SQL_photos as $UPDATE_INSERT_photo) {
			if (!empty($UPDATE_INSERT_photo)) {
				$bdd->query($UPDATE_INSERT_photo);
			}	
		}
	}

	#Crées les photos sur le serveur :
	if (!empty($functions_MUF)) {
		foreach ($functions_MUF as $MUF) {
			if (!empty($MUF)) {
				$MUF;
			}
		}
	}
	

} else {
	foreach ($erreurs_photo as $erreur) {
		echo $file_input_name . " : " . $erreur . "<br>" ;
	}
}
?>

<aside>
	<?php

	#Afficher les photos téléversées :
	while ($photos = $SELECT_photos->fetch()) {
		$photo_nom = $photos['nom'];
		echo "<img src='photos/$idSection/$photo_nom'>";
	}
	?>
</aside>

<article>

	<form method="post" id="formulaire_section" enctype="multipart/form-data" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']).$action_suffixe?>">

		<label>Photo 1 : : <input name="photo1" type="file"/></label><br>
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

	<a href="section.php?id=<?php echo $idSection ?>">Terminé !</a>

</article>

</body>
</html>