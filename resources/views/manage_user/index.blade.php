@extends('layouts.app')
@section('title', __('user.users'))
@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang( 'user.users' )<small>@lang( 'user.manage_users' )</small></h1>
</section>
<!-- Main content -->
<section class="content">
    <div class="boxform_u box-solid_u">
        <div class="box-header">
            <h3 class="box-title">@lang('user.all_users')</h3>
            @can('user.create')
            <div class="box-tools">
                <button type="button" class="btn btn-block btn-primary" data-backdrop="static" data-keyboard="false" data-toggle='modal' data-target='#modalAddUser' id="btnAddUser"><i class="fa fa-plus"></i> @lang('messages.add')</button>
            </div>
            @endcan
        </div>
        <div class="box-body">
            @can('user.view')
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-condensed table-hover" id="users_table" width="100%">
                    <thead>
                        <tr>
                            <th>@lang('user.name')</th>
                            <th>@lang('business.username')</th>
                            <th>@lang('user.role')</th>
                            <th>@lang('business.email')</th>
                            <th>@lang('messages.actions')</th>
                        </tr>
                    </thead>
                </table>
            </div>
            @endcan
        </div>
    </div>
</section>
<div class="modal fade" id="modalAddUser" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius: 20px;">
            <div class="modal-header">
                <h3 class="modal-title">@lang('user.add_user')</h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    {!! Form::open(['id' => 'user_add_form']) !!}
                    <input type="hidden" name="_token" value="{{ csrf_token() }}" id="token">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="first_name">@lang('business.first_name')</label>
                            <input type="text" id="first_name" name="first_name" class="form-control" placeholder="@lang('business.first_name')" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="last_name">@lang('business.last_name')</label>
                            <input type="text" id="last_name" name="last_name" class="form-control" placeholder="@lang('business.last_name')">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="email">@lang('business.email')</label>
                            <input type="text" id="email" name="email" class="form-control" placeholder="@lang('business.email')" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('username', __( 'business.username' ) . ':') !!}
                            @if(!empty($username_ext))
                            <div class="input-group">
                                {!! Form::text('username', null, ['id' => 'username', 'class' => 'form-control', 'placeholder' => __( 'business.username' ) ]); !!}
                                <span class="input-group-addon">{{$username_ext}}</span>
                            </div>
                            <p class="help-block" id="show_username"></p>
                            @else
                            {!! Form::text('username', null, ['id' => 'username', 'class' => 'form-control', 'placeholder' => __( 'business.username' ) ]); !!}
                            @endif
                            <p class="help-block">@lang('lang_v1.username_help')</p>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('role', __( 'user.role' ) . ':*') !!}
                            {!! Form::select('role', $roles, null, ['id' => 'role', 'class' => 'form-control']); !!}
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="custom-control custom-radio">
                            <input type="radio" class="custom-control-input" value="manual" id="manual_mode" name="password_mode" checked>
                            <label class="custom-control-label" for="password_mode">@lang('lang_v1.label_password_manual')</label>
                            @show_tooltip(__('lang_v1.tooltip_enable_password_manual'))
                            <input type="radio" class="custom-control-input" value="generated" id="automatic_mode" name="password_mode">
                            <label class="custom-control-label" for="password_mode">@lang('lang_v1.label_password_automatic')</label>
                            @show_tooltip(__('lang_v1.tooltip_enable_password_generated'))
                        </div>
                    </div>
                    <div id="div_password">
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('password', __( 'business.password' ) . ':*') !!}                
                                {!! Form::password('password', ['id' => 'password', 'class' => 'form-control', 'required', 'placeholder' => __( 'business.password' ) ]); !!}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('confirm_password', __( 'business.confirm_password' ) . ':*') !!}
                                {!! Form::password('confirm_password', ['id' => 'confirm_password', 'class' => 'form-control', 'required', 'placeholder' => __( 'business.confirm_password' ) ]); !!}
                            </div>
                        </div>
                    </div>                   
                    
                    {{-- business --}}
                    <div class="col-md-12">
                        <label>Empresas:</label>
                    </div>

                    @foreach ($business as $bs)
                    <div class="col-md-4">
                        <div class="form-group">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('business[]', $bs->id, null,
                                    ['class' => 'input-icheck', 'id' => 'business[]']); !!} {{ $bs->name }}
                                </label>
                            </div>
                        </div>
                    </div>
                    @endforeach

                    <div id="content" class="col-md-12" style="display: none;">
                        <img src="{{ asset('img/loader.gif') }}" alt="loading" />
                    </div>
                    
                    <div class="col-md-12" style="margin-top: 10px;">
                        <button type="button" class="btn btn-sm btn-primary" id="add_user_button">@lang( 'messages.save' )</button>
                        <button type="button" class="btn btn-sm btn-danger" id="close_modal_button">@lang( 'messages.close' )</button>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div><
    </div>
</div>
<div class="modal fade" id="modalEditUser" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius: 20px;">
            <div class="modal-header">
                <h3 class="modal-title">@lang('user.edit_user')</h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    {!! Form::open(['id' => 'user_edit_form']) !!}                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="efirst_name">@lang('business.first_name')</label>
                            <input type="text" id="efirst_name" name="efirst_name" class="form-control" placeholder="@lang('business.first_name')" required>
                            <input type="hidden" name="user_id" id="user_id">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="elast_name">@lang('business.last_name')</label>
                            <input type="text" id="elast_name" name="elast_name" class="form-control" placeholder="@lang('business.last_name')">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="eemail">@lang('business.email')</label>
                            <input type="text" required id="eemail" name="eemail" class="form-control" placeholder="@lang('business.email')" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('erole', __( 'user.role' ) . ':*') !!}
                            {!! Form::select('erole', $roles, null, ['id' => 'erole', 'class' => 'form-control']); !!}
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="custom-control custom-radio">
                            <input type="radio" class="custom-control-input" value="manual" id="emanual_mode" name="epassword_mode" checked>
                            <label class="custom-control-label" for="epassword_mode">@lang('lang_v1.label_password_manual')</label>
                            @show_tooltip(__('lang_v1.tooltip_enable_password_manual'))
                            <input type="radio" class="custom-control-input" value="generated" id="eautomatic_mode" name="epassword_mode">
                            <label class="custom-control-label" for="epassword_mode">@lang('lang_v1.label_password_automatic')</label>
                            @show_tooltip(__('lang_v1.tooltip_enable_password_generated'))
                        </div>
                    </div>
                    <div id="ediv_password">
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('epassword', __( 'business.password' ) . ':*') !!}
                                {!! Form::password('epassword', ['id' => 'epassword', 'class' => 'form-control', 'required', 'placeholder' => __( 'business.password' ) ]); !!}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('econfirm_password', __( 'business.confirm_password' ) . ':*') !!}
                                {!! Form::password('econfirm_password', ['id' => 'econfirm_password', 'class' => 'form-control', 'required', 'placeholder' => __( 'business.confirm_password' ) ]); !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="eis_active">{{ __('lang_v1.status_user') }}</label>
                            <select name="eis_active" id="eis_active" class="form-control">
                                <option value = 'active'>@lang('user.option_active')</option>
                                <option value = 'inactive'>@lang('user.option_inactive')</option>
                                <option value = 'pending'>@lang('user.option_pending')</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-8">
                    </div>

                    <div id="econtent" class="col-md-12" style="display: none;">
                        <img src="{{ asset('img/loader.gif') }}" alt="loading" />
                    </div>

                    <div class="col-md-12">
                        <button type="button" class="btn btn-sm btn-primary" id="eadd_user_button">@lang( 'messages.save' )</button>
                        <button type="button" class="btn btn-sm btn-danger" id="eclose_modal_button">@lang( 'messages.close' )</button>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div><
    </div>
</div>
<div class="modal fade" id="modalEditPassword" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius: 20px;">
            <div class="modal-header">
                <h3 class="modal-title">@lang('user.edit_user')</h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    {!! Form::open(['id' => 'password_edit_form']) !!}
                    <div class="col-md-12">
                        <span id="full_name"></span>
                    </div>
                    <div class="col-md-12">
                        <div class="custom-control custom-radio">
                            <input type="radio" class="custom-control-input" value="manual" id="rmanual_mode" name="rpassword_mode" checked>
                            <label class="custom-control-label" for="rpassword_mode">@lang('lang_v1.label_password_manual')</label>
                            @show_tooltip(__('lang_v1.tooltip_enable_password_manual'))
                            <input type="radio" class="custom-control-input" value="generated" id="rautomatic_mode" name="rpassword_mode">
                            <label class="custom-control-label" for="rpassword_mode">@lang('lang_v1.label_password_automatic')</label>
                            @show_tooltip(__('lang_v1.tooltip_enable_password_generated'))
                        </div>
                    </div>
                    <div id="rdiv_password">
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('rpassword', __( 'business.password' ) . ':*') !!}
                                {!! Form::password('rpassword', ['id' => 'rpassword', 'class' => 'form-control', 'required', 'placeholder' => __( 'business.password' ) ]); !!}
                                <input type="hidden" name="euser_id" id="euser_id">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('rconfirm_password', __( 'business.confirm_password' ) . ':*') !!}
                                {!! Form::password('rconfirm_password', ['id' => 'rconfirm_password', 'class' => 'form-control', 'required', 'placeholder' => __( 'business.confirm_password' ) ]); !!}
                            </div>
                        </div>
                    </div>
                    <div id="rcontent" class="col-md-12" style="display: none;">
                        <img src="{{ asset('img/loader.gif') }}" alt="loading" />
                    </div>

                    <div class="col-md-12">
                        <button type="button" class="btn btn-sm btn-primary" id="radd_user_button">@lang( 'messages.save' )</button>
                        <button type="button" class="btn btn-sm btn-danger" id="rclose_modal_button">@lang( 'messages.close' )</button>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div><
    </div>
</div>
<div class="modal fade" id="modalViewUser" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius: 20px;">
            <div class="modal-header">
                <h3 class="modal-title">@lang('user.view_user')</h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-condensed table-hover">
                            <tr>
                                <td>@lang('business.first_name')</td>
                                <td><span id="lblfirt_name"></span></td>
                            </tr>
                            <tr>
                                <td>@lang('business.last_name')</td>
                                <td><span id="lbllast_name"></span></td>
                            </tr>
                            <tr>
                                <td>@lang('business.username')</td>
                                <td><span id="lblusername"></span></td>
                            </tr>
                            <tr>
                                <td>@lang('business.email')</td>
                                <td><span id="lblemail"></span></td>
                            </tr>
                            <tr>
                                <td>@lang('lang_v1.language_user')</td>
                                <td><span id="lbllanguage"></span></td>
                            </tr>
                            <tr>
                                <td>@lang('lang_v1.status_user')</td>
                                <td><span id="lblstatus"></span></td>
                            </tr>
                            <tr>
                                <td>@lang('user.role')</td>
                                <td><span id="lblrol"></span></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-12">
                        <button type="button" class="btn btn-sm btn-danger" id="vclose_modal_button">@lang( 'messages.close' )</button>
                    </div>
                </div>
            </div>
        </div><
    </div>
</div>
<!-- /.content -->
@endsection
@section('javascript')
<script type="text/javascript">
    $(document).ready( function(){
        var table = $("#users_table").DataTable(
        {
            columnDefs: [{ "visible": false, "targets": [3] }],
            processing: true,
            serverSide: true,
            ajax: "/users/getUsersData",
            columns: [
            {data: 'full_name'},
            {data: 'username'},
            {data: 'rol'},
            {data: 'email'},
            {data: null, render: function(data){
                var actions_button = '<div class="btn-group"><button type="button" class="btn btn-primary btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">@lang("messages.actions")<span class="caret"></span></button><ul class="dropdown-menu"> @can("user.view")<li><a href="#" OnClick="viewUser('+ data.id +');"><i class="fa fa-eye"></i>@lang("messages.view")</a></li>@endcan  @can("user.update")<li><a href="#" OnClick="editUser('+ data.id +');"><i class="glyphicon glyphicon-edit"></i>@lang("messages.edit")</a></li>@endcan @can("user.update")<li><a href="#" OnClick="changePasswordUser('+ data.id +');"><i class="glyphicon glyphicon-edit"></i>@lang("messages.change_password")</a></li> @endcan @can("user.delete")<li><a href="#" OnClick="deleteUser('+ data.id +');"><i class="fa fa-trash"></i>@lang("messages.delete")</a></li></ul>@endcan</div>';
                return actions_button;
            } , orderable: false, searchable: false}
            ]
            //dom: 'Bfltip',
        });
        $.fn.dataTable.ext.errMode = 'none';
    });
    function viewUser(id)
    {
        var route = "/users/"+id;
        $.get(route, function(res){            
            $("#lblfirt_name").text(res.first_name);
            $("#lbllast_name").text(res.last_name);
            $("#lblusername").text(res.username);
            $("#lblemail").text(res.email);
            $("#lbllanguage").text(res.language);
            $("#lblstatus").text(res.status);
            $("#lblrol").text(res.rol);
            

            $('#modalViewUser').modal({backdrop: 'static', keyboard: false});
        });
    }
    function editUser(id)
    {
        limpiar2();
        $("#emanual_mode").prop("checked", true);
        $("#epassword").val('');
        $("#econfirm_password").val('');
        $("#ediv_password").show();
        $('#eadd_user_button').prop("disabled", true);
        $('#eclose_modal_button').prop("disabled", true);
        $("#econtent").show();
        var route = "/users/"+id+"/edit";
        $.get(route, function(res){
            $("#user_id").val(res.id);
            $("#efirst_name").val(res.first_name);
            $("#elast_name").val(res.last_name);
            $("#eemail").val(res.email);
            $("#eis_active").val(res.status);
            $("#erole").val(res.rol_id);
            $('#eadd_user_button').prop("disabled", false);
            $('#eclose_modal_button').prop("disabled", false);
            $("#econtent").hide();
        });
        $('#modalEditUser').modal({backdrop: 'static', keyboard: false});
    }
    function changePasswordUser(id)
    {
        var route = "/users/"+id+"/edit";
        $.get(route, function(res){
            $("#ris_active").val(res.status);
            $("#full_name").text(""+res.first_name+" ("+res.username+")");
            $("#euser_id").val(id);
            validator = $("#password_edit_form").validate();
            validator.resetForm();
            $("#rmanual_mode").prop("checked", true);
            $("#rpassword").val('');
            $("#rconfirm_password").val('');
            $("#rdiv_password").show();
            $('#modalEditPassword').modal({backdrop: 'static', keyboard: false});
            $("#rpassword").focus();
        });
    }
    function deleteUser(id)
    {
        swal({
            title: LANG.sure,
            text: '{{__('messages.delete_content')}}',
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete){
                route = '/users/'+id;
                token = $("#token").val();
                $.ajax({
                    url: route,
                    headers: {'X-CSRF-TOKEN': token},
                    type: 'DELETE',
                    dataType: 'json',                       
                    success:function(){
                        $("#users_table").DataTable().ajax.reload(null, false);
                        Swal.fire
                        ({
                            title: "{{__('user.user_delete_success')}}",
                            icon: "success",
                        });
                    }
                });
            }
        });
    }
    
    $("#close_modal_button").click(function(){
        $('#modalAddUser').modal('hide');
    });
    $("#eclose_modal_button").click(function(){
        $('#modalEditUser').modal('hide');
    });
    $("#rclose_modal_button").click(function(){
        $('#modalEditPassword').modal('hide');
    });
    $("#vclose_modal_button").click(function(){
        $('#modalViewUser').modal('hide');
    });
    $("#btnAddUser").click(function(){
        limpiar();
        $("#manual_mode").prop("checked", true);
        $("#password").val('');
        $("#confirm_password").val('');
        $("#div_password").show();
        setTimeout(function()
        {
            $('#first_name').focus();
        },
        800);
    });
    $('form#user_add_form').validate(
    {
        rules:
        {
            first_name:
            {
                required: true,
            },
            email:
            {
                email: true
            },
            password:
            {
                required: true,
                minlength: 5
            },
            confirm_password:
            {
                equalTo: "#password"
            },
            username:
            {
                minlength: 5,
                remote:
                {
                  url: "/business/register/check-username",
                  type: "post",
                  data:{
                    username: function(){
                        return $( "#username" ).val();
                    },
                    @if(!empty($username_ext))                    
                    username_ext: "{{$username_ext}}"
                    @endif}
                }
            }
        },
        messages:
        {
            password:
            {
                required: 'Este valor es requerido',
                minlength: 'Contraseña debe tener 5 caracteres como mínimo',
            },
            confirm_password:
            {
                equalTo: 'Debe repetir la contaseña'
            },
            username:
            {
                remote: 'Usuario inválido o ya existente'
            }
        }
    });
    $('form#user_edit_form').validate(
    {
        rules:
        {
            efirst_name:
            {
                required: true,
            },
            eemail:
            {
                email: true
            },
            epassword:
            {
                required: true,
                minlength: 5
            },
            econfirm_password:
            {
                equalTo: "#epassword"
            }
        },
        messages:
        {
            epassword:
            {
                required: 'Este valor es requerido',
                minlength: 'Contraseña debe tener 5 caracteres como mínimo',
            },
            econfirm_password:
            {
                equalTo: 'Debe repetir la contaseña'
            }
        }
    });
    $('form#password_edit_form').validate(
    {
        rules:
        {
            rpassword:
            {
                required: true,
                minlength: 5
            },
            rconfirm_password:
            {
                equalTo: "#rpassword"
            }
        },
        messages:
        {
            rpassword:
            {
                required: 'Este valor es requerido',
                minlength: 'Contraseña debe tener 5 caracteres como mínimo',
            },
            rconfirm_password:
            {
                equalTo: 'Debe repetir la contaseña'
            }
        }
    });
    $('#username').change( function(){
        if($('#show_username').length > 0)
        {
            if($(this).val().trim() != '')
            {
                $('#show_username').html("{{__('lang_v1.your_username_will_be')}}: <b>" + $(this).val() + "{{$username_ext}}</b>");
            }
            else
            {
                $('#show_username').html('');
            }
        }
    });
    $("#manual_mode").click(function(){
        $("#password").val('');
        $("#confirm_password").val('');
        $("#div_password").show();
        $("#password").focus();
    });
    $("#automatic_mode").click(function(){
        $("#div_password").hide();
        $("#password").val('generated');
        $("#confirm_password").val('generated');
    });
    $("#emanual_mode").click(function(){
        $("#epassword").val('');
        $("#econfirm_password").val('');
        $("#ediv_password").show();
        $("#epassword").focus();
    });
    $("#eautomatic_mode").click(function(){
        $("#ediv_password").hide();
        $("#epassword").val('generated');
        $("#econfirm_password").val('generated');
    });
    $("#rmanual_mode").click(function(){
        $("#rpassword").val('');
        $("#rconfirm_password").val('');
        $("#rdiv_password").show();
        $("#rpassword").focus();
    });
    $("#rautomatic_mode").click(function(){
        $("#rdiv_password").hide();
        $("#rpassword").val('generated');
        $("#rconfirm_password").val('generated');
    });
    $("#add_user_button").click(function(){
        $('#add_user_button').prop("disabled", true);
        $('#close_modal_button').prop("disabled", true);
        $('#content').show();
        datastring = $("#user_add_form").serialize();
        route = "/users";
        token = $("#token").val();
        $.ajax({
            url: route,
            headers: {'X-CSRF-TOKEN': token},
            type: 'POST',
            dataType: 'json',
            data: datastring,
            success:function(){
                $('#add_user_button').prop("disabled", false);
                $('#close_modal_button').prop("disabled", false);
                limpiar();
                $("#users_table").DataTable().ajax.reload(null, false);
                $('#modalAddUser').modal('hide');
                Swal.fire
                ({
                    title: "{{__('user.user_added')}}",
                    icon: "success",
                });
                $('#content').hide();
            },
            error:function(msj){
                var errormessages = "";
                $.each(msj.responseJSON.errors, function(i, field){
                    errormessages+="<li>"+field+"</li>";
                });
                Swal.fire
                ({
                    title: "{{__('messages.errors')}}",
                    icon: "error",
                    html: "<ul>"+ errormessages+ "</ul>",
                });

                $('#add_user_button').prop("disabled", false);
                $('#close_modal_button').prop("disabled", false);
                $('#content').hide();
            }
        });
    });
    $("#eadd_user_button").click(function(){
        $('#eadd_user_button').prop("disabled", true);
        $('#eclose_modal_button').prop("disabled", true);
        $('#econtent').show();        
        id = $("#user_id").val();
        first_name = $("#efirst_name").val();
        last_name = $("#elast_name").val();
        email = $("#eemail").val();
        role = $("#erole").val();

        

        if ($("#emanual_mode").is(':checked')) {
            password_mode = 'manual';
        }

        if ($("#eautomatic_mode").is(':checked')) {
            password_mode = 'generated';
        }


        password = $("#epassword").val();
        confirm_password = $("#econfirm_password").val();
        is_active = $("#eis_active").val();
        route = "/users/"+id;
        token = $("#token").val();
        $.ajax({
            url: route,
            headers: {'X-CSRF-TOKEN': token},
            type: 'PUT',
            dataType: 'json',
            data: {
                first_name: first_name,
                user_id: id,
                last_name: last_name,
                email: email,
                role: role,
                password_mode: password_mode,
                password: password,
                confirm_password: confirm_password,
                is_active: is_active
            },
            success:function(){
                $('#eadd_user_button').prop("disabled", false);
                $('#eclose_modal_button').prop("disabled", false);
                limpiar2();
                $("#users_table").DataTable().ajax.reload(null, false);
                $('#modalEditUser').modal('hide');
                Swal.fire
                ({
                    title: "{{__('user.user_update_success')}}",
                    icon: "success",
                });
                $('#econtent').hide();
            },
            error:function(msj){
                var errormessages = "";
                $.each(msj.responseJSON.errors, function(i, field){
                    errormessages+="<li>"+field+"</li>";
                });
                Swal.fire
                ({
                    title: "{{__('messages.errors')}}",
                    icon: "error",
                    html: "<ul>"+ errormessages+ "</ul>",
                });

                $('#eadd_user_button').prop("disabled", false);
                $('#eclose_modal_button').prop("disabled", false);
                $('#econtent').hide();
            }
        });
    });
    $("#radd_user_button").click(function(){
        $('#radd_user_button').prop("disabled", true);
        $('#rclose_modal_button').prop("disabled", true);
        $('#rcontent').show();        
        id = $("#euser_id").val();

        

        if ($("#rmanual_mode").is(':checked')) {
            password_mode = 'manual';
        }

        if ($("#rautomatic_mode").is(':checked')) {
            password_mode = 'generated';
        }


        password = $("#rpassword").val();
        confirm_password = $("#rconfirm_password").val();
        is_active = $("#ris_active").val();
        route = "/users/changePassword";
        token = $("#token").val();
        $.ajax({
            url: route,
            headers: {'X-CSRF-TOKEN': token},
            type: 'POST',
            dataType: 'json',
            data: {
                user_id: id,
                password_mode: password_mode,
                password: password,
                confirm_password: confirm_password,
                is_active: is_active
            },
            success:function(){
                $('#radd_user_button').prop("disabled", false);
                $('#rclose_modal_button').prop("disabled", false);                
                $("#users_table").DataTable().ajax.reload(null, false);
                $('#modalEditPassword').modal('hide');
                Swal.fire
                ({
                    title: "{{__('user.user_update_success')}}",
                    icon: "success",
                });
                $('#rcontent').hide();
            },
            error:function(msj){
                var errormessages = "";
                $.each(msj.responseJSON.errors, function(i, field){
                    errormessages+="<li>"+field+"</li>";
                });
                Swal.fire
                ({
                    title: "{{__('messages.errors')}}",
                    icon: "error",
                    html: "<ul>"+ errormessages+ "</ul>",
                });

                $('#radd_user_button').prop("disabled", false);
                $('#rclose_modal_button').prop("disabled", false);
                $('#rcontent').hide();
            }
        });
        $.fn.dataTable.ext.errMode = 'none';
    });
    function limpiar()
    {
        $("#first_name").val('');
        $("#last_name").val('');
        $("#username").val('');
        $("#email").val('');
        $("#password").val('');
        $("#confirm_password").val('');
        var validator = $("#user_add_form").validate();
        validator.resetForm();
    }

    function limpiar2()
    {
        $("#efirst_name").val('');
        $("#elast_name").val('');
        $("#eemail").val('');
        $("#epassword").val('');
        $("#econfirm_password").val('');
        var validator = $("#user_edit_form").validate();
        validator.resetForm();
    }
</script>
@endsection