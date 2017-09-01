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
        $($(elem).attr("data-value-holder")).val($(this).attr("data-value"));
        $(elem).parent().removeClass("open");
    });

    $(elem).on("keyup", function () {

        table = $(this).attr("data-table");
        field = $(this).attr("data-field");
        display_field = $(this).attr("data-display-field");
        value_field = $(this).attr("data-value-field");
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
              url: options.url + "?query=" + input + "&table=" + table + "&field=" + field + "&limit=" + limit + "&display_field=" + display_field+"&value_field="+value_field,
              type: "get",
              success: function(data) {
                $(ul).html("");
                  $.each(data,function(x,y){
                      var item = "<li data-value='" + y.value + "'>" + y.name + "</li>";
                      $(ul).append(item);
                  });

                $(eventSrc).parent().addClass("open").remove(".dropdown-menu").append(ul);
              }
          });
        },80);
    });


};
