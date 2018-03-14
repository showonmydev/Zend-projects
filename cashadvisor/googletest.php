<title>Merkao</title>
<link href="http://webdemo1.co.in/Merkao/public/front_css/bootstrap.css?1472726519" media="screen" rel="stylesheet" type="text/css" >
<link href="http://webdemo1.co.in/Merkao/public/plugins/Hover-master/css/hover.css" media="screen" rel="stylesheet" type="text/css" >
<link href="http://webdemo1.co.in/Merkao/public/front_css/style_custom.css" media="screen" rel="stylesheet" type="text/css" >
<link href="http://webdemo1.co.in/Merkao/public/front_css/custom.css?1472726519" media="screen" rel="stylesheet" type="text/css" >
<link href="http://webdemo1.co.in/Merkao/public/plugins/font-awesome/css/font-awesome.min.css" media="screen" rel="stylesheet" type="text/css" >
<link href="http://webdemo1.co.in/Merkao/public/front_css/errormsg-style.css" media="screen" rel="stylesheet" type="text/css" >
<link href="http://webdemo1.co.in/Merkao/public/front_css/animate.min.css" media="screen" rel="stylesheet" type="text/css" >
<link href="http://webdemo1.co.in/Merkao/public/plugins/mcustomscroolbar.css" media="screen" rel="stylesheet" type="text/css" >
<link href="http://webdemo1.co.in/Merkao/public/plugins/jQuery-Star-Rating/css/star-rating.css" media="screen" rel="stylesheet" type="text/css" >
<link href="http://webdemo1.co.in/Merkao/public/plugins/bxslider/bxslider.css" media="screen" rel="stylesheet" type="text/css" >
<link href="http://webdemo1.co.in/Merkao/public/plugins/lightslider/lightslider.css" media="screen" rel="stylesheet" type="text/css" >
<link href="http://webdemo1.co.in/Merkao/public/img/favicon.ico" rel="shortcut icon" >

<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!--[if lt IE 9]>
  <script src="/Merkao/public/front_js/html5shiv.js"></script>
  <script src="/Merkao/public/front_js/respond.min.js"></script>
<![endif]-->
	    
<script src="/Merkao/public/plugins/jquery-1.11.0.min.js"></script>

<script>var baseUrl = SITEURL = SITE_URL = '/Merkao';
		var backend ='Merkaoadmin';
		var controllerName='product';
		var actionName='detail';
		var facebook_appid='244307452629054';
		var Logged_user='0';
</script>	
</head>
<body>
	<style type="text/css">
.nicescroll-cursors{right:-3px !important;}
.nicescroll-rails {background-color:#fff}
.header_social_wrapper a:focus{text-decoration:none; outline:none}
</style>

   
   
<!-- Code provided by Google -->

 <script type="text/javascript">
$(document).ready(function(e) {
	
		
    $("#headerpage_").css("color","#FBA505");

		
});


$(document).ready(function(e) {
	set_langauge();
});

function set_langauge()
{	
	var another_language;
	 	 var selected_language='English';
	  
	 
	 		another_language="Spanish";
	 	 get_lng(selected_language,another_language);
	 
}
function get_lng(selected_language,another_language)
{var str="'";
//alert("selected_language"+selected_language);
//alert("another_language"+another_language);
 	  $(".lang_selected").html('<img src="http://webdemo1.co.in/Merkao/public/img/design/'+selected_language+'.png" class="social_links"/>'+selected_language);
	  $(".lang_option").find("li").html('<img src="http://webdemo1.co.in/Merkao/public/img/design/'+another_language+'.png" class="social_links"/>'+another_language);
	  $(".lang_option").find("li").attr("onclick","set_lang("+str+another_language+str+")");
 }
</script>
<script type="text/javascript">
function set_lang(lang)
{
	var selected_language=lang;
	
	if(selected_language=="Spanish")
	{
	another_language="English";
	}
	else
	{
	another_language="Spanish";
	}
	
	$.ajax({
		url: baseUrl+"/index/translatelanguage",
		//async:false,
		data: {lang:lang},
		success: function (data) 
		{
				  get_lng(selected_language,another_language);
				  var $frame = $('.goog-te-menu-frame:first');
				  if (!$frame.size()) {
					alert("Error: Could not find Google translate frame.");
					return false;
				  }
				 
				var browsername=browserinfo();
				
				if(browsername=="Safari")
				{
					var ele=$frame.contents().find('.goog-te-menu2-item span.text:contains('+lang+')').get(0);
					var evObj = document.createEvent('MouseEvents');
					evObj.initMouseEvent('click', true, true, window);
					ele.dispatchEvent(evObj);
				}
				else
				{ $frame.contents().find('.goog-te-menu2-item span.text:contains('+lang+')').get(0).click();
				}

				 return false;
			//window.location.href=window.location.href;
		}
		});
}
</script>
	<!--========================================== //End PROJECT-NAME Menu Section-->
	
		
 
					








<script type="text/javascript">
	$("#plan").fadeIn(100);
	
	$("#inner").animate(
	{"marginTop" : "95"}, 1000);

</script>
<script type="text/javascript">
function divclose()
{
	$("#inner").animate(
	{"marginTop" : "-195"}, 1000);
	setTimeout('rep();',800);
}
function rep()
	{
		$('#plan').fadeOut(100);	
		
	}
</script>
 
 					 				
			
					
<script src='http://webdemo1.co.in/Merkao/public/plugins/elevatezoom/jquery-1.8.3.min.js'></script>


<meta property="og:title" content="Traditional Bracelet" />
<meta property="og:image" content="https://casaadvisor.com/comingsoon/image/share_page_image.png" />
<meta property="og:description" content="If it is not real text, they will focus on the design. This string is randomly generated. It looks even better with you using this text. It looks even better with you using this text. A designer can use default text to simulate what text would look l" />
<link rel="image_src" href="https://casaadvisor.com/comingsoon/image/share_page_image.png"/>

<meta itemprop="name" content="Traditional Bracelet" />
<meta itemprop="description" content="If it is not real text, they will focus on the design. This string is randomly generated. It looks even better with you using this text. It looks even better with you using this text. A designer can use default text to simulate what text would look l" />
<meta itemprop="image" content="https://casaadvisor.com/comingsoon/image/share_page_image.png" />
        
<div class="container">
<div class="index_wreapper3">
		  <a id="ref_gplus" onClick='globalsharing({"desc":"If it is not real text, they will focus on the design. This string is randomly generated. It looks even better with you using this text. It looks even better with you using this text. A designer can use default text to simulate what text would look l","caption":"Traditional Bracelet","link":"https%3A%2F%2Fwww.casaadvisor.com%2F","image":"https%3A%2F%2Fwww.casaadvisor.com%2Fcomingsoon%2Fimage%2Fshare_page_image.png","name":"Traditional Bracelet","redirect_url":"http%3A%2F%2Fwebdemo1.co.in%2FMerkao%2Fproduct-detail%2F22%2FTraditional%2BBracelet%23closewindow"},3);' href="javascript: void(0)" style="text-decoration:none;">
                            <span style="background-color:#DC483C;" class="fa fa-google "></span>Google</div></a>
        

        

</div>
<input type="hidden" name="archieve_count" id="archieve_count" value="3"/>


<script type="text/javascript">


  window.fbAsyncInit = function() {
    // init the FB JS SDK
    FB.init({
	  appId      : facebook_appid,                                 
      status     : true,                                 
      xfbml      : true                                  
    });

  };

  // Load the SDK asynchronously
  (function(d, s, id){
     var js, fjs = d.getElementsByTagName(s)[0];
     if (d.getElementById(id)) {return;}
     js = d.createElement(s); js.id = id;
     js.src = "//connect.facebook.net/en_US/all.js";
     fjs.parentNode.insertBefore(js, fjs);
   }(document, 'script', 'facebook-jssdk'));


function FBShareOp(){
   var share_title='Traditional Bracelet';var share_url='http://webdemo1.co.in/Merkao/product-detail/22/Traditional+Bracelet';var share_img='http://webdemo1.co.in/Merkao/public/resources/product_images/1472275520tibet-handmade-bracelet433.jpg';var share_desc='If it is not real text, they will focus on the design. This string is randomly generated. It looks even better with you using this text. It looks even better with you using this text. A designer can use default text to simulate what text would look l';
    FB.ui({
        method: 'feed',
        name: share_title,
        link: share_url,
        picture: share_img,
        caption: "",
        description: share_desc

    }, function(response) {
        if(response && response.post_id){}
        else{}
    });

}
</script>			
	
		
	<style type="text/css">
.footer_social a:focus{text-decoration:none; outline:none}
</style>
<div class="footer">
     <div class="col-xs-12 inline-block padding_0" style="display:table;">
            <div class="col-xs-12 col-sm-4 inline-block footer_block">
                <div class="footer_heading1">CONTACT US</div> 
                <div class="footer_heading_border wow animated rubberBand">&nbsp;</div>
                <div class="footer_font1"><span class="fa fa-map-marker "></span>&nbsp;&nbsp;&nbsp;&nbsp;No. 147 ABC Road, KS 12456, USA</div> 
                <div class="footer_font1"><span class="fa fa-phone"></span>&nbsp;&nbsp;&nbsp;&nbsp;(+123) 4 567 890  (+123)4 574 741</div> 
                <div class="footer_font1"><span class="fa fa-envelope"></span>&nbsp;&nbsp;<span class="font_orange">support@merkao.com</span></div> 
            </div>
            <div class="col-xs-12 col-sm-4 inline-block footer_block" style="background-color:#212121;">
                <div class="footer_heading1">NEWSLETTER</div> 
                <div class="footer_heading_border wow animated rubberBand">&nbsp;</div>
                <div class="footer_font2">Exclusive offer for new member! Sign Up today!</div> 
                <div class="newsletter_wrapper"><input type="text" class="newsletter_box" placeholder="Enter your email address"/><img src="http://webdemo1.co.in/Merkao/public/img/design/newsletter.png" class="pull-right newsletter_img"/></div> 
            </div>
            <div class="col-xs-12 col-sm-4 inline-block footer_block">
                <div class="footer_heading1">SOCIAL MEDIA</div> 
                <div class="footer_heading_border wow animated rubberBand">&nbsp;</div>
                <div class="footer_font2">Lorem Ipsum is simply dummy text of the printing</div> 
                <div class="text-center">
                    <div class="footer_social"><a target="_blank" href="https://www.facebook.com/"><img src="http://webdemo1.co.in/Merkao/public/img/design/footer_social/fb_icon.png" /></a></div>
                    <div class="footer_social"><a target="_blank" href="https://twitter.com/"><img src="http://webdemo1.co.in/Merkao/public/img/design/footer_social/tw_icon.png" /></a></div>
                    <div class="footer_social"><a target="_blank" href="http://gmail.com/"><img src="http://webdemo1.co.in/Merkao/public/img/design/footer_social/gplus_icon.png" /></a></div>
                    <div class="footer_social"><a target="_blank" href="http://linkedin.com"><img src="http://webdemo1.co.in/Merkao/public/img/design/footer_social/linkedin_icon.png" /></a></div>
                    <div class="footer_social"><a target="_blank" href="https://www.pinterest.com/"><img src="http://webdemo1.co.in/Merkao/public/img/design/footer_social/pint_icon.png" /></a></div>
                    <div class="footer_social"><a target="_blank" href="http://youtube.com"><img src="http://webdemo1.co.in/Merkao/public/img/design/footer_social/youtube_icon.png" /></a></div>
                </div>
            </div>
    </div>
    <div class="footer_bottom">
        <div class="container">
        <div class="col-xs-12 inline-block padding_0">
            <div class="col-xs-12 col-md-6 col-sm-4 inline-block padding_0">
                <div class="footer_font3">© 2016 <span class="font_green">Merkao.</span> All Rights Reserved.</div>
            </div>
            <div class="col-xs-12 col-md-6 col-sm-8 inline-block padding_0 footer_page_link">
                <div class="footer_page"><a id="footer_home" href="/Merkao">Home</a></div>
                                  <div class="footer_page">  
                <a id="page_1" href="/Merkao/page/About_Us">About Us                </a>
                </div>
                                <div class="footer_page"><a id="contact_page" href="/Merkao/contact-us">Contact Us</a></div>
                                   <div class="footer_page">  
                <a id="page_21" href="/Merkao/page/Privacy_Policy">Privacy Policy                </a> 
                </div>
                                  <div class="footer_page">  
                <a id="page_22" href="/Merkao/page/Terms">Terms                </a> 
                </div>
                             </div>
        </div>  
        </div>
     </div>   
</div>


<!-- Modal -->
<div class="modal fade" id="stdModal" tabindex="-1" role="dialog" 
aria-labelledby="myModalLabel" aria-hidden="true">
<div class="modal-dialog form-signin" style=" margin-top:50px;">

  <div class="modal-body">
 		
 </div>
 
</div>
</div>
<!-- /.modal -->

	
  </body>
</html>
<script type="text/javascript">
function globalsharing(share,type)
{ 

  switch(type)
   {
   case 1:
   window.open("https://www.facebook.com/dialog/feed?_path=feed&app_id="+facebook_appid+"&redirect_uri="+share['redirect_url']+"&display=popup&link="+share['link']+"&picture="+share['image']+"&name="+share['caption'],"sharer","toolbar=0,status=0,width=580,height=325");
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

