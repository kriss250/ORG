function initSelectBoxes() {
    $(document).ready(function () {
        var containers = $("fieldset .select-wrapper");
       
        $.each(containers, function (key, container) {

            var select = $(container).find("select");
            var selectedItem = $(select).find(":selected");
            var items = $(select).children();
            var list = $("<ul class='dropdown-menu drm'>");

            $(container).addClass("dropdown-toggle").attr("data-toggle", "dropdown");
            $.each(items, function (x, y) {
                $(list).append($("<li>").attr("data-val", $(y).attr("value")).html($(y).html()));
            })

            $(container)
           .append("<b class='select-value'>" + $(selectedItem).html() + "<b>")
           .append(list);
        })
        

    })
   
}

function iframeLoaded() {
    var iFrameID = document.getElementById('iframe');
    if (iFrameID) {
        iFrameID.height = iFrameID.contentWindow.document.body.scrollHeight + "px";
    }
}

function SearchCompany(name,url,dest)
{
    $.ajax({
        url: url + "?name=" + name,
        type: "get",
        success: function (data)
        {
            data = JSON.parse(data);
          
            $.each(data, function (key, val) {
                $(dest).append("<li data-id='"+val.idcompanies+"'>"+val.name+"</li>");
            })
           
        }
    });
}


$(document).ready(function () {


    $('body').on('focus', '.datepicker', function () {
        $(this).datetimepicker({
            format: "YYYY-MM-DD"
        });
    });

    $('body').on('click', '.datepicker', function () {
        $(this).focus();
    });

    $('body').on('dp.change', '.datepicker', function () {
        $(this).change();
    });

    $(".main-menu .open.dropdown-menu").slimscroll({
        height:"100px"
    })

    $(".modal-body").css("height",(window.windowHeight - 150)+"px");
    $(".modal-body").slimscroll({
        height: (window.windowHeight - 150) + "px",
        alwaysVisible: true,
        railVisible: true,
    })
    

    var headerHeight = $(".main-menu").height();
    windowHeight = $(window).height();

    var contentSize = windowHeight - headerHeight-2;

    $(".the_content").height(contentSize);

    $("body").on("click", ".swipe-btn", function (e) {
        e.preventDefault();
        $(".card-info").focus().select();
    });

    $("body").on("click", ".drm li", function () {
        var parent_c = $(this).parent().parent();

        $(parent_c).find(".select-value").html($(this).html());
        $(parent_c).find("select").prop('selectedIndex',$(this).index());
    })

    $("body").on("click", ".select-wrapper", function (e) {
        e.preventDefault();
        $(this).find(".dropdown-menu").toggleClass("open");
    });

    $("body").on("keyup", ".card-info", function () {
        var tracks = $(this).val().split(';');
        var name = null;
        var NameCardNo = tracks[0].split('^');
        if (NameCardNo[0] == "%E?")
        {
            alert("Reading Error , Swipe again");
            return;
        }
        var cardNo = NameCardNo[0].replace("%B","");
        
        
        name = NameCardNo[1];
        var firstname = name.split('/')[0];
        var lastname = name.split('/')[1];
        
        $("[name='firstname']").val(firstname);
        $("[name='lastname']").val(lastname);

        $(this).focus();
       
    })

    $(".dlg-btn").click(function () {
        $(".main-modal .modal-body").html('<div class="loading-bg"><img src="/assets/images/small-loader.gif" /></div>');
        var url = $(this).attr("data-url");
        var title = $(this).attr("title");
        var desc = $(this).attr("data-desc");
        $(".main-modal").find(".title").html(title);
        $(".main-modal").find(".desc").html(desc);

        if (typeof $(this).attr("data-iframe") !="undefined" && $(this).attr("data-iframe").length > 0)
        {
            var ifr = $("<iframe>");
            $(ifr).height("500px");
            $(ifr).attr("src", url).attr("scrolling", "auto");
            $(".main-modal .modal-body").append(ifr);
            $(ifr).load(function () {
                $(".loading-bg").remove();
            })
            return;
        }
        $.ajax({
            url: url,
            type: "get",
            success: function (data) {
                $(".main-modal .modal-body").html(data);
            }
        })
    })

})
