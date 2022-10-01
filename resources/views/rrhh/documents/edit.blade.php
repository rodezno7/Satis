<div class="modal-header">
	<h3 class="modal-title" id="formModal">@lang('rrhh.edit') @lang('rrhh.document')</h3>
	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
		<span aria-hidden="true">&times;</span>
	</button>
</div>
<div class="modal-body">
	<form id="form_edit" method="post">


		
		<div class="form-group">
			<label>@lang('rrhh.document_type')</label>

			{!! Form::select("document_type_id", $types, $document->document_type_id,
			['id' => 'document_type_id', 'class' => 'form-control form-control-sm select2', 'placeholder' => __('rrhh.document_type'), 'style' => 'width: 100%;']) !!}

			<input type="hidden" name="_token" value="{{ csrf_token() }}" id="token">			
		</div>
		
		<div class="form-group">
			<label>@lang('rrhh.state_expedition')</label>

			{!! Form::select("state_id_document", $states, $document->state_id,
			['id' => 'state_id_document', 'class' => 'form-control form-control-sm select2', 'placeholder' => __('rrhh.state'), 'style' => 'width: 100%;']) !!}
			
		</div>

		<div class="form-group">
			<label>@lang('rrhh.city_expedition')</label>

			{!! Form::select("city_id_document", $cities, $document->city_id,
			['id' => 'city_id_document', 'class' => 'form-control form-control-sm select2', 'placeholder' => __('rrhh.city'), 'style' => 'width: 100%;']) !!}
			
		</div>

		<div class="form-group">
			<label>@lang('rrhh.number')</label>
			<input type="text" name='number' id='number' value='{{ $document->number }}' class="form-control form-control-sm" placeholder="@lang('rrhh.number')">
			
		</div>

		<div class="form-group">
			<label>@lang('rrhh.file')</label>
			<input type="file" name="file" id='file' class="form-control form-control-sm">
		</div>

	</form>	
</div>

<div class="modal-footer">
	<button type="button" class="btn btn-primary" id="btn_edit_document">@lang('rrhh.update')</button>
</div>
<script>

	$( document ).ready(function() {
		$.fn.modal.Constructor.prototype.enforceFocus = function() {};

		select2 = $('.select2').select2();

	});

	$('#state_id_document').change(function(){
		updateCitiesD();
	});

	validExt = ['jpg', 'jpeg', 'png', 'pdf'];

	$('#file').on('change', function() {

		extension = this.files[0].type.split('/')[1];
		console.log(this.files[0].type);

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

		$("#city_id_document").empty();
		state_id = $('#state_id_document').val();

		if (state_id) {

			var route = "/cities/getCitiesByState/"+state_id;
			$.get(route, function(res){

				$("#city_id_document").append('<option value="0" disabled selected>{{ __('messages.please_select') }}</option>');           

				$(res).each(function(key,value){
					$("#city_id_document").append('<option value="'+value.id+'">'+value.name+'</option>');

				});
			});

		}

	}

	$("#btn_edit_document").click(function() {

		$("#btn_edit_document").prop("disabled", true);
		document_id = {{ $document->id }};
		route = '/rrhh-documents-updateDocument';    
		token = $("#token").val();

		number = $("#number").val();
		document_type_id = $("#document_type_id").val();
		state_id = $("#state_id_document").val();


		datastring = new FormData();

		datastring.append('number', number);
		datastring.append('file', $('input[type=file]')[1].files[0]); 
		datastring.append('document_type_id', document_type_id);
		datastring.append('state_id', state_id);

		if (($("#city_id_document").val() == '') || ($("#city_id_document").val() == null)) {
    	//
    } else {
    	city_id = $("#city_id_document").val()
    	datastring.append('city_id', city_id);

    }
    
    datastring.append('document_id', document_id);

    $.ajax({
    	url: route,
    	headers: {'X-CSRF-TOKEN': token},
    	type: 'POST',
    	processData: false,
    	contentType: false,       
    	data: datastring,
    	success:function(result) {

    		if(result.success == true) {
    			$("#btn_edit_document").prop("disabled", false);
    			Swal.fire
    			({
    				title: result.msg,
    				icon: "success",
    				timer: 2000,
    				showConfirmButton: false,
    			});
    			$('#modal_edit_document').modal('hide');
    			getDocuments();

    		}
    		else {
    			$("#btn_edit_document").prop("disabled", false);
    			Swal.fire
    			({
    				title: result.msg,
    				icon: "error",
    			});
    		}
    	},
    	error:function(msj){
    		$("#btn_edit_document").prop("disabled", false);
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


	
	

</script>