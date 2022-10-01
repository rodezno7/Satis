<!--Purchase related settings -->
<div class="pos-tab-content">
	<div class="row">

		<div class="col-sm-4">
			<div class="form-group">
				{!! Form::label('accounting_ordinary_incomes_id', __('accounting.result_income_ordinary') . ':') !!}
				<div class="input-group">
					<span class="input-group-addon">
						<i class="fa fa-money"></i>
					</span>
					{!! Form::select('accounting_ordinary_incomes_id', $business_accounts, $business->accounting_ordinary_incomes_id,
						['class' => 'form-control select_account', 'style' => 'width: 270px;', 'placeholder' => __('accounting.result_income_ordinary')]); !!}
				</div>
			</div>
		</div>
		<div class="col-sm-4">
			<div class="form-group">
				{!! Form::label('accounting_return_sells_id', __('accounting.result_return_sells') . ':') !!}
				<div class="input-group">
					<span class="input-group-addon">
						<i class="fa fa-money"></i>
					</span>
					{!! Form::select('accounting_return_sells_id', $business_accounts, $business->accounting_return_sells_id,
						['class' => 'form-control select_account', 'style' => 'width: 270px;', 'placeholder' => __('accounting.result_return_sells')]); !!}
				</div>
			</div>
		</div>

		<div class="col-sm-4">
			<div class="form-group">
				{!! Form::label('accounting_sells_cost_id', __('accounting.result_cost') . ':') !!}
				<div class="input-group">
					<span class="input-group-addon">
						<i class="fa fa-money"></i>
					</span>
					{!! Form::select('accounting_sells_cost_id', $business_accounts, $business->accounting_sells_cost_id,
						['class' => 'form-control select_account', 'style' => 'width: 270px;', 'placeholder' => __('accounting.result_cost')]); !!}
				</div>
			</div>
		</div>


	</div>



	<div class="row">

		<div class="col-sm-4">
			<div class="form-group">
				{!! Form::label('accounting_ordinary_expenses_id', __('accounting.result_expenses_ordinary') . ':') !!}
				<div class="input-group">
					<span class="input-group-addon">
						<i class="fa fa-money"></i>
					</span>
					{!! Form::select('accounting_ordinary_expenses_id', $business_accounts, $business->accounting_ordinary_expenses_id,
						['class' => 'form-control select_account', 'style' => 'width: 270px;', 'placeholder' => __('accounting.result_expenses_ordinary')]); !!}
				</div>
			</div>
		</div>
		<div class="col-sm-4">
			<div class="form-group">
				{!! Form::label('accounting_extra_incomes_id', __('accounting.result_income_no_ordinary') . ':') !!}
				<div class="input-group">
					<span class="input-group-addon">
						<i class="fa fa-money"></i>
					</span>
					{!! Form::select('accounting_extra_incomes_id', $business_accounts, $business->accounting_extra_incomes_id,
						['class' => 'form-control select_account', 'style' => 'width: 270px;', 'placeholder' => __('accounting.result_income_no_ordinary')]); !!}
				</div>
			</div>
		</div>

		<div class="col-sm-4">
			<div class="form-group">
				{!! Form::label('accounting_extra_expenses_id', __('accounting.result_expenses_no_ordinary') . ':') !!}
				<div class="input-group">
					<span class="input-group-addon">
						<i class="fa fa-money"></i>
					</span>
					{!! Form::select('accounting_extra_expenses_id', $business_accounts, $business->accounting_extra_expenses_id,
						['class' => 'form-control select_account', 'style' => 'width: 270px;', 'placeholder' => __('accounting.result_expenses_no_ordinary')]); !!}
				</div>
			</div>
		</div>


	</div>

	<div class="row">

		<div class="col-sm-6">
			<div class="form-group">
				<label>@lang('accounting.level_childrens_ordynary_incomes')</label>
				<input type="number" step="1" class="form-control" name="level_childrens_ordynary_incomes" id="level_childrens_ordynary_incomes" value="{{$business->level_childrens_ordynary_incomes}}">
			</div>
		</div>

		<div class="col-sm-6">
			<div class="form-group">
				<label>@lang('accounting.level_childrens_ordynary_expenses')</label>
				<input type="number" step="1" class="form-control" name="level_childrens_ordynary_expenses" id="level_childrens_ordynary_expenses" value="{{$business->level_childrens_ordynary_expenses}}">
			</div>
		</div>

	</div>

	<div class="row">

		<div class="col-sm-6">
			<div class="form-group">
				<label>@lang('accounting.level_childrens_extra_incomes')</label>
				<input type="number" step="1" class="form-control" name="level_childrens_extra_incomes" id="level_childrens_extra_incomes" value="{{$business->level_childrens_extra_incomes}}">
			</div>
		</div>

		<div class="col-sm-6">
			<div class="form-group">
				<label>@lang('accounting.level_childrens_extra_expenses')</label>
				<input type="number" step="1" class="form-control" name="level_childrens_extra_expenses" id="level_childrens_extra_expenses" value="{{$business->level_childrens_extra_expenses}}">
			</div>
		</div>

	</div>


</div>