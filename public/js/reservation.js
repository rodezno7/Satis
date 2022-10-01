$(document).ready(function() {
    $.fn.modal.Constructor.prototype.enforceFocus = function() {};

    $.fn.dataTable.ext.errMode = 'throw';

    // Date filter
    $('#reservation_date_filter').daterangepicker(
        dateRangeSettings,
        function (start, end) {
            $('#reservation_date_filter span').html(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
            reservations_table.ajax.reload();
        }
    );

    $('#reservation_date_filter').on('cancel.daterangepicker', function(ev, picker) {
        $('#reservation_date_filter').html('<i class="fa fa-calendar"></i> {{ __("messages.filter_by_date") }}');
        reservations_table.ajax.reload();
    });

    // DataTable
    reservations_table = $('#reservations_table').DataTable({
        processing: true,
        serverSide: true,
        aaSorting: [[1, 'desc']],
        ajax: {
            'url': '/reservations',
            'data': function(d) {
                var start = $('#reservation_date_filter').data('daterangepicker').startDate.format('YYYY-MM-DD');
                var end = $('#reservation_date_filter').data('daterangepicker').endDate.format('YYYY-MM-DD');
                d.start_date = start;
                d.end_date = end;
                d.location_id = $("input#location").val();
                d.document_type_id = $("input#document_type").val();
                d.payment_status = $("select#payment_status").val();
            }
        },
        columns: [
            { data: 'quote_ref_no', name: 'quote_ref_no', className: 'text-center' },
            { data: 'quote_date', name: 'quote_date', className: 'text-center' },
            { data: 'customer_name', name: 'customer_name' },
            { data: 'invoiced', name: 'invoiced', className: 'text-center' },
            { data: 'total_final', name: 'total_final', className: 'text-center' },
            { data: 'note', name: 'note', className: 'text-center' },
            { data: 'amount_paid', name: 'amount_paid', className: 'text-center' },
            { data: 'employee_name', name: 'employee_name' },
            { data: 'action', orderable: false, searchable: false, className: 'text-center' }
        ],
        columnDefs: [{
            targets: 1,
            render: $.fn.dataTable.render.moment('YYYY-MM-DD', 'DD/MM/YYYY')
        }],
    });

    // Location filter
    $('select#select_location').on('change', function() {
        $("input#location").val($("select#select_location").val());
        reservations_table.ajax.reload();
    });

    // Document type filter
    $('select#select_document_type').on('change', function() {
        $("input#document_type").val($("select#select_document_type").val());
        reservations_table.ajax.reload();
    });

    // Payment status filter
    $('select#payment_status').on('change', function() {
        reservations_table.ajax.reload();
    });

    // Show order
    $(document).on("click", "a.show_reservation", function(e){
        e.preventDefault();

        console.log($(this).attr("href"));

        $.ajax({
            url: $(this).attr("href"),
            method: 'GET',
            dataType: "html",
            success: function(data){
                $('div.reservation_modal').html(data).modal('show');
            }
        });
    });

    // Delete button
    $(document).on('click', 'a.delete_reservation', function(e) {
        e.preventDefault();

        swal({  
            title: LANG.sure,
            text: LANG.delete_content,
            icon: 'warning',
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                var href = $(this).attr('href');
                var data = $(this).serialize();
                $.ajax({
                    method: 'DELETE',
                    url: href,
                    dataType: 'json',
                    data: data,
                    success: function(result) {
                        if (result.success === true) {
                            Swal.fire({
                                title: ''+result.msg+'',
                                icon: 'success',
                            });
                            reservations_table.ajax.reload();
                        } else {
                            Swal.fire
                            ({
                                title: ''+result.msg+'',
                                icon: 'error',
                            });
                        }
                    }
                });
            }
        });
    });
});