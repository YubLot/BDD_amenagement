<?php
$bdd = new PDO('mysql:host=localhost;dbname=amenagements;charset=utf8','root','',array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));

if (isset($_POST['nom_section'])) {
	$nom_section = $_POST['nom_section'];
}

$bdd->query("INSERT INTO sections (nom) VALUES ('$nom_section')");

$last_section = $bdd->query("SELECT MAX(id) AS maxId FROM sections");
$fetch_last_section = $last_section->fetch();
$idSection = $fetch_last_section['maxId'];

header('Location: modif_section.php?id='.$idSection)


?>

