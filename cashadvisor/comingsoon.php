<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?
$link=urlencode("https://casaadvisor.com/comingsoon.php");
$name="Casa Advisor";
$desc="Casa Advisor";
$image = "https://casaadvisor.com/comingsoon/image/coming_logo.png";
?>
<meta property="og:url" content="<?=$link?>" />
<meta property="og:title" content="<?=$name?>" />
<meta property="og:description" content="<?=$desc?>" />
<meta property="og:image" content="<?=$image?>" />
<meta property="og:image:width" content="300">
<meta property="og:image:height" content="330">
<meta itemprop="name" content="<?=$name?>" />
<meta itemprop="description" content="<?=$desc?>" />
<meta property="fb:app_id" content="1637930056519294" />
<?
$share=array();
$share['link']=$link;
$share['name']=$name;
$share['redirect_url']='https://casaadvisor.com';
$share['caption']=$name;
$share['desc']=$desc;
?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>CasaAdvisor</title>
<link href="https://fonts.googleapis.com/css?family=PT+Sans" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="comingsoon/css/bootstrap.css" />
<link rel="stylesheet" type="text/css" href="comingsoon/css/font-awesome.min.css" />
<link rel="stylesheet" type="text/css" href="comingsoon/css/commingsoon.css" /> 
</head>
<body>
<?php 
  	        $days_after = date("Y-m-d", strtotime("+4 month", strtotime(date("Y-m-d"))));
			$target_arr=explode("-",$days_after);
			
			$name ="Casa Advisor";
			
			$site_path=dirname($_SERVER['PHP_SELF']);
			
			$site_path="https://casaadvisor.com/";
			$share_url = "https://".$site_path;
			
			
?>
<!--Comming soon Section-->
<section class="discover-section">
   			 <div class="containerfluid">
             	<div class="coming_bg">
                	<div class="coming_wlcm_bg">
                    	<div class="text-center wlcm_div">
                    		<h3 class="wlcm_h">Welcome To </h3> <img src="comingsoon/image/coming_logo.png" />
                         </div>
                        <p class="our_site">Our website is under construction, we are working very hard to give you the best experience with this one.</p>
                      <div class="time_show">  
                            <div class="display_b left_border text-center">
                            		<div id="dday"> <div class="day_col"></div></div><p>Days</p>
                            </div>
                            <div class="display_b left_border text-center">
                           		 <div id="dhour"><div class="h_col"></div></div><p>Hours</p>
                            </div>
                            <div class=" display_b left_border text-center">
                           		 <div id="dmin"><div class="m_col"></div></div><p>Minutes</p>
                            </div>
                            <div class=" display_b text-center">
                           		 <div id="dsec"><div class="m_col"></div></div><p>Seconds</p>
                            </div>
                     </div>
                        
                      <div class="share_email_div">
                            <p class="stay_close">Stay close. We'll bring it back as fast we can. Subscribe to our list and we'll notice you!</p>
                          
					 <?php if($_SESSION["message"]) {?>
                            <div class="success-msg col-md-12 text-center">Thanks for subscribing to our mailing list.</div>
                             <?php unset($_SESSION["message"]);  } else {?>
                             
                                <form method="post"  id="validate" role="form" name="search_product" novalidate="novalidate" class="profile_form" enctype="multipart/form-data" 
                                action="<?=$site_path?>/index/commingsoon/">
                                	<div class="share_input_div"><input class="share_input required email" name="newsletter_email" placeholder="Enter your email address" type="text" /></div>
                                	<div class="share_img_div"><button  class="share_btn" type="submit"></button></div>
                               </form> 
                               
                            <? } ?>    
                      </div>
                      
                           
                     
                    <div class="col-md-12 col-xs-12 col-sm-12" align="center">
                           
                            <a onclick='globalsharing(<?php echo json_encode($share)?>,1);' class="social-icon" target="_blank"><img src="comingsoon/image/facebook.png" class="img-responsive imgclass"></a>

                           
                            <a onclick='window.open("https://twitter.com/intent/tweet?url=http://192.168.0.99/CasaAdvisor/comingsoon.php","sharer","toolbar=0,status=0,width=580,height=325","sharer","toolbar=0,status=0,width=580,height=325")' class="social-icon" target="_blank"><img src="comingsoon/image/twitter.png" class="img-responsive imgclass"></a>
                            
<?php /*?>                            <a  href="https://www.instagram.com/casaadvisor/?ref=badge" class="social-icon" target="_blank"><img src="comingsoon/image/instagram.png" class="img-responsive imgclass"></a>
<?php */?>                            

                            <a  href="https://www.instagram.com/" class="social-icon" target="_blank"><img src="comingsoon/image/instagram.png" class="img-responsive imgclass"></a>

                             <a onclick='window.open("https://plus.google.com/share?url=http://192.168.0.99/CasaAdvisor/comingsoon.php","sharer","toolbar=0,status=0,width=580,height=325","sharer","toolbar=0,status=0,width=580,height=325")' class="social-icon" target="_blank"><img src="comingsoon/image/googleplus.png" class="img-responsive imgclass"></a> 
                             
                    </div>
                    
                    </div>
                </div>
            </div>
</section>

<script src="comingsoon/js/jquery-1.11.0.min.js"></script>
<script src="comingsoon/js/bootstrap.min.js"></script>
<script src="comingsoon/js/countdown.js"></script>
<script src="comingsoon/js/jquery.validate.min.js"></script>

<script src="comingsoon/js/general.js"></script>
</body>
</html>
<script type="text/javascript">
 var goal_timer;
 theyear='<?=$target_arr[0]?>';themonth='<?=$target_arr[1]?>';theday='<?=$target_arr[2]?>';thehour='00';theminute='000'; 
						
	goal_timer=setInterval(function() {
		countdown();
	}, 1000);
	
	
	
function globalsharing(share,type)
{  //alert('hello');
  switch(type)
   {
   case 1:window.open("https://www.facebook.com/dialog/feed?_path=feed&app_id=1637930056519294&redirect_uri="+share['redirect_url']+"&display=popup&link="+share['link']+"&name="+share['caption']+"&description="+share['desc'],"sharer","toolbar=0,status=0,width=580,height=325");
   break;
   case 2:window.open("https://twitter.com/intent/tweet?screen_name="+share['caption']+"&text="+share['caption']+"&url="+share['link'],"sharer","toolbar=0,status=0,width=580,height=325");
   break;
   case 3:window.open("https://plus.google.com/share?title="+share['caption']+"&url="+share['link']+"","sharer","toolbar=0,status=0,width=580,height=325");
   break;
   case 4:
   window.open("https://www.linkedin.com/shareArticle?mini=true&url="+share['link']+"&title="+share['caption']+"&summary="+share['desc'],"sharer","toolbar=0,status=0,width=580,height=325");
   break;
   }
}
</script>				
				
