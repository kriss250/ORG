<table style="width:100%">
<tr>
    <td style="width:85px">
        <img src="/images/logo.jpg" width="80" />
    </td>
    <td>
        <h4 style="margin-bottom:0">Report</h4>
        Classic Hotel Kigali<br />
        Sonatubes
    </td>

    <td class="text-right">
    <b>Date ...../...../.......</b><br>
       Report Date : <b>{{ $_GET['date_range'] }}</b> <br />
       User :   {{ \Auth::user()->username }}

    </td>

</tr>
</table>

<div style="border:2px dashed #000;padding:5px;text-align:center;margin-top:6px;margin-bottom: 10px">
    <h4 style="margin:0;font-size:16px;font-weight:bold;padding:0;text-transform:uppercase">{{ $_GET['title'] }}</h4>
</div>