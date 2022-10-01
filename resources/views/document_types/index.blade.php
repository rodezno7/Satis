@extends('layouts.app')
@section('title', 'Documents')

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang( 'document_type.documents' )
        <small>@lang( 'document_type.manage_your_documents' )</small>
    </h1>
    <!-- <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
        <li class="active">Here</li>
    </ol> -->
</section>

<!-- Main content -->
<section class="content">

    <div class="box">
        <div class="box-header">
            <h3 class="box-title">@lang( 'document_type.manage_your_documents' )</h3>
            @can('document_type.create')
            <div class="box-tools">
                <button type="button" class="btn btn-block btn-primary btn-modal" 
                    data-href="{{action('DocumentTypeController@create')}}" 
                    data-container=".documents_modal">
                    <i class="fa fa-plus"></i> @lang( 'messages.add' )</button>
            </div>
            @endcan
        </div>
        <div class="box-body">
            @can('document_type.view')
            <div class="table-responsive">
            <table class="table table-bordered table-striped" id="documents_table">
                <thead>
                    <tr>
                        <th>@lang( 'document_type.document_name' )</th>
                        <th>@lang( 'document_type.short_name' )</th>
                        <th>
                            @lang( 'document_type.print_format' )
                            @show_tooltip(__('document_type.tooltip_print_format'))
                        </th>
                        <th>
                            @lang( 'document_type.is_active' )
                            @show_tooltip(__('document_type.tooltip_is_active'))
                        </th>
                        <th>
                            @lang( 'document_type.tax_inc' )
                            @show_tooltip(__('document_type.tooltip_tax_inc'))
                        </th>
                        <th>
                            @lang( 'document_type.tax_exempt' )
                            @show_tooltip(__('document_type.tooltip_tax_exempt'))
                        </th>
                        <th>@lang( 'document_type.is_document_sale' )</th>
                        <th>@lang( 'document_type.is_document_purchase' )</th>
                        <th>@lang( 'document_type.is_return_document' )</th>
                        <th>@lang( 'document_type.default' )</th>
                        <th>@lang( 'document_type.action' )</th>
                    </tr>
                </thead>
            </table>
            </div>
            @endcan
        </div>
    </div>

    <div class="modal fade documents_modal" tabindex="-1" role="dialog" 
        aria-labelledby="gridSystemModalLabel">
    </div>

</section>
<!-- /.content -->
@endsection
@section('javascript')
    <script src="{{ asset('js/document_type.js?v=' . $asset_v) }}"></script>
@endsection