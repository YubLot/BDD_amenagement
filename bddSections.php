<?php
$bdd = new PDO('mysql:host=localhost;dbname=amenagements;charset=utf8','root','',array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
?>


<!DOCTYPE html>
<html>
<head>
	<title>aménagements</title>
	<link rel="stylesheet" href="styles.css">
	<meta http-equiv="content-type" content="text/html; charset=utf-8" >
</head>
<body>

<aside>
	<h1>FILTRES</h1>
	<form method="POST" action="bddSections.php">
		<div>
			<h3>contexte :</h3>
			<label><input type="radio" name="contexte" value="urbain">urbain</label>
			<label><input type="radio" name="contexte" value="périurbain">périurbain</label>
			<label><input type="radio" name="contexte" value="rural">rural</label>
		</div>
		<div>
			<h3>topographie :</h3>
			<label><input type="radio" name="topographie" value="plat">plat</label>
			<label><input type="radio" name="topographie" value="faible pente">faible pente</label>
			<label><input type="radio" name="topographie" value="forte pente">forte pente</label>
		</div>
		<div>
			<h3>largeur moyenne en mètres :</h3>
			<label><input type="number" name="largeur" > mètres</label>
		</div>

		<div>
			<h3>vitesse maximale en km/h :</h3>
			<label><input type="number" name="vitesse_max" > km/h</label>
		</div>

		<div>
			<h3>mots clefs :</h3>
			<input type="search" autocomplete="on" name="mots_clefs"  placeholder="stationnement, arbres, carrefour...">
		</div>

		<input type="submit" name="envoyer">
	</form>
</aside>

<article>
	<h1>SECTIONS</h1>
	<?php

	#Vérifie si au moins un filtre a été appliqué :
	if (isset($_POST['envoyer'])) {

		$WHERE_CONDITIONS = array();

		if (isset($_POST['contexte'])) {
			$WHERE_CONTEXTE = "contexte = " . "'" . $_POST['contexte'] . "'";
			array_push($WHERE_CONDITIONS, $WHERE_CONTEXTE);
		}

		if (isset($_POST['topographie'])) {
			$WHERE_TOPOGRAPHIE = "topographie = " . "'" . $_POST['topographie'] . "'";
			array_push($WHERE_CONDITIONS, $WHERE_TOPOGRAPHIE);
		}

		if (isset($_POST['largeur']) AND $_POST['largeur'] > 0) {
			$WHERE_LARGEUR = "largeur <= " . "'" . $_POST['largeur'] . "'";
			array_push($WHERE_CONDITIONS, $WHERE_LARGEUR);
		}

		if (isset($_POST['vitesse_max']) AND $_POST['vitesse_max'] > 0) {
			$WHERE_VITESSE = "vitesse <= " . "'" . $_POST['vitesse_max'] . "'";
			array_push($WHERE_CONDITIONS, $WHERE_VITESSE);
		}

		if (isset($_POST['mots_clefs'])) {
			$WHERE_MOTS_CLEFS = "descriptif LIKE " . "'%" . $_POST['mots_clefs'] . "%'";
			array_push($WHERE_CONDITIONS, $WHERE_MOTS_CLEFS);
		}

		$WHERE = "WHERE " . implode(" AND ", $WHERE_CONDITIONS) ;

		} else {
			$WHERE = NULL;
		}

	$requete_sections = $bdd->query("SELECT * FROM sections $WHERE");

	while ($sections = $requete_sections->fetch()) {

		$idSection = $sections['id'];
	?>
		<a href="section.php?id=<?php echo $idSection;?>" target="_BLANK">
			<div class='dalle'>
				<div>
					<?php echo $sections['nom'] . " (" . $sections['ville'] . ")";?>
				</div>
				<img src='photos/<?php echo $idSection ?>/thumbs/photo1'/>
			</div>
		</a>
	<?php
	}

	$requete_sections->closeCursor();
	

	?>

	<div class="dalle">
		<div>
			<a href="nouvelle_section.php">Ajouter une section</a>
			<form method="POST" action="nouvelle_section.php">
				<label>nom : <input type="text" name="nom_section"></label>
				<input type="submit" name="creer_section">
			</form>
		</div>
	</div>
</article>
<footer>
	<?php
		print_r($WHERE);
	?>
</footer>

</body>
</html>

