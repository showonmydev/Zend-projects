/**
 * @license Copyright (c) 2003-2014, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
	
	
	
	
	config.toolbarGroups = [
		{ name: 'clipboard',   groups: [ 'clipboard', 'undo'  ] },
		{ name: 'editing',     groups: [ 'find', 'selection', 'spellchecker' ] },
		{ name: 'links' },
 		{ name: 'insert' },
		'/',
		{ name: 'forms' },
		{ name: 'tliyoutube' },
		{ name: 'tools' },
		{ name: 'document',	   groups: [ 'mode', 'document', 'doctools' ] },
		{ name: 'others'  },
		'/',
		{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
		{ name: 'paragraph',   groups: [ 'list', 'indent', 'blocks', 'align' ] },
		{ name: 'styles' },
		{ name: 'colors' },
		{ name: 'about' },
		{ name: 'strinsert' },
		{ name: 'wenzgmap' },
 		 
 	];

 	config.extraPlugins = 'strinsert,wenzgmap,tliyoutube';
	
	//config.extraPlugins = 'wenzgmap';     /* extra*/
	//config.extraPlugins = 'tliyoutube';
	
	 config.allowedContent=true;
	  CKEDITOR.dtd.$removeEmpty.span = 0;
	  CKEDITOR.dtd.$removeEmpty.i = 0;
	
		CKEDITOR.config.contentsCss =  [  baseUrl+'/public/front_css/bootstrap.css', baseUrl+'/public/front_css/casaadvisor.css', baseUrl+'/public/front_css/style_custom.css' ];

	 
	
	config.strinsert_button_label = 'Content Block';
    config.strinsert_button_title = config.strinsert_button_voice = 'Insert Content Block';
	
	
	config.filebrowserImageBrowseUrl = baseUrl+'/privatepanel/index/browse';
	config.filebrowserUploadUrl = baseUrl+'/privatepanel/index/upload';
	
 	
	
};


 