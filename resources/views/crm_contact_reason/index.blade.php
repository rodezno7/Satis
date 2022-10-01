@extends('layouts.app')
@section('title', __('crm.contact_reason'))
<script th:src="@{/js/datatables.min.js}"></script>
@section('content')
<section class="content-header">
    <h1>@lang( 'crm.contact_reason' )</h1>
</section>
<section class="content">
    <div class="boxform_u box-solid_u">
        <div class="box-header">
            <h3 class="box-title">@lang('crm.manage_contact_reason')</h3>
            @can('crm-contactreason.create')
                <div class="box-tools">
                    <button type="button" class="btn btn-block btn-primary btn-modal" data-href="{{action('CRMContactReasonController@create')}}" data-container=".contactreason_modal">
                        <i class="fa fa-plus"></i> @lang('messages.add')
                    </button>
                </div>
            @endcan
        </div>
        <div class="box-body">
            @can('crm-contactreason.view')
                <div id="lista" class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-condensed table-hover" id="contactreason_table" width="100%">
                            <thead>
                                <tr>
                                    <th>@lang('crm.name')</th>
                                    <th>@lang('crm.description')</th>
                                    <th>@lang('crm.actions')</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            @endcan
        </div>
    </div>

    {{-- Div para renderizar el modal --}}
    <div class="modal fade contactreason_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel"></div>

</section>
@endsection
@section('javascript')
    <script type="text/javascript">
        $(document).ready(function(){
            var contactreason_table = $('#contactreason_table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '/crm-contactreason/getContactReasonData',
                columnDefs: [{
                    "targets": [0,1,2],
                    "className": "text-center",
                    "orderable": true,
                    "searchable": true
                }]
            });
        });
        $(document).on('submit', 'form#contactreason_add_form', function(e){
            e.preventDefault();
            $(this).find('button[type="submit"]').attr('disabled', true);
            var data = $(this).serialize();
            $.ajax({
                method: "POST",
                url: $(this).attr("action"),
                dataType: "json",
                data: data,
                success: function(result){
                    if(result.success == true){
                        $("#contactreason_table").DataTable().ajax.reload();
                        $('div.contactreason_modal').modal('hide');
                        Swal.fire({
                            title: result.msg,
                            icon: "success",
                        });
                    }else{
                        Swal.fire({
                            title: result.msg,
                            icon: "error",
                        });
                    }
                }
            });
        });

        $(document).on('click', 'button.edit_contactreason_button', function(){
            $("div.contactreason_modal").load($(this).data('href'), function(){
                $(this).modal('show');

                $('form#contactreason_edit_form').submit(function(e){
                    e.preventDefault();
                    $(this).find('button[type="submit"]').attr('disabled', true);
                    var data = $(this).serialize();
                    $.ajax({
                        method: "POST",
                        url: $(this).attr("action"),
                        dataType: "json",
                        data: data,
                        success: function(result){
                            if(result.success == true){
                                $("#contactreason_table").DataTable().ajax.reload();
                                $('div.contactreason_modal').modal('hide');
                                Swal.fire({
                                    title: result.msg,
                                    icon: "success",
                                });
                            }else{
                                Swal.fire({
                                    title: result.msg,
                                    icon: "error",
                                });
                            }
                        }
                    });
                });
            });
        });

        $(document).on('click', 'button.delete_contactreason_button', function(){
                Swal.fire({
                    title: "{{__('crm.tittle_confirm_delete')}}",
                    text: "{{__('crm.text_confirm_delete')}}",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: "{{__('crm.confirm_button_delete')}}"
                }).then((willDelete) => {
                    if (willDelete.value) {
                        var href = $(this).data('href');
                        var data = $(this).serialize();
                        $.ajax({
                            method: "DELETE",
                            url: href,
                            dataType: "json",
                            data: data,
                            success: function(result) {
                                if (result.success == true) {
                                    $("#contactreason_table").DataTable().ajax.reload();
                                    $('div.contactreason_modal').modal('hide');
                                    Swal.fire({
                                        title: result.msg,
                                        icon: "success",
                                        timer: 2000,
                                        showConfirmButton: false,
                                    });
                                    $('#content').hide();
                                } else {
                                    Swal.fire({
                                        title: result.msg,
                                        icon: "error",
                                    });
                                }
                            }
                        });
                    }
                });
        });
        
    </script>
@endsection