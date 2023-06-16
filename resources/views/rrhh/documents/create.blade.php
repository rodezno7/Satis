{!! Form::open(['method' => 'post', 'id' => 'form_add_document','files' => true ]) !!}
<div class="modal-header">
	<h4 class="modal-title" id="formModal">@lang('rrhh.document')
		<button type="button" class="close" data-dismiss="modal" aria-label="Close" onClick="closeModal()">
			<span aria-hidden="true">&times;</span>
		</button>
	</h4>
</div>
<div class="modal-body">
		<div class="row">
			<div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
				<div class="form-group">
					<label>@lang('rrhh.document_type')</label> <span class="text-danger">*</span>
					<select name="document_type_id" id="document_type_id" class="form-control form-control-sm select2"
						placeholder="{{ __('rrhh.document_type') }}" style="width: 100%;">
						<option value="">{{ __('rrhh.document_type') }}</option>
						@foreach ($types as $type)
						<option value="{{ $type->id }}">{{ $type->value }}</option>
						@endforeach
					</select>
				</div>
			</div>
			<div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
				<div class="form-group">
					<label>@lang('rrhh.number')</label> <span class="text-danger">*</span>
					<input type="text" name='number' id='number' class="form-control form-control-sm"
						placeholder="@lang('rrhh.number')">
				</div>
			</div>
			<div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-12" style="display: none" id="div_date_expedition">
				<div class="form-group">
					<label>@lang('rrhh.date_expedition')</label> <span class="text-danger">*</span>
					{!! Form::text("date_expedition", null, ['class' => 'form-control form-control-sm', 'id' => 'date_expedition'])!!}
				</div>
			</div>
			<div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-12" style="display: none" id="div_date_expiration">
				<div class="form-group">
					<label>@lang('rrhh.date_expiration')</label> <span class="text-danger">*</span>
					{!! Form::text("date_expiration", null, ['class' => 'form-control form-control-sm', 'id' => 'date_expiration'])!!}
				</div>
			</div>
			<div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
				<div class="form-group">
					<label>@lang('rrhh.state_expedition')</label> <span class="text-danger">*</span>
					{!! Form::select("state_id", $states, null,
					['id' => 'state_id', 'class' => 'form-control form-control-sm select2', 'placeholder' =>
					__('rrhh.state'), 'style' => 'width: 100%;']) !!}

				</div>
			</div>
			<div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
				<div class="form-group">
					<label>@lang('rrhh.city_expedition')</label> <span class="text-danger">*</span>
					{!! Form::select("city_id", [], null,
					['id' => 'city_id', 'class' => 'form-control form-control-sm select2', 'placeholder' =>
					__('rrhh.city'), 'style' => 'width: 100%;']) !!}
				</div>
			</div>
			<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12">
				<div class="form-group">
					<label>@lang('rrhh.file')</label> <span class="text-danger">*</span>
					<input type="file" name="file" id='file' class="form-control form-control-sm">
				</div>
			</div>
		</div>
	
</div>

<div class="modal-footer">
	<input type="hidden" name="_token" value="{{ csrf_token() }}" id="token">
	<input type="hidden" name="date_required" id="date_required">
	<input type="hidden" name="employee_id" value="{{ $employee_id }}" id="employee_id">
	<button type="button" class="btn btn-primary" id="btn_add_document">@lang('rrhh.add')</button>
	<button type="button" class="btn btn-danger" data-dismiss="modal" onClick="closeModal()">@lang( 'messages.cancel' )</button>
</div>
{!! Form::close() !!}
<script>
	$( document ).ready(function() {
		$.fn.modal.Constructor.prototype.enforceFocus = function() {};
		select2 = $('.select2').select2();

		$('#date_expiration').datepicker({
			autoclose: true,
			format: datepicker_date_format,
		});

		$('#date_expedition').datepicker({
			autoclose: true,
			format: datepicker_date_format
		});
		updateCitiesD();
	});

	$('#state_id').change(function(){
		updateCitiesD();
	});

	validExt = ['jpg', 'jpeg', 'png', 'pdf'];

	$('#file').on('change', function() {
		extension = this.files[0].type.split('/')[1];
		//console.log(this.files[0].type);

		if(validExt.indexOf(extension) == -1){
			$('#file').val('');
			Swal.fire
			({
				title: '@lang('rrhh.only_pdf')',
				icon: "error",
			});
		} else {
			size = this.files[0].size;
			if(size > 5242880) {

				$('#file').val('');
				Swal.fire
				({
					title: '@lang('rrhh.bad_size_img')',
					icon: "error",
				});
			}
		}
	});

	function updateCitiesD() {
		$("#city_id").empty();
		state_id = $('#state_id').val();

		$('#city_id').select2({
            ajax: {
                url: "/cities/getCitiesByStateSelect2/"+state_id,
                dataType: "json",
                delay: 250,
                data: function (params) {
                    return {
                    	q: params.term, // search term
                    	page: params.page,
                    };
                },
                processResults: function (data) {
                    return {
                        results: data,
                    };
                },
            },
            minimumInputLength: 1,
            escapeMarkup: function (m) {
                return m;
            },
            templateResult: function (data) {
                if (!data.id) {
                    return data.text;
                }
                let html = data.text;
				//state_id = $('#city_id').val(data.name);
                return html;
            },
    	});	
	}


	
	$('#document_type_id').on('change', function() {
		let document_type = $(this).val();
		var types = {!! json_encode($type_documents) !!};
		
		types.forEach(function(type) {
			if (type.id == document_type) {
				if(type.date_required == 1){
					$('#div_date_expedition').show();
					$('#div_date_expiration').show();
					$("#date_expedition").prop('required', true);
					$("#date_expiration").prop('required', true);
				}else{
					$('#div_date_expedition').hide();
					$('#div_date_expiration').hide();
					$("#date_expedition").prop('required', false);
					$("#date_expiration").prop('required', false);
				}
			}
		});
	});

	$("#btn_add_document").click(function() {
		route = "/rrhh-documents";    
		token = $("#token").val();

		var form = $("#form_add_document");
		var formData = new FormData(form[0]);
		
		$.ajax({
			url: route,
			headers: {'X-CSRF-TOKEN': token},
			type: 'POST',
			processData: false,
			contentType: false,       
			data: formData,
			success:function(result) {
				if(result.success == true) {
					getDocuments($('#employee_id').val());
					Swal.fire
					({
						title: result.msg,
						icon: "success",
						timer: 1000,
						showConfirmButton: false,
					});
					$('#modal_doc').modal( 'hide' ).data( 'bs.modal', null );
				}
				else {
					Swal.fire
					({
						title: result.msg,
						icon: "error",
					});
				}
			},
			error:function(msj){
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
			}
		});
  	});

	function closeModal(){
		$('#document_modal').modal({backdrop: 'static'});
		$('#modal_doc').modal( 'hide' ).data( 'bs.modal', null );
	}
</script>