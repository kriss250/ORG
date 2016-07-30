@extends("Frontdesk::MasterIframe")

@section("contents")

<h4>Guest Edit</h4>
<form>
    <fieldset>
        <label>Firstname</label>
        <input type="text" name="todate" value="{{$guest->firstname}}" class="form-control" placeholder="Names of the guest" />
    </fieldset>

    <fieldset>
        <label>Lastname</label>
        <input type="text" name="todate" value="{{$guest->lastname}}" class="form-control" placeholder="Names of the guest" />
    </fieldset>


    <fieldset>
        <label>Phone</label>
        <input type="text" name="todate" value="{{$guest->phone}}" class="form-control" placeholder="Names of the guest" />
    </fieldset>

    <fieldset>
        <label>Email</label>
        <input type="text" name="todate" value="{{$guest->email}}" class="form-control" placeholder="Names of the guest" />
    </fieldset>

    <fieldset>
        <label>ID/Passport</label>
        <input type="text" name="todate" value="{{$guest->id_doc}}" class="form-control" placeholder="Names of the guest" />
    </fieldset>

    <fieldset>
        <label>Birthdate</label>
        <input type="text" name="todate" value="{{$guest->birtdate}}" class="form-control datepicker" placeholder="Names of the guest" />
    </fieldset>

    <input type="submit" value="Save" class="btn btn-primary" />
</form>

@stop