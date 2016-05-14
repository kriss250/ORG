$(document).ready(function () {

    $('#myTabs a').click(function (e) {
        e.preventDefault();
        $(this).tab('show');
    })

    $(".status_filter").click(function (e) {
        e.preventDefault();
        var ElClass = $(this).attr("data-target");
        $(".tab-pane > .room_item.hidden").removeClass("hidden");
        if (ElClass == "all") { return;}
        $(".tab-pane > .room_item:not(." + ElClass + ")").addClass("hidden");
    })

    

})
