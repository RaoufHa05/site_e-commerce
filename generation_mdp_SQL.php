<?php
// Tape ici le mot de passe que tu veux utiliser pour ton compte admin
$mot_de_passe = "Raouf2005";

// Générer le mot de passe hashé
$mot_de_passe_hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);

// Afficher le mot de passe hashé
echo $mot_de_passe_hash;
?>
