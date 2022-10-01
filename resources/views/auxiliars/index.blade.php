@extends('layouts.app')
@section('title', __('accounting.tittle_auxiliars'))
@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('accounting.auxiliars_menu')
        <small>@lang('accounting.report')</small>
    </h1>
    <!-- <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
        <li class="active">Here</li>
    </ol> -->
</section>
<!-- Main content -->
<section class="content">
    <div class="panel with-nav-tabs panel-default boxform_u box-solid_u">
        <div class="panel-heading">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#tab-list" id="tab_list" data-toggle="tab">@lang('accounting.list')</a></li>
                <li><a href="#tab-report" id="tab_report" data-toggle="tab">@lang('accounting.report')</a></li>
            </ul>
        </div>
        <div class="panel-body">
            <div class="tab-content">
                <div class="tab-pane fade in active" id="tab-list">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">           
                            <div class="form-group col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                <label>@lang('accounting.accounts')</label>
                                <select id="account" class="form-control select2" style="width: 100%">
                                    <option value="0" disabled selected>@lang('accounting.search_account')</option>
                                    @foreach($accounts as $account)
                                    <option value="{{ $account->id }}">{{ $account->code }} {{ $account->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-lg-4 col-md-4 col-sm-4 col-xs-12">                
                                <label>@lang('accounting.from')</label>
                                <div class="wrap-inputform">
                                    <span class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </span>
                                    {!! Form::date('from_search', \Carbon\Carbon::now()->format('Y-m-d'), ['id'=>'from_search', 'class'=>'inputform2']) !!}
                                </div>

                            </div>
                            <div class="form-group col-lg-4 col-md-4 col-sm-4 col-xs-12">                
                                <label>@lang('accounting.from')</label>
                                <div class="wrap-inputform">
                                    <span class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </span>
                                    {!! Form::date('to_search', \Carbon\Carbon::now()->format('Y-m-d'), ['id'=>'to_search', 'class'=>'inputform2']) !!}
                                </div>
                            </div>                            

                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-condensed table-hover" id="auxiliarData" width="100%">
                                    <thead id="thead-auxiliars">
                                        <th>@lang('accounting.number')</th>
                                        <th>@lang('accounting.date')</th>
                                        <th>@lang('accounting.entrie')</th>
                                        <th>@lang('accounting.detail')</th>
                                        <th>@lang('accounting.debit')</th>
                                        <th>@lang('accounting.credit')</th>
                                        <th>@lang('accounting.balance')</th>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="tab-report">
                    <h4>@lang('accounting.auxiliars_report')</h4>
                    {!! Form::open(['id'=>'form_auxiliar', 'action' => 'ReporterController@getAllAuxiliarReport', 'method' => 'post', 'target' => '_blank']) !!}
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <div id="msj-errors" class="alert alert-danger alert-dismissible" role="alert" style="display: none;">              
                                <strong id="msj"></strong>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <div class="form-group float-left col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}" id="token">
                                <label>@lang('accounting.from')</label>
                                <select name="account_from" id="account_from" class="form-control select2" style="width: 100%">
                                    <option value="0" disabled selected>@lang('messages.please_select')</option>
                                    @foreach($clasifications as $account)
                                    <option value="{{ $account->id }}">{{ $account->code }} {{ $account->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group float-left col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                <label>@lang('accounting.to')</label>
                                <select name="account_to" id="account_to" class="form-control select2" style="width: 100%">
                                    <option value="0" disabled selected>@lang('messages.please_select')</option>
                                    @foreach($clasifications as $account)
                                    <option value="{{ $account->id }}">{{ $account->code }} {{ $account->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group float-left col-lg-2 col-md-2 col-sm-2 col-xs-12" style="margin-top: 22px;">
                                <button type="button" id="btn-range" class="btn btn-primary">@lang('accounting.add_range')</button>
                            </div>
                            <div class="form-group float-left col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                <label>@lang('accounting.add_account')</label>
                                <select name="account_auxiliars" id="account_auxiliars" class="form-control select2" style="width: 100%">
                                    <option value="0" disabled selected>@lang('messages.please_select')</option>
                                    <option value="-1">@lang('accounting.all')</option>
                                    @foreach($accounts as $account)
                                    <option value="{{ $account->id }}">{{ $account->code }} {{ $account->name }}</option>
                                    @endforeach
                                </select>
                            </div>                       
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <div class="form-group float-left col-lg-4 col-md-4 col-sm-4 col-xs-12">                
                                <label>@lang('accounting.from')</label>
                                <div class="wrap-inputform">
                                    <span class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </span>
                                    {!! Form::date('from', \Carbon\Carbon::now()->format('Y-m-d'), ['name'=>'from', 'id'=>'from', 'class'=>'inputform2']) !!}
                                </div>

                            </div>
                            <div class="form-group float-left col-lg-4 col-md-4 col-sm-4 col-xs-12">                
                                <label>@lang('accounting.to')</label>
                                <div class="wrap-inputform">
                                    <span class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </span>
                                    {!! Form::date('to', \Carbon\Carbon::now()->format('Y-m-d'), ['name'=>'to', 'id'=>'to', 'class'=>'inputform2']) !!}
                                </div>
                            </div>

                            <div class="form-group float-left col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                <label>@lang('accounting.format')</label>
                                <select name="report-type" id="report-type" class="form-control select2" style="width: 100%">
                                    <option value="pdf" selected>PDF</option>
                                    <option value="excel">Excel</option>
                                </select>                       
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="left col-lg-3 col-md-3 col-sm-3 col-xs-12">
                            <label>@lang('accounting.size_font')</label>
                            <select name="size" id="size" class="form-control select2" style="width: 100%;">
                                <option value="7">7</option>
                                <option value="8">8</option>
                                <option value="9" selected>9</option>
                                <option value="10">10</option>
                            </select>                       
                        </div>
                    </div>
                    <div class="row">
                        <div id="content" class="col-lg-12" style="display: none;">
                            @lang('accounting.wait_please')...
                            <img src="{{ asset('img/loader.gif') }}" alt="loading" />
                        </div>
                    </div>
                    <div class="row" style="margin-top: 10px;">
                        <div class="form-group float-left col-lg-4 col-md-4 col-sm-4 col-xs-12" style="display: none;" id="button_report">          
                            <input type="submit" class="btn btn-success" value="@lang('accounting.generate')" id="report_pdf">
                            <input type="button" class="btn btn-danger" value="@lang('accounting.clean')" id="blimpiar">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                            <table width="100%" class="table" id="auxiliares">
                                <thead>
                                    <tr>
                                        <th colspan="2">@lang('accounting.accounts')</th>
                                    </tr>
                                </thead>
                                <tbody id="lista">                  
                                </tbody>
                                <tfoot>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</section>
<!-- /.content -->
@endsection
@section('javascript')
<script>
    var cont=0;
    valor=[];
    id_a=[];
    
    $("#account").change(function(event){
        id = $("#account").val();
        if (id != null) {
            loadData();
        }
    });
    $("#from_search").change(function(event){
        id = $("#account").val();
        if (id != null) {
            loadData();
        }
    });
    $("#to_search").change(function(event){
        id = $("#account").val();
        if (id != null) {
            loadData();
        }
    });
    $("#btn-range").click(function() {
        start = $("#account_from").val();
        end = $("#account_to").val();

        if ((start != null) && (end != null)) {
            $("#content").show();
            $("#btn-range").prop('disabled', true);
            $("#auxiliares tbody tr").remove();
            cont = 0;
            id_a =[];
            var route = "/auxiliars/getAuxiliarRange/"+start+"/"+end+"";
            $.get(route, function(res){
                $(res).each(function(key,value)
                {
                    id_c = value.id;
                    code = value.code;
                    name = value.name;
                    id_a.push(id_c);
                    valor.push(cont);
                    fila = '<tr class="selected" id="fila'+cont+'" style="height: 10px"><td style="width: 5%"><button id="bitem'+cont+'" type="button" class="btn btn-warning btn-sm" onclick="eliminar('+cont+', '+id_c+');">X</button></td><td style="width: 95%"><input type="hidden" name="id[]" id="id[]" value="'+id_c+'">'+code+' '+name+'</td></tr>';
                    $('#lista').append(fila);
                    cont++;
                });
                $("#account_from").val(0).change();
                $("#account_to").val(0).change();
                if(id_a.length > 0)
                {
                    $("#button_report").show();
                }
                $("#content").hide();
                $("#btn-range").prop('disabled', false);
            });
        }
    });

    function loadData()
    {
        var table = $("#auxiliarData").DataTable();
        table.clear().destroy();
        var table = $("#auxiliarData").DataTable(
        {
            order: [[ 0, "asc" ]],
            columnDefs: [
            { "visible": false, "targets": [0] }
            ],
            processing: true,
            serverSide: true,
            ajax: "/auxiliars/getAuxiliarDetail/"+$("#account").val()+"/"+$("#from_search").val()+"/"+$("#to_search").val()+"",
            columns: [
            {data: 'cont', orderable: false},
            {data: 'date', orderable: false},
            {data: 'correlative', orderable: false},
            {data: 'description', orderable: false},
            {data: 'debit', className: "text-right",  orderable: false},
            {data: 'credit', className: "text-right",  orderable: false},
            {data: 'balance', className: "text-right", orderable: false}
            ]
        });
    }

    $(document).ready(function()
    {
        $.fn.dataTable.ext.errMode = 'none';
    });

    $("#account_auxiliars").change(function(event) {
        id = $("#account_auxiliars").val()
        if (id != null) {
            $("#content").show();
            $("#btn-range").prop('disabled', true);

            if(id != 0 && id != -1)
            {
                agregar();
            }
            if(id == -1)
            {
                $("#auxiliares tbody tr").remove();
                cont = 0;
                id_a =[];
                var route = "/auxiliars/getAuxiliarDetails";
                $.get(route, function(res){
                    $(res).each(function(key,value)
                    {
                        id_c = value.id;
                        code = value.code;
                        name = value.name;
                        id_a.push(id_c);
                        valor.push(cont);
                        fila = '<tr class="selected" id="fila'+cont+'" style="height: 10px"><td style="width: 5%"><button id="bitem'+cont+'" type="button" class="btn btn-warning btn-sm" onclick="eliminar('+cont+', '+id_c+');">X</button></td><td style="width: 95%"><input type="hidden" name="id[]" id="id[]" value="'+id_c+'">'+code+' '+name+'</td></tr>';
                        $('#lista').append(fila);
                        cont++;
                        $("#content").hide();
                        $("#btn-range").prop('disabled', false);
                    });
                    $("#account_auxiliars").val(0).change();
                    if($('#account_auxiliars option').length > 2)
                    {
                        $("#button_report").show();
                    }
                });
            }
        }
    });

    function agregar()
    {
        var route = "/catalogue/"+id;
        $.get(route, function(res){
            id_c = res.id;
            code = res.code;
            name = res.name;
            existe = parseInt(jQuery.inArray(id_c, id_a));
            if (existe >= 0)
            {
                $('#msj').html("@lang('accounting.account_already_added')");
                $("#msj-errors").fadeIn();
                $("#msj-errors").fadeOut();
            }
            else
            {
                id_a.push(id_c);
                valor.push(cont);
                var fila='<tr class="selected" id="fila'+cont+'" style="height: 10px"><td style="width: 5%"><button type="button" id="bitem'+cont+'" class="btn btn-warning btn-sm" onclick="eliminar('+cont+', '+id_c+');">X</button></td><td style="width: 95%"><input type="hidden" name="id[]" id="id[]" value="'+id_c+'">'+code+' '+name+'</td></tr>'
                $("#lista").append(fila);
                $("#button_report").show()
                cont++;
            }
            $("#account_auxiliars").val(0).change();
            $("#content").hide();
            $("#btn-range").prop('disabled', false);
        });
    }

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
    function eliminar(index, id_p){ 
        $("#fila" + index).remove();
        id_a.removeItem(id_p);
        if(id_a.length == 0)
        {
            $("#button_report").hide();
        }
    }
    
    function limpiar()
    {
        $("#auxiliares tbody tr").remove();
        cont = 0;
        id_a=[];
        valor.length = 0;
        $("#button_report").hide();
    }
    $("#blimpiar").click(function(){
        limpiar();
    });

    $("#tab_list").click(function(){
        $("#thead-auxiliars").show();
    });

    $("#tab_report").click(function(){
        $("#thead-auxiliars").hide();
    });
</script>
@endsection