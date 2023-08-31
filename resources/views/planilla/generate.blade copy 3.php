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
            <h3 class="box-title">@lang('rrhh.wage_law')</h3>
            <div class="box-tools">

            </div>
        </div>
        <div class="box-body">
            <div class="table-responsive">
                <table class="table table-responsive table-condensed table-text-center" style="font-size: inherit;" id="planilla-detail-table">
                    <thead>
                        <tr class="active">
                            <th width="15%">@lang('rrhh.employee')</th>
                            <th>@lang('rrhh.salary')</th>
                            <th>@lang('planilla.days')</th>
                            <th>@lang('planilla.hours')</th>
                            <th>@lang('planilla.commissions')</th>
                            <th>@lang('planilla.daytime_overtime')</th>
                            <th>@lang('planilla.night_overtime_hours')</th>
                            <th>@lang('planilla.total_hours')</th>
                            <th>@lang('planilla.subtotal')</th>
                            <th>ISSS</th>
                            <th>AFP</th>
                            <th>@lang('planilla.rent')</th>
                            <th>@lang('planilla.other_deductions')</th>
                            <th>@lang('planilla.total_to_pay')</th>
                            <th>@lang('planilla.actions')</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                    <tfoot>
                        <tr class="bg-gray font-14 footer-total text-center">
                            <td><strong>@lang('report.grand_total')</strong></td>
                            <td><span class="display_currency" id="total_salary" data-currency_symbol="true"></span></td>
                            <td></td>
                            <td></td>
                            <td><span class="display_currency" id="total_commissions" data-currency_symbol="true"></span></td>
                            <td><span class="display_currency" id="total_daytime_overtime" data-currency_symbol="true"></span></td>
                            <td><span class="display_currency" id="total_night_overtime_hours" data-currency_symbol="true"></span></td>
                            <td><span class="display_currency" id="total_overtime" data-currency_symbol="true"></span></td>
                            <td><span class="display_currency" id="total_subtotal" data-currency_symbol="true"></span></td>
                            <td><span class="display_currency" id="total_isss" data-currency_symbol="true"></span></td>
                            <td><span class="display_currency" id="total_afp" data-currency_symbol="true"></span></td>
                            <td><span class="display_currency" id="tota_rent" data-currency_symbol="true"></span></td>
                            <td><span class="display_currency" id="total_other_deductions" data-currency_symbol="true"></span></td>
                            <td><span class="display_currency" id="total_total_to_pay" data-currency_symbol="true"></span></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <div class="boxform_u box-solid_u">
        <div class="box-header">
            <h3 class="box-title">@lang('rrhh.honorary')</h3>
            <div class="box-tools">

            </div>
        </div>
        <div class="box-body">
            <div class="table-responsive">
                <table class="table table-responsive table-condensed table-text-center" style="font-size: inherit;" id="planilla-detail-honorary-table">
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
                            <td><span class="display_currency" id="total_salary_honorary1" data-currency_symbol="true"></span></td>
                            <td><span class="display_currency" id="tota_rent_honorary1" data-currency_symbol="true"></span></td>
                            <td><span class="display_currency" id="total_total_to_pay_honorary1" data-currency_symbol="true"></span></td>
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
        var id = $("#id").val();
        loadPlanillas(id);  
        loadPlanillasHonorary(id);    
        $.fn.dataTable.ext.errMode = 'none';      

        $('#modal_action').on('shown.bs.modal', function () {
		    $(this).find('#rrhh_type_personnel_action_id').select2({
                dropdownParent: $(this),
			})
		})
	});

    function loadPlanillas(id) {
        var table = $("#planilla-detail-table").DataTable();
        table.destroy();
        var table = $("#planilla-detail-table").DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                "url": "/planilla-getPlanillaDetail/"+id,
                "data": function ( d ) {
                    d.type = 1;
                }
            },
            columns: [
                {data: 'employee', name: 'employee', className: "text-center"},
                {data: 'salary', name: 'salary', className: "text-center salary"},
                {data: 'days', name: 'days', className: "text-center"},
                {data: 'hours', name: 'hours', className: "text-center"},
                {data: 'commissions', name: 'commissions', className: "text-center commissions"},
                {data: 'daytime_overtime', name: 'daytime_overtime', className: "text-center daytime_overtime"},
                {data: 'night_overtime_hours', name: 'night_overtime_hours', className: "text-center night_overtime_hours"},
                {data: 'total_hours', name: 'total_hours', className: "text-center total_hours"},
                {data: 'subtotal', name: 'subtotal', className: "text-center subtotal"},
                {data: 'isss', name: 'isss', className: "text-center isss"},
                {data: 'afp', name: 'afp', className: "text-center afp"},
                {data: 'rent', name: 'rent', className: "text-center rent"},
                {data: 'other_deductions', name: 'other_deductions', className: "text-center other_deductions"},
                {data: 'total_to_pay', name: 'total_to_pay', className: "text-center total_to_pay"},
                {data: null, render: function(data) {
                    html = '<div class="btn-group"><button type="button" class="btn btn-xs btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> @lang("messages.actions") <span class="caret"></span><span class="sr-only">Toggle Dropdown</span></button><ul class="dropdown-menu dropdown-menu-right" role="menu">';
                    
                    html += '<li><a href="/planilla/'+data.id+'/generate"><i class="fa fa-user"></i>@lang('planilla.generate')</a></li>';
                    
                    @can('planilla.delete')
                    html += '<li> <a href="#" onClick="deletePlanilla('+data.id+')"><i class="glyphicon glyphicon-trash"></i>@lang('messages.delete')</a></li>';
                    @endcan
                    
                    html += '</ul></div>';

                    return html;
                } , orderable: false, searchable: false, className: "text-center"}
            ],
            "fnDrawCallback": function (oSettings) {
                $('span#total_salary').text(sum_table_col_name($('table#planilla-detail-table'), 'salary'));
                $('span#total_commissions').text(sum_table_col_name($('table#planilla-detail-table'), 'commissions'));
                $('span#total_daytime_overtime').text(sum_table_col_name($('table#planilla-detail-table'), 'daytime_overtime'));
                $('span#total_night_overtime_hours').text(sum_table_col_name($('table#planilla-detail-table'), 'night_overtime_hours'));
                $('span#total_overtime').text(sum_table_col_name($('table#planilla-detail-table'), 'total_hours'));
                $('span#total_subtotal').text(sum_table_col_name($('table#planilla-detail-table'), 'subtotal'));
                $('span#total_isss').text(sum_table_col_name($('table#planilla-detail-table'), 'isss'));
                $('span#total_afp').text(sum_table_col_name($('table#planilla-detail-table'), 'afp'));
                $('span#tota_rent').text(sum_table_col_name($('table#planilla-detail-table'), 'rent'));
                $('span#total_other_deductions').text(sum_table_col_name($('table#planilla-detail-table'), 'other_deductions'));
                $('span#total_total_to_pay').text(sum_table_col_name($('table#planilla-detail-table'), 'total_to_pay'));
                __currency_convert_recursively($('table#planilla-detail-table'));
            },
            dom:'<"row margin-bottom-12"<"col-sm-12"<"pull-left"l><"pull-right"fr>>>tip',
        });
    }

    function loadPlanillasHonorary(id) {
        var table = $("#planilla-detail-honorary-table").DataTable();
        table.destroy();
        var table = $("#planilla-detail-honorary-table").DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                "url": "/planilla-getPlanillaDetail/"+id,
                "data": function ( d ) {
                    d.type = 2;
                }
            },
            columns: [
                {data: 'employee1', name: 'employee1', className: "text-center"},
                {data: 'salary1', name: 'salary1', className: "text-center salary1"},
                {data: 'rent1', name: 'rent1', className: "text-center rent1"},
                {data: 'total_to_pay1', name: 'total_to_pay1', className: "text-center total_to_pay1"},
            ],
            "fnDrawCallback": function (oSettings) {
                $('span#total_salary1').text(sum_table_col_name($('table#planilla-detail-honorary-table'), 'salary1'));
                $('span#tota_rent1').text(sum_table_col_name($('table#planilla-detail-honorary-table'), 'rent1'));
                $('span#total_total_to_pay1').text(sum_table_col_name($('table#planilla-detail-honorary-table'), 'total_to_pay1'));
                __currency_convert_recursively($('table#planilla-detail-honorary-table'));
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