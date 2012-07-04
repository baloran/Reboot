Objectif du "framework"

	- Simplfier les tâches de gestion récurentes (redirection, erreur, parramétrage) entre les projets
	- Pister et tracer toutes les erreurs et leurs contextes (requête sql, fichier, utilisateur, erreur utilisateur, environnement utilisateur)
	- Harmoniser la structure et le développement
	- Simplifier les dialogues avec la base de donnée sur des classes "objet", souvent lié ou ayant la majorité des discusions avec une seule table.
	- Proposer une gestion harmonisé de table de base (Utilisateur, Log, Moderation)


Mise en place dans le dossier web "WWW"

	- Le dossier GLOBAL 						// Moteur du framewok, contient les classes, fonctions, fichiers de configuration des projets

	- Le projet peut être intégrer 
		- soit à la racine d'un dossier correspondant au nom de domaine (dossier "reboot.fr" => url : "reboot.fr")
		- soit dans un sous dossier de celui-ci (dossier "reboot" dans le dossier "opworldlab.com" => url : "reboot.opworldlab.com" ou "opworldlab.com/reboot")
		- soit pour la configuration localhost dans un dossier directement (dossier "reboot" => url : "localhost/reboot")


Structure du framework

	- auth				// Fichier de paramétrage de l'authentification à la base de donnée, le nom du fichier est le dossier home du projet avec des "_" au lieu des "/"
						// Ce qui donne pour l'url "http://localhost/reboot" => "localhost_reboot.php"

	- functions			// Différentes fonctions utilisé par le framework

	- classes			// Les classes qui comment par un "_" sont succeptible d'avoir une fille dans le model des projets
						// Les classes sont les seuls fichiers qui commence par une majuscule
		- _Empty.php			// classe exemple
		- _User.php				// Gestion des utilisateurs
		- Manager.php			// Gestion de projet
		- Object.php			// Gestion d'object communicant (lié à une table en base de donnée)

Structure de projet

	- controller		// CONTROLLEURS : fichiers qui seront appelé depuis les vues, utilise les classes des models et peux retourne un résultat
		- js
		- php
			- empty.php 		// controleur exemple
			- user.php

	- include			// RESSOURCES
		- css 				// feuilles de styles
		- img				// images
		- js 				// scripts
		- php 				// interface
			- builder			// Menus et fonction de construction de l'interface du projet
			- header
			- footer

	- model
		- php
		- js

	- view				// VUES : affiche le site

	- .htaccess			// Masque la structure du projet et améliore les urls