function zipcodeData(e){if(""!=e)$.ajax({url:baseUrl+"/user/ajaxdata/post_id/"+e,success:function(e){e=JSON.parse(e);var t=$("#user_travel_range").val();if(""!=t)var r=1e3*parseInt(t);else r=5e3;$("#zip_code_lat").val(e.lat),$("#zip_code_long").val(e.lng),initialize(e.lat,e.lng,e.city,r,"map")}});else{$("#zip_code_lat").val(""),$("#zip_code_long").val("");var t=$("#user_travel_range").val(),r=1e3*parseInt(t);initialize(38.889931,-77.009003,"New York",r,"map")}}function travelrange(e){var t=$("#zip_code_lat").val(),r=$("#zip_code_long").val(),a=1e3*parseInt(e);""!=t&&""!=r?initialize(t,r,"abc",a,"map"):initialize(-38.889931,-77.009003,"New York",a,"map")}function getcity(e){$.ajax({url:baseUrl+"/user/citiesdropdown",type:"GET",data:{name:e},success:function(e){var t=JSON.parse(e);if(EmptyListbox("user_city"),t.length>0)for(var r=0;r<t.length;r++){var a=t[r].zip_id,i=t[r].city;AddOptiontoListBox("user_city",a,i)}}})}function EmptyListbox(e){var t=document.getElementById(e);for(i=t.length-1;i>=0;i--)t.remove(i)}function AddOptiontoListBox(e,t,r){var a=document.getElementById(e),i=document.createElement("option");a.options.add(i),i.text=r,i.value=t}function getcategorylist(e){$.ajax({url:baseUrl+"/project/categorydata",async:!1,data:{id:e},success:function(e){if(EmptyListbox("service_sub_parent_id"),""!=e.trim()&&(AddOptiontoListBox("service_sub_parent_id","","- -Select Category - -"),""!=e)){var t=e.split("***");if(t.length>0)for(var r=0;r<t.length;r++){var a=t[r].split("|||"),i=a[0].trim(),o=a[1].trim();AddOptiontoListBox("service_sub_parent_id",i,o)}}}})}function getsubcategorylist(e){$.ajax({url:baseUrl+"/project/subcategorydata",async:!1,data:{id:e},success:function(e){if(EmptyListbox("service_id"),""!=e.trim()&&(AddOptiontoListBox("service_id","","- -Select Sub Category - -"),""!=e)){var t=e.split("***");if(t.length>0)for(var r=0;r<t.length;r++){var a=t[r].split("|||"),i=a[0].trim(),o=a[1].trim();AddOptiontoListBox("service_id",i,o)}}}})}function reloadChat(){var e=$("#current_active_div").val(),t=$("#job_ID").val();if($("#last_msz_"+e).length)var r=$("#last_msz_"+e).val();else var r="0";var a=$("#user_"+e).val();void 0!=r&&$.ajax({url:baseUrl+"/project/refreshchat/job_id/"+t+"/user_id/"+a+"/last_msz_id/"+r,success:function(e){if(e){"0"==r&&location.reload();var t=$("#current_active_div").val();if($("#rightDetail_"+t+" .mCSB_container").append(e),$("#temp_last_msg_"+a).lenght){var i=$("#temp_last_msg_"+a).val();$("#temp_last_msg_"+a).remove(),$("#last_msz_"+t).val(i)}$("li.in").length>0&&$(".comments_form_div").css("display","block")}}})}function requestjobModel(e,t){var r=$("#job_id_for_edit").val();Logged_user>0?$.ajax({url:baseUrl+"/project/postnewjob/job_id/"+r,data:{id:e,pagetype:t},success:function(e){$("#addprojectmodel .modal-body").html(e),$("#addprojectmodel").modal("show"),$("#job_deadline_date").datepicker({})}}):window.location=baseUrl+"/login"}function getallzip_old(){var e=$("#post_code").val(),t=new Array;$.ajax({url:baseUrl+"/search/allzipcodes/zip/"+e,async:!0,success:function(e){e=JSON.parse(e);for(var r=e.length,a=0;r>a;a++)t[a]={label:e[a].zip+","+e[a].city+","+e[a].state,zip_id:e[a].zip_id};$("#post_code").autocomplete({source:t,delay:200,focus:function(e,t){return $("#post_code").val(t.item.label),!1},select:function(e,t){return $("#post_code_id").val(t.item.zip_id),!1},change:function(e,t){t.item||$("#post_code_id").val("")}})}})}function globalsharing(e,t){switch(t){case 1:window.open("https://www.facebook.com/dialog/feed?_path=feed&app_id="+facebook_appid+"&redirect_uri="+e.redirect_url+"&display=popup&link="+e.link+"&picture="+e.image+"&name="+e.caption,"sharer","toolbar=0,status=0,width=580,height=325");break;case 2:window.open("https://twitter.com/intent/tweet?screen_name="+e.caption+"&text="+e.caption+"&url="+e.link,"sharer","toolbar=0,status=0,width=580,height=325");break;case 3:window.open("https://plus.google.com/share?title="+e.caption+"&url="+e.link,"sharer","toolbar=0,status=0,width=580,height=325");break;case 4:window.open("https://pinterest.com/pin/create/button/?url="+e.link+"&media="+e.image+"&description="+e.caption,"sharer","toolbar=0,status=0,width=580,height=325");break;case 5:window.open("https://www.linkedin.com/shareArticle?mini=true&url="+e.link+"&title="+e.caption+"&summary="+e.desc,"sharer","toolbar=0,status=0,width=580,height=325")}}function closeJobModal(e,t){var r=window.location.href;if("1"==e)if("0"==t)window.location.href=r;else{if(r.indexOf("pjfcID")>=0)var a=r.split("&&pjfcID="+new_post_job_id);else var a=r.split(new_post_job_id);var i=a[0];window.location.href=i}else window.location.href=r}function showAlert(e){bootbox.dialog({message:e,title:'   <h4 class="modal-title model_title_pay">Post Job</h4>',buttons:{}})}function postjobfromCatName(e){if(Logged_user>0)"1"==Logged_email_verified?"service_provider"==Logged_user_type?showAlert("<p>You registered as service provider. For post a job you need to Sign up as client.</p>"+$(".PostJobFrom3Pages").html()):requestjobModel(e,1):showAlert("<p>Before Post a new Job , Please verify your email first.</p>");else{var t=$("#JobPostHiddenInput").val(),r=t.split("/pjfcID"),a=r[0]+"/"+e;$("#loginForJobPost").attr("href",a),showAlert("<p>For post a job you need to Sign up.</p>"+$(".PostJobFrom3Pages").html())}}function afterLoginPostJobConditionChk(e){"1"==Logged_email_verified?"service_provider"==Logged_user_type?showAlert("<p>You Sign in as service provider. For post a job you need to Sign up as client.</p><p>Please logout and then sign up as client</p>"):requestjobModel(e,1):showAlert("<p>Before Post a new Job , Please verify your email first.</p>")}$.validator.addMethod("phone_validate",function(e,t){return this.optional(t)||/^[0-9 _.,!()+=`,"@$#%*-]*$/i.test(e)},"Please enter valid phone number"),$.validator.setDefaults({highlight:function(e){$(e).closest(".form-group").addClass("has-error")},unhighlight:function(e){$(e).closest(".form-group").removeClass("has-error")},errorElement:"span",errorClass:"help-block",errorPlacement:function(e,t){t.parent(".input-group").length?e.insertAfter(t.parent()):"radio"==t.attr("type")||"checkbox"==t.attr("type")?e.insertAfter(t.parent().parent()):e.insertAfter(t)}}),$(".profile_form").validate({groups:{exdate:"user_credit_card_expire_month user_credit_card_expire_year"},rules:{user_old_password:{minlength:6,maxlength:16,remote:baseUrl+"/user/checkpassword"},user_password:{minlength:6,maxlength:16},user_rpassword:{equalTo:"#user_password",minlength:6,maxlength:16},user_email:{required:!0,email:!0},user_credit_card_expire_month:{validate_ExpiryDate:!0,required:!0},user_credit_card_expire_year:{validate_ExpiryDate:!0,required:!0},cvv:{validate_CVV:!0},user_zip_code:{validate_zip_code:!0},proposal_credit:{validate_quote_price:!0},location:{required:{depends:function(e){return $("#miles").is(":filled")}}},miles:{required:{depends:function(e){return $("#location").is(":filled")}}}},messages:{user_rpassword:{equalTo:"Password Mismatch ,please enter correct password"}}}),$(".hireForm").validate({rules:{hire_quote_client:{validate_hire_quote_price:!0}}}),jQuery.validator.addMethod("validate_hire_quote_price",function(e,t){var r=$("#hire_quote_client").val(),a=/^[0-9\.]+$/;return r.match(a)?!0:!1},"Please enter valid price."),$(".MoreQuoteForm").validate({rules:{more_quote_client_request:{validate_client_more_quote_request:!0}}}),jQuery.validator.addMethod("validate_client_more_quote_request",function(e,t){var r=$("#more_quote_client_request").val(),a=/^[0-9\.]+$/;return r.match(a)&&r>0?!0:!1},"Please enter more quote request greater than 0."),jQuery.validator.addMethod("validate_quote_price",function(e,t){var r=$("#proposal_credit").val(),a=/^[0-9\.]+$/;return r.match(a)?!0:!1},"Please enter valid price."),jQuery.validator.addMethod("validate_ExpiryDate",function(e,t){var r=$("#user_credit_card_expire_month").val(),a=$("#user_credit_card_expire_year").val();if(""!=r&&""!=a){var i=new Date,o=i.getMonth()+1;if(""==r||""==a)return!1;if(2016==a&&o>r)return!1}return!0},"Please enter valid expiry date"),jQuery.validator.addMethod("validate_CVV",function(e,t){var r=$("#cvv").val(),a=r.length,i=/^[0-9]+$/;return!r.match(i)||3!=a&&4!=a?!1:!0},"Please enter valid CVV number"),$("#register_form").validate({lang:"fi",rules:{user_business_website:{url:!0},user_business_desc:{minlength:100},user_email:{required:!0,email:!0,remote:baseUrl+"/user/checkemail"},user_password:{required:!0,minlength:6},user_cpassword:{required:!0,equalTo:"#user_password",minlength:6},user_zip_code:{validate_zip_code:!0},"service_cat[]":{required:!0}},messages:{user_business_desc:{minlength:"please enter more than 100 characters"}}}),jQuery.validator.addMethod("validate_zip_code",function(e,t){var r=$.trim($("#user_zip_code").val()),a=r.length,i=/^[0-9]+$/;return r.match(i)&&5==a?!0:!1},"Please enter valid five digits Zip Code"),jQuery.validator.addClassRules("checkemail",{remote:baseUrl+"/user/checkemail"}),jQuery.validator.addClassRules("emailexists",{remote:baseUrl+"/user/checkemail/rev/1"}),jQuery.validator.addClassRules("checkemail_exclude",{remote:baseUrl+"/user/checkemail?exclude=1"}),$(".mail_verification_pulsate").pulsate({color:"#fcb322"}),$(function(){$("img.lazy").lazyload({effect:"fadeIn"})}),$(document).ready(function(e){$("#notify_li_header .dropdown-menu ul").mCustomScrollbar({mouseWheelPixels:0}),$(".allComments").mCustomScrollbar({mouseWheelPixels:0}),$(".allComments").mCustomScrollbar("update")});