@extends('layouts.app')
@section('title', __('fixed_asset.fixed_asset_types'))

@section('content')

<section class="content-header">
    <h1>@lang('fixed_asset.fixed_asset_types')
        <small>@lang('fixed_asset.manage_your_fixed_asset_types')</small>
    </h1>
</section>

<!-- Main content -->
<section class="content">

	<div class="box">
        <div class="box-header">
        	<h3 class="box-title">@lang('fixed_asset.all_your_fixed_asset_types')</h3>
            @can('fixed_asset_type.create')
            	<div class="box-tools">
                    <a class="btn btn-block btn-primary add-fixed-asset-type"
                        href="{{action('FixedAssetTypeController@create')}}">
                        <i class="fa fa-plus"></i> @lang('messages.add')</a>
                </div>
            @endcan
        </div>
        <div class="box-body">
            @can('fixed_asset_type.view')
                <div class="table-responsive">
            	<table class="table table-bordered table-striped" style="width: 99.99%;" id="fixed_asset_types_table">
            		<thead>
            			<tr>
                            <th>@lang('fixed_asset.name')</th>
                            <th>@lang('fixed_asset.description')</th>
                            <th>@lang('fixed_asset.percentage')</th>
                            <th>@lang('accounting.accounting_account')</th>
                            <th>@lang('messages.action')</th>
            			</tr>
            		</thead>
            	</table>
                </div>
            @endcan
        </div>
    </div>

    <div class="modal fade fixed_asset_types_modal" tabindex="-1" role="dialog" 
    	aria-labelledby="gridSystemModalLabel">
    </div>

</section>
<!-- /.content -->
@endsection
@section('javascript')
    <script src="{{ 'js/fixed_asset.js?v='. $asset_v }}"></script>
@endsection
