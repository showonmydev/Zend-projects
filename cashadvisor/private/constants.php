<?php
	defined('ROOT_PATH') || define('ROOT_PATH', realpath(dirname(__FILE__) . ''));
		//echo ROOT_PATH;die;
	
 	define("SITE_NAME", "CasaAdvisor");
 	define("NAME_OF_SITE", "CasaAdvisor");
	
	
	define("ADMIN_AUTH_NAMESPACE", "ADMIN_AUTH");
	define("DEFAULT_AUTH_NAMESPACE", "DEFAULT_AUTH");
         //BrainTree Configuration
          define("BRAINTREE_MODE", "production");
          define("BRAINTREE_MERCHANT_ID", "qkbk7syyk5vr6sy8");
          define("BRAINTREE_PUBLIC_KEY", "v82hsrkhdk89bkgp");
          define("BRAINTREE_PRIVATE_KEY", "887c8fb5d20e5e6f690f31f840173f9b");
	
    define("SITE_BASE_URL", dirname($_SERVER['PHP_SELF']));
	
	define("SITE_HOST_URL", "http://" . $_SERVER['HTTP_HOST']);
	
	
	if(SITE_HOST_URL=='http://192.168.0.99'){
	define("SITE_HTTP_URL", "http://" . $_SERVER['HTTP_HOST'].SITE_BASE_URL);//
	define("APPLICATION_URL", "http://" . $_SERVER['HTTP_HOST'] . SITE_BASE_URL);
	}else{
	define("SITE_HTTP_URL", "https://" . $_SERVER['HTTP_HOST']);
	define("APPLICATION_URL", "https://" . $_SERVER['HTTP_HOST']);	
		}
	
		// echo SITE_BASE_URL;die;

	//define("APPLICATION_URL", "https://" . $_SERVER['HTTP_HOST'] . SITE_BASE_URL); /*on casa define("APPLICATION_URL", "http://" . $_SERVER['HTTP_HOST'] . SITE_BASE_URL);*/ 
	define("ADMIN_APPLICATION_URL", SITE_HTTP_URL . "/privatepanel");
	
		define('PRICE_SYMBOL','$');
		

	define("FRONT_CSS_PATH",SITE_HTTP_URL.'/assets/front/css');
	define("FRONT_JS_PATH",SITE_HTTP_URL.'/public/front_js');
	define("FRONT_IMAGES_PATH",SITE_HTTP_URL.'/assets/front/img');
	
	
	
	define('ADMIN_CSS_PATH', SITE_HTTP_URL.'/assets/admin/css');
	define('ADMIN_JS_PATH', SITE_HTTP_URL.'/assets/admin/js');
	
	define('ADMIN_IMAGES_PATH', SITE_HTTP_URL.'/assets/admin/img');
 	define('ADMIN_ASSETS_PATH', SITE_HTTP_URL.'/public/plugins');
	
	
	
	
	
	
	
	
	define('ADMIN_PROFILE', '/resources/admin profile images');
	
	define('PROPERTY_IMAGES', '/resources/property images');
	
	
	define('GALLERY_IMAGES', '/resources/gallery images');
	
	define('TEAM_IMAGES', '/resources/team members');

	
	define("IMAGE_VALID_EXTENTIONS","jpg,JPG,png,PNG,jpeg,JPEG");
	define("IMAGE_VALID_SIZE","5MB");

	
	
	
	define("IMG_URL",ROOT_PATH."/assets/img/");
	define("HTTP_IMG_URL",APPLICATION_URL."/assets/img/");
	
	
	
	/* New Theme Constatns */
	define('HTTP_IMG_PATH', SITE_HTTP_URL.'/public/img');
 
 	define('HTTP_PROFILE_IMAGES_PATH', SITE_HTTP_URL.'/public/resources/profile_images');
 	define('PROFILE_IMAGES_PATH', ROOT_PATH.'/public/resources/profile_images');
	
	define('HTTP_SERVICE_IMAGES_PATH', SITE_HTTP_URL.'/public/resources/service_images');
 	define('SERVICE_IMAGES_PATH', ROOT_PATH.'/public/resources/service_images');
	
	define('HTTP_BLOG_IMAGES_PATH', SITE_HTTP_URL.'/public/resources/blog_images');
 	define('BLOG_IMAGES_PATH', ROOT_PATH.'/public/resources/blog_images');
	
	define('HTTP_TEAM_MEMBER_IMAGES_PATH', SITE_HTTP_URL.'/public/resources/about_team_member');
 	define('TEAM_MEMBER_IMAGES_PATH', ROOT_PATH.'/public/resources/about_team_member');


	
	define('HTTP_JOB_PAGE_ICON_IMAGES_PATH', SITE_HTTP_URL.'/public/resources/job_icon');
 	define('JOB_PAGE_ICON_IMAGES_PATH', ROOT_PATH.'/public/resources/job_icon');

	
 	define('CLIENT_JOB_IMAGES_PATH', ROOT_PATH.'/public/resources/job_images');
	define('HTTP_CLIENT_JOB_IMAGES_PATH', SITE_HTTP_URL.'/public/resources/job_images');

 	//define('USER_JOB_IMAGE_PATH', ROOT_PATH.'/public/resources/uploads/dummy/thumbnail');
	//define('USER_ALL_JOB_IMAGES_PATH', ROOT_PATH.'/public/resources/uploads/dummy');

	 
	 
	define('HTTP_MEDIA_IMAGES_PATH', SITE_HTTP_URL.'/public/resources/media_images');
	define('MEDIA_IMAGES_PATH', ROOT_PATH.'/public/resources/media_images');
	
	define('HTTP_SLIDER_IMAGES_PATH', SITE_HTTP_URL.'/public/resources/slider_images');
	define('SLIDER_IMAGES_PATH', ROOT_PATH.'/public/resources/slider_images');
	
	define('HTTP_SITE_IMAGES', SITE_HTTP_URL.'/public/site_images');
	define('SITE_IMAGES', ROOT_PATH.'/public/site_images');
	
	define('HTTP_LOGO_IMAGES_PATH', SITE_HTTP_URL.'/public/resources/company_logo');
 	define('LOGO_IMAGES_PATH', ROOT_PATH.'/public/resources/company_logo');
	
	define('HTTP_CV_IMAGES_PATH', SITE_HTTP_URL.'/public/resources/cv_files');
 	define('CV_IMAGES_PATH', ROOT_PATH.'/public/resources/cv_files');
	

 
 	define('HTTP_SITEIMG_PATH',SITE_HTTP_URL.'/public/site_images');
	//global $hearabout;
	$hearabout=array(
			'1' => 'Google',
			'2' => 'Gmail',
			'3' => 'Facebook',
			'4' => 'Newspaper',
			'5' => 'Friend',
			'6' => 'Other',
		);
	
	global $a, $nwords;
	$a= array(
	'1'=>	'Carpet Installation',
	'2'=>	'Carpet Removal', 
	'3'=>	'Carpet Repair or Partial Replacement ',
	'4'=>	'Concrete Flooring Installation',
	'5'=>	'Epoxy Floor Coating', 
	'6'=>	'Flooring', 
	'7'=>	'Hardwood Floor Installation', 
	'8'=>	'Hardwood Floor Refinishing', 
	'9'=>	'Hardwood Floor Repair or Partial Replacement',
	'10'=>	'Stone or Tile Flooring Installation', 
	'11'=>	'Stone or Tile Flooring Repair or Partial Replacement',
	'12'=>	'Vinyl or Linoleum Installation', 
	'13'=>	'Vinyl or Linoleum Repair or Partial Replacement',
	'14'=>	'Water Dock Services' );
	
	
 $nwords = array( "zero", "one", "two", "three", "four", "five", "six", "seven",
                   "eight", "nine", "ten", "eleven", "twelve", "thirteen",
                   "fourteen", "fifteen", "sixteen", "seventeen", "eighteen",
                   "nineteen", "twenty", 30 => "thirty", 40 => "forty",
                   50 => "fifty", 60 => "sixty", 70 => "seventy", 80 => "eighty",
                   90 => "ninety" );
				   
// array for insert services into DB from query				   
	global $service_list;
	$service_list =  array(
		"Social Media Marketing" ,
			"Marketing" ,
			"Direct Mail Marketing",
			"Marketing Strategy Consulting", 
			"Digital Marketing",
			"Email Marketing", 
			"Search Engine Marketing" ,
			"Marketing Training ",
			"Text Message Marketing",
			"Telemarketing and Telesales Services",
			"Web Content Writing",
			"Blog Writing",
			"Search Engine Optimization"
	);

	$job_deadline =array(
				'1' => "I'm flexible",
				'2' => 'In the next few days ',
				'3' => 'As soon as possible ',
				'4' => "Other (I'd need to add date)",
			);
			
