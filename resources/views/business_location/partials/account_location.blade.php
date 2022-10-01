<div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
  
      {!! Form::open(['url' => action('BusinessLocationController@postAccountingAccountByLocation'), 'method' => 'post', 'id' => 'account_business_location_form' ]) !!}
  
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">@lang( 'business.account_business_locations' ): {{ $location->name }}</h4>
      </div>
  
      <div class="modal-body">
        <div class="row">
          {!! Form::hidden("location_id", $location->id) !!}
          <div class="col-sm-4">
            <div class="form-group">
              {!! Form::label('general_cash', __('business.general_cash')) !!}
              {!! Form::select('general_cash_id', $account_location ? $account_location_names : [],
                $account_location ? $account_location->general_cash_id : null,
                ['class' => 'form-control account_select', 'placeholder' => __('business.select_account')]) !!}
            </div>
          </div>
          <div class="col-sm-4">
            <div class="form-group">
              {!! Form::label('inventory_account', __('business.inventory_account')) !!}
              {!! Form::select('inventory_account_id', $account_location ? $account_location_names : [],
                $account_location ? $account_location->inventory_account_id : null,
                ['class' => 'form-control account_select', 'placeholder' => __('business.select_account')]) !!}
            </div>
          </div>
          <div class="col-sm-4">
            <div class="form-group">
              {!! Form::label('account_receivable', __('business.account_receivable')) !!}
              {!! Form::select('account_receivable_id', $account_location ? $account_location_names : [],
                $account_location ? $account_location->account_receivable_id : null,
                ['class' => 'form-control account_select', 'placeholder' => __('business.select_account')]) !!}
            </div>
          </div>
          <div class="col-sm-4">
            <div class="form-group">
              {!! Form::label('vat_final_customer', __('business.vat_final_customer')) !!}
              {!! Form::select('vat_final_customer_id', $account_location ? $account_location_names : [],
                $account_location ? $account_location->vat_final_customer_id : null,
                ['class' => 'form-control account_select', 'placeholder' => __('business.select_account')]) !!}
            </div>
          </div>
          <div class="col-sm-4">
            <div class="form-group">
              {!! Form::label('vat_taxpayer', __('business.vat_taxpayer')) !!}
              {!! Form::select('vat_taxpayer_id', $account_location ? $account_location_names : [],
                $account_location ? $account_location->vat_taxpayer_id : null,
                ['class' => 'form-control account_select', 'placeholder' => __('business.select_account')]) !!}
            </div>
          </div>
          <div class="col-sm-4">
            <div class="form-group">
              {!! Form::label('supplier_account', __('business.supplier_account')) !!}
              {!! Form::select('supplier_account_id', $account_location ? $account_location_names : [],
                $account_location ? $account_location->supplier_account_id : null,
                ['class' => 'form-control account_select', 'placeholder' => __('business.select_account')]) !!}
            </div>
          </div>
          <div class="col-sm-4">
            <div class="form-group">
              {!! Form::label('provider_account', __('business.provider_account')) !!}
              {!! Form::select('provider_account_id', $account_location ? $account_location_names : [],
                $account_location ? $account_location->provider_account_id : null,
                ['class' => 'form-control account_select', 'placeholder' => __('business.select_account')]) !!}
            </div>
          </div>
          <div class="col-sm-4">
            <div class="form-group">
              {!! Form::label('sale_cost', __('business.sale_cost')) !!}
              {!! Form::select('sale_cost_id', $account_location ? $account_location_names : [],
                $account_location ? $account_location->sale_cost_id : null,
                ['class' => 'form-control account_select', 'placeholder' => __('business.select_account')]) !!}
            </div>
          </div>
          <div class="col-sm-4">
            <div class="form-group">
              {!! Form::label('sale_expense', __('business.sale_expense')) !!}
              {!! Form::select('sale_expense_id', $account_location ? $account_location_names : [],
                $account_location ? $account_location->sale_expense_id : null,
                ['class' => 'form-control account_select', 'placeholder' => __('business.select_account')]) !!}
            </div>
          </div>
          <div class="col-sm-4">
            <div class="form-group">
              {!! Form::label('admin_expense', __('business.admin_expense')) !!}
              {!! Form::select('admin_expense_id', $account_location ? $account_location_names : [],
                $account_location ? $account_location->admin_expense_id : null,
                ['class' => 'form-control account_select', 'placeholder' => __('business.select_account')]) !!}
            </div>
          </div>
          <div class="col-sm-4">
            <div class="form-group">
              {!! Form::label('financial_expense', __('business.financial_expense')) !!}
              {!! Form::select('financial_expense_id', $account_location ? $account_location_names : [],
                $account_location ? $account_location->financial_expense_id : null,
                ['class' => 'form-control account_select', 'placeholder' => __('business.select_account')]) !!}
            </div>
          </div>
          <div class="col-sm-4">
            <div class="form-group">
              {!! Form::label('local_sales', __('business.local_sales')) !!}
              {!! Form::select('local_sale_id', $account_location ? $account_location_names : [],
                $account_location ? $account_location->local_sale_id : null,
                ['class' => 'form-control account_select', 'placeholder' => __('business.select_account')]) !!}
            </div>
          </div>
          <div class="col-sm-4">
            <div class="form-group">
              {!! Form::label('exports', __('business.exports')) !!}
              {!! Form::select('exports_id', $account_location ? $account_location_names : [],
                $account_location ? $account_location->exports_id : null,
                ['class' => 'form-control account_select', 'placeholder' => __('business.select_account')]) !!}
            </div>
          </div>
        </div>
      </div>
  
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">@lang( 'messages.save' )</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
      </div>
  
      {!! Form::close() !!}
  
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->