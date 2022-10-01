<div class="modal-dialog" role="document">
    <div class="modal-content">
    {!! Form::open(['url' => action('CashDetailController@store'), 'method' => 'post' ]) !!}

    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">@lang( 'cash_register.add_cash_detail' )</h4>
    </div>

    <div class="modal-body">
        <div class="row">
            <div class="col-sm-3">
                <div class="form-group">
                    {!! Form::label("one_cent", __("cash_register.one_cent")) !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-money"></i>
                        </span>
                        {!! Form::text("one_cent", null, ["class" => "form-control input_number", "id" => "one_cent",
                            "placeholder" => "$0.01"]) !!}
                    </div>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group">
                    {!! Form::label("five_cents", __("cash_register.five_cents")) !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-money"></i>
                        </span>
                        {!! Form::text("five_cents", null, ["class" => "form-control input_number", "id" => "five_cents",
                            "placeholder" => "$0.05"]) !!}
                    </div>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group">
                    {!! Form::label("ten_cents", __("cash_register.ten_cents")) !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-money"></i>
                        </span>
                        {!! Form::text("ten_cents", null, ["class" => "form-control input_number", "id" => "ten_cents",
                            "placeholder" => "$0.1"]) !!}
                    </div>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group">
                    {!! Form::label("twenty_five_cents", __("cash_register.twenty_five_cents")) !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-money"></i>
                        </span>
                        {!! Form::text("twenty_five_cents", null, ["class" => "form-control input_number", "id" => "twenty_five_cents",
                            "placeholder" => "$0.25"]) !!}
                    </div>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group">
                    {!! Form::label("one_dollar", __("cash_register.one_dollar")) !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-money"></i>
                        </span>
                        {!! Form::text("one_dollar", null, ["class" => "form-control input_number", "id" => "one_dollar",
                            "placeholder" => "$1"]) !!}
                    </div>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group">
                    {!! Form::label("five_dollars", __("cash_register.five_dollars")) !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-money"></i>
                        </span>
                        {!! Form::text("five_dollars", null, ["class" => "form-control input_number", "id" => "five_dollars",
                            "placeholder" => "$5"]) !!}
                    </div>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group">
                    {!! Form::label("ten_dollars", __("cash_register.ten_dollars")) !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-money"></i>
                        </span>
                        {!! Form::text("ten_dollars", null, ["class" => "form-control input_number input_number", "id" => "ten_dollars",
                            "placeholder" => "$10"]) !!}
                    </div>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group">
                    {!! Form::label("twenty_dollars", __("cash_register.twenty_dollars")) !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-money"></i>
                        </span>
                        {!! Form::text("twenty_dollars", null, ["class" => "form-control input_number", "id" => "twenty_dollars",
                            "placeholder" => "$20"]) !!}
                    </div>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group">
                    {!! Form::label("fifty_dollars", __("cash_register.fifty_dollars")) !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-money"></i>
                        </span>
                        {!! Form::text("fifty_dollars", null, ["class" => "form-control input_number", "id" => "fifty_dollars",
                            "placeholder" => "$50"]) !!}
                    </div>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group">
                    {!! Form::label("one_hundred_dollars", __("cash_register.one_hundred_dollars")) !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-money"></i>
                        </span>
                        {!! Form::text("one_hundred_dollars", null, ["class" => "form-control input_number", "id" => "one_hundred_dollars",
                            "placeholder" => "$50"]) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal" data-toggle="modal">@lang( 'messages.close' )</button>
    </div>

    {!! Form::close() !!}
    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->