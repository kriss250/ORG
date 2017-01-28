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

    <h4>Create a new user</h4>
    <p class="subtitle" style="margin-top:-5px;">Create a new user and assign a Role</p>
  
</div>

<div style="overflow:hidden;padding:30px" class="modal-content">
    <form action=""  method="post">
        <input type="hidden" value="{{csrf_token()}}" name="_token" />
        <p>Firstname</p>
        <input type="text" name="firstname" class="form-control" placeholder="Firstname" />

        <p>Lastname</p>
        <input name="lastname" type="text" class="form-control" placeholder="Lastname" />
        <p>&nbsp;</p>
        <fieldset>
            <label>Choose Role</label>
            <div class="select-wrapper">

                <i class="fa fa-angle-down"></i>
                <select required name="role">
                    <option value="">Choose</option>
                    <option value="{{\Kris\Frontdesk\UserGroup::VIEWER}}">Viewer</option>
                    <option value="{{\Kris\Frontdesk\UserGroup::HOUSEKEEPER}}">Housekeeper</option>
                    <option value="{{\Kris\Frontdesk\UserGroup::RECEPTIONIST}}">Receptionist</option>
                    <option value="{{\Kris\Frontdesk\UserGroup::SUPERVISOR}}">Supervisor</option>
                    <option value="{{\Kris\Frontdesk\UserGroup::FOMANAGER}}">F.O Managerr</option>
                    <option value="{{\Kris\Frontdesk\UserGroup::MANAGER}}">Manager</option>
                    <option value="{{\Kris\Frontdesk\UserGroup::ADMIN}}">Admin</option>
                </select>
            </div>
        </fieldset>

        <p>Username</p>
        <input name="username" type="text" class="form-control" placeholder="Unique nickname" />

        <p>Password</p>
        <input name="password" type="password" class="form-control" placeholder="P##1" />

        <p>Repeat Password</p>
        <input type="password" name="password2" class="form-control" placeholder="P##2" />
        <p>&nbsp;</p>
        <input type="submit" value="Save" class="btn btn-success" />
        <p style="margin-bottom:2px">&nbsp;</p>
    </form>
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