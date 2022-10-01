$(document).ready(function(){
    //Documents table
    var documents_table = $('#documents_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '/documents',
        columnDefs: [{
            "targets": 2,
            "orderable": false,
            "searchable": false
        }]
    });

    $(document).on('submit', 'form#documenttype_add_form', function(e) {
        e.preventDefault();
        $(this).find('button[type="submit"]').attr('disabled', true);
        var data = $(this).serialize();

        $.ajax({
            method: "POST",
            url: $(this).attr("action"),
            dataType: "json",
            data: data,
            success: function(result) {
                if (result.success === true) {
                    $('div.documents_modal').modal('hide');
                    Swal.fire({
                        title: ""+result.msg+"",
                        icon: "success",
                    });
                    documents_table.ajax.reload();
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

    $(document).on('click', 'button.edit_documents_button', function() {
        $("div.documents_modal").load($(this).data('href'), function() {
            $(this).modal('show');

            $('form#documents_edit_form').submit(function(e) {
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
                            $('div.documents_modal').modal('hide');
                            Swal.fire({
                                title: ""+result.msg+"",
                                icon: "success",
                            });
                            documents_table.ajax.reload();
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

    $(document).on('click', 'button.delete_documents_button', function() {
        swal({
            title: LANG.sure,
            text: LANG.confirm_delete_document,
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
                            documents_table.ajax.reload();
                        } else {
                            Swal.fire
                            ({
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