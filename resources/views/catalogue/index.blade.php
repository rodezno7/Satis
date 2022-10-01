@extends('layouts.app')
@section('title', __('accounting.tittle_catalogue'))
<link rel="stylesheet" type="text/css" href="{{ asset('accounting/css/jquery-confirm.min.css') }}"/>
<link rel="stylesheet" type="text/css" href="{{ asset('accounting/css/themes/default/style.min.css') }}"/>
<link rel="stylesheet" type="text/css" href="{{ asset('accounting/css/bootstrap-toggle.min.css') }}">
@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('accounting.catalogue_menu')
        <small>@lang('accounting.all_your_accounting_accounts')</small>
    </h1>
    <!-- <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
        <li class="active">Here</li>
    </ol> -->
</section>
<!-- Main content -->
<section class="content">
    <div class="panel with-nav-tabs panel-default boxform_u box-solid_u">
        <div class="panel-heading">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#tab-catalogue" data-toggle="tab">@lang('accounting.tittle_catalogue')</a></li>
                <li><a href="#tab-report" data-toggle="tab">@lang('accounting.report')</a></li>
                @can('catalogue.import')
                <li><a href="#tab-import" data-toggle="tab">@lang('accounting.catalogue_import')</a></li>
                @endcan
            </ul>
        </div>
        <div class="panel-body">
            <div class="tab-content">
                <div class="tab-pane fade in active" id="tab-catalogue">
                    <div class="box">
                        <div class="box-body">           
                            <div id="tree">
                               <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                <input type="text" id="search" class="form-control" placeholder="@lang('accounting.search')">
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                <input type="checkbox" data-toggle="toggle" data-size="sm" data-on="@lang('accounting.contract')" data-off="@lang('accounting.expand')" data-onstyle="danger" data-offstyle="success" id="toggle_accounts">
                                
                                <button type="button" class="btn btn-primary" data-toggle='modal' data-target='#modal-add-account' id="btn-new-account"><i class="fa fa-plus"></i> @lang( 'accounting.add_group' )
                                </button>


                            </div>
                            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12" style="margin-top: 10px">
                                <input type="hidden" name="node_id" id="node_id">
                                <div id="container">
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12" style="display: none;" id="info">
                                <div class="card">
                                    <div class="card-body">
                                        <h4>@lang('accounting.account_info')</h4>
                                        <table class="table">
                                            <tr>
                                                <td>@lang('accounting.date')</td>
                                                <td>
                                                    <div class="wrap-inputform">
                                                        <span class="input-group-addon">
                                                            <i class="fa fa-calendar"></i>
                                                        </span>
                                                        {!! Form::date('date', \Carbon\Carbon::now()->format('Y-m-d'), ['name'=>'date', 'id'=>'date', 'class'=>'inputform2']) !!}
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>@lang('accounting.code')</td>
                                                <td><span id="code_account"></span></td>
                                            </tr>
                                            <tr>
                                                <td>@lang('accounting.name')</td>
                                                <td><span id="name_account"></span></td>
                                            </tr>
                                            <tr>
                                                <td>@lang('accounting.balance_type')</td>
                                                <td><span id="type_account"></span></td>
                                            </tr>
                                            <tr>
                                                <td>@lang('accounting.balance_to_date')</td>
                                                <td><span id="balance_account"></span></td>
                                            </tr>
                                            <tr>
                                                <td>@lang('accounting.level')</td>
                                                <td><span id="level_account"></span></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>                  
                            </div>
                        </div>



                        <div class="modal fade" tabindex="-1" id="modal-add-account" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document" style="width: 30%">
                                <div class="modal-content" style="border-radius: 20px;">
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

                                                <h3><span id="new_account"></span></h3>
                                                <form id='form_catalogue'>
                                                    <input type="hidden" name="_token" value="{{ csrf_token() }}" id="token">
                                                    <div class="form-group" id="div_code">
                                                        <label>@lang('accounting.code')</label>
                                                        <input type="text" id="code" name="code" class="form-control" placeholder="@lang('accounting.code')" readonly>
                                                        <input type="hidden" name="level" id="level">
                                                    </div>
                                                    <div class="form-group">
                                                        <label>@lang('accounting.name')</label>
                                                        <input type="text" id="name" name="name" class="form-control" placeholder="@lang('accounting.name')">
                                                    </div>
                                                    <div class="form-group" style="display: none;" id="div_type">
                                                        <label>@lang('accounting.balance_type')</label>
                                                        <select name="type" id="type" class="form-control select2" style="width: 100%;">
                                                            <option value="0" disabled selected>@lang('messages.please_select')</option>
                                                            <option value="debtor">@lang('accounting.debtor')</option>
                                                            <option value="creditor">@lang('accounting.creditor')</option>
                                                        </select>
                                                    </div>
                                                    <div class="form-group" id="div_parent" style="display: none;">
                                                        <label>@lang('accounting.account_parent')</label>
                                                        <select name="parent" id="parent" class="form-control select2"  style="width: 100%;">
                                                            <option value="0" disabled selected>@lang('messages.please_select')</option>
                                                            @foreach($catalogue as $account)
                                                            <option value="{{$account->id}}">{{$account->full_name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="form-group" id="div_parent2" style="display: none;">
                                                        <label for="parent2">@lang('accounting.account_parent')</label>
                                                        <input type="text" id="parent2" name="parent2" class="form-control" readonly>
                                                    </div>
                                                </form>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <input type="button" class="btn btn-primary" value="@lang('messages.save')" id="btn-add-account">
                                        <button type="button" class="btn btn-danger" data-dismiss="modal" id="btn-close-modal-add-account">@lang('messages.close')</button>
                                    </div>
                                </div>
                            </div>
                        </div>




                        <div class="modal fade" tabindex="-1" id="modal-edit-account" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document" style="width: 30%">
                                <div class="modal-content" style="border-radius: 20px;">
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

                                             <h3>@lang('accounting.account_edit')</h3>
                                             {!! Form::open(['id'=>'edit_catalogue']) !!}
                                             <input type="hidden" name="_token" value="{{ csrf_token() }}" id="token">
                                             <div class="form-group">
                                                <label for="code-e">@lang('accounting.code')</label>
                                                <input type="text" id="code-e" name="code-e" class="form-control" placeholder="@lang('accounting.code')">
                                                <input type="hidden" id="id-e" name="id-e">
                                                <input type="hidden" id="flag-edit" name="flag-edit" value="0">
                                            </div>
                                            <div class="form-group">
                                                <label for="name">@lang('accounting.name')</label>
                                                <input type="text" id="name-e" name="name-e" class="form-control" placeholder="@lang('accounting.name')">
                                            </div>
                                            <div class="form-group" style="display: none;" id="div_type_e">
                                                <label for="type-e">@lang('accounting.balance_type')</label>
                                                <select name="type-e" id="type-e" class="form-control select2" style="width: 100%;">
                                                    <option value="0" selected disabled>@lang('messages.please_select')</option>
                                                    <option value="debtor">@lang('accounting.debtor')</option>
                                                    <option value="creditor">@lang('accounting.creditor')</option>
                                                </select>
                                            </div>
                                            <div class="form-group" style="display: none;" id="div_parent_e">
                                                <div class="form-group">
                                                    <label for="parent_id-e">@lang('accounting.account_parent')</label>
                                                    <select name="parent_id-e" id="parent_id-e" class="form-control select2" style="width: 100%;">
                                                    </select>

                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="status">@lang('accounting.status')</label>
                                                <select name="status" id="status" class="form-control select2" style="width: 100%;">
                                                    <option value="1">@lang('accounting.active')</option>
                                                    <option value="0">@lang('accounting.inactive')</option>
                                                </select>
                                            </div>
                                            {!!Form::close()!!}     



                                        </div>
                                    </div>

                                    <div class="row">
                                        <div id="content" class="col-lg-12" style="display: none;">
                                            @lang('accounting.wait_please')
                                            <img src="{{ asset('img/loader.gif') }}" alt="loading" />
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <input type="button" class="btn btn-primary" value="@lang('messages.save')" id="btn-edit-account">
                                    <button type="button" class="btn btn-danger" data-dismiss="modal" id="btn-close-modal-edit-account">@lang('messages.close')</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane fade" id="tab-report">
            <div class="boxform_u box-solid_u">
                <div class="box-header">
                    <h3 class="box-title">@lang( 'accounting.export_catalogue' )</h3>
                </div>
                <div class="box-body">
                    <div class="row" style="margin-bottom: 10px;">
                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                            <label>@lang('accounting.clasification')</label>
                            <select name="filter_clasification" id="filter_clasification" class="form-control select2" style="width: 100%;">
                                <option value="0">@lang('accounting.all')</option>
                                @foreach($clasifications as $item)
                                <option value="{{ $item->code }}">{{ $item->full_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-condensed table-hover" id="catalogue-table" width="100%">
                            <thead>
                                <th>@lang('accounting.code')</th>
                                <th>@lang('accounting.name')</th>
                                <th>@lang('accounting.account_parent')</th>
                                <th>@lang('accounting.balance_type')</th>
                                <th>@lang('accounting.level')</th>
                                <th>@lang('accounting.status')</th>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        @can('catalogue.import')

        <div class="tab-pane fade" id="tab-import">
            <div class="boxform_u box-solid_u">
                <div class="box-header">
                    <h3 class="box-title">@lang( 'accounting.catalogue_import' )</h3>
                </div>
                <div class="box-body">
                    <form id="form_catalogue" method="post" role="form" accept-charset="UTF-8" enctype="multipart/form-data">
                        <div class="row" style="margin-bottom: 10px;">
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">

                                <label>@lang('accounting.file')</label>
                                <input type="file" name='catalogue_file' id='catalogue_file'>

                            </div>

                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12" style="margin-top: 20px;">
                                <button type="submit" id='btn_import' class="btn btn-primary">
                                    @lang('accounting.import')
                                </button>
                            </div>
                        </div>


                        <div class="row">
                            <div class="col-sm-4">
                                <a href="
                                @if(auth()->user()->language == 'es')
                                {{ asset('uploads/files/import_catalogue_xlsx_template_es.xlsx') }}
                                @endif
                                @if(auth()->user()->language == 'en')
                                {{ asset('uploads/files/import_catalogue_xlsx_template_en.xlsx') }}
                                @endif
                                " class="btn btn-success" download><i class="fa fa-download"></i> @lang('accounting.download_xlsx_file_template')</a>
                            </div>
                        </div>
                    </form>

                    <div class="row">
                        <div class="col-sm-12">
                            <div class="box box-solid">
                                <div class="box-header">
                                  <h3 class="box-title">@lang('lang_v1.instructions')</h3>
                              </div>
                              <div class="box-body">
                                <strong>@lang('lang_v1.instruction_line1')</strong><br>
                                <ol style="margin-left: 20px;">
                                    <li style="list-style-type: decimal;">@lang('accounting.catalogue_ins_1')</li>
                                    <li style="list-style-type: decimal;">@lang('accounting.catalogue_ins_8')</li>
                                    <li style="list-style-type: decimal;">@lang('accounting.catalogue_ins_2')</li>
                                </ol>
                                <br>
                                <table class="table table-striped">
                                    <tr>
                                        <th>@lang('lang_v1.col_no')</th>
                                        <th>@lang('lang_v1.col_name')</th>
                                        <th>@lang('lang_v1.instruction')</th>
                                    </tr>
                                    <tr>
                                        <td>1</td>
                                        <td>@lang('accounting.code') <small class="text-muted">(@lang('lang_v1.required'))</small></td>
                                        <td>@lang('accounting.catalogue_ins_3')</td>
                                    </tr>
                                    <tr>
                                        <td>2</td>
                                        <td>@lang('accounting.name') <small class="text-muted">(@lang('lang_v1.optional'))</small></td>
                                        <td>@lang('accounting.catalogue_ins_4')</td>
                                    </tr>
                                    <tr>
                                        <td>3</td>
                                        <td>@lang('accounting.account_parent') <small class="text-muted">(@lang('lang_v1.required'))</small></td>
                                        <td>@lang('accounting.catalogue_ins_5')</td>
                                    </tr>

                                     <tr>
                                        <td>4</td>
                                        <td>@lang('accounting.type') <small class="text-muted">(@lang('lang_v1.required'))</small></td>
                                        <td>@lang('accounting.catalogue_ins_6') <small class="text-muted">@lang('accounting.catalogue_ins_7')</small></td>
                                    </tr>
                                    
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        @endcan

    </div>
</div>
</div>
</section>
<!-- /.content -->
@endsection
@section('javascript')
<script type="text/javascript" src="{{ asset('accounting/js/jquery-confirm.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('accounting/js/jstree.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('accounting/js/formatCurrency.js') }}"></script>
<script type="text/javascript" src="{{ asset('accounting/js/bootstrap-toggle.min.js') }}"></script>
<script>

    //Registro de Cuenta

    $("#btn-add-account").click(function() {

        $('#btn-add-account').prop("disabled", true);
        $('#btn-close-modal-add-account').prop("disabled", true);
        $('#btn-new-account').prop("disabled", true);

        code = $("#code").val();
        name = $("#name").val();
        type = $("#type").val();        
        status = 1;
        if($("#level").val() == "") {
            level = 1;
        }
        else {
            level = $("#level").val();
        }

        if($("#parent").val() != null) {
            parent = $("#parent").val();
        }
        else {
            parent = 0;
        }
        
        route = "/catalogue";
        token = $("#token").val();
        $.ajax({
            url: route,
            headers: {'X-CSRF-TOKEN': token},
            type: 'POST',
            dataType: 'json',
            data: {
                code: code,
                name: name,
                parent: parent,
                type: type,
                status: status,
                level: level
            },
            success:function(result){
                if (result.success == true) {
                    $('#container').jstree(true).refresh();
                    update_catalogo();
                    $('#btn-add-account').prop("disabled", false);
                    $('#btn-close-modal-add-account').prop("disabled", false);
                    $('#btn-new-account').prop("disabled", false);
                    $("#modal-add-account").modal('hide');
                    Swal.fire
                    ({
                        title: result.msg,
                        icon: "success",
                    });
                    $("#catalogue-table").DataTable().ajax.reload(null, false);
                } else {

                    $('#btn-add-account').prop("disabled", false);
                    $('#btn-close-modal-add-account').prop("disabled", false);
                    $('#btn-new-account').prop("disabled", false);
                    Swal.fire
                    ({
                        title: result.msg,
                        icon: "error",
                    });
                }
                
            },
            error:function(msj){
                $('#btn-add-account').prop("disabled", false);
                $('#btn-close-modal-add-account').prop("disabled", false);
                $('#btn-new-account').prop("disabled", false);
                var errormessages = "";
                $.each(msj.responseJSON.errors, function(i, field){
                    errormessages+="<li>"+field+"</li>";
                });
                Swal.fire
                ({
                    title: "{{__('accounting.errors')}}",
                    icon: "error",
                    html: "<ul>"+ errormessages+ "</ul>",
                });
            }
        });
    });

    //Boton Nueva Cuenta

    $("#btn-new-account").click(function() {
        $('#div_parent').hide();
        $('#div_parent2').hide();
        $('#div_type').show();
        $("#code").val('');
        $("#name").val('');
        $("#level").val('');
        $("#type").val('0').change();
        $('#new_account').text("@lang('accounting.new_clasification')");
        $("#code").prop('readonly', false);
        setTimeout(function()
        {
            $("#code").focus();
        },
        800);
    });

    // Actualiza select de cuentas padres para agregar
    function update_catalogo()
    {
        $("#parent").empty();
        $("#parent_id-e").empty();
        var route = "/catalogue/getAccounts";
        $.get(route, function(res){
            $("#parent").append('<option value="0" selected disabled>@lang('messages.please_select')</option>');
            $("#parent_id-e").append('<option value="0" selected disabled>@lang('messages.please_select')</option>');
            $(res).each(function(key,value){
                $("#parent").append('<option value="'+value.id+'">'+value.full_name+'</option>');
                $("#parent_id-e").append('<option value="'+value.id+'">'+value.full_name+'</option>');
            });
        });
    }

    //Botón Editar Cuenta

    $("#btn-edit-account").click(function() {

        $("#btn-edit-account").prop("disabled", true);
        $('#btn-close-modal-edit-account').prop("disabled", true);
        $('#btn-new-account').prop("disabled", true);

        id = $("#id-e").val();
        code = $("#code-e").val();
        name = $("#name-e").val();
        parent = $("#parent_id-e").val();
        type = $("#type-e").val();
        status = $("#status").val();
        route = "/catalogue/"+id;
        token = $("#token").val();

        if(parent == 0) {

            var routeClasif = "/catalogue/verifyClasif/"+code;
            $.get(routeClasif, function(resClasif){
                if(resClasif.valor == 1) {

                    updateAccount();
                } else {

                    $("#btn-edit-account").prop('disabled', false);
                    $("#btn-close-modal-edit-account").prop('disabled', false);
                    $("#btn-new-account").prop('disabled', false);
                    Swal.fire
                    ({
                        title: "{{__('accounting.invalid_code')}}",
                        icon: "error",
                    });
                }
            });

        } else {

            var routeCode = "/catalogue/verifyCode/"+id+"/"+code;
            $.get(routeCode, function(resCode){
                if(resCode.valor == 1) {

                    updateAccount();
                } else {

                    $("#btn-edit-account").prop('disabled', false);
                    $("#btn-close-modal-edit-account").prop('disabled', false);
                    $("#btn-new-account").prop('disabled', false);
                    Swal.fire
                    ({
                        title: "{{__('accounting.invalid_code')}}",
                        icon: "error",
                    });
                }
            });
        }

    });

    function updateAccount() {

        $.ajax({
            url: route,
            headers: {'X-CSRF-TOKEN': token},
            type: 'PUT',
            dataType: 'json',
            data: {
                code: code,
                name: name,
                parent: parent,
                type: type,
                status: status
            },
            success:function(result) {
                if (result.success == true) {
                    $('#container').jstree(true).refresh();
                    update_catalogo();
                    $('#btn-edit-account').prop("disabled", false);
                    $('#btn-close-modal-edit-account').prop("disabled", false);
                    $('#btn-new-account').prop("disabled", false);
                    $("#modal-edit-account").modal('hide');
                    Swal.fire
                    ({
                        title: result.msg,
                        icon: "success",
                    });
                    $("#catalogue-table").DataTable().ajax.reload(null, false);
                } else {

                    $("#btn-edit-account").prop('disabled', false);
                    $("#btn-close-modal-edit-account").prop('disabled', false);
                    $("#btn-new-account").prop('disabled', false);
                    Swal.fire
                    ({
                        title: result.msg,
                        icon: "error",
                    });
                }
            },
            error:function(msj){
                $("#btn-edit-account").prop('disabled', false);
                $("#btn-close-modal-edit-account").prop('disabled', false);
                $("#btn-new-account").prop('disabled', false);
                var errormessages = "";
                $.each(msj.responseJSON.errors, function(i, field){
                    errormessages+="<li>"+field+"</li>";
                });
                Swal.fire
                ({
                    title: "{{__('accounting.errors')}}",
                    icon: "error",
                    html: "<ul>"+ errormessages+ "</ul>",
                });
            }
        });
    }

    $(function() {
        $('#container').jstree({
            'core' : {
                'data' : {
                    "url" : "/catalogue/getTree"
                }
            },
            "contextmenu":{         
                "items": function($node) {
                    var tree = $("#container").jstree();
                    return {
                        "Create": {
                            "label": "{{ __('accounting.create_sub_account') }}",
                            "action": function (obj){
                                $('#new_account').text("@lang('accounting.new_sub_account')");
                                $("#code").prop('readonly', true);
                                //$("#div_code").hide();
                                $('#div_parent').hide();
                                var id = tree.get_selected("id")[0].id;

                                if (id != null) {
                                    date = $("#date").val();
                                    var route = "/catalogue/getInfoAccount/"+id+"/"+date+"";
                                    $.get(route, function(res){
                                        $("#div_parent2").show();
                                        $('#code').val(res.correlative);
                                        full_name = ""+res.code+" "+res.name+"";
                                        $("#parent2").val(full_name);
                                        $("#level").val(res.level);
                                        $("#parent").val(res.id).change();
                                        $("#type").val(res.type_account).change();
                                        $("#name").val('');
                                        $('#div_type').hide();
                                        $("#modal-add-account").modal('show');
                                        setTimeout(function()
                                        {
                                            $("#name").focus();
                                        },
                                        800);
                                    });
                                }
                            }
                        },
                        "Rename": {
                            "label": "{{ __('accounting.edit') }}",
                            "action": function (obj){
                                var id = tree.get_selected("id")[0].id;

                                if (id != null) {

                                    $("#btn-edit-account").prop('disabled', true);
                                    $("#btn-close-modal-edit-account").prop('disabled', true);
                                    $("#btn-new-account").prop('disabled', true);
                                    $("#content").show();
                                    $("#flag-edit").val('0');
                                    $("#parent_id-e").empty();
                                    var route = "/catalogue/getAccountsParents/"+id;
                                    $.get(route, function(res) {

                                        $("#parent_id-e").append('<option value="0" selected disabled>@lang('messages.please_select')</option>');
                                        $(res).each(function(key,value){
                                            $("#parent_id-e").append('<option value="'+value.id+'">'+value.full_name+'</option>');
                                        });
                                        $('#div_parent').show();
                                        $('#id-e').val('');
                                        $('#code-e').val('');
                                        $('#name-e').val('');
                                        $('#type-e').val('');
                                        $('#status').val('');
                                        var route = "/catalogue/"+id+"/edit";
                                        $.get(route, function(res) {

                                            $('#code-e').val(res.code);
                                            $('#id-e').val(res.id);
                                            $('#name-e').val(res.name);
                                            $('#parent_id-e').val(res.parent).change();
                                            $('#type-e').val(res.type).change();
                                            $('#status').val(res.status).change();

                                            if(res.parent == 0)
                                            {
                                                $('#div_type_e').show();
                                                $('#div_parent_e').hide();

                                            }
                                            else
                                            {
                                                $('#div_type_e').hide();
                                                $('#div_parent_e').show();
                                            }

                                            $("#btn-edit-account").prop('disabled', false);
                                            $("#btn-close-modal-edit-account").prop('disabled', false);
                                            $("#btn-new-account").prop('disabled', false);
                                            $("#flag-edit").val('1');
                                            $("#content").hide();

                                        });

                                        $("#modal-edit-account").modal('show');

                                    });
                                }
                            }
                        },                         
                        "Remove": {
                            "label": "{{ __('messages.delete') }}",
                            "action": function (obj){
                                id = tree.get_selected("id")[0].id;

                                if (id != null) {

                                    route = "/catalogue/verifyDeleteAccount/"+id;
                                    $.get(route, function(res){
                                        if(res.result == 0)
                                        {
                                            deleteAccount();
                                        }
                                        else
                                        {
                                            Swal.fire
                                            ({
                                                title: "{{ __('accounting.account_deleted_error') }}",
                                                icon: "error",
                                            });
                                        }
                                    });
                                }

                                
                            }
                        }
                    };
                }
            },
            'plugins' : [ "search", "contextmenu" ]
        });

to = false;
$('#search').keyup(function () {
    if(to) { clearTimeout(to); }
    to = setTimeout(function () {
        v = $('#search').val();
        $('#container').jstree(true).search(v);
    }, 250);
});
});

function editAccount()
{

}

function deleteAccount() {

    Swal.fire({
        title: LANG.sure,
        text: "{{__('messages.delete_content')}}",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: "{{__('messages.delete')}}",
        cancelButtonText: "{{__('messages.cancel')}}"
    }).then((willDelete) => {
        if (willDelete.value) {
            route = '/catalogue/'+id;
            token = $("#token").val();
            $.ajax({
                url: route,
                headers: {'X-CSRF-TOKEN': token},
                type: 'DELETE',
                dataType: 'json',                       
                success:function(result){
                    if (result.success == true) {
                        $('#container').jstree(true).refresh();
                        $("#info").hide();
                        Swal.fire
                        ({
                            title: result.msg,
                            icon: "success",
                        });
                        $("#catalogue-table").DataTable().ajax.reload(null, false);
                    }
                    else
                    {
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

$('#container').on("select_node.jstree", function (e, data){
    id = data.node.id;
    $('#node_id').val(id);
    $("#info").show();
    date = $("#date").val();
    var route = "/catalogue/getInfoAccount/"+id+"/"+date+"";
    $.get(route, function(res){
        $('#name_account').text(res.name);
        $('#code_account').text(res.code);
        $('#balance_account').text(parseFloat(res.balance).toFixed(2));
        $('#balance_account').formatCurrency();
        $('#type_account').text(res.type);
        $('#level_account').text(res.level_account);
    });
});

$('#toggle_accounts').change(function(){
    var chkBool = document.getElementById('toggle_accounts').checked;
    if(chkBool == false)
    {
        $("#search").val('');
        $('#container').jstree(true).search('');
        $('#container').jstree('close_all');
    }
    else
    {
        $("#search").val('');
        $('#container').jstree(true).search('');
        $('#container').jstree('open_all');
    }
})

$('#parent_id-e').change(function(){
    id = $('#parent_id-e').val();
    flag = $('#flag-edit').val();
    if((id != null) && (flag == 1)) {
        $('#div_type_e').hide();
        date = $("#date").val();
        var route = "/catalogue/getInfoAccount/"+id+"/"+date+"";
        $.get(route, function(res){
            $("#code-e").val(res.correlative);
        });
    }
})

$("#date").change(function(event){
    id = $('#node_id').val();
    date = $("#date").val();
    var route = "/catalogue/getInfoAccount/"+id+"/"+date+"";
    $.get(route, function(res){
        $('#name_account').text(res.name);
        $('#code_account').text(res.code);
        $('#balance_account').text(res.balance);
        $('#balance_account').formatCurrency();
        $('#type_account').text(res.type);
        $('#level_account').text(res.level_account);
    });
});

function loadCatalogueData()
{
    var table = $("#catalogue-table").DataTable();
    table.destroy();
    var table = $("#catalogue-table").DataTable(
    {

        order: [],
        pageLength: 25,
        deferRender: true,
        processing: true,
        serverSide: true,
        ajax: "/catalogue/getCatalogueData/"+$("#filter_clasification").val(),
        columns: [
        {data: 'code', orderable: false, searchable: false},
        {data: 'name', orderable: false, searchable: false},
        {data: 'parent', orderable: false, searchable: false},
        {data: null, render: function(data){
            if(data.type == 'debtor') {
                type = '@lang('accounting.debtor')';
            }
            else {
                type = '@lang('accounting.creditor')';
            }
            return type;
        }, orderable: false, searchable: false},
        {data: 'level', orderable: false, searchable: false},
        {data: 'status', orderable: false, searchable: false}
        ],
    }); 
}

$(document).ready(function()
{
    loadCatalogueData();
    $.fn.dataTable.ext.errMode = 'none';
    $.fn.modal.Constructor.prototype.enforceFocus = function() {};
});

$("#filter_clasification").change(function(){
    loadCatalogueData();
});



$(document).on('submit', 'form#form_catalogue', function(e) {
    e.preventDefault();
    $("#btn_import").prop("disabled", true);
    route = "/catalogue/importCatalogue";
    token = $("#token").val();
    $.ajax({
        url: route,
        type: 'POST',
        headers: {'X-CSRF-TOKEN': token},
        data: new FormData($(this)[0]),
        processData: false,
        contentType: false,
        success:function(result){

            if (result.success == true) {

                Swal.fire
                ({
                    title: result.msg,
                    icon: "success",
                    timer: 2000,
                    showConfirmButton: false,
                });
                $("#catalogue_file").val('');

                $("#btn_import").prop("disabled", false);
                $('#container').jstree(true).refresh();



            } else {

                Swal.fire
                ({
                    title: result.msg,
                    icon: "error",
                });
                $("#catalogue_file").val('');
                
                $("#btn_import").prop("disabled", false);

            }


        },
        error:function(msj){
          var errormessages = "";
          $.each(msj.responseJSON.errors, function(i, field){
            errormessages+="<li>"+field+"</li>";
        });
          Swal.fire
          ({
            title: "@lang('accounting.errors')",
            icon: "error",
            html: "<ul>"+ errormessages+ "</ul>",
        });
          $("#btn_import").prop("disabled", false);
          $("#catalogue_file").val('');
      }
  });
});

</script>
@endsection