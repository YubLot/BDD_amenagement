<?php
$bdd = new PDO('mysql:host=localhost;dbname=amenagements;charset=utf8','root','',array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));

include('creer_miniatures.php');

$section_id = $_GET['id'];
$requete_section = $bdd->query("SELECT * FROM sections WHERE id = $section_id");
$requete_section->execute();

$section = $requete_section->fetch();

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
	<img src="photos/<?php echo $section['id'] ?>"><br>

	<a href="edit_section.php?action=modifier&id=<?php echo $section_id ?>">Modifier la section.</a>
</article>

</body>
</html>