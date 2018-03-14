<?php 
global $_allowed_resources ;

	$_allowed_resources = array(
	
 		'privatepanel'=>array(
			'index'=>array(
				"login",
				"logout"
			),
		),
		
		'default'=>array(
			"error",
			"social",
			"index",
			"search",
			"static",
			"user",
			"blogs",
			//"payment",
			/*"commingsoon",*/
			"job"=>array("viewjob","paypalnotification")
 		)
	);

	$_blocked_resources = array(
 		'admin'=>array(
		),
		'privatepanel'=>array(
		),
		'service_provider'=>array(
			"project"=>array("addnewproject","editproject","quoterequest","receivedquote","viewproject"),
 		),
		'client'=>array(
			"project"=>array("jobrequest","quotemessage","sendquote"),
 		)
	);


