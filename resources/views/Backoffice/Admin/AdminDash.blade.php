@extends("Backoffice.Admin.Master")
@section("contents")

<style>

    body {
        background: #2c3e50;
    }
    .cmd-window {
        background: #02080e;
        height: 600px;
        width: 800px;
        box-shadow: -10px 10px 0 rgba(26, 26, 26, 0.31);
        margin: 20px auto;
        color: #f5f5f5;
        font-size: 12px;
        font-family: arial;
        padding: 15px;
        
    }

    .brand {
    font-weight:bold;
    float:left;
    width:6%;
    }

    .loading-msg {
        text-transform: uppercase;
        font-size: 13px;
        line-height:.2;
        display:inline-block;
        color: green;
        font-family: Segoe UI;
        font-weight: bold;
        margin-left: 8px;
        border: 1px solid;
        border-radius: 3px;
        padding: 0 8px;
        height:13px;
        text-align:center
    }
    .cmd-active-host {
        color:red;
    margin-bottom:15px;
    }

    .cmd-prompt input {
        background:none;
    width:89%;
    border:none;
    box-shadow:none;
    float:left;
    border:none
    }

        .cmd-prompt input:focus, .cmd-prompt input:focus {
            border: none;
            box-shadow: none;
        }
    .cmd-prompt {
        max-height: 550px;
        overflow: auto;
        
    }

    .cmd-list {
        padding: 0;
        margin: 0;
        white-space: pre-line;
    }

        .cmd-list li {
            max-width:100%;
        }

        .cmd-list li.container-fluid {
            padding:0
        }

    .cmd-result {
        padding-left: 15px;
        color: #adadad;
        padding-top:6px;
        padding-bottom:6px;
    }
</style>

<script>

    var activeClient = "192.168.1.108";
    cmdPromptLocation = -1;

    $(document).ready(function () {
        $(".client-name").text(activeClient);

        $(".cmd-window").click(function () {
            $("#command").focus();
        });


        $("#cmd-form").submit(function (e) {
            e.preventDefault();

            cmd = $("#command").val();

            cmdPromptLocation = $("#command").parent().index();

            var prevCmdLi = $("<li>");
            $(prevCmdLi).text("ORG > "+cmd);

            cmds = $(".cmd-list li");
            $(cmds[cmdPromptLocation]).before(prevCmdLi);
            $("#command").val("");
        if (cmd != "clear" && cmd != "cls") {
                runCMD(cmd);
            } else {
                clearTerminal();
            }
            return;
        });
    });


    function runCMD(cmd) {
       loadingMSG = $("<span class='loading-msg'>").html("...");
        $("#command").attr("disabled","disabled");
       $(loadingMSG).appendTo($(".cmd-list li")[cmdPromptLocation]);
        cmd = "http://" + activeClient + ":3835/" + cmd;
        $.ajax({
            url:'{{action("SystemController@jsProxy")}}' ,
            type: "post",
            data: {url:cmd,_token:'{{csrf_token()}}'},
            success: function (data) {
                li = $("<li class='cmd-result'>");
                $(li).html(data).insertAfter($(".cmd-list li")[cmdPromptLocation]);
            },
            complete:function(){
                $(loadingMSG).remove();
                $("#command").removeAttr("disabled").focus();
            }
        })
    }

    function clearTerminal()
    {
        cmds = $(".cmd-list li");
        limit = cmds.length;

        $.each(cmds, function (k, v) {
            if(k+1!=limit)
            {
                cmds[k].remove();
            }
        });
    }
</script>
<div class="cmd-window">
    <div class="cmd-active-host">
        You are now talking to <span class="client-name"></span>
    </div>
    <div class="cmd-prompt">
        <form id="cmd-form" action="" method="post">
            <ul class="cmd-list">
                <li class="container-fluid"><span class="brand">ORG <i># &#x3e;</i> </span> <input autocomplete="off" id="command" type="text" /></li>
            </ul>
        </form>
    </div>
</div>

@stop
