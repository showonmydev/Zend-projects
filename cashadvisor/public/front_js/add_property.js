
var init_render = 10;
//var init_lat = "34.076550",init_lng = "-118.340778";	
$(document).ready(function() {
	
	$("#user_address").after("<div id='map_canvas'></div>");
	
	$("#user_city").after("<div class='text-right'><button class='btn green btn-xs' type='button' onclick='get_full_location()' style='height:auto;margin-top:4px;'>Get From Address</button></div>");
 	
 	
	var form_lat = $.trim($('#latitude').val()) , form_lng = $.trim($('#longitude').val());
	
	if(form_lat!="" && form_lng!=""){
		init_lat = form_lat;
		init_lng = form_lng;
		init_render = init_render; 
	}
	
	
	//options = {types: []};
	
	options = {
		  //	types: ['(cities)'],
		 	//componentRestrictions: {country: 'ke'}   

	};

	
	input_element = document.getElementById('user_address');
	
	autocomplete = new google.maps.places.Autocomplete(input_element, options );	
	
	
	
	
});



google.maps.event.addDomListener(window, 'load', add_property_map);


 	
function add_property_map(){
	
 	var mapOptions = {

		zoom: init_render,		
		center: new google.maps.LatLng(init_lat, init_lng),
		mapTypeId: google.maps.MapTypeId.ROADMAP,
		scrollwheel: false,
		zoomControlOptions: {
			style: google.maps.ZoomControlStyle.SMALL,
			position: google.maps.ControlPosition.TOP_LEFT
		},
		 mapTypeControlOptions: {
		  style: google.maps.MapTypeControlStyle.DROPDOWN_MENU
		},
		
	};
	
 
	map = new google.maps.Map(document.getElementById('map_canvas'),mapOptions);
	marker = new google.maps.Marker({map: map});
	marker.setPosition(mapOptions.center);
	marker.setVisible(true);
	marker.set("draggable", "true");

 	autocomplete.bindTo('bounds', map);
	
	google.maps.event.addListener(autocomplete, 'place_changed', function() {
		place = autocomplete.getPlace();
 		 
		// If the place has a geometry, then present it on a map.
		if (place.geometry.viewport) {
			  map.fitBounds(place.geometry.viewport);
			  map.setZoom(10);
		} else {
			  map.setCenter(place.geometry.location);
			  map.setZoom(17);  // Why 17? Because it looks good.
		}	 
  		$("#job_latitude").val(place.geometry.location.lat());
		$("#job_longitude").val(place.geometry.location.lng());
		
  
 		marker.setPosition(place.geometry.location);
		marker.setVisible(true);
		marker.set("draggable", "true");
		 
		
  });
  
 
 	 var geocoder = new google.maps.Geocoder(); 
		//Add listener to marker for reverse geocoding
		google.maps.event.addListener(marker, 'drag', function() {
			
 			geocoder.geocode({'latLng': marker.getPosition()}, function(results, status) {
			if (status == google.maps.GeocoderStatus.OK) {
				if (results[0]) {
					$('#user_address').val(results[0].formatted_address);
					$('#job_latitude').val(marker.getPosition().lat());
					$('#job_longitude').val(marker.getPosition().lng());
					currLatLng=marker.getPosition();
 				}
			}
			});
			
		});
  
}


function get_full_location(){
	
	var address_string  = $.trim($('#user_address').val());
	
   
	
	if(address_string=="" || address_string.length==0){
		alert('Please Fill the address First');
		$('#user_address').focus();
		return false; 
	}
	
	
	$.ajax({
		type: "POST",
		cache: false,
		url: SITEURL+"/index/getfulladdress",
		data: {address_string:address_string},
		async:true,
		beforeSend: function (){ 
			//$.blockUI();
		},
		error: function(xhr, textStatus, errorThrown){
 			 alert('Please check the location again , we are unable to find any information for this address.');
		},
		success: function(data){
			
			data= JSON.parse(data);
 			
		 	$('#user_city').val(data.locality);
			$('#user_state').val(data.state);
			$('#user_country').val(data.country);
			 $('#user_postal_code').val(data.postal_code);
			console.log(data);
			
		},
		complete:function(data){
			//$.unblockUI();
		}
	}); 
}