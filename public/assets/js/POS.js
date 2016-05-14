 
 $(document).ready(function(){
     $('.date-picker').datepicker({
         format: "yyyy-mm-dd",
         autoclose: true,
         todayHighlight: true
     });
 	//Time 
 	setInterval(function(){
 		tsp = Date.now();
 		date = new Date(tsp);

 		hours = date.getHours() >= 10 ? date.getHours()  : "0"+date.getHours() ;
 		min = date.getMinutes() >= 10 ? date.getMinutes() : "0"+ date.getMinutes();
 		$("i.time").html(hours+":"+min);

 	},60000);

 	$(".thechosen").chosen(); 
    
    $(".dropdown").click(function(e){
    	//e.preventDefault();

    	$(this).children(".dropdown_menu").toggle().css("height","auto");
    })

    $(".dropdown > a").click(function(e){e.preventDefault()});


	$('#slide-submenu').on('click',function() {			        
        $(this).closest('.list-group').fadeOut('slide',function(){
        	$('.mini-submenu').fadeIn();	
        });
    });

	$('.mini-submenu').on('click',function(){		
        $(this).next('.list-group').toggle('slide');
        $('.mini-submenu').hide();
	})
	

 	 $("#ajaxsave").submit(function(e){
 	 	e.preventDefault();
 	 	var form = $(this);
 	 	var saveBtn = $(this).find("[type='submit']");
 	 	var saveBtnText  = $(saveBtn).val();

 	 	$(saveBtn).val('Saving ...').attr("disabled","disabled");

 	 	$.ajax({

 	 		url:$(this).attr("action"),
 	 		type:$(this).attr("method"),
 	 		data:$(form).serialize(),
 	 		success:function(response){

 	 			try {
 	 			var res = JSON.parse(response);
	 	 		}catch(ex){
	 	 			bootstrap_alert.danger(form,"Invalid data returned");
	 	 		}

 	 			if(typeof(res) == "object"){
	 	 			if(res.errors.length ==0){
	 	 			  bootstrap_alert.success(form,res.message);

	 	 		    }else {
	 	 		    	$.each(res.errors,function(index,value){
	 	 			        bootstrap_alert.danger(form,value);
	 	 		    	});
	 	 		    }
 	 			} 	 		    
 	 		},
 	 		error:function(e,f,c){
 	 			$(saveBtn).removeAttr("disabled").val(saveBtnText);
 	 			bootstrap_alert.danger("Error : "+f);
 	 		},
 	 		timeout:120000,
 	 		complete:function() {

 	 			$(saveBtn).removeAttr("disabled").val(saveBtnText);
 	 			
 	 		},
 	 		statusCode: {
				    404: function() {
				      bootstrap_alert.danger(form,"Resource not found");
				      $(saveBtn).removeAttr("disabled").val(saveBtnText);
				    },
				    500 : function(){
				    	bootstrap_alert.danger(form,"Server Error");
				    	$(saveBtn).removeAttr("disabled").val(saveBtnText);
				    }
				}
 	 	})

 	 	
 	 })



 	bootstrap_alert = function() {

 	}
	bootstrap_alert.warning = function(location,message) {
		var alertx = $('<div class="alert alert-warning"><a class="close" data-dismiss="alert">×</a><span>'+message+'</span></div>');
	     $(location).prepend(alertx);
	     setTimeout(function() {$(alertx).fadeOut();}, 10000);
	}

	bootstrap_alert.danger = function(location,message) {
		var alertx = ('<div class="alert alert-danger"><a class="close" data-dismiss="alert">×</a><span>'+message+'</span></div>');
	     $(location).prepend(alertx);
	     setTimeout(function() {$(alertx).fadeOut();}, 10000);
	}

	bootstrap_alert.success = function(location,message) {
 		var alertx = $('<div class="alert alert-success"><a class="close" data-dismiss="alert">×</a><span>'+message+'</span></div>');
	     $(location).prepend(alertx);
	     setTimeout(function() {$(alertx).fadeOut();}, 10000);
	
	}

 });




