@extends('layouts.app')
@section('title', __('customer.portfolios'))
<script th:src="@{/js/datatables.min.js}"></script>
@section('content')
<section class="content-header">
    <h1>@lang( 'customer.portfolios' )<br>
        <small>@lang( 'customer.manage_portfolios' )</small></h1>
</section>
<section class="content">
    <div class="boxform_u box-solid_u">
        <div class="box-header">
            <div class="box-tools">
                <button type="button" class="btn btn-block btn-primary btn-modal"
                    data-href="{{action('CustomerPortfolioController@create')}}" data-container=".portfolios_modal">
                    <i class="fa fa-plus"></i> @lang('messages.add')
                </button>
            </div>
        </div><br>
        <div class="box-body">
            @can('portfolios.view')
            <div id="lista" class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="table-responsive">
                    <table class="table table-stripe table-bordered table-condensed table-hover" id="portfolios_table"
                        width="100%">
                        <thead>
                            <tr>
                                <th>@lang('customer.code')</th>
                                <th>@lang('customer.name')</th>
                                <th>@lang('customer.description')</th>
                                <th>@lang('customer.seller')</th>
                                <th>@lang('messages.actions')</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
            @endcan
        </div>
    </div>

    {{-- Div para renderizar el modal --}}
    <div class="modal fade portfolios_modal" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false" aria-labelledby="gridSystemModalLabel"></div>

</section>
@endsection
@section('javascript')
<script type="text/javascript">
    $.fn.modal.Constructor.prototype.enforceFocus = function(){};
    $(document).ready(function(){
        var portfolios_table = $('#portfolios_table').DataTable({
            processing: true,
            serverSide: true,
            type: 'GET',
            ajax: '/portfolios/getPortfoliosData',
            columnDefs: [{
                "targets": [0,1,2,3,4],
                "className": "text-center",
                "orderable": true,
                "searchable": true
            }]
        });
    });
    $(document).on('submit', 'form#portfolio_add_form', function(e){
        e.preventDefault();
        $(this).find('button[type="submit"]').attr('disabled', true);
        var data = $(this).serialize();
        $.ajax({
            method: "POST",
            url: $(this).attr("action"),
            datatype: "json",
            data: data,
            success: function(result){
                if(result.success == true){
                    $("#portfolios_table").DataTable().ajax.reload();
                    $('div.portfolios_modal').modal('hide');
                    Swal.fire({
                        title: result.msg,
                        icon: "success",
                        timer: 2000,
                        showConfirmButton: false,
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
    $('.portfolios_modal').on('shown.bs.modal', function(){
        $('#seller_id').select2();
        $('.portfolios_modal').modal({backdrop: 'static', keyboard: false})
    });

    $(document).on('click', 'button.edit_portfolios_button', function(){

        $("div.portfolios_modal").load($(this).data('href'), function(){
            $(this).modal('show');


            $('form#portfolio_edit_form').submit(function(e) {
             e.preventDefault();
            $(this).find('button[type="submit"]').attr('disabled', true);
            var data = $(this).serialize();

            $.ajax({
                method: "POST",
                url: $(this).attr("action"),
                dataType: "json",
                data: data,
                success: function(result) {
                    if (result.success == true) {
                        $("#portfolios_table").DataTable().ajax.reload();
                        $('div.portfolios_modal').modal('hide');
                        Swal.fire({
                            title: result.msg,
                            icon: "success",
                            timer: 2000,
                            showConfirmButton: false,
                        });
                        $('#content').hide();
                    } else {
                        Swal.fire({
                        title: "{{__('messages.errors')}}",
                        icon: "error",});
                    }
                }
                });
            });
        });
    });

    $(document).on('click', 'button.delete_portfolios_button', function(){
                Swal.fire({
                    title: "{{__('customer.tittle_confirm_delete')}}",
                    text: "{{__('customer.text_confirm_delete')}}",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: "{{__('customer.confirm_button_delete')}}"
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
                                    $("#portfolios_table").DataTable().ajax.reload();
                                    $('div.portfolios_modal').modal('hide');
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
                                        timer: 2000,
                                        showConfirmButton: false,
                                    });
                                }
                            }
                        });
                    }
                });
        });
    
</script>
@endsection