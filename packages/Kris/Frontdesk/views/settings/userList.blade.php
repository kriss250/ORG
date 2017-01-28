@extends("Frontdesk::MasterIframe")

@section("contents")

<script type="text/javascript">
    initSelectBoxes();
</script>
<style>
    .inline-fieldsets fieldset {
        display: block !important;
        width: 100%;
        background: rgb(253,253,253);
        border-radius: 0;
    }
</style>
<div style="padding:10px 10px;background:#fff" class="inline-fieldsets modal-header">

    <h4>Users List</h4>
    <p class="subtitle" style="margin-top:-5px;">List of users, you can block and unblock users as well as reset their passwords</p>
  
</div>

<div style="overflow:hidden" class="modal-content">
   <table class="table table-striped table-condensed table-bordered">
       
           <tr>
               <th>ID</th>
               <th>Names</th>
               <th>Username</th>
               <th>Creation Date</th>
               <th>Action</th>
           </tr>
       

       @foreach(\Kris\Frontdesk\User::all() as $user)
       <tr {!!$user->is_active==0 ? 'style="text-decoration:line-through;" ' :""!!}>
           <td>{{$user->idusers}}</td>
           <td>{{$user->firstname}} {{$user->lastname}}</td>
           <td>{{$user->username}}</td>
           <td>{{$user->date}}</td>
           <td class="text-center">
               <a style="margin-right:6px" class="text-danger" href=""><i class="fa fa-ban"></i></a>
               <a href="">
                   <i class="fa fa-key"></i>
               </a>
           </td>
       </tr>
       @endforeach
   </table>
</div>

<script>
    $(document).ready(function(){
    @if(session('msg'))
        alert("{{session('msg')}}");
    @elseif(session('errors'))
        error('{{session("errors")->first()}}');
    @endif
    });

</script>
@stop