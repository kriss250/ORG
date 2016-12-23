<?php $prop = \App\Resto::get()->first(); ?>
<style>
    body {
    font-family:"Georgia"
    }
</style>
<table style="width:100%">
<tr>
    <td style="width:85px;padding-right:15px">
        <img src="/uploads/images/{{$prop->logo_image}}" width="100" />
    </td>
    <td>
        <h4 style="margin-bottom:0">Cashbook Transaction</h4>
        {{$prop->resto_name}}<br />
        {{$prop->website}}
    </td>

    <td class="text-right">
        Date : 
    <b>{{$tr[0]->date}}</b><br>
    </td>

</tr>
</table>

<p>&nbsp;</p>

<h1 style="text-align:center;border:1px solid;display:table;margin:auto;padding:20px;">
    Voucher #
{{
$tr[0]->transactionid
}}
</h1>
<br />
<?php
$c = new \NumberFormatter("en",NumberFormatter::SPELLOUT);
$c2 = new \NumberFormatter("en",NumberFormatter::DECIMAL);
?>

<table style="width:100%">
    <tr>
        <td>
            <div style="border:1px solid;padding:15px;display:table;font-size:23px;">
                {{$c2->format($tr[0]->amount)}} {{$tr[0]->cashbook_name}}
            </div>
        </td>
        <td>
            <div style="border:1px dotted;padding:15px;display:table;font-size:18px;">
                {{$tr[0]->type}}
            </div>
        </td>
    </tr>
</table>
<br />
<h3 style="text-decoration:dotted">
   <em style="font-weight:normal">Amount in Words :</em> <b style="text-transform:capitalize; border-bottom:1px dotted">{{$c->format($tr[0]->amount)}} </b>
</h3>

<hr />
<p>Description : </p>
<b style="text-decoration:dotted">{{$tr[0]->motif}}</b>
<br />
<br />
Received By : {{$tr[0]->receiver}}
<br />
<br />
<hr />

<table style="width:100%">
    <tr>
        <td>
            Receiver Signature
            <br />
            {{$tr[0]->receiver}}
            <br />
            <p>__________________</p>

        </td>

        <td style="text-align:right">
            User Signature
            <br />
            {{$tr[0]->firstname}} {{$tr[0]->lastname}}
            <br />
            <p>__________________</p>
        </td>
    </tr>
</table>