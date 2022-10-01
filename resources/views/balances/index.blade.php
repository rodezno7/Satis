@extends('layouts.app')
@section('title', __('accounting.tittle_balances'))
@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
	<h1>@lang('accounting.balances_er_menu')
	</h1>
    <!-- <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
        <li class="active">Here</li>
    </ol> -->
</section>
<!-- Main content -->
<section class="content">
	<div class="box">
		<div class="box-body">
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">	
				{!! Form::open(['id'=>'form_balance', 'action' => 'ReporterController@getBalanceReport', 'method' => 'post', 'target' => '_blank']) !!}
				<input type="hidden" name="_token" value="{{ csrf_token() }}" id="token">
				<div class="row">
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
						<h4><strong>@lang('accounting.general_balance')</strong></h4>
					</div>
				</div>
				<div class="row">
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="display: none;">
						<div id="msj-errors" class="alert alert-danger alert-dismissible" role="alert">
							<strong id="msj"></strong>
						</div>
					</div>
				</div>
				<div class="row">					
					<div class="form-group float-left col-lg-4 col-md-4 col-sm-4 col-xs-12">				
						<label for="account">@lang('accounting.to')</label>
						<div class="wrap-inputform">
							<span class="input-group-addon">
								<i class="fa fa-calendar"></i>
							</span>
							{!! Form::date('to', \Carbon\Carbon::now()->format('Y-m-d'), ['name'=>'to', 'id'=>'to', 'class'=>'inputform2']) !!}
						</div>
					</div>
					<div class="left col-lg-2 col-md-2 col-sm-2 col-xs-12">
						<div class="form-group">
							<div class="checkbox">
								<label>
									{!! Form::checkbox('enable_foot_page', 1, null , 
									[ 'class' => 'input-icheck', 'id' => 'enable_foot_page']); !!} {{ __( 'accounting.enable_auditor_signature' ) }}
								</label>
							</div>
						</div>
					</div>
					<div class="left col-lg-2 col-md-2 col-sm-2 col-xs-12" style="margin-top: 18px;">
						<button type="button" class="btn btn-success" id="btn-edit-foot" data-toggle='modal' data-target='#modal-edit-foot'>@lang('accounting.edit_foot')</button>

					</div>
				</div>
				<div class="row">
					<div class="left col-lg-2 col-md-2 col-sm-2 col-xs-12">
						<label>@lang('accounting.format')</label>
						<select name="report-type-balance" id="report-type-balance" class="form-control select2">
							<option value="pdf" selected>PDF</option>
							<option value="excel">Excel</option>
						</select>						
					</div>

					<div class="left col-lg-3 col-md-3 col-sm-3 col-xs-12">
						<label>@lang('accounting.size_font')</label>
						<select name="size_general" id="size_general" class="form-control select2">
							<option value="7">7</option>
							<option value="8">8</option>
							<option value="9" selected>9</option>
							<option value="10">10</option>
						</select>						
					</div>


				</div>
				<div class="row">
					<div class="form-group float-left col-lg-4 col-md-4 col-sm-4 col-xs-12">
						<input type="submit" class="btn btn-primary" value="@lang('accounting.generate')" style="margin-top: 10px;">
					</div>
				</div>
				
				
				{!! Form::close() !!}
			</div>
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">	
				{!! Form::open(['id'=>'form_comprobation', 'action' => 'ReporterController@getBalanceComprobation', 'method' => 'post', 'target' => '_blank']) !!}
				<input type="hidden" name="_token" value="{{ csrf_token() }}" id="token">
				<div class="row">
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
						<h4><strong>@lang('accounting.comprobation_balance')</strong></h4>
					</div>
				</div>
				<div class="row">
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="display: none;">
						<div id="msj-errors" class="alert alert-danger alert-dismissible" role="alert">
							<strong id="msj"></strong>
						</div>
					</div>
				</div>
				<div class="row">

					<div class="form-group col-lg-3 col-md-3 col-sm-3 col-xs-12">
						<label>@lang('accounting.from')</label>
						<div class="wrap-inputform">
							<span class="input-group-addon">
								<i class="fa fa-calendar"></i>
							</span>
							{!! Form::date('comprobation_from', \Carbon\Carbon::now()->format('Y-m-d'), ['id'=>'from', 'class'=>'inputform2']) !!}
						</div>
					</div>

					<div class="form-group col-lg-3 col-md-3 col-sm-3 col-xs-12">				
						<label>@lang('accounting.to')</label>
						<div class="wrap-inputform">
							<span class="input-group-addon">
								<i class="fa fa-calendar"></i>
							</span>
							{!! Form::date('comprobation_to', \Carbon\Carbon::now()->format('Y-m-d'), ['id'=>'to', 'class'=>'inputform2']) !!}
						</div>
					</div>

					<div class="form-group col-lg-2 col-md-2 col-sm-2 col-xs-12">
						<label>@lang('accounting.from')</label>
						<select name="account_from" id="account_from" class="form-control select2" style="width: 100%">
							<option value="0" disabled selected>@lang('messages.please_select')</option>
							@foreach($clasifications as $account)
							<option value="{{ $account->code }}">{{ $account->code }} {{ $account->name }}</option>
							@endforeach
						</select>
					</div>

					<div class="form-group col-lg-2 col-md-2 col-sm-2 col-xs-12">
						<label>@lang('accounting.to')</label>
						<select name="account_to" id="account_to" class="form-control select2" style="width: 100%">
							<option value="0" disabled selected>@lang('messages.please_select')</option>
							@foreach($clasifications as $account)
							<option value="{{ $account->code }}">{{ $account->code }} {{ $account->name }}</option>
							@endforeach
						</select>
					</div>

					<div class="form-group col-lg-2 col-md-2 col-sm-2 col-xs-12">
						<label for="balance">@lang('accounting.level')</label>
						<input type="number" name="level" id="level" class="form-control" step="1" min="1" value="1" required>
					</div>


				</div>				
				<div class="row">
					<div class="left col-lg-2 col-md-2 col-sm-2 col-xs-12">
						<label>@lang('accounting.format')</label>
						<select name="report-type" id="report-type" class="form-control select2">
							<option value="pdf" selected>PDF</option>
							<option value="excel">Excel</option>
						</select>
					</div>
					<div class="left col-lg-3 col-md-3 col-sm-3 col-xs-12">
						<label>@lang('accounting.size_font')</label>
						<select name="size_comprobation" id="size_comprobation" class="form-control select2">
							<option value="7">7</option>
							<option value="8">8</option>
							<option value="9" selected>9</option>
							<option value="10">10</option>
						</select>						
					</div>
					<div class="left col-lg-2 col-md-2 col-sm-2 col-xs-12">
						<div class="form-group">
							<div class="checkbox">
								<label>
									{!! Form::checkbox('enable_empty_values', 1, null , 
									[ 'class' => 'input-icheck', 'id' => 'enable_empty_values']); !!} {{ __( 'accounting.enable_empty_values' ) }}
								</label>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="left col-lg-2 col-md-2 col-sm-2 col-xs-12">
						<input type="submit" class="btn btn-primary" value="@lang('accounting.generate')" id="report_comprobation" style="margin-top: 10px;">
					</div>
				</div>
				
				{!! Form::close() !!}
			</div>

			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">	
				{!! Form::open(['id'=>'form_result', 'action' => 'ReporterController@getResultStatus', 'method' => 'post', 'target' => '_blank']) !!}
				<input type="hidden" name="_token" value="{{ csrf_token() }}" id="token">
				<div class="row">
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
						<h4><strong>@lang('accounting.result_status')</strong></h4>
					</div>
				</div>
				<div class="row">

					<div class="form-group float-left col-lg-3 col-md-3 col-sm-3 col-xs-12">
						<label for="account">@lang('accounting.from')</label>
						<div class="wrap-inputform">
							<span class="input-group-addon">
								<i class="fa fa-calendar"></i>
							</span>
							{!! Form::date('from_result', \Carbon\Carbon::now()->format('Y-m-d'), ['id'=>'from_result', 'class'=>'inputform2']) !!}
						</div>
					</div>


					<div class="form-group float-left col-lg-3 col-md-3 col-sm-3 col-xs-12">
						<label for="account">@lang('accounting.to')</label>
						<div class="wrap-inputform">
							<span class="input-group-addon">
								<i class="fa fa-calendar"></i>
							</span>
							{!! Form::date('to_result', \Carbon\Carbon::now()->format('Y-m-d'), ['id'=>'to_result', 'class'=>'inputform2']) !!}
						</div>
					</div>

					<div class="form-group float-left col-lg-3 col-md-3 col-sm-3 col-xs-12">
						<label>@lang('accounting.format')</label>
						<select name="type_result" id="type_result" class="form-control select2">
							<option value="pdf" selected>PDF</option>
							<option value="excel">Excel</option>
						</select>						
					</div>

					<div class="left col-lg-3 col-md-3 col-sm-3 col-xs-12">
						<label>@lang('accounting.size_font')</label>
						<select name="size_result" id="size_result" class="form-control select2">
							<option value="7">7</option>
							<option value="8">8</option>
							<option value="9" selected>9</option>
							<option value="10">10</option>
						</select>						
					</div>
					
					
				</div>
				
				<div class="row">
					<div class="form-group float-left col-lg-4 col-md-4 col-sm-4 col-xs-12">
						<input type="submit" class="btn btn-primary" value="@lang('accounting.generate')" style="margin-top: 10px;">
					</div>
				</div>
				
				
				{!! Form::close() !!}
			</div>
		</div>
	</div>

	<div class="modal fade" id="modal-edit-foot" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered" role="document" style="width: 30%">
			<div class="modal-content" style="border-radius: 20px;">
				<div class="modal-body">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
					<div class="row">
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
							<h3>@lang('accounting.edit_foot')</h3>
							<form id="form-edit-foot">
								<div class="form-group">
									<label>@lang('accounting.owner')</label>
									<input type="text" id="legal_representative" name="legal_representative" class="form-control" placeholder="@lang('accounting.owner')...">
									<input type="hidden" name="business_id" id="business_id" value="{{ $business_id }}">
								</div>
								<div class="form-group">
									<label>@lang('accounting.accountant')</label>
									<input type="text" id="accountant" name="accountant" class="form-control" placeholder="@lang('accounting.accountant')...">
								</div>
								<div class="form-group">
									<label>@lang('accounting.auditor')</label>
									<input type="text" id="auditor" name="auditor" class="form-control" placeholder="@lang('accounting.auditor')...">
								</div>
								<div class="form-group">
									<label>@lang('accounting.inscription_number')</label>
									<input type="text" id="inscription_number_auditor" name="inscription_number_auditor" class="form-control" placeholder="@lang('accounting.inscription_number')...">
								</div>
							</form>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<input type="button" class="btn btn-primary" value="@lang('messages.save')" id="btn-edit-signatures">
					<button type="button" class="btn btn-danger" data-dismiss="modal" id="btn-close-modal-edit-foot">@lang('messages.close')</button>
				</div>
			</div>
		</div>
	</div>
</section>
<!-- /.content -->
@endsection
@section('javascript')
<script type="text/javascript">

	$("#btn-edit-foot").click(function(){
		id = $("#business_id").val();
		var route = "/balances/getSignatures/"+id;
		$.get(route, function(res){
			$('#legal_representative').val(res.legal_representative);
			$('#accountant').val(res.accountant);
			$('#auditor').val(res.auditor);
			$('#inscription_number_auditor').val(res.inscription_number_auditor);
		});
	});

	$("#btn-edit-signatures").click(function(){
		$("#btn-edit-signatures").prop("disabled", true);
		$("#btn-close-modal-edit-foot").prop("disabled", true);
		business_id = $("#business_id").val();
		legal_representative = $("#legal_representative").val();
		accountant = $("#accountant").val();
		auditor = $("#auditor").val();
		inscription_number_auditor = $("#inscription_number_auditor").val();
		route = "/balances/setSignatures";
		token = $("#token").val();
		$.ajax({
			url: route,
			headers: {'X-CSRF-TOKEN': token},
			type: 'POST',
			dataType: 'json',
			data: {
				business_id: business_id,
				legal_representative: legal_representative,
				accountant: accountant,
				auditor: auditor,
				inscription_number_auditor: inscription_number_auditor
			},
			success:function(result){
				if (result.success == true)
				{
					$("#btn-edit-signatures").prop("disabled", false);
					$("#btn-close-modal-edit-foot").prop("disabled", false);
					$('#modal-edit-foot').modal('hide');					
					Swal.fire
					({
						title: result.msg,
						icon: "success",
					});
				}
				else
				{
					$("#btn-edit-signatures").prop("disabled", false);
					$("#btn-close-modal-edit-foot").prop("disabled", false);
					Swal.fire
					({
						title: result.msg,
						icon: "error",
					});
				}

			},
			error:function(msj){
				$("#btn-edit-signatures").prop("disabled", false);
				$("#btn-close-modal-edit-foot").prop("disabled", false);
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
@endsection