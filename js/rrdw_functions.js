function setupEventListener() { 
	var eventMethod = window.addEventListener ? "addEventListener" : "attachEvent";
	var eventer = window[eventMethod];
	var messageEvent = eventMethod == "attachEvent" ? "onmessage" : "message";

	//Extra margin to add to the height of the iframe, to disable scrollbar
	var marginHeight = 1.03; //1.03 = 3% extra height
	
	// Listen to message from iframe      
	eventer(messageEvent,function(e) {
		var origin = e.origin || e.originalEvent.origin;
		if(rrdwObject.origin !== origin) {
			console.log("Origins are not the same, so we're not setting the height of the iframe");
			return;
		}
	    
		var height = e.data;
		
		if ( !isNaN( height) ) {
			document.getElementById( 'dash-iframe' ).height = Math.ceil((height * marginHeight));
		} 
	},false);
}

setupEventListener();

jQuery(function($) {
	$("#wp_welcome_panel-hide").change(function(){
		if($('#wp_welcome_panel-hide').is(':checked')) {
			 $('#dash-iframe').attr("src", rrdwObject.remote_url);
		}
	});
});
