@extends('layouts.app')
@section('title', __('geography.geography'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('geography.geography_settings')</h1>
    <!-- <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
        <li class="active">Here</li>
    </ol> -->
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-xs-12">
         <!--  <pos-tab-container> -->
            <div class="col-xs-12 pos-tab-container">
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 pos-tab-menu">
                    <div class="list-group">
                        <a href="#" class="list-group-item text-center active">@lang('geography.countries')</a>
                        <a href="#" class="list-group-item text-center">@lang('geography.zones')</a>
                        <a href="#" class="list-group-item text-center">@lang('geography.states')</a>
                        <a href="#" class="list-group-item text-center">@lang('geography.cities')</a>
                    </div>
                </div>
                <div class="col-lg-10 col-md-10 col-sm-10 col-xs-10 pos-tab">
                    <!-- tab 1 start -->
                    @include('geography.partials.settings_country')
                    <!-- tab 1 end -->

                    <!-- tab 2 start -->
                    @include('geography.partials.settings_zone')
                    <!-- tab 2 end -->

                    <!-- tab 3 start -->
                    @include('geography.partials.settings_state')
                    <!-- tab 3 end -->

                    <!-- tab 4 start -->
                    @include('geography.partials.settings_city')
                    <!-- tab 4 end -->    
                </div>
            </div>
            <!--  </pos-tab-container> -->
        </div>
    </div>


    <div tabindex="-1" class="modal fade" id="modal-add-country" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document" style="width: 40%">
            <form id="form-add-country" enctype="multipart/form-data">
                <input type="hidden" name="_token" value="{{ csrf_token() }}" id="token">
                <div class="modal-content" style="border-radius: 20px;">
                    <div class="modal-body">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        @include('geography.partials.form_add_country')
                    </div>
                    <div class="modal-footer">
                        <input type="submit" class="btn btn-primary" value="@lang('messages.save')" id="btn-add-country">
                        <button type="button" class="btn btn-danger" data-dismiss="modal" id="btn-close-modal-add-country">@lang('messages.close')</button>
                    </div>
                </div>
            </form>
        </div>
    </div>


    <div tabindex="-1" class="modal fade" id="modal-edit-country" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document" style="width: 40%">
            <form id="form-edit-country" enctype="multipart/form-data">
                <input type="hidden" name="_token" value="{{ csrf_token() }}" id="token">
                <div class="modal-content" style="border-radius: 20px;">
                    <div class="modal-body">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        @include('geography.partials.form_edit_country')
                    </div>
                    <div class="modal-footer">
                        <input type="submit" class="btn btn-primary" value="@lang('messages.save')" id="btn-edit-country">
                        <button type="button" class="btn btn-danger" data-dismiss="modal" id="btn-close-modal-edit-country">@lang('messages.close')</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div tabindex="-1" class="modal fade" id="modal-add-zone" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document" style="width: 40%">
            <form id="form-add-zone" enctype="multipart/form-data">
                <input type="hidden" name="_token" value="{{ csrf_token() }}" id="token">
                <div class="modal-content" style="border-radius: 20px;">
                    <div class="modal-body">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <h3>@lang('geography.add_zone')</h3>
                                <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <label>@lang('geography.name')</label>
                                    <input type="text" name="txt-name-zone" id="txt-name-zone" class="form-control" placeholder="@lang('geography.name')">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="button" class="btn btn-primary" value="@lang('messages.save')" id="btn-add-zone">
                        <button type="button" class="btn btn-danger" data-dismiss="modal" id="btn-close-modal-add-zone">@lang('messages.close')</button>
                    </div>
                </div>
            </form>
        </div>
    </div>


    <div tabindex="-1" class="modal fade" id="modal-edit-zone" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document" style="width: 40%">
            <form id="form-edit-zone" enctype="multipart/form-data">
                <input type="hidden" name="_token" value="{{ csrf_token() }}" id="token">
                <div class="modal-content" style="border-radius: 20px;">
                    <div class="modal-body">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <h3>@lang('geography.edit_zone')</h3>
                                <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <label>@lang('geography.name')</label>
                                    <input type="text" name="txt-ename-zone" id="txt-ename-zone" class="form-control" placeholder="@lang('geography.name')">
                                    <input type="hidden" name="zone_id" id="zone_id">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="button" class="btn btn-primary" value="@lang('messages.save')" id="btn-edit-zone">
                        <button type="button" class="btn btn-danger" data-dismiss="modal" id="btn-close-modal-edit-zone">@lang('messages.close')</button>
                    </div>
                </div>
            </form>
        </div>
    </div>


    <div tabindex="-1" class="modal fade" id="modal-add-state" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document" style="width: 40%">
            <form id="form-add-state" enctype="multipart/form-data">
                <input type="hidden" name="_token" value="{{ csrf_token() }}" id="token">
                <div class="modal-content" style="border-radius: 20px;">
                    <div class="modal-body">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <h3>@lang('geography.add_state')</h3>
                                <div class="row">
                                    <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                        <label>@lang('geography.name')</label>
                                        <input type="text" name="txt-name-state" id="txt-name-state" class="form-control" placeholder="@lang('geography.name')">
                                    </div>

                                    <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                        <label>@lang('geography.zip_code')</label>
                                        <input type="text" name="txt-zip_code-state" id="txt-zip_code-state" class="form-control" placeholder="@lang('geography.zip_code')">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                        <label>@lang('geography.country')</label>
                                        <select name="select-country_id-state" id="select-country_id-state" class="form-control select2" style="width: 100%">
                                            <option value="0" selected disabled>@lang('messages.please_select')</option>
                                            @foreach($countries as $item)
                                            <option value="{{$item->id}}">{{$item->name}}</option>
                                            @endforeach
                                        </select>                                        
                                    </div>
                                    <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                        <label>@lang('geography.zone')</label>
                                        <select name="select-zone_id-state" id="select-zone_id-state" class="form-control select2" style="width: 100%">
                                            <option value="0" selected disabled>@lang('messages.please_select')</option>
                                            @foreach($zones as $item)
                                            <option value="{{$item->id}}"> {{$item->name}} </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="button" class="btn btn-primary" value="@lang('messages.save')" id="btn-add-state">
                        <button type="button" class="btn btn-danger" data-dismiss="modal" id="btn-close-modal-add-state">@lang('messages.close')</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div tabindex="-1" class="modal fade" id="modal-edit-state" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document" style="width: 40%">
            <form id="form-edit-state" enctype="multipart/form-data">
                <input type="hidden" name="_token" value="{{ csrf_token() }}" id="token">
                <div class="modal-content" style="border-radius: 20px;">
                    <div class="modal-body">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <h3>@lang('geography.edit_state')</h3>
                                <div class="row">
                                    <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                        <label>@lang('geography.name')</label>
                                        <input type="text" name="txt-ename-state" id="txt-ename-state" class="form-control" placeholder="@lang('geography.name')">
                                        <input type="hidden" name="state_id" id="state_id">
                                    </div>
                                    <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                        <label>@lang('geography.zip_code')</label>
                                        <input type="text" name="txt-ezip_code-state" id="txt-ezip_code-state" class="form-control" placeholder="@lang('geography.zip_code')">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                        <label>@lang('geography.country')</label>
                                        <select name="eselect-country_id-state" id="eselect-country_id-state" class="form-control select2" style="width: 100%">
                                            <option value="0" selected disabled>@lang('messages.please_select')</option>
                                            @foreach($countries as $item)
                                            <option value="{{$item->id}}">{{$item->name}}</option>
                                            @endforeach
                                        </select>                                        
                                    </div>
                                    <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                        <label>@lang('geography.zone')</label>
                                        <select name="eselect-zone_id-state" id="eselect-zone_id-state" class="form-control select2" style="width: 100%">
                                            <option value="0" selected disabled>@lang('messages.please_select')</option>
                                            @foreach($zones as $item)
                                            <option value="{{$item->id}}"> {{$item->name}} </option>
                                            @endforeach
                                        </select>                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="button" class="btn btn-primary" value="@lang('messages.save')" id="btn-edit-state">
                        <button type="button" class="btn btn-danger" data-dismiss="modal" id="btn-close-modal-edit-state">@lang('messages.close')</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div tabindex="-1" class="modal fade" id="modal-add-city" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document" style="width: 40%">
            <form id="form-add-city" enctype="multipart/form-data">
                <input type="hidden" name="_token" value="{{ csrf_token() }}" id="token">
                <div class="modal-content" style="border-radius: 20px;">
                    <div class="modal-body">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <h3>@lang('geography.add_city')</h3>
                                <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <label>@lang('geography.name')</label>
                                    <input type="text" name="txt-name-city" id="txt-name-city" class="form-control" placeholder="@lang('geography.name')">
                                </div>
                                <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <label>@lang('geography.state')</label>
                                    <select name="select-state_id-city" id="select-state_id-city" class="form-control select2" style="width: 100%">
                                        <option value="0" selected disabled>@lang('messages.please_select')</option>
                                        @foreach($states as $item)
                                        <option value="{{$item->id}}">{{$item->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="button" class="btn btn-primary" value="@lang('messages.save')" id="btn-add-city">
                        <button type="button" class="btn btn-danger" data-dismiss="modal" id="btn-close-modal-add-city">@lang('messages.close')</button>
                    </div>
                </div>
            </form>
        </div>
    </div>


    <div tabindex="-1" class="modal fade" id="modal-edit-city" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document" style="width: 40%">
            <form id="form-edit-city" enctype="multipart/form-data">
                <input type="hidden" name="_token" value="{{ csrf_token() }}" id="token">
                <div class="modal-content" style="border-radius: 20px;">
                    <div class="modal-body">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <h3>@lang('geography.edit_city')</h3>
                                <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <label>@lang('geography.name')</label>
                                    <input type="text" name="txt-ename-city" id="txt-ename-city" class="form-control" placeholder="@lang('geography.name')">
                                    <input type="hidden" name="city_id" id="city_id">
                                </div>
                                <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <label>@lang('geography.state')</label>
                                    <select name="eselect-state_id-city" id="eselect-state_id-city" class="form-control select2" style="width: 100%">
                                        <option value="0" selected disabled>@lang('messages.please_select')</option>
                                        @foreach($states as $item)
                                        <option value="{{$item->id}}">{{$item->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="button" class="btn btn-primary" value="@lang('messages.save')" id="btn-edit-city">
                        <button type="button" class="btn btn-danger" data-dismiss="modal" id="btn-close-modal-edit-city">@lang('messages.close')</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

</section>
<!-- /.content -->
@endsection
@section('javascript')
<script type="text/javascript">


    $(document).ready(function()
    {
        loadCountriesData();
        loadZonesData();
        loadStatesData();
        loadCitiesData();
        $.fn.dataTable.ext.errMode = 'none';
    });

    function loadCountriesData()
    {
        var table = $("#countries-table").DataTable(
        {
            pageLength: 25,
            deferRender: true,
            processing: true,
            serverSide: true,
            ajax: "/geography/getCountriesData",
            columns: [
            {data: 'name'},
            {data: 'short_name'},
            {data: 'code'},
            {data: 'img', orderable: false, searchable: false},
            {data: null, render: function(data){
                edit_button = '<a class="btn btn-xs btn-primary" onClick="editCountry('+data.id+')"><i class="glyphicon glyphicon-edit"></i>@lang('messages.edit')</a>';
                delete_button = ' <a class="btn btn-xs btn-danger" onClick="deleteCountry('+data.id+')"><i class="glyphicon glyphicon-trash"></i>@lang('messages.delete')</a>';
                return edit_button + delete_button;
            } , orderable: false, searchable: false}
            ],
            columnDefs: [{
                    "targets": [0,1,2,3,4],
                    "className": "text-center"
                }]
        });
    }

    $(document).on('submit', 'form#form-add-country', function(e) {
        e.preventDefault();
        $("#btn-add-country").prop("disabled", true);
        $("#btn-close-modal-add-country").prop("disabled", true);  

        route = "/geography";
        token = $("#token").val();
        $.ajax({
            url: route,
            type: 'POST',
            headers: {'X-CSRF-TOKEN': token},
            data: new FormData($(this)[0]),
            processData: false,
            contentType: false,
            success:function(result){
                if (result.success == true) {
                    updateSelectCountries();
                    $("#btn-add-country").prop("disabled", false);
                    $("#btn-close-modal-add-country").prop("disabled", false); 
                    $("#countries-table").DataTable().ajax.reload(null, false);
                    Swal.fire
                    ({
                        title: result.msg,
                        icon: "success",
                    });
                    $("#modal-add-country").modal('hide');
                }
                else
                {
                    $("#btn-add-country").prop("disabled", false);
                    $("#btn-close-modal-add-country").prop("disabled", false);
                    Swal.fire
                    ({
                        title: result.msg,
                        icon: "error",
                    });
                }

            },
            error:function(msj){
                $("#btn-add-country").prop("disabled", false);
                $("#btn-close-modal-add-country").prop("disabled", false);
                var errormessages = "";
                $.each(msj.responseJSON.errors, function(i, field){
                    errormessages+="<li>"+field+"</li>";
                });
                Swal.fire
                ({
                    title: "{{__('accounting.errors')}}",
                    icon: "error",
                    html: "<ul>"+ errormessages+ "</ul>",
                });
            }
        });
    });

    $(document).on('submit', 'form#form-edit-country', function(e) {
        e.preventDefault();
        $("#btn-edit-country").prop("disabled", true);
        $("#btn-close-modal-edit-country").prop("disabled", true);
        id = $("#country_id").val();

        route = "/geography/update/"+id;
        token = $("#token").val();

        $.ajax({
            url: route,
            type: 'POST',
            headers: {'X-CSRF-TOKEN': token},
            data: new FormData($(this)[0]),
            processData: false,
            contentType: false,
            success:function(result){
                if (result.success == true) {
                    updateSelectCountries();
                    $("#btn-edit-country").prop("disabled", false);
                    $("#btn-close-modal-edit-country").prop("disabled", false); 
                    $("#countries-table").DataTable().ajax.reload(null, false);
                    $("#states-table").DataTable().ajax.reload(null, false);
                    Swal.fire
                    ({
                        title: result.msg,
                        icon: "success",
                    });
                    $("#modal-edit-country").modal('hide');
                }
                else
                {
                    $("#btn-edit-country").prop("disabled", false);
                    $("#btn-close-modal-edit-country").prop("disabled", false);
                    Swal.fire
                    ({
                        title: result.msg,
                        icon: "error",
                    });
                }
            },
            error:function(msj){
                $("#btn-edit-country").prop("disabled", false);
                $("#btn-close-modal-edit-country").prop("disabled", false);
                var errormessages = "";
                $.each(msj.responseJSON.errors, function(i, field){
                    errormessages+="<li>"+field+"</li>";
                });
                Swal.fire
                ({
                    title: "{{__('accounting.errors')}}",
                    icon: "error",
                    html: "<ul>"+ errormessages+ "</ul>",
                });
            }
        });
    });

    $("#btn-new-country").click(function(){
        $("#name").val('');
        $("#short_name").val('');
        $("#code").val('');
        $("#flag").val('');
        $("#country_id").val('');
        setTimeout(function()
        {               
            $('#name').focus();
        },
        800);
    });

    function deleteCountry(id)
    {
        Swal.fire({
            title: LANG.sure,
            text: '{{__('messages.delete_content')}}',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: "{{__('messages.accept')}}",
            cancelButtonText: "{{__('messages.cancel')}}"
        }).then((willDelete) => {
            if (willDelete.value) {
               route = '/geography/'+id;
               token = $("#token").val();
               $.ajax({                    
                url: route,
                headers: {'X-CSRF-TOKEN': token},
                type: 'DELETE',
                dataType: 'json',                       
                success:function(result){
                    if(result.success == true){
                        updateSelectCountries();
                        Swal.fire
                        ({
                            title: result.msg,
                            icon: "success",
                        });
                        $("#countries-table").DataTable().ajax.reload(null, false);
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

    function editCountry(id)
    {
        var route = "/geography/"+id+"/edit";
        $.get(route, function(res) {
            $("#eflag").val('');
            $('#country_id').val(res.id);
            $('#ename').val(res.name);
            $('#eshort_name').val(res.short_name);
            $('#ecode').val(res.code);

            $('#modal-edit-country').modal({backdrop: 'static'});
        });
    }




    function loadZonesData()
    {
        var table = $("#zones-table").DataTable(
        {
            pageLength: 25,
            deferRender: true,
            processing: true,
            serverSide: true,
            ajax: "/zones/getZonesData",
            columns: [
            {data: 'name'},
            {data: null, render: function(data){
                edit_button = '<a class="btn btn-xs btn-primary" onClick="editZone('+data.id+')"><i class="glyphicon glyphicon-edit"></i>@lang('messages.edit')</a>';
                delete_button = ' <a class="btn btn-xs btn-danger" onClick="deleteZone('+data.id+')"><i class="glyphicon glyphicon-trash"></i>@lang('messages.delete')</a>';
                return edit_button + delete_button;
            } , orderable: false, searchable: false}
            ]
        });
    }

    $("#btn-add-zone").click(function(){
        $("#btn-add-zone").prop("disabled", true);
        $("#btn-close-modal-add-zone").prop("disabled", true);
        name = $("#txt-name-zone").val();
        route = "/zones";
        token = $("#token").val();
        $.ajax({
            url: route,
            headers: {'X-CSRF-TOKEN': token},
            type: 'POST',
            dataType: 'json',
            data:{
                name: name
            },
            success:function(result){
                if (result.success == true) {
                    updateSelectZones();
                    $("#btn-add-zone").prop("disabled", false);
                    $("#btn-close-modal-add-zone").prop("disabled", false); 
                    $("#zones-table").DataTable().ajax.reload(null, false);
                    Swal.fire
                    ({
                        title: result.msg,
                        icon: "success",
                    });
                    $("#modal-add-zone").modal('hide');
                }
                else
                {
                    $("#btn-add-zone").prop("disabled", false);
                    $("#btn-close-modal-add-zone").prop("disabled", false);
                    Swal.fire
                    ({
                        title: result.msg,
                        icon: "error",
                    });
                }
            },
            error:function(msj){
                $("#btn-add-zone").prop("disabled", false);
                $("#btn-close-modal-add-zone").prop("disabled", false);
                var errormessages = "";
                $.each(msj.responseJSON.errors, function(i, field){
                    errormessages+="<li>"+field+"</li>";
                });
                Swal.fire
                ({
                    title: "{{__('accounting.errors')}}",
                    icon: "error",
                    html: "<ul>"+ errormessages+ "</ul>",
                });
            }
        });
    });

    $("#btn-edit-zone").click(function(){
        $("#btn-edit-zone").prop("disabled", true);
        $("#btn-close-modal-edit-zone").prop("disabled", true);
        id = $("#zone_id").val();
        name = $("#txt-ename-zone").val();

        route = "/zones/"+id;
        token = $("#token").val();

        $.ajax({
            url: route,
            headers: {'X-CSRF-TOKEN': token},
            type: 'PUT',            
            dataType: 'json',
            data:{
                name: name
            },
            success:function(result){
                if (result.success == true) {
                    updateSelectZones();
                    $("#btn-edit-zone").prop("disabled", false);
                    $("#btn-close-modal-edit-zone").prop("disabled", false); 
                    $("#zones-table").DataTable().ajax.reload(null, false);
                    $("#states-table").DataTable().ajax.reload(null, false);
                    Swal.fire
                    ({
                        title: result.msg,
                        icon: "success",
                    });
                    $("#modal-edit-zone").modal('hide');
                }
                else
                {
                    $("#btn-edit-zone").prop("disabled", false);
                    $("#btn-close-modal-edit-zone").prop("disabled", false);
                    Swal.fire
                    ({
                        title: result.msg,
                        icon: "error",
                    });
                }
            },
            error:function(msj){
                $("#btn-edit-zone").prop("disabled", false);
                $("#btn-close-modal-edit-zone").prop("disabled", false);
                var errormessages = "";
                $.each(msj.responseJSON.errors, function(i, field){
                    errormessages+="<li>"+field+"</li>";
                });
                Swal.fire
                ({
                    title: "{{__('accounting.errors')}}",
                    icon: "error",
                    html: "<ul>"+ errormessages+ "</ul>",
                });
            }
        });
    });

    $("#btn-new-zone").click(function(){
        $("#txt-name-zone").val('');
        setTimeout(function()
        {               
            $('#txt-name-zone').focus();
        },
        800);
    });

    function deleteZone(id)
    {
        Swal.fire({
            title: LANG.sure,
            text: '{{__('messages.delete_content')}}',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: "{{__('messages.accept')}}",
            cancelButtonText: "{{__('messages.cancel')}}"
        }).then((willDelete) => {
            if (willDelete.value) {
               route = '/zones/'+id;
               token = $("#token").val();
               $.ajax({                    
                url: route,
                headers: {'X-CSRF-TOKEN': token},
                type: 'DELETE',
                dataType: 'json',                       
                success:function(result){
                    if(result.success == true){
                        updateSelectZones();
                        Swal.fire
                        ({
                            title: result.msg,
                            icon: "success",
                        });
                        $("#zones-table").DataTable().ajax.reload(null, false);
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

    function editZone(id)
    {
        var route = "/zones/"+id+"/edit";
        $.get(route, function(res) {

            $('#zone_id').val(res.id);
            $('#txt-ename-zone').val(res.name);
            
            $('#modal-edit-zone').modal({backdrop: 'static'});
        });
    }




    function loadStatesData()
    {
        var table = $("#states-table").DataTable(
        {
            pageLength: 25,
            deferRender: true,
            processing: true,
            serverSide: true,
            ajax: "/states/getStatesData",
            columns: [
            {data: 'name'},
            {data: 'zip_code'},
            {data: 'country.name'},
            {data: 'zone.name'},
            {data: null, render: function(data){
                edit_button = '<a class="btn btn-xs btn-primary" onClick="editState('+data.id+')"><i class="glyphicon glyphicon-edit"></i>@lang('messages.edit')</a>';
                delete_button = ' <a class="btn btn-xs btn-danger" onClick="deleteState('+data.id+')"><i class="glyphicon glyphicon-trash"></i>@lang('messages.delete')</a>';
                return edit_button + delete_button;
            } , orderable: false, searchable: false}
            ],
            columnDefs: [{
                    "targets": [0,1,2,3,4],
                    "className": "text-center"
                }]
        });
    }

    $("#btn-add-state").click(function(){
        $("#btn-add-state").prop("disabled", true);
        $("#btn-close-modal-add-state").prop("disabled", true);
        name = $("#txt-name-state").val();
        zip_code = $("#txt-zip_code-state").val();
        country_id = $("#select-country_id-state").val();
        zone_id = $("#select-zone_id-state").val();
        route = "/states";
        token = $("#token").val();
        $.ajax({
            url: route,
            headers: {'X-CSRF-TOKEN': token},
            type: 'POST',
            dataType: 'json',
            data:{
                name: name,
                zip_code: zip_code,
                country_id: country_id,
                zone_id: zone_id
            },
            success:function(result){
                if (result.success == true) {
                    updateSelectStates();
                    $("#btn-add-state").prop("disabled", false);
                    $("#btn-close-modal-add-state").prop("disabled", false); 
                    $("#states-table").DataTable().ajax.reload(null, false);
                    Swal.fire
                    ({
                        title: result.msg,
                        icon: "success",
                    });
                    $("#modal-add-state").modal('hide');
                }
                else
                {
                    $("#btn-add-state").prop("disabled", false);
                    $("#btn-close-modal-add-state").prop("disabled", false);
                    Swal.fire
                    ({
                        title: result.msg,
                        icon: "error",
                    });
                }
            },
            error:function(msj){
                $("#btn-add-state").prop("disabled", false);
                $("#btn-close-modal-add-state").prop("disabled", false);
                var errormessages = "";
                $.each(msj.responseJSON.errors, function(i, field){
                    errormessages+="<li>"+field+"</li>";
                });
                Swal.fire
                ({
                    title: "{{__('accounting.errors')}}",
                    icon: "error",
                    html: "<ul>"+ errormessages+ "</ul>",
                });
            }
        });
    });

    $("#btn-edit-state").click(function(){
        $("#btn-edit-state").prop("disabled", true);
        $("#btn-close-modal-edit-state").prop("disabled", true);

        id = $("#state_id").val();
        name = $("#txt-ename-state").val();
        zip_code = $("#txt-ezip_code-state").val();
        country_id = $("#eselect-country_id-state").val();
        zone_id = $("#eselect-zone_id-state").val();

        route = "/states/"+id;
        token = $("#token").val();

        $.ajax({
            url: route,
            headers: {'X-CSRF-TOKEN': token},
            type: 'PUT',            
            dataType: 'json',
            data:{
                name: name,
                zip_code: zip_code,
                country_id: country_id,
                zone_id: zone_id
            },
            success:function(result){
                if (result.success == true) {
                    updateSelectStates();
                    $("#btn-edit-state").prop("disabled", false);
                    $("#btn-close-modal-edit-state").prop("disabled", false); 
                    $("#states-table").DataTable().ajax.reload(null, false);
                    $("#cities-table").DataTable().ajax.reload(null, false);
                    Swal.fire
                    ({
                        title: result.msg,
                        icon: "success",
                    });
                    $("#modal-edit-state").modal('hide');
                }
                else
                {
                    $("#btn-edit-state").prop("disabled", false);
                    $("#btn-close-modal-edit-state").prop("disabled", false);
                    Swal.fire
                    ({
                        title: result.msg,
                        icon: "error",
                    });
                }
            },
            error:function(msj){
                $("#btn-edit-state").prop("disabled", false);
                $("#btn-close-modal-edit-state").prop("disabled", false);
                var errormessages = "";
                $.each(msj.responseJSON.errors, function(i, field){
                    errormessages+="<li>"+field+"</li>";
                });
                Swal.fire
                ({
                    title: "{{__('accounting.errors')}}",
                    icon: "error",
                    html: "<ul>"+ errormessages+ "</ul>",
                });
            }
        });
    });

    $("#btn-new-state").click(function(){
        $("#txt-name-state").val('');
        $("#txt-zip_code-state").val('');
        $("#select-country_id-state").val(0).change();
        $("#select-zone_id-state").val(0).change();
        setTimeout(function()
        {               
            $('#txt-name-state').focus();
        },
        800);
    });

    function deleteState(id)
    {
        Swal.fire({
            title: LANG.sure,
            text: '{{__('messages.delete_content')}}',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: "{{__('messages.accept')}}",
            cancelButtonText: "{{__('messages.cancel')}}"
        }).then((willDelete) => {
            if (willDelete.value) {
               route = '/states/'+id;
               token = $("#token").val();
               $.ajax({                    
                url: route,
                headers: {'X-CSRF-TOKEN': token},
                type: 'DELETE',
                dataType: 'json',                       
                success:function(result){
                    if(result.success == true){
                        updateSelectStates();
                        Swal.fire
                        ({
                            title: result.msg,
                            icon: "success",
                        });
                        $("#states-table").DataTable().ajax.reload(null, false);
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

    function editState(id)
    {
        var route = "/states/"+id+"/edit";
        $.get(route, function(res) {

            $('#state_id').val(res.id);
            $('#txt-ename-state').val(res.name);
            $('#txt-ezip_code-state').val(res.zip_code);

            $('#eselect-country_id-state').val(res.country_id).change();
            $('#eselect-zone_id-state').val(res.zone_id).change();
            
            $('#modal-edit-state').modal({backdrop: 'static'});
        });
    }



    function loadCitiesData()
    {
        var table = $("#cities-table").DataTable(
        {
            pageLength: 25,
            deferRender: true,
            processing: true,
            serverSide: true,
            ajax: "/cities/getCitiesData",
            columns: [
            {data: 'name'},
            {data: 'state.name'},
            {data: null, render: function(data){
                if(data.status == 1) {
                    status = '<input type="checkbox" name="status" id="status" class="input-icheck" checked onClick="changeStatus('+data.id+')">';
                }
                else {
                    status = '<input type="checkbox" name="status" id="status" class="input-icheck" onClick="changeStatus('+data.id+')">';
                }
                return status;
            } , orderable: false, searchable: false},
            {data: null, render: function(data){
                edit_button = '<a class="btn btn-xs btn-primary" onClick="editCity('+data.id+')"><i class="glyphicon glyphicon-edit"></i>@lang('messages.edit')</a>';
                delete_button = ' <a class="btn btn-xs btn-danger" onClick="deleteCity('+data.id+')"><i class="glyphicon glyphicon-trash"></i>@lang('messages.delete')</a>';
                return edit_button + delete_button;
            } , orderable: false, searchable: false}
            ],
            columnDefs: [{
                    "targets": [0,1,2,3],
                    "className": "text-center"
                }]
        });
    }

    $("#btn-add-city").click(function(){
        $("#btn-add-city").prop("disabled", true);
        $("#btn-close-modal-add-city").prop("disabled", true);
        name = $("#txt-name-city").val();
        state_id = $("#select-state_id-city").val();
        route = "/cities";
        token = $("#token").val();
        $.ajax({
            url: route,
            headers: {'X-CSRF-TOKEN': token},
            type: 'POST',
            dataType: 'json',
            data:{
                name: name,
                state_id: state_id
            },
            success:function(result){
                if (result.success == true) {
                    $("#btn-add-city").prop("disabled", false);
                    $("#btn-close-modal-add-city").prop("disabled", false); 
                    $("#cities-table").DataTable().ajax.reload(null, false);
                    Swal.fire
                    ({
                        title: result.msg,
                        icon: "success",
                    });
                    $("#modal-add-city").modal('hide');
                }
                else
                {
                    $("#btn-add-city").prop("disabled", false);
                    $("#btn-close-modal-add-city").prop("disabled", false);
                    Swal.fire
                    ({
                        title: result.msg,
                        icon: "error",
                    });
                }
            },
            error:function(msj){
                $("#btn-add-city").prop("disabled", false);
                $("#btn-close-modal-add-city").prop("disabled", false);
                var errormessages = "";
                $.each(msj.responseJSON.errors, function(i, field){
                    errormessages+="<li>"+field+"</li>";
                });
                Swal.fire
                ({
                    title: "{{__('accounting.errors')}}",
                    icon: "error",
                    html: "<ul>"+ errormessages+ "</ul>",
                });
            }
        });
    });

    $("#btn-edit-city").click(function(){
        $("#btn-edit-city").prop("disabled", true);
        $("#btn-close-modal-edit-city").prop("disabled", true);

        id = $("#city_id").val();
        name = $("#txt-ename-city").val();
        state_id = $("#eselect-state_id-city").val();

        route = "/cities/"+id;
        token = $("#token").val();

        $.ajax({
            url: route,
            headers: {'X-CSRF-TOKEN': token},
            type: 'PUT',            
            dataType: 'json',
            data:{
                name: name,
                state_id: state_id
            },
            success:function(result){
                if (result.success == true) {
                    $("#btn-edit-city").prop("disabled", false);
                    $("#btn-close-modal-edit-city").prop("disabled", false); 
                    $("#cities-table").DataTable().ajax.reload(null, false);
                    Swal.fire
                    ({
                        title: result.msg,
                        icon: "success",
                    });
                    $("#modal-edit-city").modal('hide');
                }
                else
                {
                    $("#btn-edit-city").prop("disabled", false);
                    $("#btn-close-modal-edit-city").prop("disabled", false);
                    Swal.fire
                    ({
                        title: result.msg,
                        icon: "error",
                    });
                }
            },
            error:function(msj){
                $("#btn-edit-city").prop("disabled", false);
                $("#btn-close-modal-edit-city").prop("disabled", false);
                var errormessages = "";
                $.each(msj.responseJSON.errors, function(i, field){
                    errormessages+="<li>"+field+"</li>";
                });
                Swal.fire
                ({
                    title: "{{__('accounting.errors')}}",
                    icon: "error",
                    html: "<ul>"+ errormessages+ "</ul>",
                });
            }
        });
    });

    $("#btn-new-city").click(function(){
        $("#txt-name-city").val('');
        $("#select-state_id-city").val(0).change();
        setTimeout(function()
        {               
            $('#txt-name-city').focus();
        },
        800);
    });

    function deleteCity(id)
    {
        Swal.fire({
            title: LANG.sure,
            text: '{{__('messages.delete_content')}}',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: "{{__('messages.accept')}}",
            cancelButtonText: "{{__('messages.cancel')}}"
        }).then((willDelete) => {
            if (willDelete.value) {
               route = '/cities/'+id;
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
                        $("#cities-table").DataTable().ajax.reload(null, false);
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

    function editCity(id)
    {
        var route = "/cities/"+id+"/edit";
        $.get(route, function(res) {

            $('#city_id').val(res.id);
            $('#txt-ename-city').val(res.name);
            $('#eselect-state_id-city').val(res.state_id).change();
            
            $('#modal-edit-city').modal({backdrop: 'static'});
        });
    }

    function changeStatus(id)
    {
        var route = "/cities/changeStatus/"+id;
        $.get(route, function(result) {
            if (result.success == true) {
                $("#cities-table").DataTable().ajax.reload(null, false);
                Swal.fire
                ({
                    title: result.msg,
                    icon: "success",
                });
            }
            else {
                Swal.fire
                ({
                    title: result.msg,
                    icon: "error",
                });
            }
        });
    }

    function updateSelectCountries()
    {
        $("#select-country_id-state").empty();
        $("#eselect-country_id-state").empty();

        
        var route = "/geography/getCountries";
        $.get(route, function(res){
            $("#select-country_id-state").append('<option value="0" disabled selected>{{ __('messages.please_select') }}</option>');
            $("#eselect-country_id-state").append('<option value="0" disabled selected>{{ __('messages.please_select') }}</option>');

            $(res).each(function(key,value){
                $("#select-country_id-state").append('<option value="'+value.id+'">'+value.name+'</option>');
                $("#eselect-country_id-state").append('<option value="'+value.id+'">'+value.name+'</option>');
            });
        });
    }

    function updateSelectZones()
    {
        $("#select-zone_id-state").empty();
        $("#eselect-zone_id-state").empty();

        
        var route = "/zones/getZones";
        $.get(route, function(res){
            $("#select-zone_id-state").append('<option value="0" disabled selected>{{ __('messages.please_select') }}</option>');
            $("#eselect-zone_id-state").append('<option value="0" disabled selected>{{ __('messages.please_select') }}</option>');

            $(res).each(function(key,value){
                $("#select-zone_id-state").append('<option value="'+value.id+'">'+value.name+'</option>');
                $("#eselect-zone_id-state").append('<option value="'+value.id+'">'+value.name+'</option>');
            });
        });
    }

    function updateSelectStates()
    {
        $("#select-state_id-city").empty();
        $("#eselect-state_id-city").empty();

        
        var route = "/states/getStates";
        $.get(route, function(res){
            $("#select-state_id-city").append('<option value="0" disabled selected>{{ __('messages.please_select') }}</option>');
            $("#eselect-state_id-city").append('<option value="0" disabled selected>{{ __('messages.please_select') }}</option>');

            $(res).each(function(key,value){
                $("#select-state_id-city").append('<option value="'+value.id+'">'+value.name+'</option>');
                $("#eselect-state_id-city").append('<option value="'+value.id+'">'+value.name+'</option>');
            });
        });
    }

</script>
@endsection