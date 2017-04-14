$.fn.suggest = function(options) {
    var elem = $(this);
    var table = $(elem).attr("data-table");
    var field = $(elem).attr("data-field");
    var limit = 30;
   
    $(elem).wrap("<div class='suggestions-wrapper'></div>");
    var ul  = $("<ul class='dropdown-menu'>");
    var input  ="";

    $(".suggestions-wrapper").on("click", ".dropdown-menu li", function () {
        $(this).parent().parent().find(".suggest-input").val($(this).html());
        $(elem).parent().removeClass("open");
    });

    $(elem).on("keyup", function () {

       table = $(this).attr("data-table");
       field = $(this).attr("data-field");
       
      input =  $(this).val();
      var eventSrc = $(this);
      $(ul).html("");
      if(input.length===0)
      {
        return;
      }
        //Load data
        setTimeout(function(){
          $.ajax({
              url: options.url+"?query="+input+"&table="+table+"&field="+field+"&limit="+limit,
              type: "get",
              success: function(data) {
                $(ul).html("");
                  $.each(data,function(x,y){
                      var item = "<li>"+y.name+"</li>";
                      $(ul).append(item);
                  });

                $(eventSrc).parent().addClass("open").remove(".dropdown-menu").append(ul);
              }
          });
        },80);
    });


};
