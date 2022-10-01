<div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      {!! Form::open(['url' => action('ExpenseController@getAddExpenses'), 'method' => 'post', 'id' => "add_expenses_modal" ]) !!}
      
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">@lang('expense.add_expenses')</h4>
      </div>

      <div class="modal-body">
          <table class="table">
            <tr>
                <th style="width: 15%;">@lang('purchase.supplier')</th>
                <th style="width: 12%;">@lang('expense.ref_no')</th>
                <th style="width: 12%;">@lang('messages.date')</th>
                <th style="width: 10%;">@lang('lang_v1.sub_total')</th>
                <th style="width: 10%;">@lang('tax_rate.taxes')</th>
                <th style="width: 10%;">@lang('tax_rate.tax_amount')</th>
                <th style="width: 10%;">@lang('expense.total')</th>
                <th style="width: 10%;">
                    @lang('purchase.attach_document')
                    <i class="fa fa-info-circle text-info hover-q " aria-hidden="true" 
                    data-container="body" data-toggle="popover" data-placement="auto"
                    data-content="@lang('purchase.max_file_size', ['size' => (config('constants.document_size_limit') / 1000000)])" data-html="true" data-trigger="hover"></i>
                </th>
                <th style="width: 10%">@lang('expense.expense_note')</th>
            </tr>
            <tr>
                <td>
                    {!! Form::select('expense[0][supplier]', $suppliers, null, ['class' => 'form-control select2', 'id' => 'supplier', 'placeholder' => __('messages.please_select'), "width" => "100%"]); !!}
                </td>
                <td>{!! Form::text("expense[0][ref_no]", null, ["class" => "form-control", "id" => "ref_no", "placeholder" => __('expense.ref_no')]) !!}</td>
                <td>{!! Form::text("expense[0][transaction_date]", @format_date('now'), ["class" => "form-control", "id" => "transaction_date", "readonly"]) !!}</td>
                <td>{!! Form::text("expense[0][subtotal]", null, ["class" => "form-control input_number", "id" => "subtotal", "placeholder" => __('lang_v1.sub_total')]) !!}</td>
                <td>
                    <select name="tax_id" id="tax_id" class="form-control select2" placeholder="'Please Select'" style="width: 100%;">
                        <option value="0" selected>@lang('lang_v1.none')</option>
                        @foreach ($taxes as $tg)
                            <option value="{{ $tg['id'] }}" data-percent="{{ $tg['percent'] }}"> {{ $tg['name'] }} </option>
                        @endforeach
                    </select>
                </td>
                <td>{!! Form::text("expense[0][tax_amount]", null, ["class" => "form-control input_number", "id" => "tax_amount", "placeholder" => __('expense.tax_amount'), "readonly"]) !!}</td>
                <td>{!! Form::text("expense[0][total_amount]", null, ["class" => "form-control input_number", "id" => "total_amount", "placeholder" => __('expense.total'), "readonly"]) !!}</td>
                <td>{!! Form::file('expense[0][document]', ['id' => 'document']); !!}</td>
                <td>{!! Form::textarea('expense[0][notes]', null, ['class' => 'form-control', "id" => "notes", 'rows' => 2]); !!}</td>
            </tr>
          </table>
      </div>

      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">@lang( 'messages.save' )</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
      </div>
  
      {!! Form::close() !!}
    
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->