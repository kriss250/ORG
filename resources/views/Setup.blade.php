<?php use \App\Settings; ?>
<!DOCTYPE html>
<html>
<head>
    
    <meta name="csrf-token" content="{{csrf_token() }}" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    {!! HTML::style('assets/css/vendor/bootstrap.min.css') !!}
    {!! HTML::style('assets/css/vendor/font-awesome.min.css') !!}
    {!! HTML::style('assets/css/setup.css') !!}
    {!! HTML::script('assets/js/vendor/jquery/jquery-1.11.2.min.js') !!}
    {!!HTML::script('assets/js/vendor/bootstrap/bootstrap.min.js') !!}
    <title>ORG Software Setup</title>
</head>


<body>
    <div class="contents">
        <h2 style="background:#fff;border-radius:15px;padding:25px;display:table;margin:auto" class="text-center text-danger">
            <i class="fa fa-cog"></i> ORG SETUP
        </h2>
        <hr />
        <form class="form">
            <div class="row">
                <div class="col-md-6">
                    <h4>LOGOs</h4>
                    <div class="row">
                        <div class="col-md-3">
                            <img height="60" src="" />
                        </div>
                        <div class="col-md-9">
                            <input name="logo_1" class="" type="file" />
                        </div>
                    </div>
                    <hr />
                    <div class="row">
                        <div class="col-md-3">
                            <img height="60" src="" />
                        </div>
                        <div class="col-md-9">
                            <input name="logo_2" class="" type="file" />
                        </div>
                    </div>
                    <hr />
                    <div class="row">
                        <div class="col-md-3">
                            <img height="60" src="" />
                        </div>
                        <div class="col-md-9">
                            <input name="logo_3" class="" type="file" />
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <h4>Contacts</h4>
                    <label>Phone 1</label>
                    <input name="phone_1" type="text" class="form-control" value="{{is_null($setting = Settings::get('phones')) ? "" : $setting[0]}}" />
                    <label>Phone 2</label>
                    <input name="phone_2" value="{{ is_null($setting = Settings::get('phones')) ? " " : $setting[1]}}" type="text" class="form-control" />

                    <label>Email</label>
                    <input name="email" value="{{ is_null($setting=Settings::get('email')) ? "" : $setting}}" type="text" class="form-control" />
                    <label>Website</label>
                    <input name="website" value="{{is_null($setting = Settings::get('website')) ? "" : $setting}}" type="text" class="form-control" />
                </div>
            </div>
            <hr />
            <div class="row">
                <div class="col-md-6">
                    <h4>Signatory</h4>
                    <table class="table table-condensed table-striped table-bordered">
                        <tr>
                            <th>Title</th>
                            <th>Position</th>
                        </tr>
                    </table>
                </div>

                <div class="col-md-6">
                    <h4>Bank Accounts</h4>
                    <label>Account 1</label>
                    <input type="text" class="form-control" />
                    <label>Account 2</label>
                    <input type="text" class="form-control" />
                    <label>Account 3</label>
                    <input type="text" class="form-control" />
                </div>
            </div>


            <div class="row">
                <div class="col-md-6">
                    <h4>Notifications</h4>
                    <label>Night Audit Email 1</label>
                    <input name="auemail_1" value="{{is_null($setting = Settings::get('au_email1')) ? " " : $setting}}" type="text" class="form-control" />

                    <label>Night Audit Email 2</label>
                    <input name="auemail_2" value="{{is_null($setting = Settings::get('au_email1')) ? " " : $setting}}" type="text" class="form-control" />
                </div>
            </div>

            <hr />
            <input type="submit" class="btn btn-primary" value="Save" />
        </form>
    </div>

   
</body>
</html>