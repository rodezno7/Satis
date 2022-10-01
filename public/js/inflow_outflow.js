$(document).ready(function() {
    $.fn.modal.Constructor.prototype.enforceFocus = function() {};

    // Inflow outflow modal
    $('.inflow_outflow_modal').on('shown.bs.modal', function () {
        // Select2
        $(this).find('.select2').select2();

        // Get suppliers
        $('#supplier_id').select2({
            ajax: {
                url: '/pos/inflow-outflow/get_suppliers',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term,
                        page: params.page
                    };
                },
                processResults: function (data) {
                    return {
                        results: data
                    };
                }
            },
            minimumInputLength: 1,
            escapeMarkup: function(m) {
                return m;
            },
            templateResult: function(data){
                if (! data.id) {
                    return data.text;
                }
                var html = data.text + ' (<b>' + LANG.code + ': </b>' + data.contact_id + ' - <b>' + LANG.business + ': </b>' + data.business_name + ')';
                return html;
            },
            templateSelection: function(data) {
                return data.text;
            },
        });

        // Get employees
        $('#employee_id').select2({
            ajax: {
                url: '/pos/inflow-outflow/get_employees',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term,
                        page: params.page
                    };
                },
                processResults: function (data) {
                    return {
                        results: data
                    };
                }
            },
            minimumInputLength: 1,
            escapeMarkup: function(m) {
                return m;
            },
            templateResult: function(data){
                return data.text;
            },
            templateSelection: function(data) {
                return data.text;
            },
        });
    });

    // DataTable
    var inflow_outflows_table = $('#inflow_outflows_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '/inflow-outflow',
        columns: [
            { data: 'cashier', name: 'cashiers.name' },
            { data: 'amount', name: 'amount' },
            { data: 'reason', name: 'flow_reasons.reason' },
            { data: 'type', name: 'type' },
            { data: 'employee', name: 'employee' },
            { data: 'action', orderable: false, searchable: false }
        ],
        columnDefs: [{
            'targets': [1, 5],
            'orderable': false,
            'searchable': false
        }]
    });

    // Add form
    $(document).on('submit', 'form#inflow_outflow_add_form', function(e) {
        e.preventDefault();
        $(this).find('button[type="submit"]').attr('disabled', true);
        var data = $(this).serialize();
        $.ajax({
            method: 'POST',
            url: $(this).attr('action'),
            dataType: 'json',
            data: data,
            success: function(result) {
                if (result.success === true) {
                    $('div.inflow_outflow_modal').modal('hide');
                    Swal.fire({
                        title: '' + result.msg + '',
                        icon: 'success',
                    });
                } else {
                    Swal.fire({
                        title: '' + result.msg + '',
                        icon: 'error',
                    });
                }
            }
        });
    });

    // Edit form
    $(document).on('click', 'button.edit_inflow_outflows_button', function() {
        $('div.inflow_outflow_modal').load($(this).data('href'), function() {
            $(this).modal('show');
            $('form#inflow_outflows_edit_form').submit(function(e) {
                e.preventDefault();
                $(this).find('button[type="submit"]').attr('disabled', true);
                var data = $(this).serialize();
                $.ajax({
                    method: 'POST',
                    url: $(this).attr('action'),
                    dataType: 'json',
                    data: data,
                    success: function(result) {
                        if (result.success == true) {
                            $('div.inflow_outflow_modal').modal('hide');
                            Swal.fire({
                                title: '' + result.msg + '',
                                icon: 'success',
                            });
                            inflow_outflows_table.ajax.reload();
                        } else {
                            Swal.fire
                            ({
                                title: '' + result.msg + '',
                                icon: 'error',
                            });
                        }
                    }
                });
            });
        });
    });

    // Delete button
    $(document).on('click', 'button.delete_inflow_outflows_button', function() {
        swal({
            title: LANG.sure,
            text: LANG.delete_content,
            icon: 'warning',
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                var href = $(this).data('href');
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
                            inflow_outflows_table.ajax.reload();
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