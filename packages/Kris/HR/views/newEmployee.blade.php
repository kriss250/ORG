@extends("HR::Master")

@section("contents")

<script>
    $(document).ready(function () {
        $('[name="department"]').change(function () {
            var dp = $(this).val();
            getPosts(dp);
        });

        <?php echo isset($employee) ? "getPosts({$employee->department_id},{$employee->post_id})":""; ?>
    });

    function getPosts(dp,post)
    {
        $.get("{{action('\Kris\HR\Controllers\PostController@getPosts')}}/" + dp, function (data) {
            var postField = $("[name='post']");
            $(postField).html("<option value=''>Choose Post</option>");

            $.each(data, function (k, v) {
                $(postField).append("<option "+(typeof post !== "undefined" && post==v.idposts ? " selected " :"")+" value='" + v.idposts + "'>" + v.name + "</option>");
            });

        });
    }
</script>
    <div class="col-md-10 main-contents">
        <div class="page-title">
            <h3>Create a new employee</h3>
            <p style="font-size:12px;opacity:.6;margin-top:-5px;">Use this form to create a new employee and set their respective departments</p>
        </div>
        <div class="row" style="padding:10px 35px;">
            
            <form method="post"  action="{{action('\Kris\HR\Controllers\EmployeeController@store')}}" class="form form-horizontal">
                <input type="hidden" name="_token" value="{{csrf_token()}}" />
                <input type="hidden" name="_employee" value="{{isset($employee) ? $employee->idemployees : " 0" }}" />


                <div class="form-group col-md-4">
                    <label>Firstname</label>
                    <input value="{{isset($employee) ? $employee->firstname : ""}}" name="firstname" required type="text" class="form-control" placeholder="Name" />

                    <label>Lastname</label>
                    <input value="{{isset($employee) ? $employee->lastname : ""}}" name="lastname" required type="text" class="form-control" placeholder="Name" />


                    <label>Middle Name</label>
                    <input value="{{isset($employee) ? $employee->middlename : ""}}" name="mname" type="text" class="form-control" placeholder="Name" />
                    <label>Gender</label>
                    <p>
                        <input {{isset($employee)&& $employee->gender=='male' ? "checked" :""}} {{isset($employee) ? "" : "checked"}}" name="gender" value="male" type="radio" /> Male
                        <input  {{isset($employee)&& $employee->gender=='female' ? "checked" :""}} name="gender" type="radio" value="female" /> Female
                    </p>
                   
                    <div class="row">
                        <div class="col-md-6">
                            <label>Nationality</label>
                            <select name="country" required class="form-control">
                                <option value="">Choose Country</option>
                                @foreach(\App\Countries::$list as $country)
                                <option {{isset($employee) && $employee->address[0]->country==$country ? "selected" : ""}}>
                                    {{$country}}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label>City</label>
                            <input value="{{isset($employee) ? $employee->address[0]->city : ""}}" type="text" name="city" class="form-control" />
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <label>Birth Date</label>
                            <input value="{{isset($employee) ? $employee->birthdate : ""}}" type="text" name="birthdate" class="form-control" />
                        </div>

                        <div class="col-md-6">
                            <label>Birth Place</label>
                            <input value="{{isset($employee) ? $employee->birth_place : ""}}" type="text" name="birth_place" class="form-control" />
                        </div>
                    </div>


                    <label>ID / Passport</label>
                    <input value="{{isset($employee) ? $employee->id_passport : ""}}" required name="id_passport" type="text" placeholder="#" class="form-control" />
                </div>

                <div class="form-grou col-md-5" style="padding:0 30px">
                    <label>Father's Name</label>
                    <input value="{{isset($employee) ? $employee->father_name : ""}}" name="father" required type="text" class="form-control" placeholder="First and Last Name" />
                    <label>Mother's Name</label>
                    <input value="{{isset($employee) ? $employee->mother_name : ""}}" name="mother" required type="text" class="form-control" placeholder="First and Last Name" />

                    <label>Telephone</label>
                    <input value="{{isset($employee) ? $employee->contact[0]->phone1 : ""}}" name="phone" type="tel" class="form-control" placeholder="Phone number" />

                    <label>Email</label>
                    <input value="{{isset($employee) ? $employee->contact[0]->email1 : ""}}" name="email" type="email" class="form-control" placeholder="Email address" />
                    <label>General Description</label>
                    <textarea name="description" class="form-control" placeholder="Describe the employee">{{isset($employee) ? $employee->description : ""}}</textarea>
                </div>

                <div class="form-group col-md-3">
                    <label>Hire date</label>
                    <input value="{{isset($employee) ? $employee->hire_date : ""}}" name="hire_date" type="tel" class="form-control" placeholder="Y-m-d" />
                    <label>Department</label>
                    <select name="department" required class="form-control">
                        <option value="">Choose Department</option>
                        @foreach(\Kris\HR\Models\Department::all() as $dp)
                        <option  {{isset($employee)&& $employee->department_id==$dp->iddepartments ? "selected" :""}} value="{{$dp->iddepartments}}">{{$dp->name}}</option>
                        @endforeach
                    </select>
                    <label>Post</label>
                    <select name="post" required class="form-control">
                        <option value="">Choose Post</option>
                    </select>
                    <label>Highest Degree</label>
                    <select required name="degree" class="form-control">
                        <option value="">Choose Degree</option>
                        <option {{isset($employee) && $employee->highest_degree== \Kris\HR\Models\Degree::NONE ? "selected" : ""}} value="{{\Kris\HR\Models\Degree::NONE}}">None</option>
                        <option {{isset($employee) && $employee->highest_degree== \Kris\HR\Models\Degree::PRIMARY ? "selected" : ""}} value="{{\Kris\HR\Models\Degree::PRIMARY}}">Primary School</option>
                        <option {{isset($employee) && $employee->highest_degree== \Kris\HR\Models\Degree::SECONDARY ? "selected" : ""}} value="{{\Kris\HR\Models\Degree::SECONDARY}}">Secondary School</option>
                        <option {{isset($employee) && $employee->highest_degree== \Kris\HR\Models\Degree::COLLEGE ? "selected" : ""}} value="{{\Kris\HR\Models\Degree::COLLEGE}}">College</option>
                        <option {{isset($employee) && $employee->highest_degree== \Kris\HR\Models\Degree::BACHELOR ? "selected" : ""}} value="{{\Kris\HR\Models\Degree::BACHELOR}}">Bachelors'</option>
                        <option {{isset($employee) && $employee->highest_degree== \Kris\HR\Models\Degree::MASTERS ? "selected" : ""}} value="{{\Kris\HR\Models\Degree::MASTERS}}">Masters</option>
                        <option {{isset($employee) && $employee->highest_degree== \Kris\HR\Models\Degree::DOCTOR ? "selected" : ""}} value="{{\Kris\HR\Models\Degree::DOCTOR}}">Doctorate</option>
                        <option {{isset($employee) && $employee->highest_degree== \Kris\HR\Models\Degree::PROFESSOR ? "selected" : ""}} value="{{\Kris\HR\Models\Degree::PROFESSOR}}">Professor</option>
                    </select>
                    <label>Salary</label>
                    <?php  
                    if(isset($employee)){
                       
                        $sals = $employee->salary;
                        $sal = $sals!= null && isset($sals{count($sals)-1}) ?  $sals{count($sals)-1} :null;
                    }
                    ?>
                    <input required value="{{isset($sal) && $sal!=null ? $sal->amount : ""}}" name="salary" type="number" class="form-control" placeholder="#" />
                </div>
                
                <div class="clearfix"></div>
                <hr />
                <input type="submit" value="Save" class="btn btn-success" />
            </form>
        </div>
    </div>

@stop