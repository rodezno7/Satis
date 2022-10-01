<div class="panel panel-default">
	<div class="panel-heading">@lang('customer.general_information')
		<div class="panel-tools pull-right">
			<button type="button" class="btn btn-panel-tool"  data-toggle="collapse" data-target="#general-information-fields-box" id="btn-collapse-gi">
				<i class="fa fa-minus" id="create-icon-collapsed-gi"></i>
			</button>
		</div>
	</div>

	<div class="panel-body collapse in" id="general-information-fields-box" aria-expanded="true">
		<div class="row">

			<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
				<div class="form-group">
					<label>@lang('rrhh.name')</label>
					{!! Form::text("name", null,
					['class' => 'form-control form-control-sm', 'placeholder' => __('rrhh.name'), 'id' => 'name']) !!}
				</div>
			</div>

			<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
				<div class="form-group">
					<label>@lang('rrhh.last_name')</label>
					{!! Form::text("last_name", null,
					['class' => 'form-control form-control-sm', 'placeholder' => __('rrhh.last_name'), 'id' => 'last_name']) !!}
				</div>
			</div>

			<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
				<div class="form-group">
					<label>@lang('rrhh.gender')</label>
					{!! Form::select("gender", ['M' => __('rrhh.male'), 'F' => __('rrhh.female')], null,
					['class' => 'form-control form-control-sm select2', 'placeholder' => __('rrhh.gender'), 'style' => 'width: 100%;']) !!}
				</div>
			</div>

			<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
				<div class="form-group">
					<label>@lang('rrhh.nationality')</label>
					{!! Form::select("nationality_id", $nationalities, null,
					['class' => 'form-control form-control-sm select2', 'placeholder' => __('rrhh.nationality'), 'style' => 'width: 100%;']) !!}
				</div>
			</div>

			<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
				<div class="form-group">
					<label>@lang('rrhh.birthdate')</label>
					{!! Form::date("birthdate", null,
					['class' => 'form-control form-control-sm', 'placeholder' => __('rrhh.birthdate'), 'id' => 'birthdate']) !!}
				</div>
			</div>

			<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
				<div class="form-group">
					<label>@lang('rrhh.dni')</label>
					{!! Form::text("dni", null,
					['class' => 'form-control form-control-sm', 'placeholder' => '00000000-0', 'id' => 'dni']) !!}
				</div>
			</div>

			<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
				<div class="form-group">
					<label>@lang('rrhh.tax_number')</label>
					{!! Form::text("tax_number", null,
					['class' => 'form-control form-control-sm', 'placeholder' => '0000-000000-000-0', 'id' => 'tax_number']) !!}
				</div>
			</div>

			<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
				<div class="form-group">
					<label>@lang('rrhh.social_security_number')</label>
					{!! Form::text("social_security_number", null,
					['class' => 'form-control form-control-sm', 'placeholder' => __('rrhh.social_security_number'), 'id' => 'social_security_number']) !!}
				</div>
			</div>

			<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
				<div class="form-group">
					<label>@lang('rrhh.afp')</label>
					{!! Form::select("afp_id", $afps, null,
					['id' => 'afp_id', 'class' => 'form-control form-control-sm select2', 'placeholder' => __('rrhh.afp'), 'style' => 'width: 100%;']) !!}
				</div>
			</div>

			<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
				<div class="form-group">
					<label>@lang('rrhh.afp_number')</label>
					{!! Form::text("afp_number", null,
					['class' => 'form-control form-control-sm', 'placeholder' => __('rrhh.afp_number'), 'id' => 'afp_number']) !!}
				</div>
			</div>

			<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
				<div class="form-group">
					<label>@lang('rrhh.marital_status')</label>
					{!! Form::select("civil_status_id", $civil_statuses, null,
					['class' => 'form-control form-control-sm select2', 'placeholder' => __('rrhh.marital_status'), 'style' => 'width: 100%;']) !!}
				</div>
			</div>

			<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
				<div class="form-group">
					<label>@lang('rrhh.phone')</label>
					{!! Form::text("phone", null,
					['class' => 'form-control form-control-sm', 'placeholder' => '0000-0000', 'id' => 'phone']) !!}
				</div>
			</div>

			<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
				<div class="form-group">
					<label>Whatsapp</label>
					{!! Form::text("whatsapp", null,
					['class' => 'form-control form-control-sm', 'placeholder' => '0000-0000', 'id' => 'whatsapp']) !!}
				</div>
			</div>

			<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
				<div class="form-group">
					<label>@lang('rrhh.email')</label>
					@show_tooltip(__('rrhh.tooltip_email'))

					{!! Form::email("email", null,
					['class' => 'form-control form-control-sm', 'placeholder' => __('rrhh.email'), 'id' => 'email']) !!}
				</div>
			</div>

			

			<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
				<div class="form-group">
					<label>@lang('rrhh.address')</label>
					{!! Form::text("address", null,
					['class' => 'form-control form-control-sm', 'placeholder' => __('rrhh.address'), 'id' => 'address']) !!}
				</div>
			</div>

			<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
				<div class="form-group">
					<label>@lang('rrhh.country')</label>
					{!! Form::select("country_id", $countries, null,
					['id' => 'country_id', 'class' => 'form-control form-control-sm select2', 'placeholder' => __('rrhh.country'), 'style' => 'width: 100%;']) !!}
				</div>
			</div>

			<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
				<div class="form-group">
					<label>@lang('rrhh.state')</label>
					{!! Form::select("state_id", $states, null,
					['id' => 'state_id', 'class' => 'form-control form-control-sm select2', 'placeholder' => __('rrhh.state'), 'style' => 'width: 100%;']) !!}
				</div>
			</div>

			<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
				<div class="form-group">
					<label>@lang('rrhh.city')</label>

					

					{!! Form::select("city_id", $cities, null,
					['id' => 'city_id', 'class' => 'form-control form-control-sm select2', 'placeholder' => __('rrhh.city'), 'style' => 'width: 100%;']) !!}

					

				</div>
			</div>

			<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
				<div class="form-group">
					<label>@lang('rrhh.profession_occupation')</label>
					{!! Form::select("profession_id", $professions, null,
					['id' => 'profession_id', 'class' => 'form-control form-control-sm select2', 'placeholder' => __('rrhh.profession_occupation'), 'style' => 'width: 100%;']) !!}
				</div>
			</div>

			<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
				<div class="form-group">
					<label>@lang('rrhh.date_admission')</label>
					{!! Form::date("date_admission", null,
					['class' => 'form-control form-control-sm', 'placeholder' => __('rrhh.date_admission'), 'id' => 'date_admission']) !!}
				</div>
			</div>

			<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
				<div class="form-group">
					<label>@lang('rrhh.department')</label>
					{!! Form::select("department_id", $departments, null,
					['id' => 'department_id', 'class' => 'form-control form-control-sm select2', 'placeholder' => __('rrhh.department'), 'style' => 'width: 100%;']) !!}
				</div>
			</div>

			<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
				<div class="form-group">
					<label>@lang('rrhh.position')</label>
					{!! Form::select("position_id", $positions, null,
					['id' => 'position_id', 'class' => 'form-control form-control-sm select2', 'placeholder' => __('rrhh.position'), 'style' => 'width: 100%;']) !!}
				</div>
			</div>

			<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
				<div class="form-group">
					<label>@lang('rrhh.salary')</label>
					{!! Form::number("salary", null,
					['class' => 'form-control form-control-sm', 'placeholder' => __('rrhh.salary'), 'id' => 'salary', 'step' => '0.01', 'min' => '0.01']) !!}
				</div>
			</div>

			<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
				<div class="form-group">
					<label>@lang('rrhh.type')</label>
					{!! Form::select("type_id", $types, null,
					['id' => 'type_id', 'class' => 'form-control form-control-sm select2', 'placeholder' => __('rrhh.type'), 'style' => 'width: 100%;']) !!}
				</div>
			</div>

			<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
				<div class="form-group">
					<label>@lang('rrhh.way_to_pay')</label>
					{!! Form::select("payment_id", $payments, null,
					['id' => 'payment_id', 'class' => 'form-control form-control-sm select2', 'placeholder' => __('rrhh.way_to_pay'), 'style' => 'width: 100%;']) !!}
				</div>
			</div>

			<div id='bank_information'>

				<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
					<div class="form-group">
						<label>@lang('rrhh.bank')</label>
						{!! Form::select("bank_id", $banks, null,
						['id' => 'bank_id', 'class' => 'form-control form-control-sm select2', 'placeholder' => __('rrhh.bank'), 'style' => 'width: 100%;']) !!}
					</div>
				</div>

				<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
					<div class="form-group">
						<label>@lang('rrhh.bank_account')</label>
						{!! Form::number("bank_account", null,
						['class' => 'form-control form-control-sm', 'placeholder' => __('rrhh.bank_account'), 'id' => 'bank_account']) !!}
					</div>
				</div>
			</div>

		</div>

		<div class="row">

			<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
				<div class="form-group">
					<label>@lang('rrhh.img')</label>
					@show_tooltip(__('rrhh.tooltip_img'))

					<input type="file" id="img" name="img" class="form-control form-control-sm">
					<progress id='progress' value='0' max='100' style="width: 100%; display: none;"></progress>
				</div>
			</div>

			<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12" id='div_photo'>

			</div>

		</div>

		<div class="row">

			<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
				<div class="form-group">
					<div class="checkbox" style="margin-top: 0;">
						<label>
							{!! Form::checkbox('status', 1, $employee->status, ['id' => 'status']) !!}
							<strong>@lang('rrhh.status')</strong>
							
						</label>
					</div>
				</div>
			</div>   

		</div>

	</div>
</div>


<div class="panel panel-default">
	<div class="panel-heading">@lang('rrhh.documents')
		<div class="panel-tools pull-right">
			<button type="button" class="btn btn-panel-tool"  data-toggle="collapse" data-target="#documents-information-fields-box" id="btn-collapse-fi">
				<i class="fa fa-plus" id="create-icon-collapsed-fi"></i>
			</button>
		</div>
	</div>

	<div class="panel-body collapse" id="documents-information-fields-box" aria-expanded="false">
		<div class="row">

			
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				<div class="form-group">
					
					<button
					type="button"					
					class="btn btn-info btm-sm"
					id = 'btn_add_documents'
					style="padding: 5px 8px; margin-right: 5px; margin-top: -2px;">
					<i class="fa fa-plus"></i>
					@lang('rrhh.add_document')
				</button>

				<div class="row" id='div_documents' style="margin-top: 10px;">

				</div>
				
				
				
			</div>
		</div>




	</div>

</div>
</div>





<script>

	function updateCities() {

		$("#city_id").empty();
		state_id = $('#state_id').val();

		if (state_id) {

			var route = "/cities/getCitiesByState/"+state_id;
			$.get(route, function(res){

				$("#city_id").append('<option value="0" disabled selected>{{ __('messages.please_select') }}</option>');           

				$(res).each(function(key,value){
					$("#city_id").append('<option value="'+value.id+'">'+value.name+'</option>');

				});
			});

		}

	}

	$('#state_id').change(function(){
		updateCities();
	});

	$('#country_id').change(function(){
		updateStates();
	});


	function updateStates() {

		$("#state_id").empty();
		$("#city_id").empty();
		country_id = $('#country_id').val();

		var route = "/states/getStatesByCountry/"+country_id;
		$.get(route, function(res){
			$("#state_id").append('<option value="0" disabled selected>{{ __('messages.please_select') }}</option>');

			$(res).each(function(key,value){
				$("#state_id").append('<option value="'+value.id+'">'+value.name+'</option>');

			});
		});
	}

	function showBankInformation() {

		selected_option = $( "#payment_id option:selected" ).text();

		if (selected_option == 'Transferencia bancaria') {
			$('#bank_information').show();
		} else {
			$('#bank_information').hide();
			$('#bank_id').val('').change();
			$('#bank_account').val('');
		}
	}

	$('#payment_id').change(function() {
		showBankInformation();
	});

	validExt = ['jpg', 'jpeg', 'png'];

	$('#img').on('change', function() {

		extension = this.files[0].type.split('/')[1];
		console.log(this.files[0].type);

		if(validExt.indexOf(extension) == -1){

			$('#img').val('');
			Swal.fire
			({
				title: '@lang('rrhh.only_jpg')',
				icon: "error",
			});

		} else {

			size = this.files[0].size;
			if(size > 5242880) {

				$('#img').val('');
				Swal.fire
				({
					title: '@lang('rrhh.bad_size_img')',
					icon: "error",
				});


			} else {
				id = {{ $employee->id }}
				route = "/rrhh-employees-uploadPhoto";
				token = $("#token").val();

				datastring = new FormData();
				datastring.append('employee_id', id);
				datastring.append('img', $('input[type=file]')[0].files[0]);
				$("#div_load").show();
				$("#progress").show();

				var jqxhr = $.ajax({
					url: route,
					headers: {'X-CSRF-TOKEN': token},
					type: 'POST',
					processData: false,
					contentType: false,
					data: datastring,
					success:function(result){
						if(result.success == true) {

							$("#div_load").hide();
							$("#progress").hide();
							$progress.value = 0;
							Swal.fire
							({
								title: result.msg,
								icon: "success",
								timer: 2000,
								showConfirmButton: false,
							});

							$('#img').val('');
							getPhoto();

						} else {

							$("#div_load").hide();
							$("#progress").hide();
							$progress.value = 0;							
						}
					},
					error:function(msj){
						$("#div_load").hide();
						$("#progress").hide();

						$progress.value = 0;
						errormessages = "";
						$.each(msj.responseJSON.errors, function(i, field){
							errormessages+="<li>"+field+"</li>";
						});
						Swal.fire
						({
							title: "@lang('rrhh.error_list')",
							icon: "error",
							html: "<ul>"+ errormessages+ "</ul>",
						});
					},

					progress: downloadProgress,
					uploadProgress: uploadProgress
				});

				$(jqxhr).on('uploadProgress', uploadProgress);
			}
		}


	});


	var $progress = $('#progress')[0];

	function uploadProgress(e) {

		if (e.lengthComputable) {
			var percentComplete = (e.loaded * 100) / e.total;
			console.log(percentComplete);
			$progress.value = percentComplete;

			if (percentComplete >= 100) {

			}
		}
	}

	function downloadProgress(e) {
		if (e.lengthComputable) {
			var percentage = (e.loaded * 100) / e.total;
			console.log(percentage);
			$progress.value = percentage;

			if (percentage >= 100) {

			}
		}
	}

	


</script>