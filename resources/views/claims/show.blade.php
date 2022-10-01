<div class="modal-dialog modal-dialog-centered" role="document" style="width: 60%">    
    <div class="modal-content" style="border-radius: 20px;">
        <div class="modal-body">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <h3>@lang('crm.general_data')</h3>
            <div class="row">
                @if($claim->correlative != null)
                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                    <div class="form-group">
                        <label>@lang('crm.correlative')</label>
                        <span>{{ $claim->correlative }}</span>
                    </div>
                </div>
                @endif

                @if($claim->type != null)
                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                    <div class="form-group">
                        <label>@lang('crm.type')</label>
                        <span>{{ $claim->type }}</span>
                    </div>
                </div>
                @endif

                @if($claim->status != null)
                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                    <div class="form-group">
                        <label>@lang('crm.status')</label>
                        <span>{{ $claim->status }}</span>
                    </div>
                </div>
                @endif
                
            </div>

            <div class="row">

                @if($claim->description != null)
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="form-group">
                        <label>@lang('crm.description')</label>
                        <span>{{ $claim->description }}</span>
                    </div>
                </div>
                @endif
            </div>

            <div class="row">

                @if($claim->claim_date != null)
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <div class="form-group">
                        <label>@lang('crm.claim_date')</label>
                        <span>{{ $claim->claim_date }}</span>
                    </div>
                </div>
                @endif

                @if($claim->suggested_closing_date != null)
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <div class="form-group">
                        <label>@lang('crm.suggested_closing_date')</label>
                        <span>{{ $claim->suggested_closing_date }}</span>
                    </div>
                </div>
                @endif
            </div>

            <div class="row">

                @if($claim->review_description != null)
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="form-group">
                        <label>@lang('crm.review')</label>
                        <span>{{ $claim->review_description }}</span>
                    </div>
                </div>
                @endif

            </div>

            <div class="row">
                @if($claim->proceed == 1)
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="form-group">
                        <label>@lang('crm.proceed')</label>
                        <span>@lang('crm.yes')</span>
                    </div>
                </div>
                @endif

                @if($claim->not_proceed == 1)
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="form-group">
                        <label>@lang('crm.proceed')</label>
                        <span>@lang('crm.no')</span>
                    </div>
                </div>
                @endif


            </div>

            <div class="row">
                @if($claim->justification != null)
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="form-group">
                        <label>@lang('crm.justification')</label>
                        <span>{{ $claim->justification }}</span>
                    </div>
                </div>
                @endif
            </div>

            <div class="row">

                @if($claim->resolution != null)
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="form-group">
                        <label>@lang('crm.resolution')</label>
                        <span>{{ $claim->resolution }}</span>
                    </div>
                </div>
                @endif

            </div>

            <div class="row">

                @if($claim->authorized != null)
                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                    <div class="form-group">
                        <label>@lang('crm.authorized_by')</label>
                        <span>{{ $claim->authorized }}</span>
                    </div>
                </div>
                @endif

                @if($claim->close_date != null)
                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                    <div class="form-group">
                        <label>@lang('crm.close_date')</label>
                        <span>{{ $claim->close_date }}</span>
                    </div>
                </div>
                @endif

                @if($claim->register != null)
                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                    <div class="form-group">
                        <label>@lang('crm.register_by')</label>
                        <span>{{ $claim->register }}</span>
                    </div>
                </div>
                @endif

            </div>




            <div class="row">

                @if($claim->customer != null)
                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                    <div class="form-group">
                        <label>@lang('crm.customer')</label>
                        <span>{{ $claim->customer }}</span>
                    </div>
                </div>
                @endif

                @if($claim->variation_id != null)
                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                    <div class="form-group">
                        <label>@lang('crm.product')</label>
                        @if($claim->sku != $claim->sub_sku)
                        <span>{{ $claim->name_product }} {{ $claim->name_variation }}</span>
                        @else
                        {{ $claim->name_product }}
                        @endif
                        
                    </div>
                </div>
                @endif

                @if($claim->invoice != null)
                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                    <div class="form-group">
                        <label>@lang('crm.invoice')</label>
                        <span>{{ $claim->invoice }}</span>
                    </div>
                </div>
                @endif

            </div>


            @if($claim->equipment_reception_desc != null)
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="form-group">
                        <label>@lang('crm.equipment_reception')</label>
                        <span>{{ $claim->equipment_reception_desc }}</span>
                    </div>
                </div>
            </div>
            @endif

        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-dismiss="modal" id="btn-close-modal-view-claim">@lang('messages.close')</button>
        </div>
    </div>
    
</div>