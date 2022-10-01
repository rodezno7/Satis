@extends('layouts.app')

@section('title', __('product.edit_products'))

@section('content')

{{-- Content Header (Page header) --}}
<section class="content-header">
    <h1>
        @lang('product.edit_products')
    </h1>
</section>

{{-- Main content --}}
<section class="content">
    <div class="row">
        <div class="col-sm-12">
        	<div class="box box-solid box-default">
                <div class="box-body">
                    {!! Form::open([
                        'url' => action('ImportProductsController@checkEditFile'),
                        'method' => 'post',
                        'enctype' => 'multipart/form-data',
                        'id' => 'send_form'
                    ]) !!}

                        <div class="row">
                            {{-- file_xlsx --}}
                            <div class="col-sm-4">
                                <div class="form-group">
                                    {!! Form::label('products_xlsx', __('product.file_to_import') . ':') !!}
                                    {!! Form::file('products_xlsx', ['accept'=> '.xlsx', 'required']) !!}
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
                                @if (auth()->user()->language == 'es' || auth()->user()->language == 'en')
                                    @if (config('app.business') == 'optics')
                                        {{ asset('uploads/files/edit_products_xlsx_template_ov_' . auth()->user()->language . '.xlsx') }}
                                    @else
                                        {{ asset('uploads/files/edit_products_xlsx_template_' . auth()->user()->language . '.xlsx') }}
                                    @endif
                                @else
                                    @if (config('app.business') == 'optics')
                                        {{ asset('uploads/files/edit_products_xlsx_template_ov_en.xlsx') }}
                                    @else
                                        {{ asset('uploads/files/edit_products_xlsx_template_en.xlsx') }}
                                    @endif
                                @endif
                                " class="btn btn-success" download>
                                <i class="fa fa-download"></i>
                                @lang('accounting.download_xlsx_file_template')
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
                                        <th width="15%">@lang('lang_v1.sheet')</th>
                                        <th width="60%">@lang('accounting.description')</th>
                                    </tr>
                                </thead>
                                @foreach ($errors as $error)
                                    <tr>
                                        <td>{{ $error['row'] }}</td>
                                        <td>{{ $error['sheet'] }}</td>
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
                                'url' => action('ImportProductsController@update'),
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
            <div class="box box-solid">
                <div class="box-header">
                  <h3 class="box-title">@lang('lang_v1.instructions')</h3>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-xs-12">
                            <strong>@lang('lang_v1.instruction_line1')</strong><br>
                            <ol style="margin-left: 20px;">
                                <li style="list-style-type: decimal;">@lang('lang_v1.instruction_line3') @lang('lang_v1.instruction_line7')</li>
                                <li style="list-style-type: decimal;">@lang('lang_v1.instruction_line6')</li>
                                <li style="list-style-type: decimal;">@lang('lang_v1.instruction_line2')</li>
                                <li style="list-style-type: decimal;">@lang('lang_v1.instruction_line5')</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="panel with-nav-tabs panel-default boxform_u box-solid_u">
        <div class="panel-heading">
            <ul class="nav nav-tabs">
                <li class="active">
                    <a href="#tab-products" id="link-products" data-toggle="tab">
                        @lang('product.products')
                    </a>
                </li>
                <li>
                    <a href="#tab-services" id="link-services" data-toggle="tab">
                        @lang('product.services')
                    </a>
                </li>
                <li>
                    <a href="#tab-services" id="link-services" data-toggle="tab">
                        @lang('product.kits')
                    </a>
                </li>
                <li>
                    <a href="#tab-services" id="link-services" data-toggle="tab">
                        @lang('material.materials')
                    </a>
                </li>
            </ul>
        </div>

        <div class="panel-body">
            <div class="tab-content">
                {{-- Products tab --}}
                <div class="tab-pane fade in active" id="tab-products">
                    @include('import_products.partials.edit_products_tab')
                </div>

                {{-- Services tab --}}
                <div class="tab-pane" id="tab-services">
                    @include('import_products.partials.edit_services_tab')
                </div>

                {{-- Kits tab --}}
                <div class="tab-pane" id="tab-kits">
                    @include('import_products.partials.edit_services_tab')
                </div>

                {{-- Materials tab --}}
                <div class="tab-pane" id="tab-materials">
                    @include('import_products.partials.edit_materials_tab')
                </div>
            </div>
        </div>
    </div>
</section>

@endsection