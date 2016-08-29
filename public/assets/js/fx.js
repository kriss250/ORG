$.fn.suggest = function(options) {
    var elem = $(this);
    var table = $(elem).attr("data-table");
    var field = $(elem).attr("data-field");
    var limit = 30;

    $(elem).wrap("<div class='suggestions-wrapper'></div>");
    var ul  = $("<ul class='dropdown-menu'>");
    var input  ="";

    $(".suggestions-wrapper").on("click",".dropdown-menu li",function(){
      $(elem).val($(this).html());
      $(elem).parent().removeClass("open");
    });

    $("body").on("keyup", elem, function() {

      input =  $(elem).val();

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
                      $(item).click(function(e){
                        alert("DD");
                      });
                  });

                $(elem).parent().addClass("open").append(ul);
              }
          });
        },100);
    });


};
