@extends('layouts.app')
@section('title', __('planilla.law_discounts'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1> @lang('planilla.law_discounts')
        <small></small>
    </h1>
</section>

<!-- Main content -->
<section class="content">
    <div class="boxform_u box-solid_u">
        <div class="box-header">
            <h3 class="box-title"></h3>
            <div class="box-tools">
                @can('planilla-catalogues.create')
                    <a href="#" class="btn btn-primary" type="button" id="btn_add" onClick="addLawDiscount()">
                        <i class="fa fa-plus"></i> @lang('messages.add')
                    </a>
                @endcan
            </div>
        </div>
        <div class="box-body">
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-condensed table-hover" id="law-discount-table"
                    width="100%">
                    <thead>
                        <th>@lang('planilla.institution_law')</th>
                        <th>@lang('planilla.from')</th>
                        <th>@lang('planilla.until')</th>
                        <th>@lang('planilla.base')</th>
                        <th>@lang('planilla.fixed_fee')</th>
                        <th>@lang('planilla.employee_percentage')</th>
                        <th>@lang('planilla.employer_value')</th>
                        <th>@lang('planilla.calculation_type')</th>
                        <th>@lang('planilla.status')</th>
                        <th width="12%">@lang('rrhh.actions')</th>
                    </thead>
                </table>
                <input type="hidden" name="_token" value="{{ csrf_token() }}" id="token">
            </div>
        </div>
    </div>
</section>
<div class="modal fade" id="modal_edit" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content" id="modal_content_edit">

		</div>
	</div>
</div>

<div class="modal fade" id="modal_add" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content" id="modal_content_add">

		</div>
	</div>
</div>
@endsection

@section('javascript')
<script>
    $(document).ready(function() {
        loadLawDiscount();      
        $.fn.dataTable.ext.errMode = 'none';      
	});

    function loadLawDiscount() {
        var table = $("#law-discount-table").DataTable();
        table.destroy();
        var table = $("#law-discount-table").DataTable({
            select: true,
            deferRender: true,
            processing: true,
            serverSide: true,
            ajax: "/law-discount-getLawDiscounts",
            columns: [
            {data: 'institution_law', name: 'institution_law', className: "text-center"},
            {data: 'from', name: 'from', className: "text-center"},
            {data: 'until', name: 'until', className: "text-center"},
            {data: 'base', name: 'base', className: "text-center"},
            {data: 'fixed_fee', name: 'fixed_fee', className: "text-center"},
            {data: 'employee_percentage', name: 'employee_percentage', className: "text-center"},
            {data: 'employer_value', name: 'employer_value', className: "text-center"},
            {data: 'calculation_type', name: 'calculation_type', className: "text-center"}, 
            {data: 'status', name: 'status', className: "text-center"},
            {data: null, render: function(data) {
                html = "";
                
                @can('planilla-catalogues.update')
                html += '<a class="btn btn-xs btn-primary" onClick="editLawDiscount('+data.id+')"><i class="glyphicon glyphicon-edit"></i> @lang('messages.edit')</a>';
                @endcan

                @can('planilla-catalogues.delete')
                html += ' <a class="btn btn-xs btn-danger" onClick="deleteLawDiscount('+data.id+')"><i class="glyphicon glyphicon-trash"></i> @lang('messages.delete')</a>';
                @endcan
                
                return html;
            } , orderable: false, searchable: false, className: "text-center"}
            ],
            dom:'<"row margin-bottom-12"<"col-sm-12"<"pull-left"l><"pull-right"fr>>>tip',
        });
    }

    function deleteLawDiscount(id) {
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
                route = '/law-discount/'+id;
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
                            
                            $("#law-discount-table").DataTable().ajax.reload(null, false);   
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

    function addLawDiscount() {
        $("#modal_content_add").html('');
        var url = "{!! URL::to('/law-discount/create') !!}";
        $.get(url, function(data) {
            $("#modal_content_add").html(data);
            $('#modal_add').modal({
                backdrop: 'static'
            });
        });
    }

    function editLawDiscount(id) {
        $("#modal_content_edit").html('');
        var url = "{!! URL::to('/law-discount/:id/edit') !!}";
        url = url.replace(':id', id);
        $.get(url, function(data) {
            $("#modal_content_edit").html(data);
            $('#modal_edit').modal({
                backdrop: 'static'
            });
        });
    }
</script>
@endsection