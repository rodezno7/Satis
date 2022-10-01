<div class="modal-dialog" role="dialog">
    <div class="modal-content" style="border-radius: 10px;">
        {!! Form::open(['url' => action('CustomerPortfolioController@update', [$portfolios->id]), 'method' => 'PUT', 'id' =>
        'portfolio_edit_form']) !!}

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title">@lang('customer.add_portfolio')</h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('name', __('customer.code') . ' : ') !!}
                        <div class="wrap-inputform">
                            {!! Form::text('code', $portfolios->code, ['class' => 'form-control text-center', 'required',
                            'readonly',
                            'placeholder' => __('customer.code')]); !!}
                        </div>
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="form-group">
                        {!! Form::label('name', __('customer.name') . ' : ') !!}
                        <div class="wrap-inputform">
                            {!! Form::text('name', $portfolios->name, ['class' => 'form-control', 'required', 'placeholder' =>
                            __('crm.name')]); !!}
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        {!! Form::label('description', __('customer.description') . ' : ') !!}
                        <div class="wrap-inputform">
                            {!! Form::text('description', $portfolios->description, ['class' => 'inputform2', 'placeholder' =>
                            __('crm.description')]); !!}
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('seller_id', __('customer.seller') . ' : ') !!}
                        <div class="wrap-inputform">
                            {!! Form::select('seller_id', $sellers, $portfolios->seller_id, ['class' => 'form-control select2',
                            'required', 'id' => 'seller_id']); !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">@lang('messages.save')</button>
            <button type="button" data-dismiss="modal" aria-label="Close"
                class="btn btn-default">@lang('messages.close')</button>
        </div>
        {!! Form::close() !!}
    </div>
</div>