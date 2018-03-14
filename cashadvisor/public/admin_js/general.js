/* All Aditional Functions  */



$('.block_ui').click(function(e){$.blockUI();});

function showFlashMessage(msg,err){
	
	$('body').animate({ scrollTop: 0 }, 500);
	
 	$("#flash-message").show('slow');
	
 	if(err){
		$("#flash-message").addClass("alert-danger");
	}else{
		$("#flash-message").removeClass("alert-danger");
		$("#flash-message").addClass("alert-info");
	}
	
  	$("#flash-message").html(msg);
 	
	
	$("#flash-message").slideDown(function(){
		setTimeout(	'$("#flash-message").hide("slow")' ,3000);
	});
}


// override jquery validate plugin defaults
$.validator.setDefaults({
    highlight: function(element) {
        $(element).closest('.form-group').addClass('has-error');
    },
    unhighlight: function(element) {
        $(element).closest('.form-group').removeClass('has-error');
    },
	
	submitHandler: function(form) {
     	 $.blockUI();
		 form.submit();
    },
	
    errorElement: 'span',
    errorClass: 'help-block',
    errorPlacement: function(error, element) {
        if(element.parent('.input-group').length) {
            error.insertAfter(element.parent());
        } else {
            error.insertAfter(element);
        }
    }
});


var handleChoosenSelect = function () {
	
        if (!jQuery().chosen) {
            return;
        }
        $(".chosen").chosen();

        $(".chosen-with-diselect").chosen({
            allow_single_deselect: true
        })
    }



<!-- For Datatables  -->
function re_init(){
		var Label1 = 'Blocked';
		var Label2 = 'Active';
		/*if(controller == 'job' && action == 'index'){
			var Label1 = 'Barred';
			var Label2 = 'Allowed';
		}*/
		
		$('.checkboxes').uniform();
		
		$('.group-checkable').removeAttr('checked');
		
		$('.group-checkable').parent().removeClass('checked');
			
		$('.danger-toggle-button').toggleButtons({
			style: {
				// Accepted values ["primary", "danger", "info", "success", "warning"] or nothing
				enabled: "danger",
				disabled: "info"
				
			},
			width:126,
			height:28,
			label: {
				enabled: Label1,
				disabled: Label2
			}
		});
		
		
		$('.danger-toggle-button-1').toggleButtons({
			style: {
				// Accepted values ["primary", "danger", "info", "success", "warning"] or nothing
				enabled: "danger",
				disabled: "info"
				
			},
			label: {
				enabled: "OFF",
				disabled: "ON"
			}
		});
	
		
	 $("img.lazy").lazyload({
         effect : "fadeIn"
     });
	 
	
	
}

/*$('.validate').validate({
		rules:{
			blog_url:{required:true,remote:baseUrl+"/privatepanel/blog/blogurl"},
			},
	});
	*/
/*jQuery.validator.addClassRules("checkblogUrl", {
    remote:baseUrl+"/privatepanel/blog/blogurl"
});*/
jQuery.validator.addClassRules("checkemail",{remote: baseUrl+"/user/checkemail"});


	
$('.profile_form').validate({
	rules:{
		user_old_password:{minlength:6,maxlength:16,remote: baseUrl+"/privatepanel/index/checkpassword"},
 		user_password:{minlength:6 , maxlength:16},
		user_rpassword:{equalTo:'#user_password' , minlength:6, maxlength:16},
 		user_email:{required: true,email: true,/*remote: baseUrl+"/user/checkemail"*/},
		 cp_price: {min:1, maxlength:7},
		cp_points:{min:1,maxlength:7},
		 user_zip_code:{validate_zip_code:true,},		
 	},
 });
 
 
 

 jQuery.validator.addMethod("validate_zip_code",
	  function ( value, element ) {
	  var inputZip = $.trim($('#user_zip_code').val());
	 // console.log(inputZip);
	  var n = inputZip.length;
	//  console.log(n);
	   var digitss = /^[0-9]+$/;
	   
if(inputZip.match(digitss) &&( n==5)){
	return true;
}
		 return false;
		
 },
		"Please enter valid five digits Zip Code" 
);


 
 
 $('.package_form').validate({
	rules: {
        cp_price: {min:1,validate_price_code:true,},
		cp_points:{min:1,validate_zip_code:true,},
    },
 });

	
$('.mix-link-delete').click(function(e) {
	
	var $confirm = confirm("Please Confim Your Delete Request !");
	if($confirm){
		return true; 
	}
	return false; 
});




function checkSelects(){
 
 	var checkedRecords=false;	
	
	$(".elem_ids").each(function(index, element) {
        if(this.checked==true){
			checkedRecords=true;
		}
    });
 	
	
	if(!checkedRecords){
		showFlashMessage("No Records Selected for Delete , Please Select Records to delete ",1);
		return false;	
	}else{
		if(!confirm("Are you sure you want to delete")){
			return false;
		}
		$.blockUI();
	}
 	 
}



	
$('#deletebcchk').click(function(e) {
	var current_checked_status = $.trim($('.group-checkable').attr('checked'));
	if(current_checked_status!='checked'){
		 $('.checkboxes').removeAttr('checked');
		 $('.checkboxes').parent().removeClass('checked');;
	}
	else{
		
		$('.checkboxes').attr('checked','checked');
		$('.checkboxes').parent().addClass('checked');;
	}
	
});




function globalStatus(chkAll){
	
 	$.blockUI();
	console.log(chkAll);
	 
 	if($(chkAll).hasClass("status-1")){
			var status=0
			$(chkAll).addClass("status-0");
			$(chkAll).removeClass("status-1");
	}else{
		var status=1
			$(chkAll).addClass("status-1");
			$(chkAll).removeClass("status-0");
	}
	
	vars=	$(chkAll).attr("id").split("-");
	title=	$(chkAll).attr("title") ;
	
	
	
	 
	scriptUrl=baseUrl+"/privatepanel/ajax/setstatus/type/"+vars[0]+"/id/"+vars[1]+"/status/"+status;
 
	  if(title!==undefined && title!=""){usermessage=title+" has been changed";}
	  else{
		  field_title = vars[0].replace(/_/g," ");
		  field_title = ucwords(field_title);
		  
		  
		 usermessage='<b>'+field_title +'</b>  status has been changed';}
		$("#flash-message").slideUp();
		$.ajax({
			url: scriptUrl,
			dataType:"json",
			success: function(data) {
				if(data.success){
					showFlashMessage(data.message);	
				}else{
					showFlashMessage(data.message,1);
				}
				
			$.unblockUI();
			
/*			if(controller == 'job' && action == 'index'){
				if($(chkAll).hasClass("status-1")){
					var allow_job_id = vars[1];
				    moreQuoteAllowed(allow_job_id);
				}
			}
*/		},
		error : function(data) {
			
			showFlashMessage(" <b>Internal Server Error</b> " ,1);
			$.unblockUI();
			
			 
		}
	  
	});
		
}


function moreQuoteAllowed(allow_job_id){	
		$.ajax({ 
			url:baseUrl+'/privatepanel/job/allowmorequote/allow_job_id/'+allow_job_id,
			async:false,
			data : {job_id:allow_job_id},
			success:function(data){
				$('#sendReminderSP .modal-body').html(data);
				
				$('#sendReminderSP').modal({
				 backdrop: 'static'
				});
				
				$('#sendReminderSP').modal('show');
				
				

				$("#AccDeclineDiv_"+allow_job_id).html('Accepted');
			}
		});

}

function moreQuoteDecline(decline_job_id){
	$.ajax({ 
			url:baseUrl+'/privatepanel/job/decinemorequote/decline_job_id/'+decline_job_id,
			success:function(data){
				$("#AccDeclineDiv_"+decline_job_id).html('Declined');
			}
		});

}


function ucwords(str){
	
	return  str.toLowerCase().replace(/\b[a-z]/g, function(letter) {
    	return letter.toUpperCase();
	});

	
}



function getSubCategories($obj){
	$.blockUI();
	$.ajax({
		url: baseUrl+"/privatepanel/ajax/getsubcategories",
		dataType:'json',
		data : {id:$obj.value},
		success: function(data) {
			html = "<option value=''>-- Select SubCategory --</option>"
			for(var key in data) {
 				html += "<option value=" + data[key]['category_id']  + ">" +data[key]['category_name'] + "</option>"
			}
			$('#product_subcategory_id').html(html);
			$.unblockUI();
		},
		error : function(data) {
			showFlashMessage(" <b>Internal Server Error</b> " ,1);
			$.unblockUI();
		}

	});
	 
	
}

$(function() {
     $("img.lazy").lazyload({
         effect : "fadeIn"
     });

  });



function getcategorylist(service){
	$.ajax({ 
		url:baseUrl+"/privatepanel/service/categorydata",
		
		data : {id:service},
		success:function(data)
		  {  //alert(id)
			EmptyListbox('service_sub_parent_id');
				if(data.trim()!='')
				{ //console.log(data);
					AddOptiontoListBox('service_sub_parent_id','','- -Select Category - -');
					if(data!="")
					{ 
						var ArrData=data.split("***");
						if(ArrData.length>0)
						{
						// $("#service_sub_parent_id").removeAttr('disabled');
							for(var i=0;i<ArrData.length;i++)
							{		
								var mySplit=ArrData[i].split("|||");
								var OptionValue=mySplit[0].trim(); 
								var OptionText=mySplit[1].trim();
								AddOptiontoListBox('service_sub_parent_id',OptionValue,OptionText);
							
							}
						}
					}
				}
		}
	});
}

function EmptyListbox(listBoxId)
{
 var elSel = document.getElementById(listBoxId);
 for (i = elSel.length - 1; i>=0; i--) {
  elSel.remove(i);   
 }
}

function AddOptiontoListBox(listBoxId,Value,Text)
{
 var elSel = document.getElementById(listBoxId); 
 var opt = document.createElement("option");
 elSel.options.add(opt);
 opt.text=Text;
 opt.value=Value;
}


$(document).ready(function(e) {
	handleChoosenSelect();
});


function editreviewform(review_id){
	$.blockUI();
		$.ajax({
			url: baseUrl+"/privatepanel/job/editreview",
			type:'POST',
			data: $('#review_form').serializeArray(),
			success: function (data) 
			{
				if(data!='0'){
					var revieeData=JSON.parse(data);
					$('#review_title').val(revieeData.review_title);
					$('#review_msz').val(revieeData.review_msz);
					$('#revTitle').html(revieeData.review_title);
					$('#RevMsg').html(revieeData.review_msz);
					$('#RevRplyMsg').html(revieeData.review_reply_msz);
					showFlashMessage('Review Successfully Updated.');
				}else{
					showFlashMessage('Error found','error');
				}
				showReviewForm();
				$.unblockUI();	
				
			}
	  });
		
	}

function showReviewForm(){
	$(".formReview").toggle();
}

function removereviewform(review_id,review_job_id){
	$.blockUI();
		$.ajax({
			url: baseUrl+"/privatepanel/job/removereview/review_id/"+review_id+"/review_job_id/"+review_job_id,
			success: function (data) 
			{
				
				window.location=baseUrl+"/privatepanel/job/viewjob?job_id"+review_job_id;
				
			}
	  });
		
	}

function check_value_old(val){
$('#package_form').validate({

    rules: {
        cp_price: {min:1},
		cp_points:{min:1},
    },
		
		highlight: function(element){
			           $(element).closest('.form-group').addClass('has-error');
			},
			
	    unhighlight: function(element) {
                              $(element).closest('.form-group').removeClass('has-error');
                },
				
		errorElement: 'span',
		errorClass: 'help-block',
		errorPlacement:function(error,element){
			if(element.parent('.input-group').length){
				error.insertAfter(element.parent());
				}else{
					error.insertAfter(element);
					}
			}
	
	});
if($('#package_form').valid()){
	 $('#package_form').submit();
}

 }
 
 
function getcity(state){ 

	$.ajax({  
		url:baseUrl+"/user/citiesdropdown",
		type:"GET",
		async:false,
		data : {name:state},
		
		success:function(data)
		  {
			//console.log(data)
			 var Result = JSON.parse(data);
			//console.log(Result)
			EmptyListbox('user_city');
				if(Result.length>0)
				{
					//console.log(Result[0].zip_id);
					for(var i=0;i<Result.length;i++)
					{ 
						var OptionValue = Result[i].zip_id;
						var Text = Result[i].city;
						AddOptiontoListBox('user_city',OptionValue,Text);
						//alert(AddOptiontoListBox('user_city',OptionValue,Text));
					}
					
				}
			
		}
	});
}

$('.default_form').validate({
	rules:{
		 service_name:{validate_service_name:true,},		
 	},
 });
 
jQuery.validator.addMethod("validate_service_name",
	  function ( value, element ) {
	  var inputServiceName =  $.trim($('#service_name').val());
	  // var digitss = /^[0-9]+$/;
	  console.log(inputServiceName);
	  var checkLetter =  /^[a-zA-Z\s]+$/;
	   
if(inputServiceName.match(checkLetter)){
	return true;
}
		 return false;
		
 },
		"Only Letters allowed" 
);

