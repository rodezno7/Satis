<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="icon" type="image/png" href="/img/ISOTIPO.png"/>
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@lang('credit.credit_request')</title>
    <style>
        body {
            background-image: url('img/bktech.jpg');
        }
    </style>

    @include('layouts.partials.css')

    <!-- Jquery Steps -->
    <link rel="stylesheet" href="{{ asset('AdminLTE/plugins/pace/pace.css?v='.$asset_v) }}">
    <link rel="stylesheet" href="{{ asset('plugins/jquery.steps/jquery.steps.css?v=' . $asset_v) }}">

    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
<![endif]-->
</head>

<body>
    <div class="row" style="margin-top: 2%">
        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-12">
        </div>
        <div class="col-lg-10 col-md-10 col-sm-10 col-xs-12">
            <div class="boxform_u box-solid_u">
                <form id="form-add-credit">
                    <div class="box-header">
                        <div class="pull-left">
                            <h3>@lang( 'credit.credit_request' )</h3>
                        </div>

                        <div class="pull-right">
                            <img src="{{ asset('uploads/business_logos/'.$logo) }}" width="160px" height="80px">
                        </div>

                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                <label>@lang('credit.type_person')</label>
                                <select name="type_person" id="type_person" class="select2" style="width: 100%">
                                    <option value="legal" selected>@lang('credit.legal_person')</option>
                                    <option value="natural">@lang('credit.natural')</option>
                                </select>
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">

                            </div>
                        </div>
                        <div id="div_legal">
                            <h3>@lang('credit.legal_person')</h3>
                            <div class="row">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <div class="form-group">
                                        <label>@lang('credit.social_reason')</label>
                                        <input type="text" name="business_name" id="business_name" class="form-control" placeholder="@lang('credit.social_reason')">
                                    </div>
                                </div>  
                            </div>
                            <div class="row">
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                    <div class="form-group">
                                        <label>@lang('credit.trade_name')</label>
                                        <input type="text" name="trade_name" id="trade_name" class="form-control" placeholder="@lang('credit.trade_name')">
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                    <div class="form-group">
                                        <label>@lang('credit.nrc')</label>
                                        <input type="text" name="nrc" id="nrc" class="form-control input_number" placeholder="@lang('credit.nrc')">
                                    </div>
                                </div>  
                            </div>
                            <div class="row">
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                    <div class="form-group">
                                        <label>@lang('credit.nit')</label>
                                        <input type="text" name="nit_business" id="nit_business" class="form-control input_number" placeholder="@lang('credit.nit')">
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                    <div class="form-group">
                                        <label>@lang('credit.business_type')</label>
                                        <input type="text" name="business_type" id="business_type" class="form-control" placeholder="@lang('credit.business_type')">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <div class="form-group">
                                        <label>@lang('credit.address')</label>
                                        <input type="text" name="address" id="address" class="form-control" placeholder="@lang('credit.address')">
                                    </div>
                                </div>  
                            </div>
                            <div class="row">
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                    <div class="form-group">
                                        <label>@lang('credit.category')</label>
                                        <input type="text" name="category_business" id="category_business" class="form-control" placeholder="@lang('credit.category')">
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                    <div class="form-group">
                                        <label>@lang('credit.phone')</label>
                                        <input type="text" name="phone_business" id="phone_business" class="form-control input_number" placeholder="@lang('credit.phone')">
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                    <div class="form-group">
                                        <label>@lang('credit.fax')</label>
                                        <input type="text" name="fax_business" id="fax_business" class="form-control input_number" placeholder="@lang('credit.fax')">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                    <div class="form-group">
                                        <label>@lang('credit.legal_representative')</label>
                                        <input type="text" name="legal_representative" id="legal_representative" class="form-control" placeholder="@lang('credit.legal_representative')">
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                    <div class="form-group">
                                        <label>@lang('credit.dui')</label>
                                        <input type="text" name="dui_legal_representative" id="dui_legal_representative" class="form-control input_number" placeholder="@lang('credit.dui')">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <div class="form-group">
                                        <label>@lang('credit.purchasing_agent')</label>
                                        <input type="text" name="purchasing_agent" id="purchasing_agent" class="form-control" placeholder="@lang('credit.purchasing_agent')">
                                    </div>
                                </div>  
                            </div>
                            <div class="row">
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                    <div class="form-group">
                                        <label>@lang('credit.phone')</label>
                                        <input type="text" name="phone_purchasing_agent" id="phone_purchasing_agent" class="form-control input_number" placeholder="@lang('credit.phone')">
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                    <div class="form-group">
                                        <label>@lang('credit.fax')</label>
                                        <input type="text" name="fax_purchasing_agent" id="fax_purchasing_agent" class="form-control input_number" placeholder="@lang('credit.fax')">
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                    <div class="form-group">
                                        <label>@lang('credit.email')</label>
                                        <input type="email" name="email_purchasing_agent" id="email_purchasing_agent" class="form-control" placeholder="@lang('credit.email')">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                    <div class="form-group">
                                        <label>@lang('credit.payment_manager')</label>
                                        <input type="text" name="payment_manager" id="payment_manager" class="form-control" placeholder="@lang('credit.payment_manager')">
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                    <div class="form-group">
                                        <label>@lang('credit.phone')</label>
                                        <input type="text" name="phone_payment_manager" id="phone_payment_manager" class="form-control input_number" placeholder="@lang('credit.phone')">
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                    <div class="form-group">
                                        <label>@lang('credit.email')</label>
                                        <input type="email" name="email_payment_manager" id="email_payment_manager" class="form-control" placeholder="@lang('credit.email')">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                    <div class="form-group">
                                        <label>@lang('credit.amount_request')</label>
                                        <input type="number" min="0" name="amount_request_business" id="amount_request_business" class="form-control" placeholder="@lang('credit.amount_request')">
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                    <div class="form-group">
                                        <label>@lang('credit.term')</label>
                                        <input type="text" name="term_business" id="term_business" class="form-control" placeholder="@lang('credit.term')">
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                    <div class="form-group">
                                        <label>@lang('credit.warranty')</label>
                                        <input type="text" name="warranty_business" id="warranty_business" class="form-control" placeholder="@lang('credit.warranty')">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="div_natural" style="display: none;">
                            <h3>@lang('credit.natural')</h3>
                            <div class="row">
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                    <div class="form-group">
                                        <label>@lang('credit.name_by_dui')</label>
                                        <input type="text" name="name_natural" id="name_natural" class="form-control" placeholder="@lang('credit.name_by_dui')">
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                    <div class="form-group">
                                        <label>@lang('credit.dui')</label>
                                        <input type="text" name="dui_natural" id="dui_natural" class="form-control input_number" placeholder="@lang('credit.dui')">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                    <div class="form-group">
                                        <label>@lang('credit.age')</label>
                                        <input type="number" min="18" max="130" name="age" id="age" class="form-control">
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                    <div class="form-group">
                                        <label>@lang('credit.birthday')</label>
                                        <input type="date" name="birthday" id="birthday" class="inputform2" value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                    <div class="form-group">
                                        <label>@lang('credit.phone')</label>
                                        <input type="text" name="phone_natural" id="phone_natural" class="form-control input_number" placeholder="@lang('credit.phone')">
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                    <div class="form-group">
                                        <label>@lang('credit.category')</label>
                                        <input type="text" name="category_natural" id="category_natural" class="form-control" placeholder="@lang('credit.category')">
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                    <div class="form-group">
                                        <label>@lang('credit.nit')</label>
                                        <input type="text" name="nit_natural" id="nit_natural" class="form-control input_number" placeholder="@lang('credit.nit')">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <div class="form-group">
                                        <label>@lang('credit.address')</label>
                                        <input type="text" name="address_natural" id="address_natural" class="form-control" placeholder="@lang('credit.address')">
                                    </div>
                                </div>  
                            </div>

                            <div class="row">
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                    <div class="form-group">
                                        <label>@lang('credit.amount_request')</label>
                                        <input type="number" min="0" name="amount_request_natural" id="amount_request_natural" class="form-control" placeholder="@lang('credit.amount_request')">
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                    <div class="form-group">
                                        <label>@lang('credit.term')</label>
                                        <input type="text" name="term_natural" id="term_natural" class="form-control" placeholder="@lang('credit.term')">
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                    <div class="form-group">
                                        <label>@lang('credit.warranty')</label>
                                        <input type="text" name="warranty_natural" id="warranty_natural" class="form-control" placeholder="@lang('credit.warranty')">
                                    </div>
                                </div>
                            </div>

                            <h3>@lang('credit.own_business')</h3>
                            <div class="row">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <div class="form-group">
                                        <label>@lang('credit.own_business_name')</label>
                                        <input type="text" name="own_business_name" id="own_business_name" class="form-control" placeholder="@lang('credit.own_business_name')">
                                    </div>
                                </div>  
                            </div>

                            <div class="row">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <div class="form-group">
                                        <label>@lang('credit.own_business_address')</label>
                                        <input type="text" name="own_business_address" id="own_business_address" class="form-control" placeholder="@lang('credit.own_business_address')">
                                    </div>
                                </div>  
                            </div>
                            <div class="row">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <div class="form-group">
                                        <label>@lang('credit.own_business_time')</label>
                                        <input type="text" name="own_business_time" id="own_business_time" class="form-control" placeholder="@lang('credit.own_business_time')">
                                    </div>
                                </div>  
                            </div>
                            <div class="row">
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                    <div class="form-group">
                                        <label>@lang('credit.phone')</label>
                                        <input type="text" name="own_business_phone" id="own_business_phone" class="form-control input_number" placeholder="@lang('credit.phone')">
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                    <div class="form-group">
                                        <label>@lang('credit.fax')</label>
                                        <input type="text" name="own_business_fax" id="own_business_fax" class="form-control input_number" placeholder="@lang('credit.fax')">
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                    <div class="form-group">
                                        <label>@lang('credit.email')</label>
                                        <input type="email" name="own_business_email" id="own_business_email" class="form-control" placeholder="@lang('credit.email')">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <div class="form-group">
                                        <label>@lang('credit.average_monthly_income')</label>
                                        <input type="number" min="0" name="average_monthly_income" id="average_monthly_income" class="form-control" placeholder="@lang('credit.average_monthly_income')">
                                    </div>
                                </div>  
                            </div>
                            <h3>@lang('credit.spouse_dates')</h3>
                            <div class="row">
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                    <div class="form-group">
                                        <label>@lang('credit.spouse_name')</label>
                                        <input type="text" name="spouse_name" id="spouse_name" class="form-control" placeholder="@lang('credit.spouse_name')">
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                    <div class="form-group">
                                        <label>@lang('credit.dui')</label>
                                        <input type="text" name="spouse_dui" id="spouse_dui" class="form-control input_number" placeholder="@lang('credit.dui')">
                                    </div>
                                </div>  
                            </div>
                            <div class="row">
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                    <div class="form-group">
                                        <label>@lang('credit.spouse_work_address')</label>
                                        <input type="text" name="spouse_work_address" id="spouse_work_address" class="form-control" placeholder="@lang('credit.spouse_work_address')">
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                    <div class="form-group">
                                        <label>@lang('credit.phone')</label>
                                        <input type="text" name="spouse_phone" id="spouse_phone" class="form-control input_number" placeholder="@lang('credit.phone')">
                                    </div>
                                </div>  
                            </div>

                            <div class="row">
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                    <div class="form-group">
                                        <label>@lang('credit.spouse_income_date')</label>
                                        <input type="date" name="spouse_income_date" id="spouse_income_date" class="inputform2" value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                    <div class="form-group">
                                        <label>@lang('credit.spouse_position')</label>
                                        <input type="text" name="spouse_position" id="spouse_position" class="form-control" placeholder="@lang('credit.spouse_position')">
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                    <div class="form-group">
                                        <label>@lang('credit.spouse_salary')</label>
                                        <input type="number" min="0" name="spouse_salary" id="spouse_salary" class="form-control" placeholder="@lang('credit.spouse_salary')">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                    <div class="form-group">
                                        <label>@lang('credit.order_purchase')</label>
                                        {!! Form::checkbox('order_purchase', '1', false, ['id' => 'order_purchase']); !!}
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                    <div class="form-group">
                                        <label>@lang('credit.order_via_fax')</label>
                                        {!! Form::checkbox('order_via_fax', '1', false, ['id' => 'order_via_fax']); !!}
                                    </div>
                                </div>  
                            </div>
                        </div>
                        <h3>@lang('credit.comercial_references')</h3>
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <button type="button" class="btn btn-success btn-xs" id="btn-add-reference">+</button>
                                <div class="table-responsive">
                                    <table class="table" width="100%">
                                        <thead>
                                            <th style="width: 5%">Op</th>
                                            <th style="width: 50%">@lang('credit.name')</th>
                                            <th style="width: 15%">@lang('credit.phone')</th>
                                            <th style="width: 15%">@lang('credit.amount')</th>
                                            <th style="width: 15%">@lang('credit.date_cancelled')</th>
                                        </thead>
                                        <tbody id="list_references">
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <h3>@lang('credit.relationships')</h3>
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <button type="button" class="btn btn-success btn-xs" id="btn-add-relationship">+</button>
                                <div class="table-responsive">
                                    <table class="table" width="100%">
                                        <thead>
                                            <th style="width: 5%">Op</th>
                                            <th style="width: 30%">@lang('credit.name')</th>
                                            <th style="width: 15%">@lang('credit.relationship')</th>
                                            <th style="width: 15%">@lang('credit.phone')</th>
                                            <th style="width: 35%">@lang('credit.address')</th>
                                        </thead>
                                        <tbody id="list_relationships">
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="box-footer">
                        <input type="button" class="btn btn-primary" value="@lang('credit.save')" id="btn-add-credit">
                    </div>
                </form>
            </div>
        </div>
        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-12">
        </div>
        
    </div>
    @include('layouts.partials.javascripts')
    <!-- Scripts -->
    <script type="text/javascript">
        $(document).ready(function(){
            loadPerson();
            $('.select2').select2();
        });

        $("#type_person").change(function(){
            loadPerson();
        });
        
        function loadPerson()
        {
            type = $("#type_person").val();
            if (type != null) {
                if(type == 'natural') {
                    $("#div_legal").hide();
                    $("#div_natural").show();
                }
                else {
                    $("#div_natural").hide();
                    $("#div_legal").show();
                }
            }
        }

        $("#btn-add-reference").click(function(){
            addReference();
        });

        $("#btn-add-relationship").click(function(){
            addRelationship();
        });

        var cont = 0;
        var references = [];

        var cont2 = 0;
        var relationships = [];

        function addReference()
        {
            references.push(cont);
            
            var row = '<tr class="selected" id="row'+cont+'" style="height: 10px"><td><button type="button" class="btn btn-danger btn-xs" onclick="deleteReference('+cont+', '+cont+');"><i class="fa fa-times"></i></button></td><td><input type="text" name="name_reference[]" id="name_reference'+cont+'" class="form-control col-lg-12 col-md-12 col-sm-12 col-xs-12" placeholder="@lang('credit.name')" required></td><td><input type="text" name="phone_reference[]" class="form-control col-lg-12 col-md-12 col-sm-12 col-xs-12 input_number" placeholder="@lang('credit.phone')" required></td><td><input type="number" min="0" name="amount_reference[]" class="form-control col-lg-12 col-md-12 col-sm-12 col-xs-12" placeholder="@lang('credit.amount_request')" required></td><td><input type="date" name="date_reference[]" value="" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 inputform2" value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}" required></td></tr>';
            $("#list_references").append(row);
            $("#name_reference"+cont+"").focus();
            cont++;
        }

        function addRelationship()
        {
            relationships.push(cont2);

            
            var row2 = '<tr class="selected" id="row2'+cont2+'" style="height: 10px"><td><button type="button" class="btn btn-danger btn-xs" onclick="deleteRelationship('+cont2+', '+cont2+');"><i class="fa fa-times"></i></button></td><td><input type="text" name="name_relationship[]" id="name_relationship'+cont2+'" class="form-control col-lg-12 col-md-12 col-sm-12 col-xs-12" placeholder="@lang('credit.name')" required></td><td><select name="relation_relationship[]" class="select2" style="width: 100%" required><option value="0" disabled selected>@lang("messages.please_select")</option><option value="Padre">Padre</option><option value="Madre">Madre</option><option value="Hijo/a">Hijo/a</option><option value="Hermano/a">Hermano/a</option><option value="Tío">Tío/a</option><option value="Primo/a">Primo/a</option><option value="Abuelo/a">Abuelo/a</option></select></td><td><input type="text" name="phone_relationship[]" class="form-control col-lg-12 col-md-12 col-sm-12 col-xs-12 input_number" placeholder="@lang('credit.phone')" required></td><td><input type="text" name="address_relationship[]" value="" class="form-control col-lg-12 col-md-12 col-sm-12 col-xs-12" placeholder="@lang('credit.address')" required></td></tr>';
            $("#list_relationships").append(row2);
            $('.select2').select2();
            $("#name_relationship"+cont2+"").focus();
            cont2++;
        }



        function deleteReference(index, id){ 
            $("#row" + index).remove();
            references.removeItem(id);
            if(references.length == 0)
            {
                cont = 0;
                references = [];
            }
        }

        function deleteRelationship(index, id){ 
            $("#row2" + index).remove();
            relationships.removeItem(id);
            if(relationships.length == 0)
            {
                cont2 = 0;
                relationships = [];
            }
        }

        Array.prototype.removeItem = function (a) {
            for (var i = 0; i < this.length; i++) {
                if (this[i] == a) {
                    for (var i2 = i; i2 < this.length - 1; i2++) {
                        this[i2] = this[i2 + 1];
                    }
                    this.length = this.length - 1;
                    return;
                }
            }
        };

        $("#btn-add-credit").click(function() {
            $("#btn-add-credit").prop("disabled", true);

            datastring = $("#form-add-credit").serialize();

            route = "/credits";
            token = $("#token").val();
            $.ajax({
                url: route,
                type: 'POST',
                datatype: "json",
                headers: {'X-CSRF-TOKEN': token},
                data: datastring,
                success:function(result){
                    if (result.success == true) {
                        $("#btn-add-credit").prop("disabled", false);
                        
                        Swal.fire({
                            title: '{{__('crm.added_success')}}',
                            text: '{{__('crm.send_content')}}',
                            icon: 'success',
                            showCancelButton: false,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: "{{__('messages.accept')}}",
                            cancelButtonText: "{{__('messages.cancel')}}"
                        }).then((willDelete) => {
                            if (willDelete.value) {
                                route = '/credits/show-report';
                                token = $("#token").val();
                                $.ajax({                    
                                    url: route,
                                    headers: {'X-CSRF-TOKEN': token},
                                    cache: false,
                                    type: 'POST',
                                    data: {
                                        id: result.id
                                    },
                                    xhrFields: {
                                        responseType: 'blob'
                                    },
                                    success: function (response, status, xhr) {
                                       var filename = "";                   
                                       var disposition = xhr.getResponseHeader('Content-Disposition');

                                       if (disposition) {
                                        var filenameRegex = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
                                        var matches = filenameRegex.exec(disposition);
                                        if (matches !== null && matches[1]) filename = matches[1].replace(/['"]/g, '');
                                    } 
                                    var linkelem = document.createElement('a');
                                    try {
                                        var blob = new Blob([response], { type: 'application/octet-stream' });                        

                                        if (typeof window.navigator.msSaveBlob !== 'undefined') {

                                            window.navigator.msSaveBlob(blob, filename);
                                        } else {
                                            var URL = window.URL || window.webkitURL;
                                            var downloadUrl = URL.createObjectURL(blob);

                                            if (filename) { 

                                                var a = document.createElement("a");


                                                if (typeof a.download === 'undefined') {
                                                    window.location = downloadUrl;
                                                } else {
                                                    a.href = downloadUrl;
                                                    a.download = filename;
                                                    document.body.appendChild(a);
                                                    a.target = "_blank";
                                                    a.click();
                                                }
                                            } else {
                                                window.location = downloadUrl;
                                            }
                                        }
                                    } catch (ex) {
                                        console.log(ex);
                                    }

                                    var url = '{!!URL::to('/credits')!!}';                                    
                                    window.location.href = url;




                                }
                            });
                            }
                        });
                    }
                    else
                    {
                        $("#btn-add-credit").prop("disabled", false);
                        Swal.fire
                        ({
                            title: result.msg,
                            icon: "error",
                        });
                    }

                },
                error:function(msj){
                    $("#btn-add-credit").prop("disabled", false);
                    var errormessages = "";
                    $.each(msj.responseJSON.errors, function(i, field){
                        errormessages+="<li>"+field+"</li>";
                    });
                    Swal.fire
                    ({
                        title: "{{__('accounting.errors')}}",
                        icon: "error",
                        html: "<ul>"+ errormessages+ "</ul>",
                    });
                }
            });
});

</script>
</body>
</html>
