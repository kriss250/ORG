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


$(document).ready(function () {

    $(".modal-btn").click(function (e) {
        e.preventDefault();
        window.open($(this).attr("href"),"_blank","width=920,height=600");
    })
    var pageTitle = $(".page-title");
    if(typeof pageTitle !== "undefined")
    {
        $("<a href='" + window.baseUrl + "'>")
            .html("<i class='fa fa-close'></i>")
            .addClass("page-close-btn")
            .css({"top":(($(".page-title").height()/2)-18)+"px"})
            .appendTo($(pageTitle));
    }

    //Size 

    var menuHeight = $(".main-menu").height();
    var windowHeight = window.screen.height;
    var titleHeight = $(".main-contents .page-title").height();
    titleHeight = typeof titleHeight === "undefined" ? 0 : titleHeight;
    $(".main-contents > .row").css({ "overflow": "auto", "margin-bottom": "15px" }).height(windowHeight - menuHeight - titleHeight - 180);
    $(".body-tab-content").height(windowHeight - menuHeight - titleHeight - 280)
    $('body').on('focus', '.datepicker', function () {
        var minDate = typeof $(this).attr("data-mindate") != "undefined" ? $(this).attr("data-mindate") : null;
        $(this).datepicker({
            format: "yyyy-mm-dd",
            autoclose: true,
            startDate: minDate,
            todayHighlight: true
        });
    });
});
