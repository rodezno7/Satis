@extends('layouts.app')

@section('title', __('apportionment.apportionments'))

@section('content')
{{-- Content Header (Page header) --}}
<section class="content-header no-print">
    <h1>@lang('apportionment.apportionments')
        <small>@lang('apportionment.manage_apportionments')</small>
    </h1>
</section>

{{-- Main content --}}
<section class="content no-print">
    <div class="box">
        <div class="box-header">
            <h3 class="box-title">@lang('apportionment.all_apportionments')</h3>
            @can('apportionment.create')
            <div class="box-tools">
                <a class="btn btn-block btn-primary" href="{{ action('ApportionmentController@create') }}">
                    <i class="fa fa-plus"></i> @lang('messages.add')
                </a>
            </div>
            @endcan
        </div>
        
        <div class="box-body">
            @can('apportionment.view')
            <div class="table-responsive">
            <table class="table table-striped table-text-center" id="apportionments_table" width="100%">
                <thead>
                    <tr>
                        <th class="text-center">@lang('messages.date')</th>
                        <th class="text-center">DUCA</th>
                        <th>@lang('crm.name')</th>
                        <th class="text-center">@lang('messages.action')</th>
                    </tr>
                </thead>
            </table>
            </div>
            @endcan
        </div>
    </div>

    {{-- Apportionments modal --}}
    <div class="modal fade apportionments_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel"></div>
</section>

{{-- Print section --}}
<section id="receipt_section" class="print_section"></section>
@endsection

@section('javascript')
{{-- Moment JS --}}
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.8.4/moment.min.js"></script>

{{-- Datetime JS --}}
<script type="text/javascript" src="https://cdn.datatables.net/plug-ins/1.10.13/dataRender/datetime.js"></script>

<script>
    $(document).ready(function() {
        $.fn.modal.Constructor.prototype.enforceFocus = function() {};

        // Select2
        $('.apportionments_modal').on('shown.bs.modal', function () {
            $(this).find('.select2').select2();
        });

        // Data Table
        var apportionments_table = $('#apportionments_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '/apportionments',
            columns: [
                { data: 'apportionment_date', name: 'apportionment_date', searchable: false, className: 'text-center' },
                { data: 'reference', name: 'reference', className: 'text-center' },
                { data: 'name', name: 'name' },
                { data: 'action', name: 'action', searchable: false, orderable: false, className: 'text-center' },
            ],
            columnDefs: [{
                targets: [0],
                render: $.fn.dataTable.render.moment('YYYY-MM-DD', 'DD/MM/YYYY')
            }]
        });

        // Edit Form
        $(document).on('click', 'button.edit_apportionments_button', function() {
            $("div.apportionments_modal").load($(this).data('href'), function() {
                $(this).modal('show');
                $('form#apportionments_edit_form').submit(function(e) {
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
                                $('div.apportionments_modal').modal('hide');
                                Swal.fire({
                                    title: ""+result.msg+"",
                                    icon: "success",
                                });
                                apportionments_table.ajax.reload();
                            } else {
                                Swal.fire
                                ({
                                    title: ""+result.msg+"",
                                    icon: "error",
                                });
                            }
                        }
                    });
                });
            });
        });

        // Delete
        $(document).on('click', '.delete_apportionments_button', function() {
            swal({
                title: LANG.sure,
                text: '{{__('messages.delete_content')}}',
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) {
                    var href = $(this).data('href');
                    var data = $(this).serialize();

                    $.ajax({
                        method: "DELETE",
                        url: href,
                        dataType: "json",
                        data: data,
                        success: function(result) {
                            if (result.success === true) {
                                Swal.fire({
                                    title: ""+result.msg+"",
                                    icon: "success",
                                });
                                apportionments_table.ajax.reload();
                            } else {
                                Swal.fire({
                                    title: ""+result.msg+"",
                                    icon: "error",
                                });
                            }
                        }
                    });
                }
            });
        });
    });
</script>
@endsection