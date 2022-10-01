@extends('layouts.app')
@section('title', __( 'crm.credit_requests' ))
@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('crm.credit_requests' )</h1>
</section>

<!-- Main content -->
<section class="content">
    <div class="panel with-nav-tabs panel-default boxform_u box-solid_u">
        <div class="panel-heading">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#tab-credits" data-toggle="tab">@lang('crm.requests')</a></li>
            </ul>
        </div>
        <div class="panel-body">
            <div class="tab-content">
                <div class="tab-pane fade in active" id="tab-credits">
                    <div class="boxform_u box-solid_u">
                        <div class="box-header">
                            <h3 class="box-title">@lang( 'crm.all_your_credit_requests' )</h3>
                        </div>
                        <div class="box-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-condensed table-hover" id="credits-table" width="100%">
                                    <thead>
                                        <th>@lang('crm.correlative')</th>
                                        <th>@lang('credit.type_person')</th>
                                        <th>@lang('credit.date_request')</th>
                                        <th>@lang('credit.observations')</th>
                                        <th>@lang('credit.file')</th>
                                        <th>@lang('credit.status')</th>
                                        <th>@lang( 'messages.actions' )</th>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div tabindex="-1" class="modal fade" id="modal-edit-credit" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document" style="width: 40%">
            <form id="form-edit-credit" enctype="multipart/form-data">
                <input type="hidden" name="_token" value="{{ csrf_token() }}" id="token">
                <div class="modal-content" style="border-radius: 20px;">
                    <div class="modal-body">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h3>@lang('crm.edit_credit_request')</h3>

                        <div class="form-group">
                            <label>@lang('credit.status')</label>

                            <select name="status" id="status" class="select2" style="width: 100%">
                                <option value = "pending">@lang('credit.pending')</option>
                                <option value = "approved">@lang('credit.approved')</option>
                                <option value = "denied">@lang('credit.denied')</option>
                            </select>
                            <input type="hidden" name="credit_id" id="credit_id">
                            
                        </div>

                        <div class="form-group">
                            <label>@lang('credit.observations')</label>
                            <textarea name="observations" id="observations" class="form-control">
                            </textarea>
                        </div>

                        <div class="form-group">
                            <label>@lang('credit.file')</label>
                            <input type="file" name="file" id="file" accept=".pdf, image/*" placeholder="@lang('credit.file')" style="width: 100%">
                        </div>
                        
                    </div>
                    <div class="modal-footer">
                        <input type="submit" class="btn btn-primary" value="@lang('messages.save')" id="btn-edit-credit">
                        <button type="button" class="btn btn-danger" data-dismiss="modal" id="btn-close-modal-edit-credit">@lang('messages.close')</button>
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
        loadCreditsData();
        $.fn.dataTable.ext.errMode = 'none';
    });

    function loadCreditsData()
    {
        var table = $("#credits-table").DataTable(
        {
            pageLength: 25,
            deferRender: true,
            processing: true,
            serverSide: true,
            ajax: "/manage-credit-requests/getCreditsData",
            columns: [
            {data: 'correlative'},
            {data: 'type_person_label'},
            {data: 'date_request'},
            {data: 'observations'},
            {data: 'file'},
            {data: 'status_label'},
            {data: null, render: function(data){
                view_button = '<a class="btn btn-xs btn-info" onClick="viewRequest('+data.id+')"><i class="glyphicon glyphicon-eye-open"></i> @lang('messages.view')</a>';
                edit_button = ' <a class="btn btn-xs btn-primary" onClick="editRequest('+data.id+')"><i class="glyphicon glyphicon-edit"></i>@lang('messages.edit')</a>';
                delete_button = ' <a class="btn btn-xs btn-danger" onClick="deleteRequest('+data.id+')"><i class="glyphicon glyphicon-trash"></i>@lang('messages.delete')</a>';
                return view_button + edit_button + delete_button;
            } , orderable: false, searchable: false}
            ],
            columnDefs: [{
                "targets": '_all',
                "className": "text-center"
            }]
        });
    }

    function viewRequest(id)
    {
        var url = '{!!URL::to('/manage-credit-requests/view/:id')!!}';
        url = url.replace(':id', id);
        window.open(url, '_blank');
    }

    

    function editRequest(id)
    {
        var route = "/manage-credit-requests/"+id+"/edit";
        $.get(route, function(res) {
            $('#credit_id').val(res.id);
            $('#status').val(res.status).change();
            $('#observations').val(res.observations);
            $('#modal-edit-credit').modal({backdrop: 'static'});
        });
    }

    

    $(document).on('submit', 'form#form-edit-credit', function(e) {
        e.preventDefault();
        $("#btn-edit-credit").prop("disabled", true);
        $("#btn-close-modal-edit-credit").prop("disabled", true);
        route = "/manage-credit-requests/edit";
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
                    $("#btn-edit-credit").prop("disabled", false);
                    $("#btn-close-modal-edit-credit").prop("disabled", false); 
                    $("#credits-table").DataTable().ajax.reload(null, false);
                    Swal.fire
                    ({
                        title: result.msg,
                        icon: "success",
                        timer: 3000,
                        showConfirmButton: false,
                    });
                    $("#modal-edit-credit").modal('hide');
                }
                else
                {
                    $("#btn-edit-credit").prop("disabled", false);
                    $("#btn-close-modal-edit-credit").prop("disabled", false);
                    Swal.fire
                    ({
                        title: result.msg,
                        icon: "error",
                    });
                }

            },
            error:function(msj){
                $("#btn-edit-credit").prop("disabled", false);
                $("#btn-close-modal-edit-credit").prop("disabled", false);
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

    

    function deleteRequest(id)
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
                route = '/manage-credit-requests/'+id;
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
                                timer: 3000,
                                showConfirmButton: false,
                            });
                            $("#credits-table").DataTable().ajax.reload(null, false);
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

    
</script>
@endsection