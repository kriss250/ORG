$.fn.suggest  = function(options)
{
  var elem  = $(this);
  var table = "";
  var identifier="";
  var limit = 25;

$(elem).wrap("<div class='suggestions-wrapper'></div>");

$("body").on("keyup","",function(){
  //Load data

  $.ajax({
    url:"",
    type:"get",
    success:function(data)
    {

    }
  })
});


}
