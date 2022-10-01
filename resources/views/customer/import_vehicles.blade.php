@extends('layouts.app')

@section('title', __('customer.import_vehicles'))

@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('customer.import_customer_vehicles')</h1>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-sm-12">
            <div class="box box-solid box-default">
                <div class="box-body">
                    {!! Form::open([
                        'url' => action('CustomerVehicleController@postImporter'),
                        'method' => 'post',
                        'enctype' => 'multipart/form-data',
                        'id' => 'send_form'
                    ]) !!}
                    <div class="row">
                        {{-- file_xlsx --}}
                        <div class="col-sm-4">
                            <div class="form-group">
                                {!! Form::label('file_xlsx', __('product.file_to_import') . ':') !!}
                                {!! Form::file('file_xlsx', ['accept' => '.xlsx', 'required']) !!}
                            </div>
                        </div>

                        {{-- button --}}
                        <div class="col-sm-4">
                            <button type="submit" class="btn btn-primary" id="submit_send" style="margin-top: 12px;">
                                @lang('messages.submit')
                            </button>
                        </div>
                    </div>
                    {!! Form::close() !!}

                    {{-- Download template --}}
                    <div class="row" style="margin-top: 5px;">
                        <div class="col-sm-4">
                            <a href="
                                @if (auth()->user()->language == 'es')
                                    {{ asset('uploads/files/import_vehicles_template_' . auth()->user()->language . '.xlsx') }}
                                @else
                                    {{ asset('uploads/files/import_vehicles_template_en.xlsx') }}
                                @endif
                                " class="btn btn-success" download>
                                <i class="fa fa-download"></i>
                                @lang('product.download_csv_file_template')
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if (isset($flag) && isset($exception))
        {{-- Errors --}}
        @if (! $flag)
            <div class="row">
                <div class="col-sm-12">
                    <div class="box box-solid box-danger">
                        <div class="box-header">
                            <h3 class="box-title">@lang('accounting.errors')</h3>
                        </div>

                        <div class="box-body">
                            <table class="table table-condensed table-striped table-text-center table-th-gray" id="errors_table">
                                <thead>
                                    <tr>
                                        <th width="15%">@lang('purchase.row_number')</th>
                                        <th width="85%">@lang('accounting.description')</th>
                                    </tr>
                                </thead>
                                @foreach ($errors as $error)
                                    <tr>
                                        <td>{{ $error['row'] }}</td>
                                        <td>{{ $error['msg'] }}</td>
                                    </tr>
                                @endforeach
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        {{-- Process purchase --}}
        @else
            <div class="row">
                <div class="col-sm-12">
                    <div class="box box-solid box-success">
                        <div class="box-header">
                            <h3 class="box-title">@lang('customer.import_data')</h3>
                        </div>

                        <div class="box-body">
                            {!! Form::open([
                                'url' => action('CustomerVehicleController@import'),
                                'method' => 'post',
                                'enctype' => 'multipart/form-data',
                                'id' => 'import_form'
                            ]) !!}
                            <div class="col-sm-12 text-center">
                                <div class="form-group" style="margin-bottom: 0;">
                                    <button type="submit" class="btn btn-success" id="submit_import">
                                        @lang('purchase.import')
                                    </button>
                                </div>
                            </div>
                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endif

    {{-- Instructions --}}
    <div class="row">
        <div class="col-sm-12">
            <div class="box box-solid box-default">
                <div class="box-header">
                    <h3 class="box-title">@lang('lang_v1.instructions')</h3>
                </div>

                <div class="box-body">
                    <strong>@lang('lang_v1.instruction_line1')</strong>
                    
                    <ol style="margin-left: 20px; margin-top: 5px; margin-bottom: 15px;">
                        <li style="list-style-type: decimal;">@lang('lang_v1.instruction_line3')</li>
                        <li style="list-style-type: decimal;">@lang('lang_v1.instruction_line4')</li>
                        <li style="list-style-type: decimal;">@lang('lang_v1.instruction_line2')</li>
                    </ol>

                    <table class="table table-condensed table-striped table-text-center table-th-gray">
                        <thead>
                            <tr>
                                <th style="width: 15%;">@lang('lang_v1.col_no')</th>
                                <th style="width: 25%;">@lang('lang_v1.col_name')</th>
                                <th style="width: 60%;">@lang('lang_v1.instruction')</th>
                            </tr>
                        </thead>

                        <tr>
                            <td>1</td>
                            <td>
                                @lang('customer.dni_column')
                                <br>
                                <small class="text-muted">
                                    (@lang('customer.required_if_nit_is_empty'))
                                </small>
                            </td>
                            <td>&nbsp;</td>
                        </tr>

                        <tr>
                            <td>2</td>
                            <td>
                                @lang('customer.nit_column')
                                <br>
                                <small class="text-muted">
                                    (@lang('customer.required_if_dni_is_empty'))
                                </small>
                            </td>
                            <td>&nbsp;</td>
                        </tr>

                        <tr>
                            <td>3</td>
                            <td>
                                @lang('customer.license_plate')
                                <br>
                                <small class="text-muted">
                                    (@lang('lang_v1.required'))
                                </small>
                            </td>
                            <td>&nbsp;</td>
                        </tr>

                        <tr>
                            <td>4</td>
                            <td>
                                @lang('brand.brand')
                                <br>
                                <small class="text-muted">
                                    (@lang('lang_v1.optional'))
                                </small>
                            </td>
                            <td>
                                @lang('lang_v1.brand_ins'). @lang('lang_v1.brand_ins2').
                            </td>
                        </tr>

                        <tr>
                            <td>5</td>
                            <td>
                                @lang('card_pos.model')
                                <br>
                                <small class="text-muted">
                                    (@lang('lang_v1.optional'))
                                </small>
                            </td>
                            <td>&nbsp;</td>
                        </tr>

                        <tr>
                            <td>6</td>
                            <td>
                                @lang('accounting.year')
                                <br>
                                <small class="text-muted">
                                    (@lang('lang_v1.optional'))
                                </small>
                            </td>
                            <td>
                                @lang('customer.year_greater_equal_zero')
                            </td>
                        </tr>

                        <tr>
                            <td>7</td>
                            <td>
                                @lang('crm.color')
                                <br>
                                <small class="text-muted">
                                    (@lang('lang_v1.optional'))
                                </small>
                            </td>
                            <td>&nbsp;</td>
                        </tr>

                        <tr>
                            <td>8</td>
                            <td>
                                @lang('customer.responsible_vehicle')
                                <br>
                                <small class="text-muted">
                                    (@lang('lang_v1.optional'))
                                </small>
                            </td>
                            <td>&nbsp;</td>
                        </tr>

                        <tr>
                            <td>9</td>
                            <td>
                                @lang('customer.engine_num')
                                <br>
                                <small class="text-muted">
                                    (@lang('lang_v1.optional'))
                                </small>
                            </td>
                            <td>&nbsp;</td>
                        </tr>

                        <tr>
                            <td>10</td>
                            <td>
                                @lang('customer.vin_chassis')
                                <br>
                                <small class="text-muted">
                                    (@lang('lang_v1.optional'))
                                </small>
                            </td>
                            <td>&nbsp;</td>
                        </tr>

                        <tr>
                            <td>11</td>
                            <td>
                                @lang('customer.mi_km')
                                <br>
                                <small class="text-muted">
                                    (@lang('lang_v1.optional'))
                                </small>
                            </td>
                            <td>&nbsp;</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
