<?php 
global $_site_assets_front_admin , $_site_assets_path_front_admin; 

	$_site_assets_front_admin = array(
	'css' =>array(
 		'privatepanel'=>array(
 			'guest'=>array(
				"plugins/font-awesome/css/font-awesome.min.css",
				"plugins/simple-line-icons/simple-line-icons.min.css",
				"plugins/bootstrap/css/bootstrap.min.css",
				"plugins/uniform/css/uniform.default.css",
				"plugins/bootstrap-switch/css/bootstrap-switch.min.css"	,
				"admin_css/login-soft.css",
				"admin_css/components.css",
				"admin_css/plugins.css",
				"admin_css/layout.css",
				"admin_css/default.css",
				"admin_css/custom.css",
				"admin_css/style_custom.css",
				"admin_css/datepicker.css",
				
			),
			
			'user'=>array(
				"plugins/font-awesome/css/font-awesome.min.css",
				"plugins/simple-line-icons/simple-line-icons.min.css",
				"plugins/bootstrap/css/bootstrap.min.css",
				"plugins/uniform/css/uniform.default.css",
				"plugins/bootstrap-switch/css/bootstrap-switch.min.css",
				"plugins/gritter/css/jquery.gritter.css",
				"plugins/bootstrap-daterangepicker/daterangepicker-bs3.css",
				"plugins/fullcalendar/fullcalendar/fullcalendar.css",
				"plugins/jqvmap/jqvmap/jqvmap.css",
				"plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css",
				
				"plugins/mcustomscroolbar.css",
				"admin_css/components.css",
				"admin_css/plugins.css",	
				"admin_css/tasks.css",
				"admin_css/layout.css",
				"admin_css/default.css",
				"admin_css/custom.css",	
				"plugins/bootstrap-toggle-buttons/static/stylesheets/bootstrap-toggle-buttons.css",
				"admin_css/style_custom.css",
				"admin_css/datepicker.css",
				
 				"module"=>array(
					"static"=>array(
						"graphicmedia"=>array(
							"plugins/fancybox/source/jquery.fancybox.css",
							"admin_css/portfolio.css",
							)
					),
					"index"=>array(
						"browse"=>array(
							"plugins/fancybox/source/jquery.fancybox.css",
							"admin_css/portfolio.css",
							)
					),
					"profile"=>array(
						"image"=>array(
								"plugins/bootstrap-fileinput/bootstrap-fileinput.css"
							)
					
					),
					"user"=>array(
						"account"=>array(
								"admin_css/profile.css",
								"front_css/jquery-ui.css",
								"front_css/star-rating.min.css"
							),
							"detail"=>array(
								"admin_css/profile.css",
								"front_css/jquery-ui.css",
								"front_css/star-rating.min.css"
							),
						"password"=>array(
								"admin_css/profile.css"
							),
						"image"=>array(
								"admin_css/profile.css",
								"plugins/bootstrap-fileinput/bootstrap-fileinput.css"
							)
					
					),
				
				)
 			),
		),
		
		'default'=>array(
		
 			'guest'=>array(
				"front_css/bootstrap.css",
				"front_css/style_custom.css",
				"plugins/font-awesome/css/font-awesome.min.css",
				"front_css/casaadvisor.css?".time(),
				
				"module"=>array(
					"index"=>array(
						"index"=>array(
								"front_css/owl.carousel.css",
								"front_css/owl.theme.css",
								"front_css/animate.css",
							),
						"seeallcategory"=>array(
								"front_css/jquery.steps.css",
								"front_css/masonry.css",
							),
						"seeallservice"=>array(
								"front_css/masonry.css",
							),
						"seeallsubcategory"=>array(
								"front_css/masonry.css",
							),
							
					),
					"search"=>array(
						"index"=>array(
							'front_css/jquery-ui.css',
							'front_css/star-rating.min.css',
							),
						"homesearch"=>array(
								"front_css/masonry.css",
							),	
						"providerprofile"=>array(
							'front_css/jquery-ui.css',
							'front_css/star-rating.min.css',
						),		
									
					),
					
					"static"=>array(
						"index"=>array(
								"front_css/owl.carousel.css",
								"front_css/owl.theme.css",
							),
					),
					
					"user"=>array(
						"registerbusniess"=>array(
								"front_css/jquery.steps.css",
								"plugins/chosen-bootstrap/chosen//chosen.css",
								"plugins/mcustomscroolbar.css",
							),
					),
					
				),
 			),
			
			'user'=>array(
				"front_css/bootstrap.css",
				"plugins/font-awesome/css/font-awesome.min.css",
				"front_css/casaadvisor.css?".time(),
				"plugins/mcustomscroolbar.css",
								
				"module"=>array(
					"profile"=>array(
						"image"=>array(
								"plugins/bootstrap-fileinput/bootstrap-fileinput.css"
							),
						"cropimage"=>array(
								"plugins/jcrop/css/jquery.Jcrop.min.css",
							)
					),
					
					"index"=>array(
						"index"=>array(
								"front_css/owl.carousel.css",
								"front_css/owl.theme.css",
								"front_css/animate.css",
							),
					 "seeallcategory"=>array(
								"front_css/masonry.css",
							),
					"seeallservice"=>array(
								"front_css/masonry.css",
							),
				   "seeallsubcategory"=>array(
								"front_css/masonry.css",
								"plugins/jquery-file-upload/css/jquery.fileupload.css",
								"plugins/jquery-file-upload/css/jquery.fileupload-ui.css",
							),
					),
					
					"search"=>array(
						"index"=>array(
							'front_css/jquery-ui.css',
							'front_css/star-rating.min.css',
						),
						"homesearch"=>array(
								"plugins/jquery-file-upload/css/jquery.fileupload.css",
								"plugins/jquery-file-upload/css/jquery.fileupload-ui.css",
								"front_css/masonry.css",
						),
						"providerprofile"=>array(
							'front_css/jquery-ui.css',
							'front_css/star-rating.min.css',
						),
					),
					
					"project"=>array(
							"addnewproject"=>array(
									"front_css/jquery.steps.css",
									"admin_css/datepicker.css",
									 "plugins/jquery-file-upload/css/jquery.fileupload.css",
									 "plugins/jquery-file-upload/css/jquery.fileupload-ui.css",
								),
							"editproject"=>array(
									"front_css/jquery.steps.css",
									"admin_css/datepicker.css",
									"plugins/jquery-file-upload/css/jquery.fileupload.css",
									"plugins/jquery-file-upload/css/jquery.fileupload-ui.css",
								),	
							"postreview"=>array(
								"front_css/jquery.rating.css",
								),		
							),
					
					"static"=>array(
						"index"=>array(
								"front_css/owl.carousel.css",
								"front_css/owl.theme.css",
							),
						),
				),
 				"front_css/style_custom.css",
			),
		)
	),
	'js' =>array(
		'privatepanel'=>array(
			'guest'=>array(
				"plugins/jquery-1.12.3.min.js",
				"plugins/jquery-migrate-1.2.1.min.js",
				"plugins/bootstrap/js/bootstrap.min.js",
				"plugins/uniform/jquery.uniform.min.js",
				"plugins/jquery-validation/js/jquery.validate.min.js",
				"plugins/backstretch/jquery.backstretch.min.js",
				"admin_js/metronic.js",
				"admin_js/layout.js",
				"admin_js/login-soft.js",
				"admin_js/bootstrap-datepicker.js",
				
			),
			'user'=>array(
  				"plugins/jquery-1.12.3.min.js",
				"plugins/jquery-migrate-1.2.1.min.js",
				"plugins/jquery-ui/jquery-ui-1.10.3.custom.min.js",
				"plugins/jquery-validation/js/jquery.validate.min.js",
				"plugins/jquery-validation/js/additional-methods.min.js",
				"plugins/bootstrap/js/bootstrap.min.js",
				"plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js",
				"plugins/jquery-slimscroll/jquery.slimscroll.min.js",
				"plugins/jquery.blockui.min.js",
				"plugins/jquery.cokie.min.js",
				"plugins/uniform/jquery.uniform.min.js",
				"plugins/bootstrap-switch/js/bootstrap-switch.min.js",
				"plugins/jqvmap/jqvmap/jquery.vmap.js",
				"plugins/flot/jquery.flot.min.js",
				"plugins/flot/jquery.flot.resize.min.js",
				"plugins/flot/jquery.flot.categories.min.js",
				"plugins/jquery.pulsate.min.js",
				"plugins/bootstrap-daterangepicker/moment.min.js",
				"plugins/bootstrap-daterangepicker/daterangepicker.js",
				"plugins/fullcalendar/fullcalendar/fullcalendar.min.js",
				"plugins/jquery-easypiechart/jquery.easypiechart.min.js",
				"plugins/datatables/media/js/jquery.dataTables.min.js",
				"plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js",
				"plugins/bootstrap-toggle-buttons/static/js/jquery.toggle.buttons.js",
				"plugins/ckeditor/ckeditor.js",
				"plugins/jquery.sparkline.min.js",
				"plugins/gritter/js/jquery.gritter.js",
				"admin_js/metronic.js",
				"admin_js/layout.js",
				"admin_js/quick-sidebar.js",
				"admin_js/index.js",
				"plugins/jquery.lazyload.min.js",
				"admin_js/tasks.js",
				"admin_js/bootstrap-datepicker.js",
				"plugins/mcustomscroolbar.js",
				
 				"module"=>array(
					"static"=>array(
						"graphicmedia"=>array(
							"plugins/jquery-mixitup/jquery.mixitup.min.js",
							"plugins/fancybox/source/jquery.fancybox.pack.js",
							"admin_js/portfolio.js",
							
							)
					),
					
					
					"index"=>array(
						"browse"=>array(
							"plugins/jquery-mixitup/jquery.mixitup.min.js",
							"plugins/fancybox/source/jquery.fancybox.pack.js",
							"admin_js/portfolio.js",
							
							),
							"index"=>array(
								"admin_js/highcharts.js",
							)
					),
					"profile"=>array(
						"image"=>array(
							"plugins/bootstrap-fileinput/bootstrap-fileinput.js",
							
							)
					),
					"user"=>array(
						"image"=>array(
							"plugins/bootstrap-fileinput/bootstrap-fileinput.js",
							),
						"account"=>array(
							'front_js/jquery-ui.js',
							'front_js/star-rating.min.js',
							),	
						"detail"=>array(
							'front_js/jquery-ui.js',
							'front_js/star-rating.min.js',
							),	
					),
					
				),
				
				"admin_js/general.js?".time(),
				"front_js/placeholder.js",
  			),
		),
 		
		'default'=>array(
			'guest'=>array(
				"plugins/jquery.pulsate.min.js",
				"front_js/bootstrap.min.js",
				"plugins/jquery-validation/js/jquery.validate.min.js",
				"plugins/jquery-validation/js/additional-methods.min.js",
				"plugins/mcustomscroolbar.js",
				"plugins/jquery.blockui.min.js",
				"plugins/jquery.lazyload.min.js",
				"module"=>array(
					"profile"=>array(
						"image"=>array(
							"plugins/bootstrap-fileinput/bootstrap-fileinput.js",
							),
						 "cropimage"=>
						 	array(
								"plugins/jcrop/js/jquery.Jcrop.min.js"
								)
					),
					
					"index"=>array(
						"index"=>array(
								"plugins/bxslider-4-master/jquery.bxslider.min.js",
								"front_js/owl.carousel.js",
								"plugins/jquery-autocomplete/jquery.auto-complete.min.js",
								"front_js/wow.js",
							),
						"seeallcategory"=>array(
								"plugins/jquery-autocomplete/jquery.auto-complete.min.js",
								"front_js/masonry.pkgd.min.js",
								"front_js/bootbox.min.js",
							),
						"seeallservice"=>array(
							"plugins/jquery-autocomplete/jquery.auto-complete.min.js",
							"front_js/masonry.pkgd.min.js",
							),
						"seeallsubcategory"=>array(
							"plugins/jquery-autocomplete/jquery.auto-complete.min.js",
							"front_js/masonry.pkgd.min.js",
							"front_js/bootbox.min.js",
							),
					),
					
					
					"static"=>array(
						"index"=>array(
								"plugins/bxslider-4-master/jquery.bxslider.min.js",
								"front_js/owl.carousel.js",
							),
					),
					"user"=>array(
						"registerbusniess"=>array(
								"front_js/jquery.steps.js",
								"plugins/mcustomscroolbar.js",
								"plugins/chosen-bootstrap/chosen/chosen.jquery.js"
							),
					),
					
					"search"=>array(
						"index"=>array(
							'front_js/jquery-ui.js',
							'front_js/star-rating.min.js',
							),
						"homesearch"=>array(
								"front_js/masonry.pkgd.min.js",
								"front_js/bootbox.min.js",
							),
						"providerprofile"=>array(
							'front_js/jquery-ui.js',
							'front_js/star-rating.min.js',
						),				
					),
					 
				),
				"front_js/general.js?".time(),
			),
			'user'=>array(
 				"plugins/jquery.pulsate.min.js",
				"front_js/bootstrap.min.js",
				"plugins/jquery.lazyload.min.js",
				"plugins/jquery-validation/js/jquery.validate.min.js",
				"plugins/jquery-validation/js/additional-methods.min.js",
				"plugins/mcustomscroolbar.js",
				
				
				"module"=>array(
					"profile"=>array(
						"image"=>array(
							"plugins/bootstrap-fileinput/bootstrap-fileinput.js",
							
							),
						 "cropimage"=>array(
								"plugins/jcrop/js/jquery.Jcrop.min.js"
							)
							
					),
					"index"=>array(
						"index"=>array(
								"plugins/bxslider-4-master/jquery.bxslider.min.js",
								"front_js/owl.carousel.js",
								"plugins/jquery-autocomplete/jquery.auto-complete.min.js",
								"front_js/wow.js",
							),
						"seeallcategory"=>array(
								"plugins/jquery-autocomplete/jquery.auto-complete.min.js",
								"front_js/jquery.steps.js",
								"front_js/masonry.pkgd.min.js",
								"front_js/bootbox.min.js",
							),
						"seeallservice"=>array(
							"plugins/jquery-autocomplete/jquery.auto-complete.min.js",
							"front_js/masonry.pkgd.min.js",
							),
						"seeallsubcategory"=>array(
							"plugins/jquery-autocomplete/jquery.auto-complete.min.js",
							"front_js/masonry.pkgd.min.js",
							"front_js/bootbox.min.js",
							),
					),
					
					"project"=>array(
						"addnewproject"=>array(
								"plugins/jquery-autocomplete/jquery.auto-complete.min.js",
								"admin_js/bootstrap-datepicker.js",
							),
						 "editproject"=>array(
								"plugins/jquery-autocomplete/jquery.auto-complete.min.js",
								"admin_js/bootstrap-datepicker.js",
							),	
						"postreview"=>array(
								"front_js/jquery.rating.js",
								),		
					),
					
					"search"=>array(
						"index"=>array(
							'front_js/jquery-ui.js',
							'front_js/star-rating.min.js',
						 ),
						"homesearch"=>array(
								"front_js/masonry.pkgd.min.js",
								"front_js/bootbox.min.js",
							),			
						"providerprofile"=>array(
							'front_js/jquery-ui.js',
							'front_js/star-rating.min.js',
						),
							
					),
					
					
					"static"=>array(
						"index"=>array(
								"front_js/owl.carousel.js",
							),
					),
					"product"=>array(
						 "index"=>array(
							)
							
					)
					
				),
				"front_js/general.js?".time(),
			),
		)
	),
);





$_site_assets_path_front_admin  = array(
	"css"=>array(
		"privatepanel"=>APPLICATION_URL."/public/",
		"default"=>APPLICATION_URL."/public/",
	),
	"js"=>array(
		"privatepanel"=>APPLICATION_URL."/public/",
		"default"=>APPLICATION_URL."/public/",
	),
);


