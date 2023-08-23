@extends('layouts.app')
@section('title', __('planilla.planilla'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1> @lang('planilla.planilla')
        <small></small>
    </h1>
</section>

<!-- Main content -->
<section class="content">
    <div class="boxform_u box-solid_u">
        <div class="box-header">
            <h3 class="box-title"></h3>
            <div class="box-tools">
                @can('planilla.create')
                    <a href="#" class="btn btn-primary" type="button" id="btn_add" onClick="addPlanilla()">
                        <i class="fa fa-plus"></i> @lang('messages.add')
                    </a>
                @endcan
            </div>
        </div>
        <div class="box-body">
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-condensed table-hover" id="planilla-table"
                    width="100%">
                    <thead>
                        <th width="22%">@lang('planilla.year')</th>
                        <th>@lang('planilla.month')</th>
                        <th>@lang('planilla.period')</th>
                        <th>@lang('planilla.payment_period')</th>
                        <th>@lang('planilla.calculation_type')</th>
                        <th width="12%">@lang('planilla.actions')</th>
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
        loadPlanillas();      
        $.fn.dataTable.ext.errMode = 'none';      

        $('#modal_action').on('shown.bs.modal', function () {
		    $(this).find('#rrhh_type_personnel_action_id').select2({
                dropdownParent: $(this),
			})
		})
	});

    function loadPlanillas() 
    {
        var table = $("#planilla-table").DataTable();
        table.destroy();
        var table = $("#planilla-table").DataTable({
            select: true,
            deferRender: true,
            processing: true,
            serverSide: true,
            ajax: "/planilla-getPlanillas",
            columns: [
            {data: 'year', name: 'year', className: "text-center"},
            {data: 'month', name: 'month', className: "text-center"},
            {data: 'period', name: 'period', className: "text-center"},
            {data: 'payment_period', name: 'payment_period', className: "text-center"},
            {data: 'calculation_type', name: 'calculation_type', className: "text-center"},
            {data: null, render: function(data) {
                html = '<div class="btn-group"><button type="button" class="btn btn-xs btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> @lang("messages.actions") <span class="caret"></span><span class="sr-only">Toggle Dropdown</span></button><ul class="dropdown-menu dropdown-menu-right" role="menu">';
                html += '<li><a href="/planilla/'+data.id+'"><i class="fa fa-eye"></i>@lang('messages.view')</a></li>';
                
                @can('planilla.update')
                html += '<li><a href="/planilla/'+data.id+'/edit"><i class="glyphicon glyphicon-edit"></i>@lang('messages.edit')</a></li>';
                @endcan
                html += '<li> <a href="#" onClick="addEconomicDependencies('+data.id+')"><i class="fa fa-user"></i>@lang('planilla.economic_dependencies')</a></li>';
                html += '<li> <a href="#" onClick="addStudies('+data.id+')"><i class="fa fa-user"></i>@lang('planilla.studies')</a></li>';
                
                @can('planilla.delete')
                html += '<li> <a href="#" onClick="deletePlanilla('+data.id+')"><i class="glyphicon glyphicon-trash"></i>@lang('messages.delete')</a></li>';
                @endcan
                
                html += '</ul></div>';

                return html;
            } , orderable: false, searchable: false, className: "text-center"}
            ],
            dom:'<"row margin-bottom-12"<"col-sm-12"<"pull-left"l><"pull-right"fr>>>tip',
        });
    }

    function deletePlanilla(id) {
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
                route = '/planilla/'+id;
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
                            
                            $("#planilla-table").DataTable().ajax.reload(null, false);
                            
                            
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

    function addPlanilla() {
        $("#modal_content_add").html('');
        var url = "{!! URL::to('/planilla/create') !!}";
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