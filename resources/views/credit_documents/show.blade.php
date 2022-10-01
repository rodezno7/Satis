<div class="modal-dialog modal-lg" role="dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title">@lang('cxc.show_cdocs')</h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <i class="fa fa-money"></i> &nbsp;<strong>@lang('cxc.sale_info')</strong>
                        </div>
                        <div class="panel-body">

                            <ul class="list-group">
                                <li class="list-group-item"><b>@lang('cxc.doctypes'):</b> {{ $document_type->document_name }}</li>
                                <li class="list-group-item"><b>@lang('cxc.invoice'):  </b>{{ $credit_document->correlative }}</li>
                                <li class="list-group-item"><b>@lang('cxc.date'): </b> {{ $credit_document->transaction_date }}</li>
                                <li class="list-group-item"><b>@lang('cxc.amount'):</b> {{ $credit_document->final_total }} </li>
                                <li class="list-group-item"><b>@lang('cxc.customer'): </b> {{ $credit_document->name }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <i class="fa fa-lock"></i> &nbsp;<strong><b></b>@lang('cxc.rec_cus_docs')</strong>
                        </div>
                        <div class="panel-body">
                            <ul class="list-group">
                                <li class="list-group-item"><b>@lang('cxc.reason'): </b> {{ $reason->name }}</li>
                                <li class="list-group-item"><b>@lang('cxc.courier'):</b>  {{ $courier->firstname . $courier->last_name }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
