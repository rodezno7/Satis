<div class="modal-dialog modal-lg no-print" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close no-print" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="modalTitle"><b>{{ $product->name }}</b></h4>
            <input type="hidden" id="product_id" value="{{ $product->id }}">
        </div>

        <div class="modal-body">
            @can('product.view')
                <div id="lista" class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <table class="table table-striped table-bordered table-condensed table-hover" id="history_purchase"
                        width="100%">
                        <thead>
                            <tr>
                                <th>@lang('Fecha de compra')</th>
                                <th>@lang('purchase.supplier')</th>
                                <th>@lang('lang_v1.quantity')</th>
                                <th>@lang('purchase.unit_price')</th>
                                <th>@lang('receipt.total')</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            @endcan
        </div>
        <div class="modal-footer" style="border: none;">
            <button type="button" class="btn btn-info no-print" data-dismiss="modal">@lang('messages.close')</button>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        let product_id = $('input#product_id').val();
        setTimeout(() => {
            datatableHistoryPurchase(product_id);
            $('a.buttons-collection').css('display', 'none');
        }, 500);
    });

    function datatableHistoryPurchase(product_id) {
        $("#history_purchase").DataTable({
            pageLength: 10,
            // deferRender: true,
            processing: true,
            serverSide: true,
            aaSorting: [[0, 'desc']],
            ajax: "/products/get_history_purchase/" + product_id,
            columns: [
                {data: 'transaction_date', name: 't.transaction_date'},
                {data: 'name', name: 'c.name'},
                {data: 'quantity', searchable: false, orderable: false},
                {data: 'purchase_price', searchable: false, orderable: false},
                {data: 'total', searchable: false, orderable: false}
            ],
            columnDefs: [{
                "targets": '_all',
                "className": "text-center"
            }]
        });
    }

</script>
