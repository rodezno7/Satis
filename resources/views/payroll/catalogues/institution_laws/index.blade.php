@extends('layouts.app')
@section('title', __('payroll.payroll'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1> @lang('payroll.institution_laws')
        <small></small>
    </h1>
</section>

<!-- Main content -->
<section class="content">
    <div class="boxform_u box-solid_u">
        <div class="box-header">
            <h3 class="box-title"></h3>
            <div class="box-tools">
                @can('payroll-catalogues.create')
                    <a href="#" class="btn btn-primary" type="button" id="btn_add" onClick="addInstitutionLaw()">
                        <i class="fa fa-plus"></i> @lang('messages.add')
                    </a>
                @endcan
            </div>
        </div>
        <div class="box-body">
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-condensed table-hover" id="institution-law-table"
                    width="100%">
                    <thead>
                        <th width="22%">@lang('payroll.name')</th>
                        <th>@lang('payroll.description')</th>
                        <th>@lang('payroll.employeer_number')</th>
                        <th width="20%">@lang('payroll.actions')</th>
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
        loadInstitutionLaw();      
        $.fn.dataTable.ext.errMode = 'none';      
	});

    function loadInstitutionLaw() {
        var table = $("#institution-law-table").DataTable();
        table.destroy();
        var table = $("#institution-law-table").DataTable({
            select: true,
            deferRender: true,
            processing: true,
            serverSide: true,
            ajax: "/institution-law-getInstitutionLaws",
            columns: [
            {data: 'name', name: 'name', className: "text-center"},
            {data: 'description', name: 'description', className: "text-center"},
            {data: 'employeer_number', name: 'employeer_number', className: "text-center"},
            {data: null, render: function(data) {
                html = "";
                
                @can('payroll-catalogues.update')
                html += '<a class="btn btn-xs btn-primary" onClick="editInstitutionLaw('+data.id+')"><i class="glyphicon glyphicon-edit"></i> @lang('messages.edit')</a>';
                @endcan

                @can('payroll-catalogues.delete')
                html += ' <a class="btn btn-xs btn-danger" onClick="deleteInstitutionLaw('+data.id+')"><i class="glyphicon glyphicon-trash"></i> @lang('messages.delete')</a>';
                @endcan
                
                return html;
            } , orderable: false, searchable: false, className: "text-center"}
            ],
            dom:'<"row margin-bottom-12"<"col-sm-12"<"pull-left"l><"pull-right"fr>>>tip',
        });
    }

    function deleteInstitutionLaw(id) {
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
                route = '/institution-law/'+id;
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
                            
                            $("#institution-law-table").DataTable().ajax.reload(null, false);   
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

    function addInstitutionLaw() {
        $("#modal_content_add").html('');
        var url = "{!! URL::to('/institution-law/create') !!}";
        $.get(url, function(data) {
            $("#modal_content_add").html(data);
            $('#modal_add').modal({
                backdrop: 'static'
            });
        });
    }

    function editInstitutionLaw(id) {
        $("#modal_content_edit").html('');
        var url = "{!! URL::to('/institution-law/:id/edit') !!}";
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