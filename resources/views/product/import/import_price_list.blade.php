@extends('layouts.app')
@section('title', __('Importar lista de precios'))

@section('content')
    <br />
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>@lang('Importar lista de precios')</h1>
    </section>

    <!-- Main content -->
    <section class="content">

        @if (session('notification') || !empty($notification))
            <div class="row">
                <div class="col-sm-12">
                    <div class="alert alert-danger alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        @if (!empty($notification['msg']))
                            {{ $notification['msg'] }}
                        @elseif(session('notification.msg'))
                            {{ session('notification.msg') }}
                        @endif
                    </div>
                </div>
            </div>
        @endif
        <div class="row">
            <div class="col-sm-12">
                <div class="box">
                    <div class="box-body">
                        <form action="{{ action('ProductController@postPriceList') }}" method="POST"
                            enctype="multipart/form-data">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        {!! Form::label('name', __('product.file_to_import') . ':') !!}
                                        {{-- @show_tooltip(__('lang_v1.tooltip_import_price_list')) --}}
                                        <input type="file" name="prices_csv" id="prices_csv" accept=".csv" required>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        {!! Form::label('price_list', __('Grupos de precios de venta') . ':') !!}
                                        {{-- @show_tooltip(__('lang_v1.group_price_list')) --}}
                                        <select name="group_id" id="group_ids" class="form-control select2" required>
                                            <option value="" selected>@lang('messages.please_select')</option>
                                            @foreach ($price_list as $pl)
                                                <option value="{{ $pl->id }}">{{ $pl->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <br>
                                    <button type="submit" class="btn btn-primary"
                                        id="btn-su">@lang('messages.submit')</button>
                                </div>
                            </div>
                        </form>
                        <br><br>
                        <div class="row">
                            @php
                                $language = auth()->user()->language ?? 'en';
                            @endphp
                            <div class="col-sm-4">
                                <a href="{{ asset('uploads/files/import_price_list_csv_template_' . $language . '.csv') }}"
                                    class="btn btn-success" download><i class="fa fa-download"></i>
                                    @lang('product.download_csv_file_template')</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="box box-solid">
                    <div class="box-header">
                        <h3 class="box-title">@lang('lang_v1.instructions')</h3>
                    </div>
                    <div class="box-body">
                        <strong>@lang('lang_v1.instruction_line1')</strong><br>
                        @lang('lang_v1.instruction_line2')
                        <br><br>
                        <table class="table table-striped">
                            <tr>
                                <th>@lang('lang_v1.col_no')</th>
                                <th>@lang('lang_v1.col_name')</th>
                                <th>@lang('lang_v1.instruction')</th>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>@lang('product.sub_sku')<small class="text-muted">(@lang('lang_v1.required'))</small>
                                </td>
                                <td>@lang('product.sub_sku_ins')</td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>@lang('product.product_inc_tax')<small class="text-muted">(@lang('lang_v1.required'))
                                <td>@lang('product.product_inc_tax_ins')</td>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- /.content -->

@endsection
