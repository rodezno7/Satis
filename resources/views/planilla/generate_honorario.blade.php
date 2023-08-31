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
            <h3 class="box-title">@lang('rrhh.honorary')</h3>
            <div class="box-tools">

            </div>
        </div>
        <div class="box-body">
            <div class="table-responsive">
                <table class="table table-responsive table-condensed table-text-center" style="font-size: inherit; width: 100%" id="planilla-detail-table">
                    <thead>
                        <tr class="active">
                            <th width="15%">@lang('rrhh.employee')</th>
                            <th>@lang('planilla.total_calculation')</th>
                            <th>@lang('planilla.rent')</th>
                            <th>@lang('planilla.total_to_pay')</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                    <tfoot>
                        <tr class="bg-gray font-14 footer-total text-center">
                            <td><strong>@lang('report.grand_total')</strong></td>
                            <td><span class="display_currency" id="total_salary" data-currency_symbol="true"></span></td>
                            <td><span class="display_currency" id="tota_rent" data-currency_symbol="true"></span></td>
                            <td><span class="display_currency" id="total_total_to_pay" data-currency_symbol="true"></span></td>
                        </tr>
                    </tfoot>
                </table>
                
            </div>
        </div>
    </div>
    <input type="hidden" name="_token" value="{{ csrf_token() }}" id="token">
    <input type="hidden" name="id" value="{{ $planilla->id }}" id="id">
</section>

<div class="modal fade" id="modal_edit" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content" id="modal_content_edit">

		</div>
	</div>
</div>
@endsection

@section('javascript')

<script src="{{ asset('js/functions.js?v=' . $asset_v) }}"></script>
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

    function loadPlanillas() {
        var id = $("#id").val();
        var table = $("#planilla-detail-table").DataTable();
        table.destroy();
        var table = $("#planilla-detail-table").DataTable({
            processing: true,
            serverSide: true,
            ajax: "/planilla-getPlanillaDetail/"+id,
            columns: [
                {data: 'employee', name: 'employee1', className: "text-center"},
                {data: 'salary', name: 'salary1', className: "text-center salary"},
                {data: 'rent', name: 'rent1', className: "text-center rent"},
                {data: 'total_to_pay', name: 'total_to_pay', className: "text-center total_to_pay"},
            ],
            "fnDrawCallback": function (oSettings) {
                $('span#total_salary').text(sum_table_col_name($('table#planilla-detail-table'), 'salary'));
                $('span#tota_rent').text(sum_table_col_name($('table#planilla-detail-table'), 'rent'));
                $('span#total_total_to_pay').text(sum_table_col_name($('table#planilla-detail-table'), 'total_to_pay'));
                __currency_convert_recursively($('table#planilla-detail-table'));
            },
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
                            
                            $("#planilla-detail-table").DataTable().ajax.reload(null, false);
                            
                            
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

    function sum_table_col_name(table, class_name){
        var sum = 0;

        table.find('tbody').find('tr').each( function(){
            $(this).find('td.' + class_name).each( function(){
                sum += parseFloat(__number_uf($(this).html(), false));
            });
        });
        
        return sum;
    }
</script>
@endsection