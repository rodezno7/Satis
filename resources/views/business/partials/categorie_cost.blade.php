<!--Purchase related settings -->
<div class="pos-tab-content">
	<div class="row">
		<div class="panel with-nav-tabs panel-default boxform_u box-solid_u">
			<div class="panel-heading">
				<ul class="nav nav-tabs">
					<li class="active"><a href="#tab-cost" data-toggle="tab">@lang('accounting.categorie_cost')</a></li>
				</ul>
			</div>
			<div class="panel-body">

				@if(isset($cost_main_account))

				<div class="tab-content">
					<div class="tab-pane fade in active" id="tab-cost">
						<div class="boxform_u box-solid_u">
							<div class="box-header">
								<h3 class="box-title">@lang( 'category.manage_your_categories' )</h3>
							</div>
							<div class="box-body">
								<div class="table-responsive">
									<table class="table table-striped table-bordered table-condensed table-hover" id="categories-table" width="100%">
										<thead>
											<th>@lang( 'category.category' )</th>
											<th>@lang( 'accounting.account' )</th>
											<th>@lang( 'messages.action' )</th>
										</thead>
									</table>
								</div>
								<input type="hidden" id="cost_main_account" value="{{ $cost_main_account->code }}">
							</div>
						</div>
					</div>
				</div>
				@else
				<h2>@lang('accounting.define_cost_account')</h2>
				@endif
			</div>
		</div>
	</div>
</div>