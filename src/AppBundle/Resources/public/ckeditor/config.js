/**
 * @license Copyright (c) 2003-2017, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {

	// Define changes to default configuration here. For example:
	config.language = 'fr';
	//config.uiColor = '#AADC6E';
	//config.extraPlugins = 'filebrowser';


	//Largeur vidéo:
	config.youtube_width = '640';
	
	//Hauteur vidéo:
	config.youtube_height = '480';
	
	//Rendre réactif (ignorer la largeur et la hauteur, adapter à la largeur):
	config.youtube_responsive = true;
	
	//Afficher des vidéos similaires:
	config.youtube_related = true;	

	//Utiliser l'ancien code d'intégration:	
	config.youtube_older = false;
	
	//Activer le mode de confidentialité amélioré:	
	config.youtube_privacy = false;
	
	//Démarrer la vidéo automatiquement:	
	config.youtube_autoplay = false;
	
	//Afficher les commandes du lecteur:	
	config.youtube_controls = true;
};