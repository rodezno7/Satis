<div class="boxform_u box-solid_u">
    <div class="box-header">
        <h3 class="box-title"></h3>
        <div class="box-tools">
            @can('rrhh_overall_payroll.create')
            <button type="button" class="btn btn-primary" id="btn_add"><i class="fa fa-plus"></i> @lang( 'messages.add' )
            </button>

            <button type="button" class="btn btn-primary" id="btn_undo" style="display: none;">@lang( 'rrhh.back' )
            </button>
            @endcan
        </div>
    </div>

    <div class="box-body">

        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">

            <div class="table-responsive">
                <table class="table table-striped table-bordered table-condensed table-hover" id="employees-table" width="100%">
                    <thead>
                        <th>@lang('rrhh.code')</th>
                        <th>@lang('rrhh.name')</th>
                        <th>@lang('rrhh.actions' )</th>
                    </thead>
                </table>
                <input type="hidden" name="_token" value="{{ csrf_token() }}" id="token">
            </div>
        </div>

        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">

            <div class="boxform_u box-solid_u">
                <div class="box-header">
                    <h3 class="box-title">@lang( 'rrhh.employee_information' )</h3>
                    <div class="box-tools">
                    </div>
                </div>
                <div class="box-body" id='div_info'>
                </div>
            </div>
        </div>
    </div>
</div>



<script>

    $(document).ready(function() {

        $(document).on("preInit.dt", function(){
            $(".dataTables_filter input[type='search']").attr("size", 7);

        });
        loadEmployees();      
        $.fn.dataTable.ext.errMode = 'none';      

    });



    function loadEmployees() {

        var table = $("#employees-table").DataTable();
        table.destroy();
        var table = $("#employees-table").DataTable({
            select: true,
            deferRender: true,
            processing: true,
            serverSide: true,
            ajax: "/rrhh-employees-getEmployees",
            columns: [
            {data: 'code', name: 'e.code', className: "text-center"},
            {data: 'full_name', name: 'full_name', className: "text-center"},
            {data: null, render: function(data) {

                html = "";
                
                @can('rrhh_overall_payroll.update')
                html += '<a class="btn btn-xs btn-primary" onClick="editItem('+data.id+')"><i class="glyphicon glyphicon-edit"></i>@lang('messages.edit')</a>';
                @endcan

                @can('rrhh_overall_payroll.delete')
                html += ' <a class="btn btn-xs btn-danger" onClick="deleteItem('+data.id+')"><i class="glyphicon glyphicon-trash"></i>@lang('messages.delete')</a>';
                @endcan
                
                return html;
            } , orderable: false, searchable: false, className: "text-center"}
            ],
            dom:'<"row margin-bottom-12"<"col-sm-12"<"pull-left"l><"pull-right"fr>>>tip',
        });

        $('#employees-table').on( 'click', 'tr', function () {
            var item = table.row(this).data();
            if (typeof item.id != "undefined") {

                $("#div_info").html('');
                var url = '{!!URL::to('/rrhh-employees/:id')!!}';
                url = url.replace(':id', item.id);
                $.get(url, function(data) {

                    $("#div_info").html(data);

                });

            }
        });

    }

    $("#btn_add").click(function() {

        $("#modal_content").html('');
        var url = '{!!URL::to('/rrhh-employees/create')!!}';
        $.get(url, function(data) {
            $("#modal_content").html(data);
            $('#modal').modal({backdrop: 'static'});
        });
        
    });

    function editItem(id) {

        $("#div_content").html('');
        var url = '{!!URL::to('/rrhh-employees/:id/edit')!!}';
        url = url.replace(':id', id);
        $.get(url, function(data) {
            
            

            $("#div_content").html(data);
            
            
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