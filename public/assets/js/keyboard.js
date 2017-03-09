$(document).ready(function () {
    var input = null;
    $(".k-keyboard").draggable();
    $(".k-keyboard li > a").click(function (e) {
        e.preventDefault();
        var previewContents = getPreviewContents();
        var contents = $(input).val();
        //aselection
        cursorLoc = getCursorPosition();
        previewContents = previewContents.substr(0, cursorLoc) + $(this).attr("key").toLowerCase() + previewContents.substr(cursorLoc);
        var inputText = previewContents;
        $(".input-preview input").val(inputText);

        $(input).val(inputText).trigger("keyup")
    })

    $(".hide-key-btn").click(function (e) {
        hideKeyboard();
    });
    $("body").on("focus", "input[type=text]", function () {
        var elem = $(this);
       
        if (typeof ($(this).attr("readonly")) != "undefined") return;

        if ($(this).hasClass("preview-text-box")) {
            return;
        }

        $(".preview-text-box").val($(this).val());
        input = elem;
        if ($(".keyboard-focused").length > 0) {
            $(".keyboard-focused").removeClass("keyboard-focused");
        }
        $(input).addClass("keyboard-focused");
        $(".k-keyboard").show().animate({
            "bottom": "0"
        }, 100)
    });

    $(".erase-key-btn").click(function (e) {
        e.preventDefault();
        curPos = getCursorPosition();
        contents = getPreviewContents();

        $(".preview-text-box").val(
        contents.substr(0, curPos - 1) + contents.substr(curPos)
        );

        $(input).val($(".preview-text-box").val()).trigger("keyup")
    });

    $(".pin-keyboard li").click(function (e) {
        e.preventDefault();
        var selectorQ = typeof $(this).attr("data-field") == "undefined" ?  "#waiter-pin-input" : $(this).attr("data-field");
        contents = $(selectorQ).val();
        if ($(this).hasClass("pin-delete-btn"))
        {
            $(selectorQ).val(contents.substr(0, contents.length - 1));
            return;
        }
        $(selectorQ).val(contents+$(this).attr("data-key"));
    })
});


var getCursorPosition = function () {
    return document.getElementsByClassName("preview-text-box")[0].selectionStart;
};

var getPreviewContents = function () {
    return $(".input-preview input").val();
};


var hideKeyboard = function () {
    $(".k-keyboard").hide().animate({
        "bottom": "-800px"
    }, 100);
};