$(document).ready(function () {
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr("content") }
    });
    if (typeof JSObj !== "undefined") {
        window.confirm = function (text) {
            return JSObj.confirm(text);
        };
        //working in ORGBox

        JSObj.ToolStripBG(250, 250, 250);
        JSObj.ToolStripColor(35,35, 35);
    } else {
        notifyBrowser();
    }

    $(".alert.success").delay(3800).slideUp(200, function() {
        $(this).alert('close');
    });

    //Create copy button

    var cpBtn = $("<button class='clipboard-copy-btn'>");
    cpBtn.html("<i class='fa fa-clipboard'></i>").attr("data-clipboard-target", ".table");
    
    $(".report-filter").append(cpBtn);

    $("body").on("submit", ".ajax-form", function (e) {
        var form = $(this);
        e.preventDefault();

        $(this).children("button[type='submit']").attr("disabled", "disabled").append("...");
        var data = $(this).serialize();

        $.ajax({
            url: $(form).attr("action"),
            type: "post",
            data: data,
            success: function (data) {
                try {
                    jData = JSON.parse(data);
                } catch (ex) {
                    ualert.error(ex);
                    return;
                }

                if (jData.errors != null && Object.keys(jData.errors).length > 0) {
                    str = "";
                    $.each(jData.errors, function (index, value) {
                        str += value + "<br/>";
                    });
                    ualert.error(str);
                } else {
                    ualert.success(jData.message);
                    $(form).find(".form-control:not([type='submit'])").val("");
                }

                if (jData.success == 1) {
                    ualert.success("User successfully created");
                    location.refresh();
                }


            },
            error: function () {
                ualert.error("Error saving user");
            }
        }).done(function () {
            $(form).find("button[type='submit']").html("Submit").removeAttr("disabled");
        });

    });

    $('body').on('focus', ".date-picker-single", function () {
        $(this).daterangepicker({
            singleDatePicker: true,
            locale: {
                format: 'YYYY-MM-DD'
            }
        });
    });

    $('.date-picker').datepicker({
        format: "yyyy-mm-dd",
        autoclose: true,
        todayHighlight: true
    });

    $('body').on("click", ".delete_bill_btn", function (e) {
        e.preventDefault();
        if (!confirm("Are you sure you want to delete this bill ?")) {
            return false;
        }
        var url = $(this).attr("data-url");
        var id = $(this).attr("data-id");
        $.ajax({
            url: url,
            data: { id: id, retain: 1 },
            type: "delete",
            success: function (data) {
                if (data == "1") {
                    alert("The bill has been deleted successfuly !");
                    $(".delete_bill_btn").removeClass("btn-danger").addClass("btn-primary").attr("disabled", "disabled");
                } else {
                    alert("Unable to delete the bill");
                }
            },
            error: function () {
                ualert.error("Error Deleting Bill");
            }
        });
    });

    $("body").on("click", ".delete-trans-btn", function (e) {
        e.preventDefault();
        if ($(this).attr("disabled") == "disabled") {
            return false;
        }

        if (!confirm("Are you sure you want to remove this transaction ?")) {
            return false;
        }

        $(this).attr("disabled", "disabled");
        var btn = $(this);
        var url = $(this).attr("data-url");
        $.get(url, function (data) {
            if (data == "1") {
                location.reload();
            } else {
                alert("Error removing transaction");
            }
        })

    })

    $(".expand-btn").click(function (e) {
        e.preventDefault();
        $("body").toggleClass("fullscreen");
    })

    $(".dropdown-btn").click(function (e) {
        e.preventDefault();
        $(this).parent().children(".dropdown-menu").toggleClass("shown");
    })
    $("body").on("click", '.close-btn', function (e) {
        e.preventDefault();
        closeModal();

    });



    function closeModal() {
        $(".modal-window").fadeOut(300).remove();
        $(".modal-bg").fadeOut(300).remove();
    }

    $("body").on("click", ".modal-btn", function (e) {
        if ($(".modal-window").length > 0) {
            closeModal();
        }

        e.preventDefault();
        var uri = $(this).attr("href");
        var modalDiv = $("<div class='modal-window'>");
        var modalBackgroud = $("<div class='modal-bg'>");
        var screenHeight = window.screen.height;
        var screenWidth = window.screen.width;
        var closeBtn = $("<button class='close-btn'>").html('<i class="fa fa-times-circle"></i>').css("font-size", "22px");
        var centerHeight = (screenHeight * 50) / 100;
        var centerWidth = (screenWidth * 50) / 100;

        $("body").bind({
            keypress:function(e){
                 if(e.keyCode==27)
                 {
                    closeModal();
                 }
             }

        });

        if (parseInt($(this).attr("data-width")) != "NaN") {
            $(modalDiv).width($(this).attr("data-width"));
        }

        if (parseInt($(this).attr("data-height")) != "NaN") {
            $(modalDiv).height($(this).attr("data-height"));
        }


        $("body").prepend($(modalBackgroud));
        p = $("<p class='text-center'>").css({ "font-size": "26px", "position": "absolute", "top": (centerHeight - 50) + "px", "width": "100%" }).html(" Loading ....");
        $(modalDiv).html($(p));
        Xspace = (screenWidth - $(modalDiv).width()) / 2;
        $(modalDiv).css({ "left": Xspace + "px", "right": Xspace + "px" });

        $("body").prepend($(modalDiv));

        $.ajax({
            url: uri,
            type: "get",
            success: function (data) {
                var innerDiv = $("<div class='innerDiv'>");
                $(innerDiv).width = $(modalDiv).width();
                $(innerDiv).html(data);
                $(innerDiv).css("position", "relative");

                $(modalDiv).html($(innerDiv));
                $(modalDiv).prepend($(closeBtn));
                Xspace = (screenWidth - $(modalDiv).width()) / 2;

                //$(modalDiv).css({"height":heightc+"px","left":Xspace+"px","right":Xspace+"px"});

                $("body").prepend($(modalDiv));

                $(modalDiv).css({ "height": $(innerDiv).height() + 70 + "px", "left": Xspace + "px", "right": Xspace + "px" });
            }
        })

        $("body,html").scrollTop(0);
    })


    //Master Search

    var searchDiv = $(".search-results");
    var searchList = $("<ul class='search-results-list'>");

    $("#master-search").blur(function () {
        setTimeout(function () {
            $(searchDiv).html($(searchList)).removeClass("open");
            $(".loader-img").removeClass("shown");
        }, 1500);

    })

    $("#master-search").keyup(function () {
        $(".loader-img").addClass("shown");
        var q = $(this).val();
        var url = $(this).attr("data-search-url");

        switch ($("#master-search-location").val().toLowerCase()) {
            case "room":
                q = "room " + q;
                break;
        }

        url = url.replace('%7Bquery%7D', q);

        var previewUrl = $("option:selected", "#master-search-location").attr("data-preview-url");

        setTimeout(function () {
            $.ajax({
                url: url,
                type: "get",
                contentType: "json",
                success: function (data) {
                    data = JSON.parse(data);
                    $(searchList).html("");

                    $.each(data, function (e, v) {
                        var uri = previewUrl + "/?type=" + v.location.split('=')[1] + "&id=" + v.ID;
                        $(searchList).append("<li><a data-height='600' data-width='580' class='modal-btn' href='" + uri + "'>" + v.text +
                            "<span>Found in <em>" + v.location.split('=')[1] + "</em></span></a></li>");
                    });

                    $(searchDiv).html($(searchList)).addClass("open");
                    $(".loader-img").removeClass("shown");
                }
            })
        },500);

    });

})

function showRangeValue(src) {
    var destination = $(src).attr("data-preview");
    $(destination).html($(src).val());
}

function refresh(url) {
    $.get(url + "?date=" + $('.date-picker-single').val(), function (data) {
        $(".content-container").html(data);
    })
}

function refreshPage(url) {
    $.get(url + "?date=" + $('.date-picker-single').val(), function (data) {
        $(document).html(data);
    })
}

function confirmAnnouncement(id) {
    alert(id);
}

function notifyBrowser()
{
    $(document).ready(function () {
        con = $("<div>");
        $(con).addClass("alert alert-danger text-center").html("You are using an unsupported browser some features may not work correctly, Please use ORG Box app for better experience !");
        $(con).css({
            "left": "0",
            "right": "0",
            "position": "relative",
            "width": "100%",
            "padding": "5px",
            "font-size": "11px",
            "display":"none"
        })
        $("html").prepend($(con))

        setTimeout(function () {
            $(con).fadeIn(500)
        },1000)
    });
}
