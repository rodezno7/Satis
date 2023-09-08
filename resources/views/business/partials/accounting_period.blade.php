<!--Purchase related settings -->
<div class="pos-tab-content">
	<div class="row">


		<div class="panel with-nav-tabs panel-default boxform_u box-solid_u">
			<div class="panel-heading">
				<ul class="nav nav-tabs">
					<li class="active"><a href="#tab-years" data-toggle="tab">@lang('accounting.fiscal_years')</a></li>
					<li><a href="#tab-periods" data-toggle="tab">@lang('accounting.periods')</a></li>
				</ul>
			</div>
			<div class="panel-body">
				<div class="tab-content">
					<div class="tab-pane fade in active" id="tab-years">
						<div class="boxform_u box-solid_u">
							<div class="box-header">
								<h3 class="box-title">@lang( 'accounting.all_your_years' )</h3>
								<div class="box-tools">
									<button type="button" class="btn btn-primary" data-toggle='modal' data-target='#modal-add-year' data-backdrop="static" data-keyboard="false" id="btn-new-year"><i class="fa fa-plus"></i> @lang( 'messages.add' )
									</button>
								</div>
							</div>
							<div class="box-body">
								<div class="table-responsive">
									<table class="table table-striped table-bordered table-condensed table-hover" id="years-table" width="100%">
										<thead>
											<th>@lang('accounting.name')</th>
											<th>@lang( 'messages.actions' )</th>
										</thead>
									</table>
								</div>
							</div>
						</div>
					</div>
					<div class="tab-pane fade" id="tab-periods">
						<div class="boxform_u box-solid_u">
							<div class="box-header">
								<h3 class="box-title">@lang( 'accounting.all_your_periods' )</h3>
								<div class="box-tools">
									<button type="button" class="btn btn-primary" data-toggle='modal' data-target='#modal-add-period' data-backdrop="static" data-keyboard="false" id="btn-new-period"><i class="fa fa-plus"></i> @lang( 'messages.add' )
									</button>
								</div>
							</div>
							<div class="box-body">
                                <div class="row">
                                    <div class="col-lg-3 col-md-3 col-sm-4">
                                        <div class="form-group">
                                            <label for="fiscal_year">@lang('lang_v1.year')</label>
                                            {!! Form::select(null, $fiscal_years, null, ['class' => 'form-control',
                                                'id' => 'period_fiscal_year', 'placeholder' => __('accounting.all_years')]) !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12 col-md-12 col-sm-12">
                                        <div class="table-responsive">
                                            <table class="table table-striped table-bordered table-condensed table-hover" id="periods-table" width="100%">
                                                <thead>
                                                    <th>@lang('accounting.name')</th>
                                                    <th>@lang('accounting.year')</th>
                                                    <th>@lang('accounting.month')</th>
                                                    <th>@lang('accounting.status')</th>
                                                    <th>@lang( 'messages.actions' )</th>
                                                </thead>
                                            </table>
                                        </div>
                                    </div>
                                </div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>