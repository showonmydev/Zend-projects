<?php
function sendsms($phone_number,$msg) 
{
    $no=$phone_number;
	$siteconfig=Zend_Registry ::get("site_config");
    $sid=$siteconfig['twilio_sid'];
	$token=$siteconfig['twilio_token'];
	$phonenumber = $siteconfig['twilio_number'];
	
	
	 $version = "2010-04-01"; // Twilio REST API version
	// Set our Account SID and AuthToken
		$client = new Services_Twilio($sid, $token, $version); //initialise the Twilio client
		try
		{
			$message = $client->account->messages->create(array(
				"From" => $phonenumber,
				"To" => $phone_number,
				"Body" => $msg,
			));
			// Display a confirmation message on the screen
		}
		catch (Exception $e) 
		{
            echo 'Error: ' . $e->getMessage();
        }
		
}
 function xml2array ( $result, $out = array () ){
		foreach ( (array) $result as $index => $node )
			$out[$index] = ( is_object ( $node ) ) ? xml2array ( $node ) : $node;
		return $out;
	}
	
function siteIdEncode($id,$type){
	
	return base64_encode($id.'_'.$type);
}

function siteIdDecode($id,$type){
		
	if($type=="creditcard"){
		return str_replace('_'.$type,"",base64_decode($id));		
	}else{
		return (int)str_replace('_'.$type,"",base64_decode($id));		
	}
}
	
function format_category_name($name){
	
	$name = strtolower($name);
	
	$name = str_replace(" ","-",$name);
	
	return $name ;	
}
function Memory_Usage($decimals = 2)
{
    $result = 0;

    if (function_exists('memory_get_usage'))
    {
        $result = memory_get_usage() / 1024;
    }

    else
    {
        if (function_exists('exec'))
        {
            $output = array();

            if (substr(strtoupper(PHP_OS), 0, 3) == 'WIN')
            {
                exec('tasklist /FI "PID eq ' . getmypid() . '" /FO LIST', $output);

                $result = preg_replace('/[\D]/', '', $output[5]);
            }

            else
            {
                exec('ps -eo%mem,rss,pid | grep ' . getmypid(), $output);

                $output = explode('  ', $output[0]);

                $result = $output[1];
            }
        }
    }

    return number_format(intval($result) / 1024, $decimals, '.', '');
}

function format_size($size) {
      $sizes = array(" Bytes", " KB", " MB", " GB", " TB", " PB", " EB", " ZB", " YB");
      if ($size == 0) { return('n/a'); } else {
      return (round($size/pow(1024, ($i = floor(log($size, 1024)))), 2) . $sizes[$i]); }
}

function aftercomission($fee,$percent)
{
	$comission_amount=($fee*$percent)/100;
	$after_comission=$fee-$comission_amount;
	return $after_comission;
}

function getLnt($zip){
$url = "http://maps.googleapis.com/maps/api/geocode/json?address=
".urlencode($zip)."&sensor=false";
$result_string = file_get_contents($url);
$result = json_decode($result_string, true);
$result1[]=$result['results'][0];
$result2[]=$result1[0]['geometry'];
$result3[]=$result2[0]['location'];
return $result3[0];
}

function change_subscription_status( $data_post, $action ) {
 
 $siteconfig=Zend_Registry ::get("site_config");
  $profile_id=$data_post['subscription_profile_id'];
    $api_request = 'USER=' . urlencode($siteconfig['site_paypal_username'])
                .  '&PWD=' . urlencode($siteconfig['site_paypal_password'])
                .  '&SIGNATURE=' . urlencode($siteconfig['site_paypal_signature'])
                .  '&VERSION=76.0'
                .  '&METHOD=ManageRecurringPaymentsProfileStatus'
                .  '&PROFILEID=' . urlencode( $profile_id )
                .  '&ACTION=' . urlencode( $action )
                .  '&NOTE=' . urlencode( 'Profile cancelled at store' );
 
    $ch = curl_init();
    curl_setopt( $ch, CURLOPT_URL, 'https://api-3t.sandbox.paypal.com/nvp' ); // For live transactions, change to 'https://api-3t.paypal.com/nvp'
    curl_setopt( $ch, CURLOPT_VERBOSE, 1 );
 
    // Uncomment these to turn off server and peer verification
    // curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, FALSE );
    // curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, FALSE );
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
    curl_setopt( $ch, CURLOPT_POST, 1 );
 
    // Set the API parameters for this transaction
    curl_setopt( $ch, CURLOPT_POSTFIELDS, $api_request );
 
    // Request response from PayPal
    $response = curl_exec( $ch );
 
    // If no response was received from PayPal there is no point parsing the response
    if( ! $response )
       // die( 'Calling PayPal to change_subscription_status failed: ' . curl_error( $ch ) . '(' . curl_errno( $ch ) . ')' );
    array();
 
    curl_close( $ch );
 
    // An associative array is more usable than a parameter string
    parse_str( $response, $parsed_response );
 
    return $parsed_response;
}
/* Match Passwordss */
function match_old_password($value){
	$loggedUser = Zend_Session::namespaceGet(ADMIN_AUTH_NAMESPACE);
 	if(isset($loggedUser['storage']) and !empty($loggedUser['storage'])){
  		return $loggedUser['storage']->user_password === md5($value);
	};
	return false ;
}
function match_old_password_front($value){
	$logged_identity = Zend_Auth::getInstance()->getInstance();
	if($logged_identity->hasIdentity()){
		$logged_identity = $logged_identity->getIdentity();
		return $logged_identity->user_password === md5($value);
	}
	return false ;
}


function genratePassword($string){
	return $string.rand(1,999);
}
function formatImageName($images_name){
	
	return str_replace(
		array(
			"/"," ",
		),
		array(
			"-","-"
		),
		$images_name
		
	);
	
}

function getUserImage($user_image , $full_url = false){

	if($user_image!="" and file_exists(PROFILE_IMAGES_PATH."/".$user_image)){
		$image_url =$user_image;
	}else{
		$image_url = "default.png";
	}
	
	if(!$full_url){
		return $image_url; 
	}
			
	switch($full_url){
		
		case '60': return $image_url = HTTP_PROFILE_IMAGES_PATH."/60/$image_url"; break;
		case '160': return  $image_url = HTTP_PROFILE_IMAGES_PATH."/160/$image_url"; break;
		case 'thumb': return  $image_url = HTTP_PROFILE_IMAGES_PATH."/thumb/$image_url"; break;
		default: return  $image_url = HTTP_PROFILE_IMAGES_PATH."/thumb/$image_url"; break;
		
		
	}	
}






function loadClass($classpath){
	$cpath = str_replace("_", "/", $classpath);
	include( $classpath);
}


/* FourSquare API for Get Places  */

function FourSquare($lat ,$lng){
		
	$categories = array(
			"Casino"=>"4bf58dd8d48988d17c941735",
			"Multiplex"=>"4bf58dd8d48988d180941735",
			"Zoo"=>"4bf58dd8d48988d17b941735",
			"Burger Joint"=>"4bf58dd8d48988d16c941735",
			"Café"=>"4bf58dd8d48988d16d941735",
			"Coffee Shop"=>"4bf58dd8d48988d1e0931735",
			"Fast Food Restaurant"=>"4bf58dd8d48988d16e941735",
			"Restaurant"=>"4bf58dd8d48988d1c4941735",
			"Library"=>"4bf58dd8d48988d12f941735",
			"Hospital"=>"4bf58dd8d48988d196941735",
			"Bank"=>"4bf58dd8d48988d10a951735",
			"Department Store"=>"4bf58dd8d48988d1f6941735",
			"Wine Shop"=>"4bf58dd8d48988d119951735",
			"Gym"=>"4bf58dd8d48988d176941735",
			"Mall"=>"4bf58dd8d48988d1fd941735",
			"Bus Line"=>"4bf58dd8d48988d12b951735",
			"Train"=>"4bf58dd8d48988d12a951735",
		);
  	
	$catIds = implode(",",array_values($categories));
	
	$url = "https://api.foursquare.com/v2/venues/search?ll=$lat,$lng&categoryId=$catIds&oauth_token=KQXALOQX0PDLIDHH1PANCROEKH2MRPMVEGQFXN1P0D2XTHHC&v=20140101&radius=500&limit=50";
	
	$response = @file_get_contents($url); 
	
	$json = json_decode($response);
	 
	$other_stuffs = array();
	$i=0;
	foreach ($json->response->venues as $result){

		$temp_array = array();
		$place = $result ;
		$temp_array['name']=$place->name;
		$temp_array['latitude']=$place->location->lat;
		$temp_array['longitude']=$place->location->lng;
		
		if(isset($place->location->city))
			$temp_array['city']=$place->location->city;
		else
			$temp_array['city']="";
		
		$temp_array['category']= $place->categories[0]->shortName;
		
		switch($temp_array['category']){
			case "American": $icon_name="default_color.png"; break;
			case "Burgers": $icon_name="burger_color.png"; break;
			case "Bus": $icon_name="busstation_color.png"; break;
			case "Café": $icon_name="coffeeshop_color.png"; break;
			case "Cineplex": $icon_name="movietheater_color.png"; break;
			case "Coffee Shop": $icon_name="cafe_color.png"; break;
			case "Department Store": $icon_name="departmentstore_color.png"; break;
			case "Gastropub": $icon_name="casino_color.png"; break;
			case "Grocery Store": $icon_name="departmentstore_color.png"; break;
			case "Gym": $icon_name="gym_color.png"; break;
			case "Gym / Fitness": $icon_name="gym_color.png"; break;
			case "Hotel": $icon_name="financial_color.png"; break;
			case "Mall": $icon_name="mall_color.png"; break;
			case "Medical School": $icon_name="medical_color.png"; break;
			case "Tea Room": $icon_name="cafe_color.png"; break;
			default : $icon_name="waterpark_color.png"; break ;
		}
		
		$temp_array['icon']= $icon_name ;// "maps/default.png";//$icon_name; //$place->categories[0]->icon->prefix."32".$place->categories[0]->icon->suffix;
		$other_stuffs[] = $temp_array;
		$i++; 
	}    
		
	
	return $other_stuffs ; 
		
 		
}







function isImage($name,$type){
	
	switch($type){
		case "admin profile" :
  			if(!empty($name) and file_exists(ROOT_PATH."/".ADMIN_PROFILE."/".$name)){
				return $name;
			}else{
				return "avatar.png";
			}
			
		break ;
		case "" : break ;
		case "" : break ;
		case "" : break ;
		
	}
	
	
}

function blogCommentCount($blogId){
	$db = Zend_Registry::get('db');
	$totalComment=$db->select()->from('blog_comments',array('count(blog_comment_id)as totalcomment'))->join('users','user_id=blog_comment_user_id')->where('blog_comment_blog_id='.$blogId.' and blog_comment_status="1" ')->query()->fetch();
	return $totalComment['totalcomment'];
	
}

function MY_setLayout($layout){
	Zend_Layout::getMvcInstance()->setLayout($layout);
}


/* Return Difference Between Two Dates */

function getDiffrence($date1 ,$date2 , $in ='d'){
	
 	
  	$diff = abs(strtotime($date2) - strtotime($date1));
 	$years = floor($diff / (365*60*60*24));
	$months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
	$days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
	$hours = floor($diff / 60 /  60);
	$minites = floor($diff / 60 );
	
	switch($in){
  		case 'd':
 			$op_days = $years*365 + $months*30 + $days; 
			return $op_days ;
 			/* Return No of Days Between Two Days*/
		break;
		
		case 'h':
 			$hours = $hours % 24;
			
			if($minites!=0){
				return  $hours +1;
			}
			return $hours ;
		/* Return No of Hours After Day */
		break;
  	
	}



 	
	if($years){
		return $years." years " ;
	}
	elseif($months){
		return $months." months "; 
	}
	elseif($days){
		return $days." days ";
	}
	elseif($hours){
		return $hours." hours ";
	}
	else{
		return $minites." minites ";
	} 
	
	//printf("%d years, %d months, %d days\n", $years, $months, $days);
	
	//2 days
	
}




##----------------------##
## ** THEJAMSTOP **
##   change_to_language
##-----------------------##

function change_to_language($str){
	return $str; 
}

##----------------------##
## ** THEJAMSTOP **
##   saveThumbnail
##-----------------------##
function saveThumbnail( $file , $width , $height , $outputfile , $propotional=false , $corp=false , $priority='H' ,$oversize=false ){

	$use_linux_commands = false ;
	$oversize=false ;   
	

	/* For File  */
	$output = 'file' ;   
	$outputfile = ROOT_PATH."/".$outputfile;
    $info = @getimagesize($file);
    $image = '';

    $final_width = 0;
    $final_height = 0;
    list($width_old, $height_old) = $info;
 
    if (@$proportional) {
      if ($width == 0) $factor = $height/$height_old;
      elseif ($height == 0) $factor = $width/$width_old;
      else {
	  if($priority=='H'){$factor = max ( $width / $width_old, $height / $height_old);   }
	  else{ $factor = min ( $width / $width_old, $height / $height_old);   }}
 		
		
		if($oversize){
			$ratio=$width_old/ $height_old ;
			if($priority=='H'){
			
				
				
				
				 $final_height = round ($height );
				 $final_width =  round ($height *$ratio);
				 	
				 
			}else{
				 $final_width = round ($width);
				 $final_height = round ($width * $factor);	
			}
			 	
		
		}elseif($width_old > $width || $height_old > $height) { 
 			 $final_width = round ($width_old * $factor);
      		 $final_height = round ($height_old * $factor);		
		
		} 	else {
		$final_width = $width_old; 
		$final_height = $height_old;
		}
		
		
    
 
    }
    else {
	
		if($oversize){
		
			 $final_width =  $width  ;
      		 $final_height =  $height  ;		
		
		}else{
	
			  $final_width = ( $width <= 0 ) ? $width_old : $width;
			  $final_height = ( $height <= 0 ) ? $height_old : $height;
	  	}
    }


     switch ( $info[2] ) {
      case 1://case IMAGETYPE_GIF:
        $image = imagecreatefromgif($file);
      break;
      case 2://case IMAGETYPE_JPEG:
        $image = imagecreatefromjpeg($file);
      break;
      case 3://case IMAGETYPE_PNG:
        $image = imagecreatefrompng($file);
      break;
      default:
        return false;
    }
  
  
    $image_resized = imagecreatetruecolor( $final_width, $final_height );
 
    if ( ($info[2] == 1) || ($info[2] == 3) ) {  //if ( ($info[2] == IMAGETYPE_GIF) || ($info[2] == IMAGETYPE_PNG) ) {
     
	  $trnprt_indx = imagecolortransparent($image);

      // If we have a specific transparent color
      if ($trnprt_indx >= 0) {
 
        // Get the original image's transparent color's RGB values
        $trnprt_color    = imagecolorsforindex($image, $trnprt_indx);
 
        // Allocate the same color in the new image resource
        $trnprt_indx    = imagecolorallocate($image_resized, $trnprt_color['red'], $trnprt_color['green'], $trnprt_color['blue']);
 
        // Completely fill the background of the new image with allocated color.
        imagefill($image_resized, 0, 0, $trnprt_indx);
 
        // Set the background color for new image to transparent
        imagecolortransparent($image_resized, $trnprt_indx);
 
 
      } 
      // Always make a transparent background color for PNGs that don't have one allocated already
      elseif ($info[2] == 3) { //elseif ($info[2] == IMAGETYPE_PNG) {

        // Turn off transparency blending (temporarily)
        imagealphablending($image_resized, false);
 
        // Create a new transparent color for image
        $color = imagecolorallocatealpha($image_resized, 0, 0, 0, 127);
 
        // Completely fill the background of the new image with allocated color.
        imagefill($image_resized, 0, 0, $color);
 
        // Restore transparency blending
        imagesavealpha($image_resized, true);
      }
    }
 
    imagecopyresampled($image_resized, $image, 0, 0, 0, 0, $final_width, $final_height, $width_old, $height_old);
 
  
 
  
    switch ( strtolower($output) ) {
      case 'browser':
        $mime = image_type_to_mime_type($info[2]);
        header("Content-type: $mime");
        $output = NULL;
      break;
      case 'file':
        $output = $outputfile;
      break;
      case 'return':
        return $image_resized;
      break;
      default:
      break;
    }
 
    switch ( $info[2] ) {
      case IMAGETYPE_GIF:
        imagegif($image_resized);
      break;
      case IMAGETYPE_JPEG:
        imagejpeg($image_resized, $output);
      break;
      case IMAGETYPE_PNG:
        imagepng($image_resized, $output);
      break;
      default:
        return false;
    }
}

##----------------------##
## ** THEJAMSTOP **
##   getAdminData
##-----------------------##
function getAdminData(){
 	$db = Zend_Registry::get('db');
 	return $db->select()->from('admin')->where('admin_id=1')->query()->fetch();
}


function isMobile($obj=false){
	
	$user_agent = new Zend_Http_UserAgent();
  	$bool = "Zend_Http_UserAgent_Mobile"==get_class($user_agent->getDevice());
	if($obj){
		if(!$bool){
			$obj->redirect('');
		}
	}
	return $bool ;
}



function getMonthName($date , $short = false){
 	$monthNames  = array("January", "February", "March", "April", "May", "June",  "July", "August", "September", "October", "November", "December" );
	$name  = $monthNames[date("m", strtotime($date))-1] ;
	if($short)
		return substr($name,0,3);
	return $name ;
}


function getDayName($date , $short = false){
  	switch(date("D", strtotime($date))){
 		case 'Mon': return ucwords('monday');
		case 'Tue': return ucwords('tuesday');
		case 'Wed': return ucwords('wednesday');
		case 'Thu': return ucwords('thursday');
		case 'Fri': return ucwords('friday') ;
		case 'Sat': return ucwords('saturday');
		case 'Sun': return ucwords('sunday');
		
	}
	
	return ;
 }

function getDMYFormat($data , $saperater="."){
	
	$timestamp = strtotime($data); 
	
	$format ="d".$saperater."m".$saperater."Y" ;
	
	$new_date = date($format , $timestamp);
	
	return $new_date ;
	
}

function showPostTime($date){
 	// 2 years, 3 months and 2 days format
	
	$date1 = $date;
	$date2 = date('Y-m-d H:i:s') ;
 	$diff = abs(strtotime($date2) - strtotime($date1));
 	$years = floor($diff / (365*60*60*24));
	$months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
	$days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
	$hours = floor($diff / 60 /  60);
	$minites = floor($diff / 60 );

 	
	if($years){
		return $years." years " ;
	}
	elseif($months){
		return $months." months "; 
	}
	elseif($days){
		return $days." days ";
	}
	elseif($hours){
		return $hours." hours ";
	}
	else{
		return $minites." minutes ";
	} 
	
	//printf("%d years, %d months, %d days\n", $years, $months, $days);
	
	//2 days
	
}

function CommentPostedTime($date){
	
	$date1 = $date;
	$date2 = date('Y-m-d H:i:s') ;
 	$diff = abs(strtotime($date2) - strtotime($date1));
 	$years = floor($diff / (365*60*60*24));
	$months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
	$days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
	$hours = floor($diff / 60 /  60);
	$minites = floor($diff / 60 );

 	
	if($years){
		return $years." Year " ;
	}
	elseif($months){
		return $months." Month"; 
	}
	elseif($days){
		return $days." day ";
	}
	elseif($hours){
		return $hours." h ";
	}
	else{
		return $minites." m ";
	}

}



function setBlockDate($date){
	$monthNames  = array("January", "February", "March", "April", "May", "June",  "July", "August", "September", "October", "November", "December" );
	$name  = $monthNames[date("m", strtotime($date))-1] ;
	return 	date("d", strtotime($date)).' '.substr($name,0,3) ;
}

function getYear($date){
	return  date("Y", strtotime($date)) ;
}

function getCommentTime($date){
	return  date("D", strtotime($date)).' at '.date("g:i A", strtotime($date)) ;
}

function addNewPost($post_apv_id,$post_type){
 	$user  = isLogged(true);
	$user_id = $user->user_id ; 
 	$dataInsert = array();
	$dataInsert["post_user_id"] =$user_id;
	$dataInsert["post_apv_id"] =$post_apv_id;
 	$dataInsert["post_type"] =$post_type;
 	$db = Zend_Registry::get('db');
 	$db->insert('posts',$dataInsert);
}

function deletePost($post_apv_id,$post_type){
	$db = Zend_Registry::get('db');
	$condition = "post_apv_id = ".$post_apv_id." and post_type=".(int)$post_type."";
 	$db->delete('posts',$condition);
}

function countryName($countryId){
	$db = Zend_Registry::get('db');
	$countrydata=$db->query('select name from countries where country_id='.$countryId)->fetch();
 	return $countrydata['name'];
}



function isLogged($info=false)
{	
	$user = Zend_Auth::getInstance()->getIdentity();	 
	if($user){ 
		if($info) return $user  ;
		else return $user->user_id ;
	}else{
		return false;	
	}
}


function setHeaderBlock($rContent , $lContent="YOUR ACCOUNT"){
	$str = '<div class="second-container-heading"><div class="container"><div class="row-fluid"><h3>'.$lContent.'</h3>
			<h3 class="span3">'.$rContent.'</h3></div></div></div>';
	Zend_Registry::set('heading-block', $str); 

}

function getSeoUrl($str){
	$str=strtolower(trim($str)) ;	
	$str=str_ireplace(" ","-",$str) ;	
	return $str;
}



function addQueryString($qvar,$qvalue){
	$qa=explode("&",$_SERVER['QUERY_STRING'] ) ;
 	$query_string=array();	
	foreach($qa as $v){
		$vars=explode("=",$v) ;
		if($vars[0]!=$qvar && $vars[0]!=""){
			$query_string[]=@$vars[0]."=".@$vars[1];	
		}
		
	}
	$query_string[]=$qvar."=".$qvalue;	
	$qstr=implode("&",$query_string ) ;	
	return $qstr ;
	
}

function resetSeoUrl($str){
	 
	$str=str_ireplace("-"," ",$str) ;
	$str=str_ireplace(".html","",($str)) ; 	
	return ucwords($str);  
}
function setFlashErrorMessage($message){	 
	$registry = Zend_Registry::getInstance();
	$registry->set("flash_error",$message);
}

function getFileExtension($filename=''){	
	
	$ext=@array_pop(explode(".",$filename));
	return $ext ;
	
}
 function showDateOnly($dateTimeStr) 
{ 
    $array = explode(" ",$dateTimeStr); 
	return $array[0];
}

function formatDate($dateTimeStr){
	$dtobj=strtotime($dateTimeStr);
	$format=date("F j, Y",$dtobj) ;
	return $format ;
}
function formatDateTime($dateTimeStr){
	$dtobj=strtotime($dateTimeStr);
	$format=date("g:i a F j, Y ",$dtobj) ;
	return $format ;
}
function trimValues(&$value) 
{ 
    $value = trim($value); 
}

function shortString($str,$length=10,$addDots=false){
	$substr=substr($str,0,$length);
	
	if($addDots){return $substr.'...' ;}
	else{ return $substr ;}
}


function test_print($item, $key)
{
    echo "$key holds $item\n";
}


 function nicetime($date) { if(empty($date)) { return "No date provided"; }
	$periods = array("second", "minute", "hour", "day", "week", "month", "year", "decade");
	$lengths = array("60","60","24","7","4.35","12","10");
	$now = time();
	$unix_date = strtotime($date);
	if(empty($unix_date)) { return "Bad date"; }
	if($now > $unix_date) {
	$difference = $now - $unix_date;
	$tense = "ago";
	} else {
	$difference = $unix_date - $now;
	$tense = "from now";
	}
	for($j = 0; $difference >= $lengths[$j] && $j < count($lengths)-1; $j++) { $difference /= $lengths[$j]; }
	$difference = round($difference);
	if($difference != 1) { $periods[$j].= "s"; }
	return "$difference $periods[$j] {$tense}";
}



     function prn($var)
    {
        echo '<pre>';
        print_r($var);
        echo '</pre>';
    }

    function pr($var)
    {
        echo '<pre>';
        print_r($var);
        echo '</pre>';
    }

    function prd($var)
    {
        echo '<pre>';
        print_r($var);
        echo '</pre>';
        die;
    }

    function gcm($var)
    {
        if (is_object($var))
            $var = get_class($var);
        echo '<pre>';
        prn(get_class_methods($var));
        echo '</pre>';
    }

    function getActivationKey($string)
    {
        $key = md5($string);
        return $key;
    }

    function getClientBrowserName()
    {
        $u_agent = $_SERVER['HTTP_USER_AGENT'];
        $ub = '';
        if (preg_match('/MSIE/i', $u_agent))
        {
            $ub = "Internet Explorer";
        }
        elseif (preg_match('/Firefox/i', $u_agent))
        {
            $ub = "Mozilla Firefox";
        }
        elseif (preg_match('/Safari/i', $u_agent))
        {
            $ub = "Apple Safari";
        }
        elseif (preg_match('/Chrome/i', $u_agent))
        {
            $ub = "Google Chrome";
        }
        elseif (preg_match('/Flock/i', $u_agent))
        {
            $ub = "Flock";
        }
        elseif (preg_match('/Opera/i', $u_agent))
        {
            $ub = "Opera";
        }
        elseif (preg_match('/Netscape/i', $u_agent))
        {
            $ub = "Netscape";
        }

        return $ub;
    }

    function getClientOS()
    {
        $user_agent = $_SERVER['HTTP_USER_AGENT'];

        $os_platform = "Unknown OS Platform";

        $os_array = array (
            '/windows nt 6.2/i' => 'Windows 8',
            '/windows nt 6.1/i' => 'Windows 7',
            '/windows nt 6.0/i' => 'Windows Vista',
            '/windows nt 5.2/i' => 'Windows Server 2003/XP x64',
            '/windows nt 5.1/i' => 'Windows XP',
            '/windows xp/i' => 'Windows XP',
            '/windows nt 5.0/i' => 'Windows 2000',
            '/windows me/i' => 'Windows ME',
            '/win98/i' => 'Windows 98',
            '/win95/i' => 'Windows 95',
            '/win16/i' => 'Windows 3.11',
            '/macintosh|mac os x/i' => 'Mac OS X',
            '/mac_powerpc/i' => 'Mac OS 9',
            '/linux/i' => 'Linux',
            '/ubuntu/i' => 'Ubuntu',
            '/iphone/i' => 'iPhone',
            '/ipod/i' => 'iPod',
            '/ipad/i' => 'iPad',
            '/android/i' => 'Android',
            '/blackberry/i' => 'BlackBerry',
            '/webos/i' => 'Mobile'
        );

        foreach ($os_array as $regex => $value)
        {

            if (preg_match($regex, $user_agent))
            {
                $os_platform = $value;
            }
        }

        return $os_platform;
    }

    function loggedUser()
    {
        $x = new Zend_Auth_Storage_Session('Admin_Auth');
        $y = $x->read();
        //prd($y);
        if (empty($y))
            return 0;
        else
            return $y;
    }
	
	function loggedUserFront()
    {
    	$x = new Zend_Auth_Storage_Session('User_Auth');
    	$y = $x->read();
    	//prd($y);
    	if (empty($y))
    		return 0;
    	else
    		return (object)$y;
    }
	
	
  function checkIsUserLoggedIn($info=false)
    {	
        $user = Zend_Auth::getInstance()->getIdentity();	 
	  	if($user){ 
			if($info) return $user  ;
			else return $user->user_id ;
		}else{
			return false;	
		}
    }
	
	function get_url_contents($url){
 
        $crl = curl_init();
        $timeout = 5;
        curl_setopt ($crl, CURLOPT_URL,$url);
        curl_setopt ($crl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt ($crl, CURLOPT_CONNECTTIMEOUT, $timeout);
        $ret = curl_exec($crl);
        curl_close($crl);
        return $ret;
}

function int_to_words($x){

	   global $nwords;

	   if(!is_numeric($x)){
		  $w = '#';
	   }
	   else if(fmod($x, 1) != 0){
		  $w = '#';
	   }
	   else {
		  if($x < 0) {

			 $w = 'minus ';
			 $x = -$x;
		  } else{
			 $w = '';
		  }
		  // ... now $x is a non-negative integer.
		
		  if($x < 21){   // 0 to 20
			 $w .= $nwords[$x];
		  }
		  else if($x < 100) {   
		  // 21 to 99
			 $w .= $nwords[10 * floor($x/10)];
			 $r = fmod($x, 10);
			 if($r > 0)
				$w .= '-'. $nwords[$r];
		  } else if($x < 1000) {   // 100 to 999
			 $w .= $nwords[floor($x/100)] .' hundred';
			 $r = fmod($x, 100);
			 if($r > 0)
				$w .= ' and '. int_to_words($r);
		  } else if($x < 1000000) {   // 1000 to 999999
			 $w .= int_to_words(floor($x/1000)) .' thousand';
			 $r = fmod($x, 1000);
			 if($r > 0) {
				$w .= ' ';
				if($r < 100)
				   $w .= 'and ';
				$w .= int_to_words($r);
			 }
		  } else {    //  millions
			 $w .= int_to_words(floor($x/1000000)) .' million';
			 $r = fmod($x, 1000000);
			 if($r > 0) {
				$w .= ' ';
				if($r < 100)
				   $word .= 'and ';
				$w .= int_to_words($r);
			 }
		  }
	   }
	   return $w;
	}

function DeleteDirfileupload($path)
{
 if (is_dir($path.'/thumbnail')) {
 $image_source=$path.'/thumbnail';
 $dir12 = @ dir($image_source.'/');
 while (($file = $dir12->read()) !== false)
 {
 //echo $uploadFolder1.$file;die;
 
 @unlink($image_source.'/'.$file);  
 }
 
 rmdir($image_source);
 }
 
 if (is_dir($path)) {
 $image_source1=$path;
 $dir12 = @ dir($image_source1.'/');
 while (($file = $dir12->read()) !== false)
 {
 //echo $uploadFolder1.$file;die;
 
 @unlink($image_source1.'/'.$file);  
 }
 
 rmdir($image_source1);
 }
} 

 
/*function array_column(array $array, $columnKey, $indexKey = null)
{
	$result = array();
	foreach ($array as $subArray) {
		if (!is_array($subArray)) {
			continue;
		} elseif (is_null($indexKey) && array_key_exists($columnKey, $subArray)) {
			$result[] = $subArray[$columnKey];
		} elseif (array_key_exists($indexKey, $subArray)) {
			if (is_null($columnKey)) {
				$result[$subArray[$indexKey]] = $subArray;
			} elseif (array_key_exists($columnKey, $subArray)) {
				$result[$subArray[$indexKey]] = $subArray[$columnKey];
			}
		}
	}
	return $result;
}
*/function rating_display($num)
 { 
  $IMAGE_URL = HTTP_SITEIMG_PATH.'/';
  switch($num)
  {
   case 0: return '<img alt="" src="'.$IMAGE_URL.'star-grey.png"  style="float:left;margin-left:2px" />
       <img alt="" src="'.$IMAGE_URL.'star-grey.png"  style="float:left;margin-left:2px" />
       <img alt="" src="'.$IMAGE_URL.'star-grey.png"  style="float:left;margin-left:2px;" />
       <img alt="" src="'.$IMAGE_URL.'star-grey.png"  style="float:left;margin-left:2px" />  
       <img alt="" src="'.$IMAGE_URL.'star-grey.png"  style="float:left;margin-left:2px;" />';
     break;
   case 1: return '<img alt="" src="'.$IMAGE_URL.'star-blue.png"  style="float:left;margin-left:2px" />
       <img alt="" src="'.$IMAGE_URL.'star-grey.png"  style="float:left;margin-left:2px" />
       <img alt="" src="'.$IMAGE_URL.'star-grey.png"  style="float:left;margin-left:2px;" />
       <img alt="" src="'.$IMAGE_URL.'star-grey.png"  style="float:left;margin-left:2px" />  
       <img alt="" src="'.$IMAGE_URL.'star-grey.png"  style="float:left;margin-left:2px;" />';
     break;
   case 2: return '<img alt="" src="'.$IMAGE_URL.'star-blue.png" style="float:left;margin-left:2px" />
       <img alt="" src="'.$IMAGE_URL.'star-blue.png" style="float:left;margin-left:2px" />
       <img alt="" src="'.$IMAGE_URL.'star-grey.png" style="float:left;margin-left:2px" />
       <img alt="" src="'.$IMAGE_URL.'star-grey.png"  style="float:left;margin-left:2px" />  
       <img alt="" src="'.$IMAGE_URL.'star-grey.png"  style="float:left;margin-left:2px" />';
     break;
   case 3: return '<img alt="" src="'.$IMAGE_URL.'star-blue.png" style="float:left;margin-left:2px" />
       <img alt="" src="'.$IMAGE_URL.'star-blue.png" style="float:left;margin-left:2px" />
       <img alt="" src="'.$IMAGE_URL.'star-blue.png" style="float:left;margin-left:2px" />
       <img alt="" src="'.$IMAGE_URL.'star-grey.png"  style="float:left;margin-left:2px" />  
       <img alt="" src="'.$IMAGE_URL.'star-grey.png"  style="float:left;margin-left:2px" />';
     break;
   case 4: return '<img alt="" src="'.$IMAGE_URL.'star-blue.png" style="float:left;margin-left:2px" />
       <img alt="" src="'.$IMAGE_URL.'star-blue.png" style="float:left;margin-left:2px" />
       <img alt="" src="'.$IMAGE_URL.'star-blue.png" style="float:left;margin-left:2px" />
       <img alt="" src="'.$IMAGE_URL.'star-blue.png" style="float:left;margin-left:2px" />  
       <img alt="" src="'.$IMAGE_URL.'star-grey.png"  style="float:left;margin-left:2px" />';
     break;
   case 5: return '<img alt="" src="'.$IMAGE_URL.'star-blue.png" style="float:left;margin-left:2px" />
       <img alt="" src="'.$IMAGE_URL.'star-blue.png" style="float:left;margin-left:2px" />
       <img alt="" src="'.$IMAGE_URL.'star-blue.png" style="float:left;margin-left:2px" />
       <img alt="" src="'.$IMAGE_URL.'star-blue.png" style="float:left;margin-left:2px" />  
       <img alt="" src="'.$IMAGE_URL.'star-blue.png" style="float:left;margin-left:2px" />';
     break;
  }
 }
 
 
 if (! function_exists('array_column')) {
    function array_column(array $input, $columnKey, $indexKey = null) {
        $array = array();
        foreach ($input as $value) {
            if ( !array_key_exists($columnKey, $value)) {
                trigger_error("Key \"$columnKey\" does not exist in array");
                return false;
            }
            if (is_null($indexKey)) {
                $array[] = $value[$columnKey];
            }
            else {
                if ( !array_key_exists($indexKey, $value)) {
                    trigger_error("Key \"$indexKey\" does not exist in array");
                    return false;
                }
                if ( ! is_scalar($value[$indexKey])) {
                    trigger_error("Key \"$indexKey\" does not contain scalar value");
                    return false;
                }
                $array[$value[$indexKey]] = $value[$columnKey];
            }
        }
        return $array;
    }
}

function encodeBlogUrl($blogUrlName)
{
 $blogUrlName=str_replace(" ","-",$blogUrlName);
 $blogUrlName=str_replace(".","-",$blogUrlName);
 $blogUrlName=str_replace("'","-",$blogUrlName);
 $blogUrlName=str_replace('"',"-",$blogUrlName);
 $blogUrlName=str_replace("/","-",$blogUrlName);
 $blogUrlName=str_replace("+","-",$blogUrlName);
 $blogUrlName=urlencode($blogUrlName);
 return ($blogUrlName);
}


function compress($source, $destination, $quality) {

  $info = getimagesize($source);

  if ($info['mime'] == 'image/jpeg') 
   $image = imagecreatefromjpeg($source);

  elseif ($info['mime'] == 'image/gif') 
   $image = imagecreatefromgif($source);

  elseif ($info['mime'] == 'image/png') 
   $image = imagecreatefrompng($source);

  imagejpeg($image, $destination, $quality);

  return $destination;
}