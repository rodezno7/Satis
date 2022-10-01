$(document).ready(function() {
    $.fn.modal.Constructor.prototype.enforceFocus = function() {};

    // Select2
    $('.flow_reasons_modal').on('shown.bs.modal', function () {
        $(this).find('.select2').select2();
    });

    // Datatable
    var flow_reasons_table = $('#flow_reasons_table').DataTable({
        pageLength: 25,
        processing: true,
        serverSide: true,
        ajax: '/flow-reason',
        columns: [
            { data: 'reason', name: 'reason' },
            { data: 'description', name: 'description' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ]
    });

    // Add form
    $(document).on('submit', 'form#flow_reason_add_form', function(e) {
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
                    $('div.flow_reasons_modal').modal('hide');
                    Swal.fire({
                        title: '' + result.msg + '',
                        icon: 'success',
                    });
                    flow_reasons_table.ajax.reload();
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

    // Edit Form
    $(document).on('click', 'button.edit_flow_reasons_button', function() {
        $('div.flow_reasons_modal').load($(this).data('href'), function() {
            $(this).modal('show');
            $('form#flow_reasons_edit_form').submit(function(e) {
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
                            $('div.flow_reasons_modal').modal('hide');
                            Swal.fire({
                                title: '' + result.msg + '',
                                icon: 'success',
                            });
                            flow_reasons_table.ajax.reload();
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

    // Delete
    $(document).on('click', 'button.delete_flow_reasons_button', function() {
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
                                title: '' + result.msg + '',
                                icon: 'success',
                            });
                            flow_reasons_table.ajax.reload();
                        } else {
                            Swal.fire
                            ({
                                title: '' + result.msg + '',
                                icon: 'error',
                            });
                        }
                    }
                });
            }
        });
    });
});