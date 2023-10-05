@extends('layouts.app')
@section('title', __('rrhh.rrhh'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1> @lang('rrhh.general_payroll')
        <small></small>
    </h1>
</section>

<!-- Main content -->
<section class="content">
    <div class="boxform_u box-solid_u">
        <div class="box-header">
            <h3 class="box-title"></h3>
            <div class="box-tools">
                @can('rrhh_employees.create')
                    <a href="{!!URL::to('/rrhh-employees/create')!!}" type="button" class="btn btn-primary" id="btn_add"><i
                        class="fa fa-plus"></i> @lang('messages.add')
                    </a>
                @endcan
            </div>
        </div>
        <div class="box-body">
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-condensed table-hover" id="employees-table"
                    width="100%">
                    <thead>
                        <th width="22%">@lang('rrhh.name')</th>
                        <th>@lang('rrhh.email')</th>
                        <th>@lang('rrhh.department')</th>
                        <th>@lang('rrhh.position')</th>
                        <th>@lang('rrhh.status')</th>
                        <th width="12%">@lang('rrhh.actions')</th>
                    </thead>
                </table>
                <input type="hidden" name="_token" value="{{ csrf_token() }}" id="token">
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
	<div class="modal-dialog modal-dialog-scrollable">
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

<div class="modal fade" id="modal_show" tabindex="-1">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content" id="modal_content_show">

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
    $(document).ready(function() {
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
            ajax: "/salarial-constance",
            columns: [
            {data: 'full_name', name: 'full_name', className: "text-center"},
            {data: 'email', name: 'email', className: "text-center"},
            {data: 'department', name: 'department', className: "text-center"},
            {data: 'position', name: 'position', className: "text-center"},
            {data: 'status', name: 'status', className: "text-center"},
            {data: null, render: function(data) {
                html = '<a href="/salarial-constance/'+data.id+'/download" class="btn btn-xs btn-primary"><i class="fa fa-download"></i> @lang('messages.download')</a></li>';
                return html;
            } , orderable: false, searchable: false, className: "text-center"}
            ],
            dom:'<"row margin-bottom-12"<"col-sm-12"<"pull-left"l><"pull-right"fr>>>tip',
        });
    }
</script>
@endsection