@extends('layouts.app')
@section('title', __('rrhh.rrhh'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1> @lang('rrhh.catalogues')
        <small>@lang('rrhh.manage_your_catalogues')</small>
    </h1>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="col-xs-12 pos-tab-container">
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 pos-tab-menu">
                    <div class="list-group">
                        <a href="#" class="list-group-item text-center active">@lang('rrhh.marital_statuses')</a>
                        <a href="#" class="list-group-item text-center">@lang('rrhh.departments')</a>
                        <a href="#" class="list-group-item text-center">@lang('rrhh.positions')</a>
                        <a href="#" class="list-group-item text-center">@lang('rrhh.afps')</a>
                        <a href="#" class="list-group-item text-center">@lang('rrhh.types')</a>
                        <a href="#" class="list-group-item text-center">@lang('rrhh.nationalities')</a>
                        <a href="#" class="list-group-item text-center">@lang('rrhh.banks')</a>
                        <a href="#" class="list-group-item text-center">@lang('rrhh.professions_occupations')</a>
                        <a href="#" class="list-group-item text-center">@lang('rrhh.way_to_pays')</a>
                        <a href="#" class="list-group-item text-center">@lang('rrhh.document_types')</a>
                    </div>
                </div>
                <div class="col-lg-10 col-md-10 col-sm-10 col-xs-10 pos-tab">

                    <!-- tab 1 start -->
                    @include('rrhh.catalogues.marital_statuses.index')
                    <!-- tab 1 end -->

                    <!-- tab 2 start -->
                    @include('rrhh.catalogues.departments.index')
                    <!-- tab 2 end -->

                    <!-- tab 3 start -->
                    @include('rrhh.catalogues.positions.index')
                    <!-- tab 3 end -->

                    <!-- tab 4 start -->
                    @include('rrhh.catalogues.afps.index')
                    <!-- tab 4 end -->

                    <!-- tab 5 start -->
                    @include('rrhh.catalogues.types.index')
                    <!-- tab 5 end -->

                    <!-- tab 6 start -->
                    @include('rrhh.catalogues.nationalities.index')
                    <!-- tab 6 end -->

                    <!-- tab 7 start -->
                    @include('rrhh.catalogues.banks.index')
                    <!-- tab 7 end -->

                    <!-- tab 8 start -->
                    @include('rrhh.catalogues.professions.index')
                    <!-- tab 8 end -->

                    <!-- tab 9 start -->
                    @include('rrhh.catalogues.pays.index')
                    <!-- tab 9 end -->

                    <!-- tab 10 start -->
                    @include('rrhh.catalogues.documents.index')
                    <!-- tab 10 end -->

                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="modal" tabindex="-1">
        <div class="modal-dialog" role="document">
            <div class="modal-content" id="modal_content">

            </div>
        </div>
    </div>




</section>
<!-- /.content -->
@endsection
@section('javascript')
<script type="text/javascript">
    $.fn.modal.Constructor.prototype.enforceFocus = function() {};

    $(document).ready(function() {

        loadMaritalStatuses();
        loadDepartments();
        loadPositions();
        loadAfps();
        loadTypes();
        loadNationalities();
        loadBanks();
        loadProfessions();
        loadWayToPays();
        loadDocumentTypes();
        $.fn.dataTable.ext.errMode = 'none';

    });



    function loadMaritalStatuses() {

        var table = $("#marital-statuses-table").DataTable();
        table.destroy();
        var table = $("#marital-statuses-table").DataTable({

            deferRender: true,
            processing: true,
            serverSide: true,
            ajax: "/rrhh/getCataloguesData/1",
            columns: [
            {data: 'value'},
            {data: 'status'},
            {data: null, render: function(data){

                html = "";
                
                @can('rrhh_catalogues.update')
                html += '<a class="btn btn-xs btn-primary" onClick="editItem('+data.id+')"><i class="glyphicon glyphicon-edit"></i>@lang('messages.edit')</a>';
                @endcan

                @can('rrhh_catalogues.delete')
                html += ' <a class="btn btn-xs btn-danger" onClick="deleteItem('+data.id+')"><i class="glyphicon glyphicon-trash"></i>@lang('messages.delete')</a>';
                @endcan
                
                return html;
            } , orderable: false, searchable: false}
            ]
        });

    }

    function loadDepartments() {

        var table2 = $("#departments-table").DataTable();
        table2.destroy();
        var table2 = $("#departments-table").DataTable({

            deferRender: true,
            processing: true,
            serverSide: true,
            ajax: "/rrhh/getCataloguesData/2",
            columns: [
            {data: 'code'},
            {data: 'value'},
            {data: 'status'},
            {data: null, render: function(data){

                html = "";
                
                @can('rrhh_catalogues.update')
                html += '<a class="btn btn-xs btn-primary" onClick="editItem('+data.id+')"><i class="glyphicon glyphicon-edit"></i>@lang('messages.edit')</a>';
                @endcan

                @can('rrhh_catalogues.delete')
                html += ' <a class="btn btn-xs btn-danger" onClick="deleteItem('+data.id+')"><i class="glyphicon glyphicon-trash"></i>@lang('messages.delete')</a>';
                @endcan
                
                return html;
            } , orderable: false, searchable: false}
            ]
        });

    }

    function loadPositions() {

        var table3 = $("#positions-table").DataTable();
        table3.destroy();
        var table3 = $("#positions-table").DataTable({

            deferRender: true,
            processing: true,
            serverSide: true,
            ajax: "/rrhh/getCataloguesData/3",
            columns: [
            {data: 'code'},
            {data: 'value'},
            {data: 'status'},
            {data: null, render: function(data){

                html = "";
                
                @can('rrhh_catalogues.update')
                html += '<a class="btn btn-xs btn-primary" onClick="editItem('+data.id+')"><i class="glyphicon glyphicon-edit"></i>@lang('messages.edit')</a>';
                @endcan

                @can('rrhh_catalogues.delete')
                html += ' <a class="btn btn-xs btn-danger" onClick="deleteItem('+data.id+')"><i class="glyphicon glyphicon-trash"></i>@lang('messages.delete')</a>';
                @endcan
                
                return html;
            } , orderable: false, searchable: false}
            ]
        });

    }

    function loadAfps() {

        var table4 = $("#afps-table").DataTable();
        table4.destroy();
        var table4 = $("#afps-table").DataTable({

            deferRender: true,
            processing: true,
            serverSide: true,
            ajax: "/rrhh/getCataloguesData/4",
            columns: [
            {data: 'code'},
            {data: 'short_name'},
            {data: 'value'},
            {data: 'status'},
            {data: null, render: function(data){

                html = "";
                
                @can('rrhh_catalogues.update')
                html += '<a class="btn btn-xs btn-primary" onClick="editItem('+data.id+')"><i class="glyphicon glyphicon-edit"></i>@lang('messages.edit')</a>';
                @endcan

                @can('rrhh_catalogues.delete')
                html += ' <a class="btn btn-xs btn-danger" onClick="deleteItem('+data.id+')"><i class="glyphicon glyphicon-trash"></i>@lang('messages.delete')</a>';
                @endcan
                
                return html;
            } , orderable: false, searchable: false}
            ]
        });

    }

    function loadTypes() {

        var table5 = $("#types-table").DataTable();
        table5.destroy();
        var table5 = $("#types-table").DataTable({

            deferRender: true,
            processing: true,
            serverSide: true,
            ajax: "/rrhh/getCataloguesData/5",
            columns: [
            {data: 'code'},
            {data: 'value'},
            {data: 'status'},
            {data: null, render: function(data){

                html = "";
                
                @can('rrhh_catalogues.update')
                html += '<a class="btn btn-xs btn-primary" onClick="editItem('+data.id+')"><i class="glyphicon glyphicon-edit"></i>@lang('messages.edit')</a>';
                @endcan

                @can('rrhh_catalogues.delete')
                html += ' <a class="btn btn-xs btn-danger" onClick="deleteItem('+data.id+')"><i class="glyphicon glyphicon-trash"></i>@lang('messages.delete')</a>';
                @endcan
                
                return html;
            } , orderable: false, searchable: false}
            ]
        });

    }

    function loadNationalities() {

        var table = $("#nationalities-table").DataTable();
        table.destroy();
        var table = $("#nationalities-table").DataTable({

            deferRender: true,
            processing: true,
            serverSide: true,
            ajax: "/rrhh/getCataloguesData/6",
            columns: [
            {data: 'value'},
            {data: 'status'},
            {data: null, render: function(data){

                html = "";
                
                @can('rrhh_catalogues.update')
                html += '<a class="btn btn-xs btn-primary" onClick="editItem('+data.id+')"><i class="glyphicon glyphicon-edit"></i>@lang('messages.edit')</a>';
                @endcan

                @can('rrhh_catalogues.delete')
                html += ' <a class="btn btn-xs btn-danger" onClick="deleteItem('+data.id+')"><i class="glyphicon glyphicon-trash"></i>@lang('messages.delete')</a>';
                @endcan
                
                return html;
            } , orderable: false, searchable: false}
            ]
        });

    }

    function loadProfessions() {

        var table = $("#professions-table").DataTable();
        table.destroy();
        var table = $("#professions-table").DataTable({

            deferRender: true,
            processing: true,
            serverSide: true,
            ajax: "/rrhh/getCataloguesData/7",
            columns: [
            {data: 'value'},
            {data: 'status'},
            {data: null, render: function(data){

                html = "";
                
                @can('rrhh_catalogues.update')
                html += '<a class="btn btn-xs btn-primary" onClick="editItem('+data.id+')"><i class="glyphicon glyphicon-edit"></i>@lang('messages.edit')</a>';
                @endcan

                @can('rrhh_catalogues.delete')
                html += ' <a class="btn btn-xs btn-danger" onClick="deleteItem('+data.id+')"><i class="glyphicon glyphicon-trash"></i>@lang('messages.delete')</a>';
                @endcan
                
                return html;
            } , orderable: false, searchable: false}
            ]
        });

    }

    function loadWayToPays() {

        var table = $("#way-to-pays-table").DataTable();
        table.destroy();
        var table = $("#way-to-pays-table").DataTable({

            deferRender: true,
            processing: true,
            serverSide: true,
            ajax: "/rrhh/getCataloguesData/8",
            columns: [
            {data: 'value'},
            {data: 'status'},
            {data: null, render: function(data){

                html = "";

                if (data.value != 'Transferencia bancaria') {

                    @can('rrhh_catalogues.update')
                    html += '<a class="btn btn-xs btn-primary" onClick="editItem('+data.id+')"><i class="glyphicon glyphicon-edit"></i>@lang('messages.edit')</a>';
                    @endcan

                    @can('rrhh_catalogues.delete')
                    html += ' <a class="btn btn-xs btn-danger" onClick="deleteItem('+data.id+')"><i class="glyphicon glyphicon-trash"></i>@lang('messages.delete')</a>';
                    @endcan
                }
                
                return html;
            } , orderable: false, searchable: false}
            ]
        });

    }

    function loadDocumentTypes() {

        var table = $("#document-types-table").DataTable();
        table.destroy();
        var table = $("#document-types-table").DataTable({

            deferRender: true,
            processing: true,
            serverSide: true,
            ajax: "/rrhh/getCataloguesData/9",
            columns: [
            {data: 'value'},
            {data: 'status'},
            {data: null, render: function(data){

                html = "";

                @can('rrhh_catalogues.update')
                html += '<a class="btn btn-xs btn-primary" onClick="editItem('+data.id+')"><i class="glyphicon glyphicon-edit"></i>@lang('messages.edit')</a>';
                @endcan

                @can('rrhh_catalogues.delete')
                html += ' <a class="btn btn-xs btn-danger" onClick="deleteItem('+data.id+')"><i class="glyphicon glyphicon-trash"></i>@lang('messages.delete')</a>';
                @endcan
                
                return html;
            } , orderable: false, searchable: false}
            ]
        });

    }


    function loadBanks() {

        var table = $("#banks-table").DataTable();
        table.destroy();
        var table = $("#banks-table").DataTable({

            deferRender: true,
            processing: true,
            serverSide: true,
            ajax: "/rrhh/getBanksData",
            columns: [
            {data: 'name'},
            {data: null, render: function(data){

                html = "";
                
                @can('rrhh_catalogues.update')
                html += '<a class="btn btn-xs btn-primary" onClick="editBank('+data.id+')"><i class="glyphicon glyphicon-edit"></i>@lang('messages.edit')</a>';
                @endcan

                @can('rrhh_catalogues.delete')
                html += ' <a class="btn btn-xs btn-danger" onClick="deleteBank('+data.id+')"><i class="glyphicon glyphicon-trash"></i>@lang('messages.delete')</a>';
                @endcan
                
                return html;
            } , orderable: false, searchable: false}
            ]
        });

    }

    $("#add_marital_status, #add_department, #add_position, #add_afp, #add_type, #add_nationality, #add_profession, #add_way_to_pay, #add_document_type").click(function(){

        $("#modal_content").html('');
        catalogue_id = $(this).val();
        var url = '{!!URL::to('/rrhh/create-item/:catalogue_id')!!}';
        url = url.replace(':catalogue_id', catalogue_id);
        $.get(url, function(data) {

            $("#modal_content").html(data);
            $('#modal').modal({backdrop: 'static'});
            
        });
    });

    $("#add_bank").click(function(){

        $("#modal_content").html('');        
        var url = '{!!URL::to('/rrhh-banks/create')!!}';
        $.get(url, function(data) {

            $("#modal_content").html(data);
            $('#modal').modal({backdrop: 'static'});
            
        });
    });

    function editItem(id) {

        $("#modal_content").html('');
        var url = '{!!URL::to('/rrhh/edit-item/:id')!!}';
        url = url.replace(':id', id);
        $.get(url, function(data) {

            $("#modal_content").html(data);
            $('#modal').modal({backdrop: 'static'});
            
        });
    }

    function editBank(id) {

        $("#modal_content").html('');
        var url = '{!!URL::to('/rrhh-banks/:id/edit')!!}';
        url = url.replace(':id', id);
        $.get(url, function(data) {

            $("#modal_content").html(data);
            $('#modal').modal({backdrop: 'static'});
            
        });
    }

    function deleteItem(id) {

        $.confirm({
            title: '@lang('rrhh.confirm_delete')',
            content: '@lang('rrhh.delete_message')',
            icon: 'fa fa-warning',
            theme: 'modern',
            closeIcon: true,
            animation: 'scale',
            type: 'red',
            buttons: {
                confirm:{
                    text: '@lang('rrhh.delete')',            
                    action: function()
                    {
                        route = '/rrhh-catalogues-data/'+id;
                        token = $("#token").val();
                        $.ajax({
                            url: route,
                            headers: {'X-CSRF-TOKEN': token},
                            type: 'DELETE',
                            dataType: 'json',                       
                            success:function(result){
                                if(result.success == true) {
                                    Swal.fire
                                    ({
                                        title: result.msg,
                                        icon: "success",
                                        timer: 2000,
                                        showConfirmButton: false,
                                    });
                                    $("#afps-table").DataTable().ajax.reload(null, false);
                                    $("#marital-statuses-table").DataTable().ajax.reload(null, false);
                                    $("#departments-table").DataTable().ajax.reload(null, false);
                                    $("#positions-table").DataTable().ajax.reload(null, false);
                                    $("#types-table").DataTable().ajax.reload(null, false);
                                    $("#nationalities-table").DataTable().ajax.reload(null, false);
                                    $("#professions-table").DataTable().ajax.reload(null, false);
                                    $("#way-to-pays-table").DataTable().ajax.reload(null, false);
                                    $("#document-types-table").DataTable().ajax.reload(null, false);
                                    $('#modal').modal('hide');
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
                },
                cancel:{
                    text: '@lang('rrhh.cancel')',
                },
            }
        });
    }

    function deleteBank(id) {

        $.confirm({
            title: '@lang('rrhh.confirm_delete')',
            content: '@lang('rrhh.delete_message')',
            icon: 'fa fa-warning',
            theme: 'modern',
            closeIcon: true,
            animation: 'scale',
            type: 'red',
            buttons: {
                confirm:{
                    text: '@lang('rrhh.delete')',            
                    action: function()
                    {
                        route = '/rrhh-banks/'+id;
                        token = $("#token").val();
                        $.ajax({
                            url: route,
                            headers: {'X-CSRF-TOKEN': token},
                            type: 'DELETE',
                            dataType: 'json',                       
                            success:function(result){
                                if(result.success == true) {
                                    Swal.fire
                                    ({
                                        title: result.msg,
                                        icon: "success",
                                        timer: 2000,
                                        showConfirmButton: false,
                                    });
                                    $("#banks-table").DataTable().ajax.reload(null, false);
                                    $('#modal').modal('hide');
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
                },
                cancel:{
                    text: '@lang('rrhh.cancel')',
                },
            }
        });
    }



</script>
@endsection