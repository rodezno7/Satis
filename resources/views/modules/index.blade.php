@extends('layouts.app')
@section('title', __( 'user.modules_permissions' ))
@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('user.modules_permissions' )</h1>
</section>

<!-- Main content -->
<section class="content">
    <div id="div_modules">
        <div class="boxform_u box-solid_u">
            <div class="box-header">
                <h3 class="box-title">@lang( 'user.all_your_modules' )</h3>
                @can('module.create')
                <div class="box-tools">
                    <button id="new_module" type="button" class="btn btn-block btn-primary" data-backdrop="static" data-keyboard="false" data-toggle='modal' data-target='#modalAddModule'><i class="fa fa-plus"></i> @lang('messages.add')</button>
                </div>
                @endcan
            </div>
            <div class="box-body">
                @can('module.view')
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="module_table" width="100%">
                        <thead>
                            <tr>
                                <th>@lang( 'role.module_name' )</th>
                                <th>@lang( 'role.module_description' )</th>
                                {{-- <th>@lang( 'role.status' )</th> --}}
                                <th>@lang( 'messages.action' )</th>
                            </tr>
                        </thead>
                    </table>
                </div>
                @endcan
            </div>
        </div>
    </div>

    <div id="div_permissions">
        <div class="boxform_u box-solid_u">
            <div class="box-header">
                <h3 class="box-title">@lang( 'user.all_your_permissions' )</h3>
                @can('permission.create')
                <div class="box-tools">
                    <button id="new_permission" type="button" class="btn btn-block btn-primary" data-backdrop="static" data-keyboard="false" data-toggle='modal' data-target='#modalAddPermission'><i class="fa fa-plus"></i> @lang('messages.add')</button>
                </div>
                @endcan
            </div>
            <div class="box-body">
                @can('permission.view')
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="permission_table" width="100%">
                        <thead>
                            <tr>
                                <th>@lang( 'role.module_name' )</th>
                                <th>@lang( 'role.module_description' )</th>
                                <th>@lang( 'role.module' )</th>
                                <th>@lang( 'messages.action' )</th>
                            </tr>
                        </thead>
                    </table>
                </div>
                @endcan
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalAddModule" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content" style="border-radius: 20px;">
                @can('module.create')
                {!! Form::open(['id' => 'module_add_form']) !!}
                <input type="hidden" name="_token" value="{{ csrf_token() }}" id="token">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h3 class="modal-title">@lang('role.add_module')</h3>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="module_name">@lang('role.module_name')</label>
                        <input type="text" id="module_name" name="module_name" class="form-control" placeholder="@lang('role.module_name')" required>
                    </div>
                    <div class="form-group">
                        <label for="module_description">@lang('role.module_description')</label>
                        <input type="text" id="module_description" name="module_description" class="form-control" placeholder="@lang('role.module_description')" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="add_module_button">@lang( 'messages.save' )</button>
                    <button type="button" class="btn btn-default" id="close_modal_add_module_button" data-dismiss="modal">@lang( 'messages.close' )</button>
                </div>
                {!! Form::close() !!}
                @endcan
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalEditModule" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content" style="border-radius: 20px;">
                @can('module.update')
                {!! Form::open(['id' => 'module_edit_form']) !!}
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h3 class="modal-title">@lang('role.edit_module')</h3>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="module_ename">@lang('role.module_name')</label>
                        <input type="text" id="module_ename" name="module_ename" class="form-control" placeholder="@lang('role.module_name')" readonly>
                        <input type="hidden" name="module_id" id="module_id">
                    </div>
                    <div class="form-group">
                        <label for="module_edescription">@lang('role.module_description')</label>
                        <input type="text" id="module_edescription" name="module_edescription" class="form-control" placeholder="@lang('role.module_description')" required>
                    </div>
                    {{-- <div class="form-group">
                        <select name="status" id="status" class="form-control select2">
                            <option value="1">@lang('user.option_active')</option>
                            <option value="0">@lang('user.option_inactive')</option>
                        </select>
                    </div> --}}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="edit_module_button">@lang( 'messages.save' )</button>
                    <button type="button" class="btn btn-default" id="close_modal_edit_module_button" data-dismiss="modal">@lang( 'messages.close' )</button>
                </div>
                {!! Form::close() !!}
                @endcan
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalAddPermission" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content" style="border-radius: 20px;">
                @can('permission.create')
                {!! Form::open(['id' => 'permission_add_form']) !!}
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h3 class="modal-title">@lang('role.add_permission')</h3>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="permission_name">@lang('role.module_name')</label>
                        <input type="text" id="permission_name" name="permission_name" class="form-control" placeholder="@lang('role.module_name')" required>
                    </div>
                    <div class="form-group">
                        <label for="permission_description">@lang('role.module_description')</label>
                        <input type="text" id="permission_description" name="permission_description" class="form-control" placeholder="@lang('role.module_description')" required>
                    </div>
                    <div class="form-group">
                        <label for="select_module_id">@lang('role.module')</label>
                        {!! Form::select('select_module_id', [], null, ['style' => 'width: 100%', 'class' => 'form-control select2', 'id' => 'select_module_id', 'placeholder' => __('messages.please_select'), 'required']); !!}
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="add_permission_button">@lang( 'messages.save' )</button>
                    <button type="button" class="btn btn-default" id="close_modal_add_permission_button" data-dismiss="modal">@lang( 'messages.close' )</button>
                </div>
                {!! Form::close() !!}
                @endcan
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalEditPermission" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content" style="border-radius: 20px;">
                @can('permission.update')
                {!! Form::open(['id' => 'permission_edit_form']) !!}
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h3 class="modal-title">@lang('role.edit_permission')</h3>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="permission_ename">@lang('role.module_name')</label>
                        <input type="text" id="permission_ename" name="permission_ename" class="form-control" placeholder="@lang('role.module_name')" required>
                        <input type="hidden" name="permission_id" id="permission_id">
                    </div>
                    <div class="form-group">
                        <label for="permission_edescription">@lang('role.module_description')</label>
                        <input type="text" id="permission_edescription" name="permission_edescription" class="form-control" placeholder="@lang('role.module_description')" required>
                    </div>
                    <div class="form-group">
                        <label for="eselect_module_id">@lang('role.module')</label>
                        {!! Form::select('eselect_module_id', [], null, ['style' => 'width: 100%', 'class' => 'form-control select2', 'id' => 'eselect_module_id', 'placeholder' => __('messages.please_select'), 'required']); !!}
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="edit_permission_button">@lang( 'messages.save' )</button>
                    <button type="button" class="btn btn-default" id="close_modal_edit_permission_button" data-dismiss="modal">@lang( 'messages.close' )</button>
                </div>
                {!! Form::close() !!}
                @endcan
            </div>
        </div>
    </div>


</div>

</section>
<!-- /.content -->

@endsection
@section('javascript')
<script type="text/javascript">

    $.fn.modal.Constructor.prototype.enforceFocus = function() {};

    // Carga de datatables
    $(document).ready( function(){        
        var tableModules = $("#module_table").DataTable();
        tableModules.destroy();
        var tableModules = $("#module_table").DataTable(
        {
            processing: true,
            deferRender: true,
            serverSide: true,
            ajax: "/modules/getModulesData",
            columns: [
            {data: 'name', name: 'module.name'},
            {data: 'description', name: 'module.description'},
            // {data: null, render: function(data){
            //     if(data.status == 1){
            //         return 'Activo';
            //     }
            //     else{
            //         return 'Inactivo';
            //     }
            // } , orderable: false, searchable: false},
            {data: null, render: function(data){
                edit_button = '@can('module.update')<a class="btn btn-xs btn-primary" onClick="editModule('+data.id+')"><i class="glyphicon glyphicon-edit"></i>@lang('messages.edit')</a>@endcan';
                delete_button = ' @can('module.delete')<a class="btn btn-xs btn-danger" onClick="deleteModule('+data.id+')"><i class="glyphicon glyphicon-trash"></i>@lang('messages.delete')</a>@endcan';
                return edit_button + delete_button;
            } , orderable: false, searchable: false}
            ]
        });

        var tablePermissions = $("#permission_table").DataTable();
        tablePermissions.destroy();
        var tablePermissions = $("#permission_table").DataTable(
        {
            processing: true,
            deferRender: true,
            serverSide: true,
            ajax: "/permissions/getPermissionsData",
            columns: [
            {data: 'name', name: 'permission.name'},
            {data: 'description', name: 'permission.description'},
            {data: 'module', name: 'module.name'},
            {data: null, render: function(data){
                edit_button = '@can('permission.update')<a class="btn btn-xs btn-primary" onClick="editPermission('+data.id+')"><i class="glyphicon glyphicon-edit"></i>@lang('messages.edit')</a>@endcan';
                delete_button = ' @can('permission.delete')<a class="btn btn-xs btn-danger" onClick="deletePermission('+data.id+')"><i class="glyphicon glyphicon-trash"></i>@lang('messages.delete')</a>@endcan';
                return edit_button + delete_button;
            } , orderable: false, searchable: false}
            ]
        });

        $.fn.dataTable.ext.errMode = 'none';
        updateModules();
    });

    //Botón nuevo módulo
    $("#new_module").click(function(){
        validator.resetForm();
        $('#module_name').removeClass('error');
        $("#module_description").removeClass('error');
        $('#module_name').val('');
        $("#module_description").val('');
        setTimeout(function()
        {
            $('#module_name').focus();
        },
        800);
    });

    //Botón nuevo permiso
    $("#new_permission").click(function(){
        validatorAddPermission.resetForm();
        $('#permission_name').removeClass('error');
        $("#permission_description").removeClass('error');
        $('#permission_name').val('');
        $("#permission_description").val('');
        setTimeout(function()
        {
            $('#permission_name').focus();
        },
        800);
    });

    //Validación de formularios
    validator = $('form#module_add_form').validate(
    {
        rules:
        {
            module_name:
            {
                required: true,
            },
            module_description:
            {
                required: true,
            }
        }
    });

    validatorEdit = $('form#module_edit_form').validate(
    {
        rules:
        {
            // module_ename:
            // {
            //     required: true,
            // },
            module_edescription:
            {
                required: true,
            }
        }
    });

    validatorAddPermission = $('form#permission_add_form').validate(
    {
        rules:
        {
            permission_name:
            {
                required: true,
            },
            permission_description:
            {
                required: true,
            },
            select_module_id:
            {
                required: true,
            }
        }
    });

    validatorEditPermission = $('form#permission_edit_form').validate(
    {
        rules:
        {
            permission_ename:
            {
                required: true,
            },
            permission_edescription:
            {
                required: true,
            },
            eselect_module_id:
            {
                required: true,
            }
        }
    });

    //Botón agregar módulo
    $("#add_module_button").click(function(){
        if($("form#module_add_form").valid()){
            $('#add_module_button').prop("disabled", true);
            $('#close_modal_add_module_button').prop("disabled", true);
            name = $("#module_name").val();
            description = $("#module_description").val();
            route = "/modules";
            token = $("#token").val();
            $.ajax({
                url: route,
                headers: {'X-CSRF-TOKEN': token},
                type: 'POST',
                dataType: 'json',
                data: {
                    name: name,
                    description: description
                },
                success:function(result){
                    if (result.success == true){
                        updateModules();
                        $('#add_module_button').prop("disabled", false);
                        $('#close_modal_add_module_button').prop("disabled", false);

                        validator.resetForm();

                        $("#module_table").DataTable().ajax.reload(null, false);
                        $("#permission_table").DataTable().ajax.reload(null, false);
                        $("#modalAddModule").modal('hide');
                        Swal.fire
                        ({
                            title: ""+result.msg+"",
                            icon: "success",
                        });
                    }
                    else{
                        Swal.fire
                        ({
                            title: ""+result.msg+"",
                            icon: "error",
                        });
                        $('#add_module_button').prop("disabled", false);
                        $('#close_modal_add_module_button').prop("disabled", false);
                    }
                }
            });
        }
    });

    //Función editar módulo
    function editModule(id)
    {
        validatorEdit.resetForm();
        $('#module_ename').removeClass('error');
        $("#module_edescription").removeClass('error');
        $('#module_id').val('');
        $('#module_ename').val('');
        $('#module_edescription').val('');
        var route = "/modules/"+id+"/edit";
        $.get(route, function(res){
            $('#module_id').val(res.id);
            $('#module_ename').val(res.name);
            $('#module_edescription').val(res.description);
            $('#status').val(res.status).change();

        });
        $('#modalEditModule').modal({backdrop: 'static', keyboard: false});
    }

    //Botón editar módulo
    $("#edit_module_button").click(function(){
        if($("form#module_edit_form").valid()){
            $('#edit_module_button').prop("disabled", true);
            $('#close_modal_edit_module_button').prop("disabled", true);
            id = $("#module_id").val();
            name = $("#module_ename").val();
            description = $("#module_edescription").val();
            status = $("#status").val();
            route = "/modules/"+id;
            token = $("#token").val();
            $.ajax({
                url: route,
                headers: {'X-CSRF-TOKEN': token},
                type: 'PUT',
                dataType: 'json',
                data: {
                    name: name,
                    description: description,
                    status: status
                },
                success:function(result){
                    if (result.success == true){
                        updateModules();
                        $('#edit_module_button').prop("disabled", false);
                        $('#close_modal_edit_module_button').prop("disabled", false);
                        validatorEdit.resetForm();
                        $("#module_table").DataTable().ajax.reload(null, false);
                        $("#permission_table").DataTable().ajax.reload(null, false);
                        $("#modalEditModule").modal('hide');
                        Swal.fire
                        ({
                            title: ""+result.msg+"",
                            icon: "success",
                        });
                    }
                    else{
                        Swal.fire
                        ({
                            title: ""+result.msg+"",
                            icon: "error",
                        });
                        $('#edit_module_button').prop("disabled", false);
                        $('#close_modal_edit_module_button').prop("disabled", false);
                    }
                }
            });
        }
    });

    //Eliminar Módulo
    function deleteModule(id){
        swal({
            title: LANG.sure,
            text: '{{__('messages.delete_content')}}',
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete){
                route = '/modules/'+id;
                token = $("#token").val();
                $.ajax({
                    url: route,
                    headers: {'X-CSRF-TOKEN': token},
                    type: 'DELETE',
                    dataType: 'json',                       
                    success:function(result){
                        if(result.success == true){
                            updateModules();
                            Swal.fire
                            ({
                                title: result.msg,
                                icon: "success",
                            });
                            $("#module_table").DataTable().ajax.reload(null, false);
                        } else {
                            Swal.fire
                            ({
                                title: result.msg,
                                icon: "error",
                            });
                        }
                    }
                });
            }
        });
    }

    //Llenar select de modulos
    function updateModules()
    {
        $("#select_module_id").empty();
        $("#eselect_module_id").empty();
        var route = "/modules/getModules";
        $.get(route, function(res){
            $(res).each(function(key,value){
                $("#select_module_id").append('<option value="'+value.id+'">'+value.name+'</option>');
                $("#eselect_module_id").append('<option value="'+value.id+'">'+value.name+'</option>');
            });
        });
    }

    //Botón agregar permiso
    $("#add_permission_button").click(function(){
        if($("form#permission_add_form").valid()){
            $('#add_permission_button').prop("disabled", true);
            $('#close_modal_add_permission_button').prop("disabled", true);
            name = $("#permission_name").val();
            description = $("#permission_description").val();
            guard_name = 'web';
            module_id = $("#select_module_id").val();
            route = "/permissions";
            token = $("#token").val();
            $.ajax({
                url: route,
                headers: {'X-CSRF-TOKEN': token},
                type: 'POST',
                dataType: 'json',
                data: {
                    name: name,
                    description: description,
                    guard_name: guard_name,
                    module_id: module_id
                },
                success:function(result){
                    if (result.success == true){
                        $('#add_permission_button').prop("disabled", false);
                        $('#close_modal_add_permission_button').prop("disabled", false);

                        validatorAddPermission.resetForm();
                        
                        $("#permission_table").DataTable().ajax.reload(null, false);
                        $("#modalAddPermission").modal('hide');
                        Swal.fire
                        ({
                            title: ""+result.msg+"",
                            icon: "success",
                        });
                    }
                    else{
                        Swal.fire
                        ({
                            title: ""+result.msg+"",
                            icon: "error",
                        });
                        $('#add_permission_button').prop("disabled", false);
                        $('#close_modal_add_permission_button').prop("disabled", false);
                    }
                }
            });
        }
    });

    //Eliminar permiso
    function deletePermission(id){
        swal({
            title: LANG.sure,
            text: '{{__('messages.delete_content')}}',
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete){
                route = '/permissions/'+id;
                token = $("#token").val();
                $.ajax({
                    url: route,
                    headers: {'X-CSRF-TOKEN': token},
                    type: 'DELETE',
                    dataType: 'json',                       
                    success:function(result){
                        if(result.success == true){
                            Swal.fire
                            ({
                                title: result.msg,
                                icon: "success",
                            });
                            $("#permission_table").DataTable().ajax.reload(null, false);
                        } else {
                            Swal.fire
                            ({
                                title: result.msg,
                                icon: "error",
                            });
                        }
                    }
                });
            }
        });
    }

    //Función editar permiso
    function editPermission(id)
    {
        validatorEditPermission.resetForm();
        updateModules();
        $('#permission_ename').removeClass('error');
        $("#permission_edescription").removeClass('error');
        $('#permission_id').val('');
        $('#permission_ename').val('');
        $('#permission_edescription').val('');
        var route = "/permissions/"+id+"/edit";
        $.get(route, function(res){
            $('#permission_id').val(res.id);
            $('#permission_ename').val(res.name);
            $('#permission_edescription').val(res.description);
            $('#eselect_module_id').val(res.module_id).change();
        });
        $('#modalEditPermission').modal({backdrop: 'static', keyboard: false});
    }

    //Botón editar permiso
    $("#edit_permission_button").click(function(){
        if($("form#permission_edit_form").valid()){
            $('#edit_permission_button').prop("disabled", true);
            $('#close_modal_edit_permission_button').prop("disabled", true);
            id = $("#permission_id").val();
            name = $("#permission_ename").val();
            description = $("#permission_edescription").val();
            module_id = $("#eselect_module_id").val();
            route = "/permissions/"+id;
            token = $("#token").val();
            $.ajax({
                url: route,
                headers: {'X-CSRF-TOKEN': token},
                type: 'PUT',
                dataType: 'json',
                data: {
                    name: name,
                    description: description,
                    module_id: module_id
                },
                success:function(result){
                    if (result.success == true){
                        $('#edit_permission_button').prop("disabled", false);
                        $('#close_modal_edit_permission_button').prop("disabled", false);
                        validatorEditPermission.resetForm();
                        $("#permission_table").DataTable().ajax.reload(null, false);
                        $("#modalEditPermission").modal('hide');
                        Swal.fire
                        ({
                            title: ""+result.msg+"",
                            icon: "success",
                        });
                    }
                    else{
                        Swal.fire
                        ({
                            title: ""+result.msg+"",
                            icon: "error",
                        });
                        $('#edit_permission_button').prop("disabled", false);
                        $('#close_modal_edit_permission_button').prop("disabled", false);
                    }
                }
            });
        }
    });
    
</script>
@endsection