$(document).ready(function(){
    /** fix select2 bug */
    $.fn.modal.Constructor.prototype.enforceFocus = function() {};

    /** select2 for get accounting accounts */
    $('div.fixed_asset_types_modal').on('shown.bs.modal', function(){
        $("select.select-account").select2({
            ajax: {
                type: "post",
                url: "/catalogue/get_accounts_for_select2",
                dataType: "json",
                data: function(params){
                    return {
                        q: params.term
                    };
                },
                processResults: function(data) {
                    return {
                        results: data
                    };
                }
            },
            minimumInputLength: 1,
            escapeMarkup: function (markup) {
                return markup;
            }
        });
    });

    /** fixed asset datatable */
    var fixed_assets = $('table#fixed_assets_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '/fixed-assets',
        columns: [
            { data: 'name', name: 'name' },
            { data: 'code', name: 'code' },
            { data: 'type', name: 'type' },
            { data: 'location_name', name: 'bl.name' },
            { data: 'initial_value', name: 'initial_value' },
            { data: 'current_value', name: 'current_value' },
            { data: 'action', name: 'action' }
        ],
        "fnDrawCallback": function (oSettings) {
            __currency_convert_recursively($('table#fixed_assets_table'));
        }
    });

    /** create fixed assets */
    $(document).on('click', 'a.add-fixed-asset', function(e){
        e.preventDefault();

        $('div.fixed_assets_modal').load($(this).attr('href'), function(){
            let modal = $(this);
            modal.modal('show');
            modal.find('select.select2').select2();

            $('form#fixed_asset_add_form').off('submit').on('submit', function(e){
                e.preventDefault();
                let btn = $(this).find('button[type="submit"]');
                btn.attr('disabled', true);

                $.ajax({
                    method: "POST",
                    url: $(this).attr('action'),
                    dataType: 'json',
                    data: $(this).serialize(),
                    success: function(data){
                        if(data.success){
                            modal.modal('hide');
                            fixed_assets.ajax.reload();
                            toastr.success(data.msg);
                        } else{
                            btn.attr('disabled', false);
                            toastr.error(data.msg);
                        }
                    }
                });
            });
        });
    });

    /** edit fixed assets */
    $(document).on("click", "a.edit_fixed_asset", function(e){
        e.preventDefault();

        $('div.fixed_assets_modal').load($(this).attr('href'), function(){
            let modal = $(this);
            modal.modal('show');
            modal.find('select.select2').select2();

            $('form#fixed_asset_edit_form').off('submit').on('submit', function(e){
                e.preventDefault();
                let btn = $(this).find('button[type="submit"]');
                btn.attr('disabled', true);

                $.ajax({
                    method: "POST",
                    url: $(this).attr('action'),
                    dataType: 'json',
                    data: $(this).serialize(),
                    success: function(data){
                        if(data.success){
                            modal.modal('hide');
                            fixed_assets.ajax.reload();
                            toastr.success(data.msg);
                        } else{
                            btn.attr('disabled', false);
                            toastr.error(data.msg);
                        }
                    }
                });
            });
        });
        
    });

    /** delete fixed asset types */
    $(document).on("click", "a.delete_fixed_asset", function(e){
        e.preventDefault();
        
        swal({
            title: LANG.sure,
            text: LANG.wont_be_able_revert,
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                $.ajax({
                    method: "DELETE",
                    url: $(this).attr('href'),
                    dataType: "json",
                    data: $(this).serialize(),
                    success: function(result) {
                        if (result.success) {
                            fixed_assets.ajax.reload();
                            toastr.success(result.msg);
                        } else {
                            toastr.error(result.msg);
                        }
                    }
                });
            }
        });
    });

    /** fixed asset types datatable */
    var fixed_asset_types = $('table#fixed_asset_types_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '/fixed-asset-types',
        columns: [
            { data: 'name', name: 'name' },
            { data: 'description', name: 'description' },
            { data: 'percentage', name: 'percentage' },
            { data: 'account_name', name: 'c.name' },
            { data: 'action', name: 'action' }
        ]
    });

    /** create fixed asset types */
    $(document).on('click', 'a.add-fixed-asset-type', function(e){
        e.preventDefault();

        $('div.fixed_asset_types_modal').load($(this).attr('href'), function(){
            let modal = $(this);
            modal.modal('show');

            $('form#fixed_asset_type_add_form').off('submit').on('submit', function(e){
                e.preventDefault();
                let btn = $(this).find('button[type="submit"]');
                btn.attr('disabled', true);

                $.ajax({
                    method: "POST",
                    url: $(this).attr('action'),
                    dataType: 'json',
                    data: $(this).serialize(),
                    success: function(data){
                        if(data.success){
                            modal.modal('hide');
                            fixed_asset_types.ajax.reload();
                            toastr.success(data.msg);
                        } else{
                            btn.attr('disabled', false);
                            toastr.error(data.msg);
                        }
                    }
                });
            });
        });
    });

    /** edit fixed asset types */
    $(document).on("click", "a.edit_fixed_asset_type", function(e){
        e.preventDefault();

        $('div.fixed_asset_types_modal').load($(this).attr('href'), function(){
            let modal = $(this);
            modal.modal('show');

            $('form#fixed_asset_type_edit_form').off('submit').on('submit', function(e){
                e.preventDefault();
                let btn = $(this).find('button[type="submit"]');
                btn.attr('disabled', true);

                $.ajax({
                    method: "POST",
                    url: $(this).attr('action'),
                    dataType: 'json',
                    data: $(this).serialize(),
                    success: function(data){
                        if(data.success){
                            modal.modal('hide');
                            fixed_asset_types.ajax.reload();
                            toastr.success(data.msg);
                        } else{
                            btn.attr('disabled', false);
                            toastr.error(data.msg);
                        }
                    }
                });
            });
        });
        
    });

    /** delete fixed asset types */
    $(document).on("click", "a.delete_fixed_asset_type", function(e){
        e.preventDefault();
        
        swal({
            title: LANG.sure,
            text: LANG.wont_be_able_revert,
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                $.ajax({
                    method: "DELETE",
                    url: $(this).attr('href'),
                    dataType: "json",
                    data: $(this).serialize(),
                    success: function(result) {
                        if (result.success) {
                            fixed_asset_types.ajax.reload();
                            toastr.success(result.msg);
                        } else {
                            toastr.error(result.msg);
                        }
                    }
                });
            }
        });
    });
});