<?php
class Zend_View_Helper_Nav extends Zend_Navigation_Container
{
 	public function __construct(){
		/*$this->setContainer($this->getNavArray());*/
	}

	public function getNavArray(){
		
  		 $pages = array (
		
			/* Dashboard */
			array(
				'label' => 'Dashboard',
				'icon' =>'icon-home',
				'module' => 'privatepanel',
				'controller' => 'index',
 				'action' => 'index',
				'privilege' => 'index',
				'route'=>'default',
     		),
			
			
			/* 
				Admin Navigation 
				Manage Profile
			 */
			array(
				'label' => 'Manage Profile',
				'icon' =>'icon-user',
				'uri' => 'javascript:void(0)',
 				'pages' =>array(
					array(
						'label'=>'Update Profile',
						'icon' =>'fa fa-edit',
						'route'=>'update_profile_admin'
  					),
					array(
						'label'=>'Profile Image',
						'icon' =>'fa fa-edit',
						'route'=>'update_image_admin'
  					),
					array(
						'label'=>'Change Password',
						'icon' =>'fa fa-edit',
						'route'=>'update_password_admin'
  					),
   				)
     		),
			
			/* 
				(END) Manage Profile
			 */
			 
			
			 			/* 
				Admin Navigation 
				Site Setting
			 */ 
			array(
				'label' => 'Site Configurations',
				'icon' =>'icon-settings',
				'uri' => 'javascript:void(0)',
 				'pages' =>array(
					array(
						'label'=>'Site Configuration',
						'icon' =>'fa icon-settings',
 						'route'=>'admin_site_configs',
 					),
					
					 
   				)
     		),
			/* 
				(END) Site Setting
			 */
			 
			 
			/* 
				Admin Navigation 
				Slider Images
			 */ 
			array(
				'label' => 'Slider Images',
				'icon' =>'fa fa-file-image-o',
				'uri' => 'javascript:void(0)',
				'route'=>'default',
 				'pages' =>array(
					array(
						'label'=>'Slider Images',
						'icon' =>'fa fa-file-image-o',
						'module'=>'privatepanel',
						'controller'=>'slider',
						'action'=>'index',
						'route'=>'default',
						'pages'=>array(
 							array(
								'label'=>'Slider Images',
								'icon' =>'fa fa-file-image-o',
								'module'=>'privatepanel',
								'controller'=>'slider',
								'action'=>'index',
								'route'=>'default',
							),
							array(
								'label'=>'Add Slider Image ',
								'icon' =>'icon-edit',
								'module'=>'privatepanel',
								'controller'=>'slider',
								'action'=>'add',
								'route'=>'default',
							),
							array(
								'label'=>'Edit Static Pages',
								'icon' =>'icon-edit',
								'module'=>'privatepanel',
								'controller'=>'slider',
								'action'=>'edit',
								'route'=>'default',
							),
						
						)
 					),
					
					 
   				)
     		),
			/* 
				(END) Site Setting
			 */

 
			 
 			 
			 
			/* 
				Admin Navigation 
				Static Content
			 */ 
 			array(
				'label' => 'Static Content',
				'icon' =>'fa fa-file-text',
 				'uri' => 'javascript:void(0)',
  				'pages' =>array(
					array(
						'label'=>'Manage Pages',
						'icon' =>'fa fa-file-text-o',
 						'route'=>'admin_static_pages',
						'pages' =>array(
							array(
								'label'=>'Static Pages',
								'icon' =>'icon-paste',
 								'route'=>'admin_static_pages',
							),
							array(
								'label'=>'Edit Static Pages',
								'icon' =>'icon-edit',
 								'module' => 'privatepanel',
								'controller' => 'static',
								'action' => 'edit',
							),
							array(
								'label'=>'Edit Static Pages',
								'icon' =>'icon-edit',
 								'module' => 'privatepanel',
								'controller' => 'static',
								'action' => 'add',
							),
							 array(
									'label'=>'View Page Info',
									'icon' =>'icon-edit',
									'module' => 'privatepanel',
									'controller' => 'static',
									'action' => 'viewpage',
								),
						 
						)
						
   					),
				/*	array(
						'label'=>'Content Blocks',
						'icon' =>'fa fa-copy',
 						'route'=>'admin_content_block',
						'pages' =>array(
							array(
								'label'=>'Content Blocks',
								'icon' =>'icon-paste',
 								'route'=>'admin_content_block',
							),
							array(
								'label'=>'Add Content Block',
								'icon' =>'icon-edit',
 								'module' => 'admin',
								'controller' => 'static',
								'action' => 'addblock',
							),
							array(
								'label'=>'Edit Content Block',
								'icon' =>'icon-edit',
 								'module' => 'admin',
								'controller' => 'static',
								'action' => 'editcontentblock',
							),
							array(
								'label'=>'View Content Block',
								'icon' =>'icon-eye-open',
 								'module' => 'admin',
								'controller' => 'static',
								'action' => 'viewblock',
							),
						 
						 
						)
						
   					),*/
				
					/*array(
						'label' => 'Graphic Media',
						'icon' =>'icon-picture',
						'route'=>'admin_graphic_media',
						'pages' =>array(
							array(
								'label' => 'Graphic Media',
								'icon' =>'icon-camera',
 								'route'=>'admin_graphic_media',
							),
							array(
								'icon' =>'icon-edit',
								'label' => 'Edit Graphic Media',
								'module' => 'admin',
								'controller' => 'static',
								'action' => 'editgraphicmedia',
						),
							array(
								'icon' =>'fa fa-plus',
								'label' => 'Add New  Graphic Media',
 								'route'=>'admin_add_graphic_media'
							),
						)
					),*/
					
					array(
						'label'=>'Email Templates',
						'icon' =>'icon-envelope-letter',
						'route'=>'admin_email_templates',
						'pages' =>array(
							array(
								'icon' =>'icon-envelope-alt',
								'label' => 'Email Templates',
 								'module' => 'privatepanel',
								'controller' => 'email',
								'action' => 'index',
							),
							array(
								'icon' =>'icon-edit',
 								'label' => 'Edit Template ',
								'module' => 'privatepanel',
								'controller' => 'static',
								'action' => 'editmailtemplate',
							),
						 
 						)
						
  					),
					array(
						'label'=>'FAQ',
						'icon' =>'fa icon-question',
						'module' =>'privatepanel',
						'controller' =>'static',
						'action' =>'faq',
						'route'=>'default',
						'pages' =>array(
							array(
								'icon' =>'fa fa-question',
								'label' => 'Email Templates',
 								'module' => 'privatepanel',
								'controller' => 'static',
								'action' => 'addfaq',
							),
							
						 
 						)
						
  					),
					array(
						'label'=>'About Team Member',
						'icon' =>'fa fa-user',
						'module' =>'privatepanel',
						'controller' =>'static',
						'action' =>'aboutteam',
						'route'=>'default',
						'pages' =>array(
							array(
								'icon' =>'fa fa-user',
								'label' => 'Team Member',
 								'module' => 'privatepanel',
								'controller' => 'static',
								'action' => 'addaboutteam',
							),
							
						 
 						)
						
  					),
 					
   				)
     		),
  			/* 
				(END) Site Setting
			 */
  			
 
			
 			
 			/* 
				Admin Navigation 
				User Management
			 */
//subscribe user
			array(
				'label' => 'Subscriber',
				'icon' =>'fa fa-users',
				'uri' => 'javascript:void(0)',
 				'pages' =>array(
					array(
						'label'=>'All',
						'icon' =>'fa  fa-users',
 						'module' =>'privatepanel',
						'controller' =>'user',
						'action' =>'subscriber',
						'route'=>'default',
						
 					),
   				)
     		),
						 
			 
			array(
				'label' => 'Clients',
				'icon' =>'fa fa-users',
				'uri' => 'javascript:void(0)',
 				'pages' =>array(
					array(
						'label'=>'All',
						'icon' =>'fa  fa-users',
 						'module' =>'privatepanel',
						'controller' =>'user',
						'action' =>'index',
						'route'=>'default',
						'pages' =>array(
							array(
								'label'=>'All',
								'icon' =>'icon-user',
 								'module' => 'privatepanel',
								'controller' => 'user',
								'action' => 'index',
							),
							array(
								'label'=>'User Information ',
								'icon' =>'icon-zoom-in',
 								'module' => 'privatepanel',
								'controller' => 'user',
								'action' => 'account',
							),
							array(
								'label'=>'User Image ',
								'icon' =>'icon-zoom-in',
 								'module' => 'privatepanel',
								'controller' => 'user',
								'action' => 'image',
							),
							array(
								'label'=>'Reset User Password ',
								'icon' =>'icon-zoom-in',
 								'module' => 'privatepanel',
								'controller' => 'user',
								'action' => 'password',
							),
  						)
 					),
					array(
						'label'=>'Verified',
						'icon' =>'fa fa-check-circle',
 						'module' =>'privatepanel',
						'controller' =>'user',
						'action' =>'verified',
						'route'=>'default',
					 
 					),
					array(
						'label'=>'Blocked',
						'icon' =>'fa fa-warning ',
 						'module' =>'privatepanel',
						'controller' =>'user',
						'action' =>'blocked',
						'route'=>'default',
  					),
   				)
     		),
			
			
			array(
				'label' => 'Service Providers',
				'icon' =>'fa fa-users',
				'uri' => 'javascript:void(0)',
 				'pages' =>array(
					array(
						'label'=>'All',
						'icon' =>'fa  fa-users',
 						'module' =>'privatepanel',
						'controller' =>'user',
						'action' =>'serviceproviders',
						'route'=>'default',
						'pages' =>array(
							array(
								'label'=>'All ',
								'icon' =>'icon-user',
 								'module' => 'privatepanel',
								'controller' => 'user',
								'action' => 'serviceproviders',
							),
							array(
								'label'=>'User Information ',
								'icon' =>'icon-zoom-in',
 								'module' => 'privatepanel',
								'controller' => 'user',
								'action' => 'account',
							),
							array(
								'label'=>'User Image ',
								'icon' =>'icon-zoom-in',
 								'module' => 'privatepanel',
								'controller' => 'user',
								'action' => 'image',
							),
							array(
								'label'=>'Reset User Password ',
								'icon' =>'icon-zoom-in',
 								'module' => 'privatepanel',
								'controller' => 'user',
								'action' => 'password',
							),
  						)
 					),
					array(
						'label'=>'Verified',
						'icon' =>'fa fa-check-circle',
 						'module' =>'privatepanel',
						'controller' =>'user',
						'action' =>'verifiedserviceproviders',
						'route'=>'default',
					 
 					),
					array(
						'label'=>'Blocked',
						'icon' =>'fa fa-warning ',
 						'module' =>'privatepanel',
						'controller' =>'user',
						'action' =>'blockedserviceproviders',
						'route'=>'default',
  					),
   				)
     		),
			
// manage services
	
			array(
				'label' => 'Manage Services ',
				'icon' =>'fa fa-server',
				'uri' => 'javascript:void(0)',
 				'pages' =>array(
					array(
						'label'=>'Services ',
						'icon' =>'fa fa-server',
						'module' => 'privatepanel',
						'controller' => 'service',
 						'action' => 'index',
						'route'=>'default',
						'pages' =>array(
							array(
								'module' => 'privatepanel',
								'controller' => 'service',
								'action' => 'addservices',
							),
							array(
								'module' => 'privatepanel',
								'controller' => 'service',
								'action' => 'editservices',
							),
						),
  					),
					array(
						'label'=>'Service Categories',
						'icon' =>'fa fa-sitemap',
						'module' => 'privatepanel',
						'controller' => 'service',
 						'action' => 'servicecategory',
						'route'=>'default',
						'pages' =>array(
							array(
								'module' => 'privatepanel',
								'controller' => 'service',
								'action' => 'addservicecategory',
							),
							array(
								'module' => 'privatepanel',
								'controller' => 'service',
								'action' => 'default',
							),
						),
  					),
					array(
						'label'=>'Service Sub-Categories',
						'icon' =>'fa fa-sitemap',
						'module' => 'privatepanel',
						'controller' => 'service',
 						'action' => 'servicesubcategory',
						'route'=>'default',
						'pages' =>array(
							array(
								'module' => 'privatepanel',
								'controller' => 'service',
								'action' => 'addsubcategory',
							),
							array(
								'module' => 'privatepanel',
								'controller' => 'service',
								'action' => 'editsubcategory',
							),
						),
  					),
					
				array(
						'label'=>'Home Categories',
						'icon' =>'fa fa-sitemap',
						'module' => 'privatepanel',
						'controller' => 'service',
 						'action' => 'homecategories',
						'route'=>'default',
  					),
				array(
						'label'=>'Category Form',
						'icon' =>'fa fa-sitemap',
						'module' => 'privatepanel',
						'controller' => 'service',
 						'action' => 'categoryform',
						'route'=>'default',
  					),
					
					
   				)
     		),
			
// end manage services	

// manage credit package
	
			array(
				'label' => 'Manage Credit Packages ',
				'icon' =>'fa fa-credit-card',
				'uri' => 'javascript:void(0)',
 				'pages' =>array(
					array(
						'label'=>'Packages ',
						'icon' =>'fa fa-credit-card',
						'module' => 'privatepanel',
						'controller' => 'packages',
 						'action' => 'index',
						'route'=>'default',
						'pages' =>array(
							array(
								'module' => 'privatepanel',
								'controller' => 'packages',
								'action' => 'addpackage',
							),
							array(
								'module' => 'privatepanel',
								'controller' => 'service',
								'action' => 'editpackages',
							),
						),

  					),
					array(
						'label'=>'Page Description  ',
						'icon' =>'fa fa-file-text',
						'module' => 'privatepanel',
						'controller' => 'packages',
 						'action' => 'editpackagepage',
						'route'=>'default',
  					),
   				)
     		),
			
// end manage credit package	]


// manage Payment Report
	
			array(
				'label' => 'Payment report ',
				'icon' =>'fa fa-credit-card',
				'uri' => 'javascript:void(0)',
 				'pages' =>array(
					array(
						'label'=>'Provider credit History ',
						'icon' =>'fa fa-credit-card',
						'module' => 'privatepanel',
						'controller' => 'packages',
 						'action' => 'providercredithistory',
						'route'=>'default',
  					),
   				)
     		),
			
// end manage Payment Report	

// start manage job
			array(
				'label' => 'Manage Jobs',
				'icon' =>'fa fa-credit-card',
				'uri' => 'javascript:void(0)',
 				'pages' =>array(
					array(
						'label'=>'Job ',
						'icon' =>'fa fa-credit-card',
						'module' => 'privatepanel',
						'controller' => 'job',
 						'action' => 'index',
						'route'=>'default',
  					),
					
					array(
						'label'=>'Add Job Page Content ',
						'icon' =>'fa fa-file-text',
						'module' => 'privatepanel',
						'controller' => 'job',
 						'action' => 'jobpage',
						'route'=>'default',
						'pages' =>array(
							array(
								'module' => 'privatepanel',
								'controller' => 'job',
								'action' => 'addjobpage',
							),
							array(
								'module' => 'privatepanel',
								'controller' => 'job',
								'action' => 'editjobpage',
							),
						
						),
  					),
					array(
						'label'=>'Job Main Page Content ',
						'icon' =>'fa fa-file-text',
						'module' => 'privatepanel',
						'controller' => 'job',
 						'action' => 'editprojectmainpage',
						'route'=>'default',
  					),
   				)
     		),
// end manage job

// manage blog
	
			array(
				'label' => 'Manage Blog',
				'icon' =>'fa fa-rss',
				'uri' => 'javascript:void(0)',
 				'pages' =>array(
					array(
						'label'=>'Blog ',
						'icon' =>'fa fa-rss',
						'module' => 'privatepanel',
						'controller' => 'blog',
 						'action' => 'index',
						'route'=>'default',
						'pages' =>array(
							array(
								'module' => 'privatepanel',
								'controller' => 'blog',
								'action' => 'addblog',
							),
							array(
								'module' => 'privatepanel',
								'controller' => 'blog',
								'action' => 'editblog',
							),
						),

  					),
					array(
						'label'=>'Blog Category ',
						'icon' =>'fa fa-tags',
						'module' => 'privatepanel',
						'controller' => 'blog',
 						'action' => 'blogcat',
						'route'=>'default',
						'pages' =>array(
							array(
								'module' => 'privatepanel',
								'controller' => 'blog',
								'action' => 'addblogcat',
							),
							array(
								'module' => 'privatepanel',
								'controller' => 'blog',
								'action' => 'addblogcat',
							),
						),

  					),
   				)
     		),
			
// end manage credit package	]

			
			
			/* 
				(END) User Management
			 */
 			
		);
		
 		 
		
		 	 
		 
		 return $pages;
	}

}

?>