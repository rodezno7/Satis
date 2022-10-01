@extends('layouts.app')
@section('title', __('user.roles'))
<style>
  hr {
    border: 0;
    clear:both;
    display:block;
    width: 96%;               
    background-color:#FFFF00;
    height: 12px;
}
</style>
@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang( 'user.roles' )
        <small>@lang('user.manage_roles')</small>
    </h1>
</section>

<!-- Main content -->
<section class="content">
    <div id="div_roles">
        <div class="boxform_u box-solid_u">
            <div class="box-header">
                <h3 class="box-title">@lang('role.all_roles')</h3>
                @can('roles.create')
                <div class="box-tools">
                    <button type="button" id="btnAddRole" class="btn btn-block btn-primary"><i class="fa fa-plus"></i> @lang('messages.add')</button>
                </div>
                @endcan
            </div>
            <div class="box-body">
                <div class="table-responsive">
                    @can('roles.view')
                    <table class="table table-bordered table-striped" id="roles_table" width="100%">
                        <thead>
                            <tr>
                                <th>@lang('user.roles')</th>
                                <th>@lang('messages.actions')</th>
                            </tr>
                        </thead>
                    </table>
                    @endcan
                </div>
            </div>
        </div>
        <div class="boxform_u box-solid_u">
            <div class="box-header">
                <h3 class="box-title">@lang('role.roles_permissions')</h3>
            </div>
            <div class="box-body">
                <div class="table-responsive">
                    @can('roles.view')
                    <table class="table table-bordered table-striped" id="all_table" width="100%">
                        <thead>
                            <tr>
                                <th style="width: 15%">Módulo</th>
                                <th style="width: 45%">Permiso</th>
                                @foreach($roles as $rol)
                                @if(($rol->rol == 'Admin') || ($rol->rol == 'Cashier'))
                                <th>@lang('lang_v1.'.$rol->rol.'')</th>
                                @else
                                <th>{{ $rol->rol }}</th>
                                @endif
                                @endforeach
                            </tr>
                        </thead>

                    </table>
                    @endcan
                </div>
            </div>
        </div>
    </div>

    <div id="div_create" style="display: none;">
        @include('role.create')
    </div>
</section>
<!-- /.content -->
@endsection
@section('javascript')
<script type="text/javascript">
    var columns = [];
    function getDT(){
        $.ajax({
            url: "/roles/getPermissionsByRoles",
            success: function (data) {
                columnNames = Object.keys(data.data[0]);
                for (var i in columnNames) {
                  columns.push({data: columnNames[i], 
                    title: capitalizeFirstLetter(columnNames[i])});
              }
              $('#all_table').DataTable( {
                processing: true,
                serverSide: true,
                pageLength: 5,
                ajax: "/roles/getPermissionsByRoles",
                columns: columns,
                columnDefs: [
                { targets: '_all', searchable: false, orderable: false }
                ]
            } );
          }
      });
    }


    function capitalizeFirstLetter(string) {
        return string.charAt(0).toUpperCase() + string.slice(1);
    }
    $(document).ready( function(){
        var table = $("#roles_table").DataTable();
        table.destroy();
        var table = $("#roles_table").DataTable(
        {
            processing: true,
            serverSide: true,
            pageLength: 5,
            ajax: "/roles/getRolesData",
            columns: [
            {data: 'name'},
            {data: null, render: function(data){
                if(data.is_default == 1)
                {
                    var actions_button = ''+data.action+'';

                }
                else
                {
                    var actions_button = ''+data.action+' <button class="btn btn-xs btn-danger delete_role_button" onClick="deleteRole('+data.id+')"><i class="glyphicon glyphicon-trash"></i>@lang("messages.delete")</buttton>';

                }
                
                return actions_button;

                
            } , orderable: false, searchable: false}
            ]
            //dom: 'Bfltip',
        });
        $.fn.dataTable.ext.errMode = 'none';

        getDT();
    });    

    function deleteRole(id)
    {
        var route = "/roles/verifyDelete/"+id;
        $.get(route, function(res){
            if(res.result == 'success')
            {
                swal({
                    title: LANG.sure,
                    text: '{{__('messages.delete_content')}}',
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                }).then((willDelete) => {
                    if (willDelete){
                        route = '/roles/'+id;
                        token = $("#token").val();
                        $.ajax({
                            url: route,
                            headers: {'X-CSRF-TOKEN': token},
                            type: 'DELETE',
                            dataType: 'json',                       
                            success:function(result){
                                $("#roles_table").DataTable().ajax.reload(null, false);
                                if(result.success == true){
                                    Swal.fire
                                    ({
                                        title: result.msg,
                                        icon: "success",
                                    });
                                    var url = '{!!URL::to('/roles')!!}';
                                    window.location.href = url;
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
            else
            {
                Swal.fire
                ({
                    title: "{{__('role.role_dont_delete')}}",
                    icon: "error",
                });
            }
        });
    }
    
    $("#btnAddRole").click(function(){
        $('#name').val('');
        $('#div_roles').hide();
        $('#div_create').show();
        $('input[name="selectores"]').addClass('check_all');
        $('#name').focus();
    });
    $("#btnBack").click(function(){        
        $('#div_create').hide();
        $('#div_roles').show();
        $('input[name="selectores"]').removeClass('check_all');
    });
    $("#btnUndo").click(function(){
        $('#div_create').hide();
        $('#div_roles').show();
        $('input[type="checkbox"]').removeClass('check_all');
        
    });
    /*$("#btnStoreRole").click(function(){
        if($('#name').val() != '')
        {
            $("#btnStoreRole").prop('disabled', true);
            $("#btnUndo").prop('disabled', true);
            $("#btnBack").prop('disabled', true);
        }
    });*/
</script>
@endsection
