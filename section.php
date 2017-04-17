<?php
$bdd = new PDO('mysql:host=localhost;dbname=amenagements;charset=utf8','root','',array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));

$idSection = $_GET['id'];

$requete_section = $bdd->query("SELECT * FROM sections WHERE id = $idSection");
$section = $requete_section->fetch();

$requete_photos = $bdd->query("SELECT * FROM photos WHERE refSection = $idSection");

include('creer_miniatures.php');

?>

<!DOCTYPE html>
<html>
<head>
	<title><?php echo $section['nom']; ?></title>
	<link rel="stylesheet" href="styles.css">
</head>
<body>

<aside>
	<a href="bddSections.php">
		Retour à la liste
	</a>
</aside>

<article>
	<h1><?php echo $section['nom']; ?></h1>
	<h2><?php echo $section['ville']; ?></h2>
	<div id="donnees_tech">
		<ul>
			<li><?php echo $section['largeur'] . " mètres" ?></li>
			<li><?php echo $section['topographie']; ?></li>
			<li><?php echo $section['type']; ?></li>
			<li><?php echo $section['contexte']; ?></li>
			<li><?php echo $section['vitesse'] . " km/h"; ?></li>
	</div>
	<p><?php echo $section['descriptif']; ?></p>
	<?php

	while ($photos = $requete_photos->fetch()) {
		$photo_nom = $photos['nom'];
 		echo "<img src='photos/$idSection/$photo_nom'>";
 	} 
	
	?>

	<a href="modif_section.php?id=<?php echo $idSection ?>">Modifier la section.</a>
</article>

</body>
</html>