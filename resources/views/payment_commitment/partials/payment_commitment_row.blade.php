@if ($type == 'automatic')
    <tr>
        <td>
            {{ $transaction->doc_type }}
            {!! Form::hidden("doc_type", $transaction->doc_type, ["data-name" => "doc_type"]) !!}
            {!! Form::hidden("pcl_id", 0, ["data-name" => "pcl_id"]) !!}
            {!! Form::hidden("transaction_id", $transaction->transaction_id, ["data-name" => "transaction_id"]) !!}
        </td>
        <td>
            {{ $transaction->ref_no }}
            {!! Form::hidden("ref_no", $transaction->ref_no, ["data-name" => "ref_no"]) !!}
        </td>
        <td>
            <span class="display_currency" data-currency_symbol="true">{{ $transaction->amount }}</span>
            {!! Form::hidden("amount", $transaction->amount, ["class" => "row_amount", "data-name" => "amount"]) !!}
        </td>
        <td style="text-align: center; vertical-align: middle;">
            <button type="button" class="btn btn-danger btn-xs btn-delete-row"><i class="fa fa-times" aria-hidden="true"></i></button>
        </td>
    </tr>
@else
    <tr>
        <td>
            {!! Form::text("doc_type", null, ["class" => "form-control input-sm", "data-name" => "doc_type"]) !!}
            {!! Form::hidden("pcl_id", 0, ["data-name" => "pcl_id"]) !!}
        </td>
        <td>
            {!! Form::text("ref_no", null, ["class" => "form-control input-sm", "data-name" => "ref_no"]) !!}
        </td>
        <td>
            {!! Form::text("amount", null, ["class" => "form-control input-sm row_amount", "data-name" => "amount"]) !!}
        </td>
        <td style="text-align: center; vertical-align: middle;">
            <button type="button" class="btn btn-danger btn-xs btn-delete-row"><i class="fa fa-times" aria-hidden="true"></i></button>
        </td>
    </tr>
@endif