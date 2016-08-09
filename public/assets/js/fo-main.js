window.autoRefresh = false;

if (typeof JSObj !== "undefined") {
    window.confirm = function (text) {
        return JSObj.confirm(text);
    };

    window.warning = function (text) {
        return JSObj.warning(text);
    };

    window.error = function (text) {
        return JSObj.error(text);
    };

    //working in ORGBox

    JSObj.ToolStripBG(38, 114, 164);
    JSObj.ToolStripColor(215, 215, 215);
}


function openDialog(url, title, features, _src)
{
    $("body").prepend("<div class='modal-bg'>");

    setTimeout(function () {
        window.src = _src;
        opener = window.open(url, title, features);
        opener.src = _src;

            opener.onunload = function () {

                var timer = window.setInterval(function () {
                    if (opener.closed) {

                        window.clearInterval(timer);
                        
                        $(".modal-bg").remove();
                        if (window.autoRefresh) {
                            window.location.reload(1);
                        }
                    }
                }, 200);

            };
        

    }, 90);
    
   
}
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
        //iFrameID.height = iFrameID.contentWindow.document.body.scrollHeight + "px";
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

    $('body').on('click', function (e) {
        //did not click a popover toggle or popover
        if ($(e.target).data('toggle') !== 'popover'
            && $(e.target).parents('.popover.in').length === 0) {
            $('[data-toggle="popover"]').popover('hide');
        }
    });

    $(".pop-toggle").popover({
        animation: true, content: function () {
            return $(this).parent().find(".dropdown-menu").html()
        }, container: "body", html: true
    });

    $('body').on('focus', '.datepicker', function () {
        var minDate = typeof $(this).attr("data-mindate") != "undefined" ? $(this).attr("data-mindate") : null;
        $(this).datepicker({
            format: "yyyy-mm-dd",
            autoclose: true,
            startDate:minDate,
            todayHighlight: true
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

    $(".modal-body").css("height",(window.windowHeight - 100)+"px");
  
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
            //iFrame
            var ifr = $("<iframe>");
            $(ifr).height("500px");
            $(ifr).attr("src", url).attr("scrolling", "auto");
            $(".main-modal .modal-body").addClass("if-modal-body").append(ifr);
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
