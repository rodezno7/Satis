{!! Form::open(['method' => 'post', 'id' => 'form_edit','files' => true ]) !!}
<div class="modal-header">
	<h4 class="modal-title" id="formModal">@lang('rrhh.edit') @lang('rrhh.document')
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
				<select name="1document_type_id" id="1document_type_id" class="form-control form-control-sm select2"
					placeholder="{{ __('rrhh.document_type') }}" style="width: 100%;" disabled>
					<option value="{{ $type->id }}">{{ $type->value }}</option>
				</select>
				
				<input type="hidden" name="document_type_id" value="{{ $type->id }}" id="document_type_id">
				<input type="hidden" name="_token" value="{{ csrf_token() }}" id="token">
			</div>
		</div>
		<div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
			<div class="form-group">
				<label>@lang('rrhh.number')</label> <span class="text-danger">*</span>
				<input type="text" name='number' id='number' value='{{ $document->number }}'
					class="form-control form-control-sm" placeholder="@lang('rrhh.number')">
			</div>
		</div>
		<div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-12"  @if ($type->date_required != 1) style="display:none" @endif>
			<div class="form-group">
				<label>@lang('rrhh.date_expedition')</label> <span class="text-danger">*</span>
				{!! Form::text("date_expedition", @format_date($document->date_expedition), ['class' => 'form-control form-control-sm', 'id' =>
				'date_expedition1'])!!}
			</div>
		</div>
		<div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-12" @if ($type->date_required != 1) style="display:none" @endif>
			<div class="form-group">
				<label>@lang('rrhh.date_expiration')</label> <span class="text-danger">*</span>
				{!! Form::text("date_expiration", @format_date($document->date_expiration), ['class' => 'form-control form-control-sm', 'id' =>
				'date_expiration1'])!!}
			</div>
		</div>
		<div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
			<div class="form-group">
				<label>@lang('rrhh.state_expedition')</label> <span class="text-danger">*</span>
				{!! Form::select("state_id", $states, $document->state_id,
				['id' => 'state_id1', 'class' => 'form-control form-control-sm select2', 'placeholder' =>
				__('rrhh.state'), 'style' => 'width: 100%;']) !!}
			</div>
		</div>
		<div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
			<div class="form-group">
				<label>@lang('rrhh.city_expedition')</label> <span class="text-danger">*</span>
				{!! Form::select("city_id", $cities, $document->city_id,
				['id' => 'city_id1', 'class' => 'form-control form-control-sm select2', 'placeholder' =>
				__('rrhh.city'), 'style' => 'width: 100%;']) !!}
			</div>
		</div>
		<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12">
			<div class="form-group">
				<label>@lang('rrhh.file')</label> <span class="text-danger">*</span>
				<input type="file" name="files[]" id='files' class="form-control form-control-sm" multiple>
			</div>
		</div>
	</div>
</div>

<div class="modal-footer">
	<input type="hidden" name="id" value="{{ $document->id }}" id="id">
	<input type="hidden" name="employee_id" value="{{ $employee_id }}" id="employee_id_doc1">
	<button type="button" class="btn btn-primary" id="btn_edit_document">@lang('rrhh.update')</button>
	<button type="button" class="btn btn-danger" data-dismiss="modal" onClick="closeModal()">@lang( 'messages.cancel')</button>
</div>
{!! Form::close() !!}
<script>
	$( document ).ready(function() {
		$.fn.modal.Constructor.prototype.enforceFocus = function() {};
		select2 = $('.select2').select2();
		//updateCitiesD();
	});

	$('#state_id1').change(function(){
		updateCitiesD();
	});

	$('#date_expiration1').datepicker({
		autoclose: true,
		format: datepicker_date_format,
	});

	$('#date_expedition1').datepicker({
		autoclose: true,
		format: datepicker_date_format
	});

	validExt = ['jpg', 'jpeg', 'png', 'pdf'];

	$('#file').on('change', function() {
		extension = this.files[0].type.split('/')[1];

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

		$("#city_id1").empty();
		state_id = $('#state_id1').val();

		$('#city_id1').select2({
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
                return html;
            },
    	});	
	}

	$("#btn_edit_document").click(function() {
		employee_id = $('#employee_id_doc1').val();
		route = '/rrhh-documents-updateDocument';    
		token = $("#token").val();

		var form = $("#form_edit");
		var formData = new FormData(form[0]);
		formData.append('employee_id', employee_id);
	
		$.ajax({
			url: route,
			headers: {'X-CSRF-TOKEN': token},
			type: 'POST',
			processData: false,
			contentType: false,       
			data: formData,
			success:function(result) {
				if(result.success == true) {
					getDocuments(employee_id);
					//$('#employee_id_doc1').val('');
					Swal.fire
						({
							title: result.msg,
							icon: "success",
							timer: 1000,
							showConfirmButton: false,
						});
					$('#modal_edit_action').modal( 'hide' ).data('bs.modal', null);
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
		$('#modal_edit_action').modal( 'hide' ).data( 'bs.modal', null );
	}
</script>