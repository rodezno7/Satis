@extends('layouts.app')
@section('title', __('accounting.accounting_settings'))
<style>
    


</style>

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('accounting.accounting_settings')</h1>
    <!-- <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
        <li class="active">Here</li>
    </ol> -->
</section>

<!-- Main content -->
<section class="content">
    <form id="form-update-settings">
        <input type="hidden" name="_token" value="{{ csrf_token() }}" id="token">
        <div class="row">
            <div class="col-xs-12">
               <!--  <pos-tab-container> -->
                <div class="col-xs-12 pos-tab-container">
                    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 pos-tab-menu">
                        <div class="list-group">
                            <a href="#" class="list-group-item text-center active">@lang('accounting.accounts')</a>
                            <a href="#" class="list-group-item text-center">@lang('accounting.options')</a>
                            <a href="#" class="list-group-item text-center">@lang('accounting.accounting_period')</a>
                            <a href="#" class="list-group-item text-center">@lang('accounting.categorie_cost')</a>
                            <a href="#" class="list-group-item text-center">@lang('accounting.result_status')</a>
                            <a href="#" class="list-group-item text-center">@lang('accounting.shortcuts')</a>
                            
                        </div>
                    </div>
                    <div class="col-lg-10 col-md-10 col-sm-10 col-xs-10 pos-tab">
                        <!-- tab 1 start -->
                        @include('business.partials.settings_accounts')
                        <!-- tab 1 end -->

                        <!-- tab 2 start -->
                        @include('business.partials.settings_options_accounting')
                        <!-- tab 2 end -->

                        <!-- tab 3 start -->
                        @include('business.partials.accounting_period')
                        <!-- tab 3 end -->

                        <!-- tab 4 start -->
                        @include('business.partials.categorie_cost')
                        <!-- tab 4 end -->

                        <!-- tab 5 start -->
                        
                        @include('business.partials.result_state')
                        
                        <!-- tab 5 end -->

                        <!-- tab 6 start -->
                        
                        @include('business.partials.shortcuts')
                        
                        <!-- tab 6 end -->

                    </div>
                </div>
                <!--  </pos-tab-container> -->
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <button type="button" class="btn btn-danger pull-right" id="btn-post-settings">@lang('business.update_settings')</button>
            </div>
        </div>
    </form>

    <div class="modal fade" id="modal-add-year" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document" style="width: 30%">
            <div class="modal-content" style="border-radius: 20px;">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <h3>@lang('accounting.add_year')</h3>
                            <form id="form-add-year">
                                <div class="form-group">
                                    <input type="text" name="date" id="date" class="form-control">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="button" class="btn btn-primary" value="@lang('messages.save')" id="btn-add-year">
                    <button type="button" class="btn btn-danger" data-dismiss="modal" id="btn-close-modal-add-year">@lang('messages.close')</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-edit-year" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document" style="width: 30%">
            <div class="modal-content" style="border-radius: 20px;">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <h3>@lang('accounting.edit_period')</h3>
                            <form id="form-edit-year">
                                <div class="form-group">
                                    <input type="text" name="edate" id="edate" class="form-control">
                                    <input type="hidden" name="year_id" id="year_id">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="button" class="btn btn-dark" value="@lang('messages.save')" id="btn-edit-year">
                    <button type="button" class="btn btn-danger" data-dismiss="modal" id="btn-close-modal-edit-year">@lang('messages.close')</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-add-period" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document" style="width: 30%">
            <div class="modal-content" style="border-radius: 20px;">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <h3>@lang('accounting.add_period')</h3>
                            <form id="form-add-period">
                                <div class="form-group">
                                    <label>@lang('accounting.year')</label>
                                    <select name="year" id="year" class="form-control select2" style="width: 100%">
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>@lang('accounting.month')</label>
                                    <select name="month" id="month" class="form-control select2" style="width: 100%">
                                        <option value="0" disabled selected>@lang('messages.please_select')</option>
                                        <option value="1">@lang('accounting.january')</option>
                                        <option value="2">@lang('accounting.february')</option>
                                        <option value="3">@lang('accounting.march')</option>
                                        <option value="4">@lang('accounting.april')</option>
                                        <option value="5">@lang('accounting.may')</option>
                                        <option value="6">@lang('accounting.june')</option>
                                        <option value="7">@lang('accounting.july')</option>
                                        <option value="8">@lang('accounting.august')</option>
                                        <option value="9">@lang('accounting.september')</option>
                                        <option value="10">@lang('accounting.october')</option>
                                        <option value="11">@lang('accounting.november')</option>
                                        <option value="12">@lang('accounting.december')</option>
                                    </select>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="button" class="btn btn-primary" value="@lang('messages.save')" id="btn-add-period">
                    <button type="button" class="btn btn-danger" data-dismiss="modal" id="btn-close-modal-add-period">@lang('messages.close')</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-edit-period" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document" style="width: 30%">
            <div class="modal-content" style="border-radius: 20px;">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <h3>@lang('accounting.edit_period')</h3>
                            <form id="form-edit-period">
                                <div class="form-group">
                                    <label>@lang('accounting.year')</label>
                                    <select name="eyear" id="eyear" class="form-control select2" style="width: 100%">
                                    </select>
                                    <input type="hidden" name="period_id" id="period_id">
                                </div>
                                <div class="form-group">
                                    <label>@lang('accounting.month')</label>
                                    <select name="emonth" id="emonth" class="form-control select2" style="width: 100%">
                                        <option value="0" disabled selected>@lang('messages.please_select')</option>
                                        <option value="1">@lang('accounting.january')</option>
                                        <option value="2">@lang('accounting.february')</option>
                                        <option value="3">@lang('accounting.march')</option>
                                        <option value="4">@lang('accounting.april')</option>
                                        <option value="5">@lang('accounting.may')</option>
                                        <option value="6">@lang('accounting.june')</option>
                                        <option value="7">@lang('accounting.july')</option>
                                        <option value="8">@lang('accounting.august')</option>
                                        <option value="9">@lang('accounting.september')</option>
                                        <option value="10">@lang('accounting.october')</option>
                                        <option value="11">@lang('accounting.november')</option>
                                        <option value="12">@lang('accounting.december')</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>@lang('accounting.status')</label>
                                    <select name="status" id="status" class="form-control select2" style="width: 100%;">
                                        <option value="1">@lang('accounting.open')</option>
                                        <option value="0">@lang('accounting.closed')</option>
                                    </select>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="button" class="btn btn-primary" value="@lang('messages.save')" id="btn-edit-period">
                    <button type="button" class="btn btn-danger" data-dismiss="modal" id="btn-close-modal-edit-period">@lang('messages.close')</button>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="modal-edit-categorie" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document" style="width: 30%">
            <div class="modal-content" style="border-radius: 20px;">
                <div class="modal-body">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <h3>@lang('category.edit_category')</h3>
                            <form id="form-edit-categorie">
                                <div class="form-group">
                                    <label>@lang('accounting.name')</label>
                                    <input type="text" id="txt-ename-categorie" name="txt-ename-categorie" class="form-control" placeholder="@lang('accounting.name')..." readonly>
                                    <input type="hidden" name="categorie_id" id="categorie_id">
                                </div>
                                <div class="form-group">
                                    <label>@lang('accounting.catalogue_account')</label>
                                    {!! Form::select("eselect-catalogue-id-categorie", [], null,
                                        ['class' => 'form-control', 'id' => 'eselect-catalogue-id-categorie', 'placeholder' => __('messages.please_select')]) !!}
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="button" class="btn btn-primary" value="@lang('messages.save')" id="btn-edit-category">
                    <button type="button" class="btn btn-danger" data-dismiss="modal" id="btn-close-modal-edit-category">@lang('messages.close')</button>
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

    $(document).ready(function(){
        $("select.select_account").select2({
            ajax: {
                type: "post",
                url: "/catalogue/get_accounts_for_select2",
                dataType: "json",
                data: function(params){
                    return {
                        q: params.term
                    };
                },
                processResults: function(data) {
                    return {
                        results: data
                    };
                }
            },
            minimumInputLength: 1,
            escapeMarkup: function (markup) {
                return markup;
            }
        });

        $('div#modal-edit-categorie').on('shown.bs.modal', function(){
            var cost_main_account = $('input#cost_main_account').val();
            cost_main_account = cost_main_account ? cost_main_account : null;

            $("select#eselect-catalogue-id-categorie").select2({
            ajax: {
                type: "post",
                url: "/catalogue/get_accounts_for_select2",
                dataType: "json",
                data: function(params){
                    return {
                        q: params.term,
                        main_account: cost_main_account
                    };
                },
                processResults: function(data) {
                    return {
                        results: data
                    };
                }
            },
            minimumInputLength: 1,
            escapeMarkup: function (markup) {
                return markup;
            }
        });
        });
    });

    $("#btn-post-settings").click(function(){        
        $('#btn-post-settings').prop("disabled", true);
        datastring = $("#form-update-settings").serialize();
        route = "/business/update-accounting";
        token = $("#token").val();
        $.ajax({
            url: route,
            headers: {'X-CSRF-TOKEN': token},
            type: 'POST',
            dataType: 'json',
            data: datastring,
            success:function(result){
                if (result.success == true){
                    $('#btn-post-settings').prop("disabled", false);
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
                    $('#btn-post-settings').prop("disabled", false);
                }
            }
        });
    });

    $("#date").datepicker( {
        format: "yyyy",
        viewMode: "years", 
        minViewMode: "years"
    });

    $("#edate").datepicker( {
        format: "yyyy",
        viewMode: "years", 
        minViewMode: "years"
    });

    $("#month").datepicker( {
        format: "mm",
        viewMode: "months", 
        minViewMode: "months"
    });



    $(document).ready(function()
    {
        loadYearsData();
        loadPeriodsData();
        loadCategoriesData();
        updateSelects();
        $.fn.dataTable.ext.errMode = 'none';

        $('select#period_fiscal_year').on('change', function () {
            loadPeriodsData();
        });
    });

    function loadYearsData()
    {
        var table = $("#years-table").DataTable();
        table.destroy();
        var table = $("#years-table").DataTable(
        {
            deferRender: true,
            processing: true,
            serverSide: true,
            ajax: "/fiscal-years/getFiscalYearsData",
            columns: [
            {data: 'year'},            
            {data: null, render: function(data){
                edit_button = '<a class="btn btn-xs btn-primary" onClick="editYear('+data.id+')"><i class="glyphicon glyphicon-edit"></i>@lang('messages.edit')</a>';
                delete_button = ' <a class="btn btn-xs btn-danger" onClick="deleteYear('+data.id+')"><i class="glyphicon glyphicon-trash"></i>@lang('messages.delete')</a>';
                return edit_button + delete_button;
            } , orderable: false, searchable: false}
            ]
        });
    }

    function loadPeriodsData()
    {
        var table = $("#periods-table").DataTable();
        table.destroy();
        var table = $("#periods-table").DataTable(
        {
            deferRender: true,
            processing: true,
            serverSide: true,
            aaSorting: [[1, 'desc'], [2, 'desc']],
            ajax: {
                method: 'get',
                url: "/accounting-periods/getPeriodsData",
                data: function (d) {
                    d.year = $('select#period_fiscal_year').val();
                }
            },
            columns: [
            {data: 'name'},
            {data: 'year'},
            {data: 'month'},
            {data: null, render: function(data){
                if(data.status == 1){
                    return "{{__('accounting.open')}}";
                }
                else
                {
                    return "{{__('accounting.closed')}}";
                }
            },},
            {data: null, render: function(data){
                edit_button = '<a class="btn btn-xs btn-primary" onClick="editPeriod('+data.id+')"><i class="glyphicon glyphicon-edit"></i>@lang('messages.edit')</a>';
                delete_button = ' <a class="btn btn-xs btn-danger" onClick="deletePeriod('+data.id+')"><i class="glyphicon glyphicon-trash"></i>@lang('messages.delete')</a>';
                return edit_button + delete_button;
            } , orderable: false, searchable: false}
            ]
        });
    }

    $("#btn-add-year").click(function(){
        $("#btn-add-year").prop("disabled", true);
        $("#btn-close-modal-add-year").prop("disabled", true);  
        year = $("#date").val();
        route = "/fiscal-years";
        token = $("#token").val();
        $.ajax({
            url: route,
            headers: {'X-CSRF-TOKEN': token},
            type: 'POST',
            dataType: 'json',
            data: {
                year: year
            },
            success:function(){
                updateSelects();
                $("#btn-add-year").prop("disabled", false);
                $("#btn-close-modal-add-year").prop("disabled", false); 
                $("#years-table").DataTable().ajax.reload(null, false);
                Swal.fire
                ({
                    title: "{{__('accounting.year_added')}}",
                    icon: "success",
                });
                $("#modal-add-year").modal('hide');
            },
            error:function(msj){
                $("#btn-add-year").prop("disabled", false);
                $("#btn-close-modal-add-year").prop("disabled", false);
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

    $("#btn-new-year").click(function(){
        $("#date").val('');
    });

    

    $("#btn-new-period").click(function(){
        $("#year").val('0').change();
        $("#month").val('0').change();
    });

    $("#btn-edit-year").click(function(){
        $("#btn-edit-year").prop("disabled", true);
        $("#btn-close-modal-edit-year").prop("disabled", true);
        id = $("#year_id").val();
        year = $("#edate").val();
        route = "/fiscal-years/"+id;
        token = $("#token").val();
        $.ajax({
            url: route,
            headers: {'X-CSRF-TOKEN': token},
            type: 'PUT',
            dataType: 'json',
            data: {
                year: year
            },
            success:function(){
                updateSelects();
                $("#btn-edit-year").prop("disabled", false);
                $("#btn-close-modal-edit-year").prop("disabled", false);
                $("#years-table").DataTable().ajax.reload(null, false);
                Swal.fire
                ({
                    title: "{{__('accounting.year_updated')}}",
                    icon: "success",
                });
                $('#modal-edit-year').modal('hide');
            },
            error:function(msj){
                $("#btn-edit-year").prop("disabled", false);
                $("#btn-close-modal-edit-year").prop("disabled", false);
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

    function editYear(id)
    {
        $('#edate').val('');
        var route = "/fiscal-years/"+id+"/edit";
        $.get(route, function(res){
            $('#edate').val(res.year);
            $('#year_id').val(res.id);
        });
        $('#modal-edit-year').modal({backdrop: 'static', keyboard: false});
    }

    function deleteYear(id)
    {
        swal({
            title: LANG.sure,
            text: '{{__('messages.delete_content')}}',
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete){
                route = '/fiscal-years/'+id;
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
                            $("#years-table").DataTable().ajax.reload(null, false);
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

    function updateSelects()
    {
        $("#year").empty();
        $("#eyear").empty();
        
        var route = "/fiscal-years/getYears";
        $.get(route, function(res){
            $("#year").append('<option value="0" disabled selected>{{ __('messages.please_select') }}</option>');
            $("#eyear").append('<option value="0" disabled selected>{{ __('messages.please_select') }}</option>');
            $(res).each(function(key,value){
                $("#year").append('<option value="'+value.id+'">'+value.year+'</option>');
                $("#eyear").append('<option value="'+value.id+'">'+value.year+'</option>');
            });
        });
    }   

    $("#btn-add-period").click(function(){
        $("#btn-add-period").prop("disabled", true);
        $("#btn-close-modal-add-period").prop("disabled", true);  
        fiscal_year_id = $("#year").val();
        fiscal_year = $("#year option:selected").text();
        month = $("#month").val();
        month2 = $("#month option:selected").text();
        name = month2+"/"+fiscal_year;
        route = "/accounting-periods";
        token = $("#token").val();
        $.ajax({
            url: route,
            headers: {'X-CSRF-TOKEN': token},
            type: 'POST',
            dataType: 'json',
            data: {
                name: name,
                fiscal_year_id: fiscal_year_id,
                month: month
            },
            success:function(){
                $("#btn-add-period").prop("disabled", false);
                $("#btn-close-modal-add-period").prop("disabled", false); 
                $("#periods-table").DataTable().ajax.reload(null, false);
                Swal.fire
                ({
                    title: "{{__('accounting.period_added')}}",
                    icon: "success",
                });
                $("#modal-add-period").modal('hide');
            },
            error:function(msj){
                $("#btn-add-period").prop("disabled", false);
                $("#btn-close-modal-add-period").prop("disabled", false);
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

    function editPeriod(id)
    {
        var route = "/accounting-periods/"+id+"/edit";
        $.get(route, function(res){
            $('#period_id').val(res.id);
            $('#eyear').val(res.fiscal_year_id).change();
            $('#emonth').val(res.month).change();
            $('#status').val(res.status).change();
        });
        $('#modal-edit-period').modal({backdrop: 'static', keyboard: false});
    }

    $("#btn-edit-period").click(function(){
        $("#btn-edit-period").prop("disabled", true);
        $("#btn-close-modal-edit-period").prop("disabled", true);  
        fiscal_year_id = $("#eyear").val();
        fiscal_year = $("#eyear option:selected").text();
        month = $("#emonth").val();
        month2 = $("#emonth option:selected").text();
        name = month2+"/"+fiscal_year;
        id = $("#period_id").val();
        status = $("#status").val();
        route = "/accounting-periods/"+id;
        token = $("#token").val();
        $.ajax({
            url: route,
            headers: {'X-CSRF-TOKEN': token},
            type: 'PUT',
            dataType: 'json',
            data: {
                name: name,
                fiscal_year_id: fiscal_year_id,
                month: month,
                status: status
            },
            success:function(){
                $("#btn-edit-period").prop("disabled", false);
                $("#btn-close-modal-edit-period").prop("disabled", false); 
                $("#periods-table").DataTable().ajax.reload(null, false);
                Swal.fire
                ({
                    title: "{{__('accounting.period_updated')}}",
                    icon: "success",
                });
                $("#modal-edit-period").modal('hide');
            },
            error:function(msj){
                $("#btn-edit-period").prop("disabled", false);
                $("#btn-close-modal-edit-period").prop("disabled", false);
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

    function deletePeriod(id)
    {
        swal({
            title: LANG.sure,
            text: '{{__('messages.delete_content')}}',
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete){
                route = '/accounting-periods/'+id;
                token = $("#token").val();
                $.ajax({                    
                    url: route,
                    headers: {'X-CSRF-TOKEN': token},
                    type: 'DELETE',
                    dataType: 'json',                       
                    success:function(result){
                        if(result.success == true){
                            updateSelects();
                            Swal.fire
                            ({
                                title: result.msg,
                                icon: "success",
                            });
                            $("#periods-table").DataTable().ajax.reload(null, false);
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

    function loadCategoriesData()
    {
        var table = $("#categories-table").DataTable();
        table.destroy();
        var table = $("#categories-table").DataTable(
        {
            deferRender: true,
            processing: true,
            serverSide: true,
            ajax: "/categories/getCategoriesData",
            columns: [
            {data: 'name'},
            {data: 'account'},
            {data: null, render: function(data){
                edit_button = '<a class="btn btn-xs btn-primary" onClick="editCategorie('+data.id+')"><i class="glyphicon glyphicon-edit"></i>@lang('messages.edit')</a>';
                
                return edit_button;
            } , orderable: false, searchable: false}
            ]
        });
    }

    function editCategorie(id)
    {
        var route = "/categories/"+id;
        $('#eselect-catalogue-id-categorie').val("").change();
        $.get(route, function(res){
            $('#categorie_id').val(id);
            $('#txt-ename-categorie').val(res.name);
            if(res.catalogue){
                var option = new Option(res.catalogue.code + ' ' + res.catalogue.name, res.catalogue.id, true, true);
                $('#eselect-catalogue-id-categorie').append(option);
            }
        });
        $('#modal-edit-categorie').modal({backdrop: 'static', keyboard: false});
    }
    $("#btn-edit-category").click(function(){
        $("#btn-edit-category").prop("disabled", true);
        $("#btn-close-modal-edit-category").prop("disabled", true);
        id = $("#categorie_id").val();
        name = $("#txt-ename-categorie").val();
        catalogue_id = $("#eselect-catalogue-id-categorie").val();
        route = "/categories/updateCatalogueId";
        token = $("#token").val();
        $.ajax({
            url: route,
            headers: {'X-CSRF-TOKEN': token},
            type: 'POST',
            dataType: 'json',
            data: {
                id: id,
                name: name,
                catalogue_id: catalogue_id
            },
            success:function(result){
                if (result.success == true) {
                    $("#btn-edit-category").prop("disabled", false);
                    $("#btn-close-modal-edit-category").prop("disabled", false);
                    $("#categories-table").DataTable().ajax.reload(null, false);
                    Swal.fire
                    ({
                        title: result.msg,
                        icon: "success",
                    });
                    $('#modal-edit-categorie').modal('hide');
                }
                else {
                    $("#btn-edit-category").prop("disabled", false);
                    $("#btn-close-modal-edit-category").prop("disabled", false);
                    $("#categories-table").DataTable().ajax.reload(null, false);
                    Swal.fire
                    ({
                        title: result.msg,
                        icon: "error",
                    });
                }
            },
            error:function(msj){
                $("#btn-edit-category").prop("disabled", false);
                $("#btn-close-modal-edit-category").prop("disabled", false);
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
</script>
@endsection