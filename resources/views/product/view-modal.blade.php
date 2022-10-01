<div class="modal-dialog modal-xl" role="document">
	<div class="modal-content" style="border-radius: 20px;">
	  <div class="modal-header">
		<button type="button" class="close no-print" data-dismiss="modal" aria-label="Close"><span
			aria-hidden="true">&times;</span></button>
		<h4 class="modal-title" id="modalTitle">{{ $product->name }}</h4>
	  </div>
  
	  <div class="modal-body">
		<div class="row">
		  <div class="col-sm-3 col-md-3 invoice-col">
			<div class="thumbnail">
			  <img src="{{ $product->image_url }}" alt="Product image">
			</div>
		  </div>
  
		  <div class="col-sm-9">
			<div class="nav-tabs-custom">
			  <ul class="nav nav-tabs">
				<li class="active"><a href="#general" data-toggle="tab">@lang('product.general')</a></li>
				@can("product.view_cost")
					<li><a href="#cost" data-toggle="tab">@lang('product.cost')</a></li>
				@endcan
				<li><a href="#stock" data-toggle="tab">@lang('product.stock2')</a></li>
				<li><a href="#sale_price" onclick="getPrices()" data-toggle="tab">@lang('product.sale_price_scale')</a></li>
				<li><a href="#price_list" data-toggle="tab">@lang('product.price_list')</a></li>
			  </ul>
  
			  <div class="tab-content">
				<div class="active tab-pane" id="general">
				  @include('product.partials.tab_general')
				</div>
  
				@can("product.view_cost")
					<div class="tab-pane" id="cost">
					@if ($product->type == 'single')
						@include('product.partials.single_product_details')
					@else
						@include('product.partials.variable_product_details')
					@endif
					@include('product.partials.last_purchases')
					</div>
				@endcan
				<div class="tab-pane" id="stock">
				  @include('product.partials.tab_stock')
				</div>
  
				<div class="tab-pane" id="sale_price">
				  @include('product.partials.tab_sale_price')
				</div>
  
				<div class="tab-pane" id="price_list">
				  @include('product.partials.tab_price_list')
				</div>
			  </div>
			</div>
			<!-- /.nav-tabs-custom -->
		  </div>
		</div>
	  </div>
  
	  <div class="modal-footer">
		{{--
		<button type="button" class="btn btn-primary no-print" aria-label="Print"
		  onclick="$(this).closest('div.modal').printThis();">
		  <i class="fa fa-print"></i> @lang( 'messages.print' )
		</button>
		--}}
		{{--
		<a href="javascript:printDiv('product_detail_print')" class="btn btn-primary no-print" aria-label="Print">
		  <i class="fa fa-print"></i> @lang('messages.print')
		</a>
		--}}
  
		<button type="button" class="btn btn-default no-print" data-dismiss="modal">@lang('messages.close')</button>
	  </div>
	</div>
  </div>
  
  {{--
  @include('product.partials.product_detail_print')
  --}}
  
  <script type="text/javascript">
	$(document).ready(function() {
	  var element = $('div.modal-xl');
	  __currency_convert_recursively(element);
	});
  </script>
  
  <script>
	function printDiv(idp) {
	  //$(this).closest('div.modal');
	  var detail = document.getElementById(idp);
	  var win = window.open(' ', 'pop_print');
	  win.document.write(detail.innerHTML);
	  win.document.close();
	  win.print();
	  win.close();
	}
  
	// Datatable of sale price tab
	function getPrices() {
	  if ($.fn.dataTable.isDataTable('#prices_table')) {
		table = $('#prices_table').DataTable();
	  }
	  else {
		@if ($product->type == 'single')
		col_targets = [3];
		@else
		col_targets = [4];
		@endif
		var prices_table = $('#prices_table').DataTable({
		  processing: true,
		  serverSide: true,
		  pageLength: 5,
		  searching: false,
		  rowId: 'id',
		  fixedHeader: false,
		  ajax: '/get_sale_price_scale/{{  $product->id }}',
		  columnDefs: [
			{
			  'targets': col_targets,
			  'orderable': false,
			  'searchable': false
			},
			{
			  'targets': '_all',
			  'className': 'text-center'
			}
		  ],
		  columns: [
			@if ($product->type != 'single')
			{ data: 'name', name: 'v.name' },
			@endif
			{ data: 'from', name: 'sale_price_scales.from' },
			{ data: 'to', name: 'sale_price_scales.to' },
			{ data: 'price', name: 'sale_price_scales.price' },
			{ data: 'action', name: 'action' }
		  ]
		});
	  }
	}
  </script>
  