@extends('layouts.app')
@section('title',  __('cash_register.open_cash_register'))

@section('content')
<style type="text/css">



</style>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('cash_register.open_cash_register')</h1>
    <!-- <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
        <li class="active">Here</li>
    </ol> -->
</section>

<!-- Main content -->
<section class="content">
{!! Form::open(['url' => action('CashRegisterController@store'), 'method' => 'post', 
'id' => 'add_cash_register_form' ]) !!}
  <div class="box box-solid">
    <div class="box-body">
      <div class="row">
        <div class="col-sm-12 col-sm-offset-0 col-md-3 col-md-offset-2">
          <div class="form-group">
            {!! Form::label('amount', __('cash_register.cash_in_hand') . ':') !!}
            {!! Form::text('amount', null, ['class' => 'form-control input_number',
              'placeholder' => __('cash_register.enter_amount')]); !!}
          </div>
        </div>
        <div class="col-sm-12 col-md-5">
          <div class="form-group">
            {!! Form::label("cashier", __("cashier.select_cashier")) !!}
            {!! Form::select("cashier", $cashiers, null, ["class" => "form-control select2",
              "placeholder" => __("cashier.select_cashier"), "required"]) !!}
          </div>
        </div>
        <div class="col-sm-6 col-sm-offset-6 col-md-9 col-md-offset-1">
          <button type="submit" class="btn btn-primary pull-right">@lang('cash_register.open_register')</button>
        </div>
      </div>
    </div>
  </div>
  {!! Form::close() !!}
</section>
<!-- /.content -->
@endsection
@section('javascript')
  <script>
    $(function() {
      $(document).on('click', 'form#add_cash_register_form button[type="submit"]', function(e) {
        e.preventDefault();
        let btn = $(this);
        btn.attr('disabled', 'true');

        setTimeout(() => {
          btn.removeAttr('disabled');  
        }, 8000);

        $('form#add_cash_register_form').trigger('submit');
      });
    });
  </script>
@endsection