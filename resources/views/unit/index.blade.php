@extends('layouts.app')
@section('title', __( 'unit.units' ))
<link rel="stylesheet" type="text/css" href="{{ asset('accounting/css/bootstrap-select.css') }}"/>
@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('unit.units_groups' )</h1>
</section>

<!-- Main content -->
<section class="content">
    <div id="div_units">
        <div class="boxform_u box-solid_u">
            <div class="box-header">
                <h3 class="box-title">@lang( 'unit.all_your_units' )</h3>
                @can('unit.create')
                <div class="box-tools">
                    <button id="create_unit" type="button" class="btn btn-block btn-primary btn-modal" 
                    data-href="{{action('UnitController@create')}}" 
                    data-container=".unit_modal">
                    <i class="fa fa-plus"></i> @lang( 'messages.add' )</button>
                </div>
                @endcan
            </div>
            <div class="box-body">
                @can('unit.view')
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="unit_table" width="100%">
                        <thead>
                            <tr>
                                <th>@lang( 'unit.name' )</th>
                                <th>@lang( 'unit.short_name' )</th>
                                <th>@lang( 'unit.allow_decimal' ) @show_tooltip(__('tooltip.unit_allow_decimal'))</th>
                                <th>@lang( 'messages.action' )</th>
                            </tr>
                        </thead>
                    </table>
                </div>
                @endcan
            </div>
        </div>
    </div>

    @if($conf_units == 1)

    <div id="div_groups">
        <div class="boxform_u box-solid_u">
            <div class="box-header">
                <h3 class="box-title">@lang( 'unit.all_your_groups' )</h3>
                @can('unit.create')
                <div class="box-tools">
                    <button id="new_group" type="button" class="btn btn-block btn-primary" data-backdrop="static" data-keyboard="false" data-toggle='modal' data-target='#modalAddGroup'><i class="fa fa-plus"></i> @lang('messages.add')</button>
                </div>
                @endcan
            </div>
            <div class="box-body">
                @can('unit.view')
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="group_table" width="100%">
                        <thead>
                            <tr>
                                <th>@lang( 'unit.name' )</th>
                                <th>@lang( 'unit.master_unit' )</th>
                                <th>@lang( 'messages.action' )</th>
                            </tr>
                        </thead>
                    </table>
                </div>
                @endcan
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalAddGroup" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content" style="border-radius: 20px;">
                @can('unit.create')
                {!! Form::open(['id' => 'group_add_form']) !!}
                <input type="hidden" name="_token" value="{{ csrf_token() }}" id="token">
                <div class="modal-header">
                    <h3 class="modal-title">@lang('unit.add_group')</h3>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="description">@lang('unit.name')</label>
                                <input type="text" id="description" name="description" class="form-control" placeholder="@lang('unit.name')" required>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="unit_parent">@lang('unit.master_unit')</label>
                                {!! Form::select('unit_parent', [], null, ['style' => 'width: 100%', 'class' => 'form-control select2', 'id' => 'unit_parent', 'placeholder' => __('messages.please_select'), 'required']); !!}
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="unit_child">@lang('unit.children_unit')</label>
                                {!! Form::select('unit_child', [], null, ['style' => 'width: 100%', 'class' => 'form-control select2', 'id' => 'unit_child', 'placeholder' => __('messages.please_select')]); !!}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            @can('unit.view')
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped" id="measure_table" width="100%">
                                    <thead>
                                        <tr>
                                            <th style="width: 10%;">@lang( 'messages.action' )</th>
                                            <th style="width: 22.5%;">@lang( 'unit.name' )</th>
                                            <th style="width: 22.5%;">@lang( 'unit.short_name' )</th>
                                            <th style="width: 22.5%;">@lang( 'unit.factor' )</th>
                                        </tr>
                                    </thead>
                                    <tbody id="lista">
                                    </tbody>
                                </table>
                            </div>
                            @endcan
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="add_group_button">@lang( 'messages.save' )</button>
                    <button type="button" class="btn btn-default" id="close_modal_button" data-dismiss="modal">@lang( 'messages.close' )</button>
                </div>
                {!! Form::close() !!}
                @endcan
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalEditGroup" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content" style="border-radius: 20px;">
                @can('unit.update')
                {!! Form::open(['id' => 'group_edit_form']) !!}
                <div class="modal-header">
                    <h3 class="modal-title">@lang('unit.edit_group')</h3>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="edescription">@lang('unit.name')</label>
                                <input type="text" id="edescription" name="edescription" class="form-control" placeholder="@lang('unit.name')" required>
                                <input type="hidden" name="unitgroup_id" id="unitgroup_id">
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="eunit_parent">@lang('unit.master_unit')</label>
                                {!! Form::select('eunit_parent', $units, null, ['style' => 'width: 100%', 'class' => 'form-control select2', 'id' => 'eunit_parent', 'placeholder' => __('messages.please_select'), 'required']); !!}
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="eunit_child">@lang('unit.children_unit')</label>
                                {!! Form::select('eunit_child', [], null, ['style' => 'width: 100%', 'class' => 'form-control select2', 'id' => 'eunit_child', 'placeholder' => __('messages.please_select')]); !!}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            @can('unit.view')
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped" id="emeasure_table" width="100%">
                                    <thead>
                                        <tr>
                                            <th style="width: 10%;">@lang( 'messages.action' )</th>
                                            <th style="width: 22.5%;">@lang( 'unit.name' )</th>
                                            <th style="width: 22.5%;">@lang( 'unit.short_name' )</th>
                                            <th style="width: 22.5%;">@lang( 'unit.factor' )</th>
                                        </tr>
                                    </thead>
                                    <tbody id="elista">
                                    </tbody>
                                </table>
                            </div>
                            @endcan
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div id="content" class="col-md-12" style="display: none;">
                        <img src="{{ asset('img/loader.gif') }}" alt="loading" />
                    </div>
                    <button type="button" class="btn btn-primary" id="edit_group_button">@lang( 'messages.save' )</button>
                    <button type="button" class="btn btn-default" id="close_modaledit_button"data-dismiss="modal">@lang( 'messages.close' )</button>
                </div>
                {!! Form::close() !!}
                @endcan
            </div>
        </div>
    </div>
    @endif



    <div class="modal fade unit_modal" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>

</section>
<!-- /.content -->

@endsection
@section('javascript')
<script type="text/javascript" src="{{ asset('accounting/js/bootstrap-select.min.js') }}"></script>
<script type="text/javascript">
    // Carga de Grupos en Datatable
    $(document).ready( function(){
        var table = $("#group_table").DataTable();
        table.destroy();
        var table = $("#group_table").DataTable(
        {
            processing: true,
            pageLength: 5,
            serverSide: true,
            ajax: "/unitgroups/getUnitGroupsData",
            columns: [
            {data: 'description'},
            {data: 'unit.actual_name'},
            {data: 'action', orderable: false, searchable: false}
            ]
        });
        $.fn.dataTable.ext.errMode = 'none';
        updateUnits();
        eupdateUnits();
    });

    //Muestra div para agregar grupo
    $("#new_group").click(function(){
        $("#lista").empty();
        cont = 0;
        unit_ids = [];
        $('#description').val('');
        var validator = $("#group_add_form").validate();
        validator.resetForm();
        setTimeout(function()
        {
            $('#description').focus();
        },
        800);
    });

    //Agregar Grupo
    $("#add_group_button").click(function(){
        if($("form#group_add_form").valid()){         
            $('#add_group_button').prop("disabled", true);
            $('#close_modal_button').prop("disabled", true);
            datastring = $("#group_add_form").serialize();
            route = "/unitgroups";
            token = $("#token").val();
            $.ajax({
                url: route,
                headers: {'X-CSRF-TOKEN': token},
                type: 'POST',
                dataType: 'json',
                data: datastring,
                success:function(result){
                    if (result.success == true){
                        $('#add_group_button').prop("disabled", false);
                        $('#close_modal_button').prop("disabled", false);
                        $("#lista").empty();
                        cont = 0;
                        unit_ids = [];
                        $('#description').val('');
                        $("#unit_parent").val("").change();
                        $("#unit_child").val("").change();
                        var validator = $("#group_add_form").validate();
                        validator.resetForm();

                        $("#group_table").DataTable().ajax.reload(null, false);
                        $("#modalAddGroup").modal('hide');
                        Swal.fire
                        ({
                            title: ""+result.msg+"",
                            icon: "success",
                        });
                    }
                    else{
                        Swal.fire
                        ({
                            title: ""+result.msg+"",
                            icon: "error",
                        });
                        $('#add_group_button').prop("disabled", false);
                        $('#close_modal_button').prop("disabled", false);
                    }
                }
            });
        }
    });

    //Actualizar Grupo
    $("#edit_group_button").click(function(){
        if($("form#group_edit_form").valid()){         
            $('#edit_group_button').prop("disabled", true);
            $('#close_modaledit_button').prop("disabled", true);
            datastring = $("#group_edit_form").serialize();
            id = $("#unitgroup_id").val();
            route = "/unitgroups/"+id;
            token = $("#token").val();
            $.ajax({
                url: route,
                headers: {'X-CSRF-TOKEN': token},
                type: 'PUT',
                dataType: 'json',
                data: datastring,
                success:function(result){
                    if (result.success == true){
                        $('#edit_group_button').prop("disabled", false);
                        $('#close_modaledit_button').prop("disabled", false);
                        $("#elista").empty();
                        econt = 0;
                        eunit_ids = [];
                        $('#edescription').val('');
                        $("#eunit_parent").val("").change();
                        $("#eunit_child").val("").change();
                        var validator = $("#group_edit_form").validate();
                        validator.resetForm();
                        $("#group_table").DataTable().ajax.reload(null, false);
                        $("#modalEditGroup").modal('hide');
                        Swal.fire
                        ({
                            title: ""+result.msg+"",
                            icon: "success",
                        });
                    }
                    else{
                        Swal.fire
                        ({
                            title: ""+result.msg+"",
                            icon: "error",
                        });
                        $('#edit_group_button').prop("disabled", false);
                        $('#close_modaledit_button').prop("disabled", false);
                    }
                }
            });
        }
    });

    //Valida formulario de crear
    $('form#group_add_form').validate(
    {
        rules:
        {
            description:
            {
                required: true,
            }
        }
    });

    //Valida formulario de actualizar
    $('form#group_edit_form').validate(
    {
        rules:
        {
            description:
            {
                required: true,
            }
        }
    });

    //Cargar modal con datos a editar
    function editUnitGroup(id){
        eupdateUnits();
        $("#unitgroup_id").val(id);
        $("#elista").empty();
        econt = 0;
        eunit_ids = [];
        $('#edescription').val('');
        var validator = $("#group_edit_form").validate();
        validator.resetForm();
        var route = "/unitgroups/"+id+"/edit";
        $('#edit_group_button').prop("disabled", true);
        $('#close_modaledit_button').prop("disabled", true);
        $("#content").show()
        $.get(route, function(res){
            $("#edescription").val(res.description);
            $("#eunit_parent").val(res.unit_id).change();
            $("#eunit_parent").prop('disabled', true);
            var route = "/unitgroups/groupHasLines/"+id
            $.get(route, function(res){
                $(res).each(function(key,value){
                    eunit_id = value.unit_id
                    ename = value.actual_name;
                    eshort_name = value.short_name;
                    eunit_ids.push(eunit_id);
                    var efila = '<tr class="selected" id="efila'+econt+'" style="height: 10px"><td><button id="bitem'+econt+'" type="button" class="btn btn-danger btn-xs remove-item" onclick="edeleteUnit('+econt+', '+eunit_id+');"><i class="fa fa-times"></i></button></td><td><input type="hidden" name="eunit_ids['+econt+']" value="'+eunit_id+'">'+ename+'</td><td>'+eshort_name+'</td><td><input type="text" id="efactor'+econt+'" name="efactor['+econt+']" class="form-control form-control-sm" value="'+value.factor+'" required></td></tr>';
                    $("#elista").append(efila);
                    econt++;
                });
                $("#content").hide();
                $('#edit_group_button').prop("disabled", false);
                $('#close_modaledit_button').prop("disabled", false);
            });
            $('#modalEditGroup').modal({backdrop: 'static', keyboard: false});
        });
    }

    //Eliminar Grupo
    function deleteUnitGroup(id){
        swal({
            title: LANG.sure,
            text: '{{__('messages.delete_content')}}',
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete){
                route = '/unitgroups/'+id;
                token = $("#token").val();
                $.ajax({
                    url: route,
                    headers: {'X-CSRF-TOKEN': token},
                    type: 'DELETE',
                    dataType: 'json',                       
                    success:function(result){
                        if(result.success == true){
                            Swal.fire
                            ({
                                title: result.msg,
                                icon: "success",
                            });
                            $("#group_table").DataTable().ajax.reload(null, false);
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

    //variables globales de array de medidas para tabla dinamica de agregar
    var cont = 0;
    var unit_ids = [];

    //metodo para agregar medida
    function addUnit()
    {
        var route = "/units/"+id;
        $.get(route, function(res){
          unit_id = res.id;
          name = res.actual_name;
          short_name = res.short_name;
          count = parseInt(jQuery.inArray(unit_id, unit_ids));
          if (count >= 0)
          {
            Swal.fire
            ({
                title: "{{__('unit.unit_already_added')}}",
                icon: "error",
            });
        }
        else
        {
            id_parent = $("#unit_parent").val();
            if(id == id_parent)
            {
                $("#unit_child").val("").change();
                Swal.fire
                ({
                    title: "{{__('unit.parent_added')}}",
                    icon: "error",
                });
            }
            else
            {
                unit_ids.push(unit_id);
                var fila = '<tr class="selected" id="fila'+cont+'" style="height: 10px"><td><button id="bitem'+cont+'" type="button" class="btn btn-danger btn-xs remove-item" onclick="deleteUnit('+cont+', '+unit_id+');"><i class="fa fa-times"></i></button></td><td><input type="hidden" name="unit_ids['+cont+']" value="'+unit_id+'">'+name+'</td><td>'+short_name+'</td><td><input type="text" id="factor'+cont+'" name="factor['+cont+']" class="form-control form-control-sm" required></td></tr>';
                $("#lista").append(fila);                
                $("#factor"+cont+"").focus();
                cont++;
            }
        }
    });
    }

    //evento change de select para agregar medidas
    $("#unit_child").change(function(event){
        id = $("#unit_child").val();
        if(id){
            if(id.length > 0)
            {
                addUnit();
            }
        }        
    });

    //evento change de cuenta padre
    $("#unit_parent").change(function(event){
        id = $("#unit_parent").val();
        var route = "/units/"+id;
        $.get(route, function(res){
            unit_id = res.id;
            count = parseInt(jQuery.inArray(unit_id, unit_ids));
            if (count >= 0){
                $("#unit_parent").val("").change();
                Swal.fire
                ({
                    title: "{{__('unit.parent_added')}}",
                    icon: "error",
                });
            }
        });
    });

    //metodo para eliminar del array de medidas
    function deleteUnit(index, id){ 
        $("#fila" + index).remove();
        unit_ids.removeItem(id);
    }

    //metodo para poder remover elementos de un array
    Array.prototype.removeItem = function (a) {
        for (var i = 0; i < this.length; i++) {
            if (this[i] == a) {
                for (var i2 = i; i2 < this.length - 1; i2++) {
                    this[i2] = this[i2 + 1];
                }
                this.length = this.length - 1;
                return;
            }
        }
    };


    //variables globales de array de medidas para tabla dinamica de editar
    var econt = 0;
    var eunit_ids = [];

    //metodo para agregar medida
    function eaddUnit()
    {
        var route = "/units/"+id;
        $.get(route, function(res){
            eunit_id = res.id;
            ename = res.actual_name;
            eshort_name = res.short_name;
            ecount = parseInt(jQuery.inArray(eunit_id, eunit_ids));
            if (ecount >= 0)
            {
                Swal.fire
                ({
                    title: "{{__('unit.unit_already_added')}}",
                    icon: "error",
                });
            }
            else
            {
                eid_parent = $("#eunit_parent").val();
                if(id == eid_parent)
                {
                    $("#eunit_child").val("").change();
                    Swal.fire
                    ({
                        title: "{{__('unit.parent_added')}}",
                        icon: "error",
                    });
                }
                else
                {
                    eunit_ids.push(eunit_id);
                    var efila = '<tr class="selected" id="efila'+econt+'" style="height: 10px"><td><button id="bitem'+econt+'" type="button" class="btn btn-danger btn-xs remove-item" onclick="edeleteUnit('+econt+', '+eunit_id+');"><i class="fa fa-times"></i></button></td><td><input type="hidden" name="eunit_ids['+econt+']" value="'+eunit_id+'">'+ename+'</td><td>'+eshort_name+'</td><td><input type="text" id="efactor'+econt+'" name="efactor['+econt+']" class="form-control form-control-sm" required></td></tr>';
                    $("#elista").append(efila);                
                    $("#efactor"+econt+"").focus();
                    econt++;
                }
            }
        });
    }

    //evento change de select para agregar medidas
    $("#eunit_child").change(function(event){
        id = $("#eunit_child").val();
        if(id){
            if(id.length > 0)
            {
                eaddUnit();
            }
        }
    });

    //evento change de cuenta padre
    $("#eunit_parent").change(function(event){
        id = $("#eunit_parent").val();
        if(id){
            var route = "/units/"+id;
            $.get(route, function(res){
                eunit_id = res.id;
                ecount = parseInt(jQuery.inArray(eunit_id, eunit_ids));
                if (ecount >= 0){
                    $("#eunit_parent").val("").change();
                    Swal.fire
                    ({
                        title: "{{__('unit.parent_added')}}",
                        icon: "error",
                    });
                }
            });
        }
    });

    //metodo para eliminar del array de medidas
    function edeleteUnit(index, id){
        $("#efila" + index).remove();
        eunit_ids.removeItem(id);
    }


    //métodos pata actualizar selects de medidas en caso de haber agregado nueva medida
    function updateUnits()
    {
        $("#unit_parent").empty();
        $("#unit_child").empty();
        var route = "/units/getUnits";
        $.get(route, function(res){
            $(res).each(function(key,value){
                $("#unit_parent").append('<option value="'+value.id+'">'+value.actual_name+'</option>');
                $("#unit_child").append('<option value="'+value.id+'">'+value.actual_name+'</option>');
            });
        });
    }

    function eupdateUnits()
    {
        $("#eunit_child").empty();
        var route = "/units/getUnits";
        $.get(route, function(res){
            $(res).each(function(key,value){
                $("#eunit_child").append('<option value="'+value.id+'">'+value.actual_name+'</option>');
            });
        });
    }

    $('.unit_modal').on('shown.bs.modal', function(){
        $('#allow_decimal').select2();
    });
</script>
@endsection
