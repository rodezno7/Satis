@extends('layouts.app')
@section('title', __('rrhh.rrhh'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1> @lang('rrhh.overall_payroll')
        <small></small>
    </h1>
</section>

<!-- Main content -->
<section class="content">
    <div class="boxform_u box-solid_u">
        <div class="box-header">
            <h3 class="box-title"></h3>
            <div class="box-tools">
                @can('rrhh_overall_payroll.create')
                <a href="{!!URL::to('/rrhh-employees/create')!!}" type="button" class="btn btn-primary" id="btn_add"><i
                        class="fa fa-plus"></i> @lang( 'messages.add' )
                </a>
                @endcan
            </div>
        </div>

        <div class="box-body">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-condensed table-hover" id="employees-table"
                        width="100%">
                        <thead>
                            <th>@lang('rrhh.code')</th>
                            <th>@lang('rrhh.name')</th>
                            <th>@lang('rrhh.email')</th>
                            <th>@lang('rrhh.dni')</th>
                            <th>@lang('rrhh.actions' )</th>
                        </thead>
                    </table>
                    <input type="hidden" name="_token" value="{{ csrf_token() }}" id="token">
                </div>
            </div>
        </div>
    </div>
</section>
<div tabindex="-1" class="modal fade" id="document_modal" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
</div>
<div tabindex="-1" class="modal fade" id="modal_action" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
</div>
<div tabindex="-1" class="modal fade" id="modal_action_ap" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
</div>

<div class="modal fade" id="modal_photo" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content" id="modal_content_photo">

		</div>
	</div>
</div>

<div class="modal fade" id="modal_edit_action" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content" id="modal_content_edit_document">

		</div>
	</div>
</div>

<div class="modal fade" id="modal_personnel_action" tabindex="-1">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content" id="modal_content_personnel_action">

      </div>
    </div>
</div>

<div class="modal fade" id="modal_doc" tabindex="-1">
    <div class="modal-dialog" role="document">
      <div class="modal-content" id="modal_content_document">

      </div>
    </div>
</div>
@endsection

@section('javascript')
<script>
    $(document).ready(function() 
    {
        $(document).on("preInit.dt", function(){
            $(".dataTables_filter input[type='search']").attr("size", 7);
        });
        loadEmployees();      
        $.fn.dataTable.ext.errMode = 'none';      

        $('#modal_action').on('shown.bs.modal', function () {
		    $(this).find('#rrhh_type_personnel_action_id').select2({
                dropdownParent: $(this),
			})
		})
	});

    function loadEmployees() 
    {
        var table = $("#employees-table").DataTable();
        table.destroy();
        var table = $("#employees-table").DataTable({
            select: true,
            deferRender: true,
            processing: true,
            serverSide: true,
            ajax: "/rrhh-employees-getEmployees",
            columns: [
            {data: 'agent_code', name: 'e.agent_code', className: "text-center"},
            {data: 'full_name', name: 'full_name', className: "text-center"},
            {data: 'email', name: 'email', className: "text-center"},
            {data: 'dni', name: 'dni', className: "text-center"},
            {data: null, render: function(data) {
                html = '<div class="btn-group"><button type="button" class="btn btn-xs btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> @lang("messages.actions") <span class="caret"></span><span class="sr-only">Toggle Dropdown</span></button><ul class="dropdown-menu dropdown-menu-right" role="menu">';
                html += '<li><a href="/rrhh-employees/'+data.id+'"><i class="fa fa-eye"></i>@lang('messages.view')</a></li>';
                
                @can('rrhh_overall_payroll.update')
                html += '<li><a href="/rrhh-employees/'+data.id+'/edit"><i class="glyphicon glyphicon-edit"></i>@lang('messages.edit')</a></li>';
                @endcan

                html += '<li> <a href="#" onClick="addDocument('+data.id+')"><i class="fa fa-file"></i>@lang('rrhh.documents')</a></li>';
                html += '<li> <a href="#" onClick="addEconomicDependencies('+data.id+')"><i class="fa fa-user"></i>@lang('rrhh.economic_dependencies')</a></li>';
                html += '<li> <a href="#" onClick="addPesonnelAction('+data.id+')"><i class="fa fa-file"></i>@lang('rrhh.personnel_actions')</a></li>';

                @can('rrhh_overall_payroll.delete')
                html += '<li> <a onClick="deleteItem('+data.id+')"><i class="glyphicon glyphicon-trash"></i>@lang('messages.delete')</a></li>';
                @endcan
                
                html += '</ul></div>';

                return html;
            } , orderable: false, searchable: false, className: "text-center"}
            ],
            dom:'<"row margin-bottom-12"<"col-sm-12"<"pull-left"l><"pull-right"fr>>>tip',
        });
    }

    function deleteItem(id) {
        Swal.fire({
            title: LANG.sure,
            text: "{{ __('messages.delete_content') }}",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: "{{ __('messages.accept') }}",
            cancelButtonText: "{{ __('messages.cancel') }}"
        }).then((willDelete) => {
            if (willDelete.value) {
                route = '/rrhh-employees/'+id;
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

                            $("#div_info").html('');
                            
                            $("#employees-table").DataTable().ajax.reload(null, false);
                            
                            
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

    function addDocument(id) {
        var route = '/rrhh-documents-getByEmployee/'+id;
        $("#document_modal").load(route, function() {
            $(this).modal({
            backdrop: 'static'
            });
        });
    }

    function addEconomicDependencies(id){
        $("#modal_action").html('');
        var route = '/rrhh-economic-dependence-getByEmployee/'+id;
        $("#modal_action").load(route, function() {
            $(this).modal({
            backdrop: 'static'
            });
        });
    }

    function addPesonnelAction(id){
        $("#modal_action").html('');
        var route = '/rrhh-personnel-action-getByEmployee/'+id;
        $("#modal_action").load(route, function() {
            $(this).modal({
            backdrop: 'static'
            });
        });
    }
</script>
@endsection