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
                <a href="{!!URL::to('/rrhh-employees/create')!!}" type="button" class="btn btn-primary" id="btn_add"><i class="fa fa-plus"></i> @lang( 'messages.add' )
                </a>
                @endcan
            </div>
        </div>
    
        <div class="box-body">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-condensed table-hover" id="employees-table" width="100%">
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
                },
                cancel:{
                    text: '@lang('rrhh.cancel')',
                },
            }
        });
    }
</script>
@endsection





