{!! Form::open(['method' => 'post', 'id' => 'form_add_bonus_calculation' ]) !!}
<div class="modal-header">
  <h4 class="modal-title" id="formModal">@lang('planilla.bonus_calculations_table')
    <button type="button" class="close" data-dismiss="modal" aria-label="Close" onClick="closeModal()">
      <span aria-hidden="true">&times;</span>
    </button>
  </h4>
</div>

<div class="modal-body">
  <div class="row">

    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
      <div class="form-group">
        <label>@lang('planilla.from_year')</label> <span class="text-danger">*</span>
        {!! Form::number("from", null, ['class' => 'form-control form-control-sm', 'placeholder' => __('planilla.from'), 
        'id' => 'from', 'required'])!!}
      </div>
    </div>

    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
      <div class="form-group">
        <label>@lang('planilla.until_year')</label>
        {!! Form::number("until", null, ['class' => 'form-control form-control-sm', 'placeholder' => __('planilla.until'), 
        'id' => 'until'])!!}
      </div>
    </div>

    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
      <div class="form-group">
        <label>@lang('planilla.days')</label> <span class="text-danger">*</span>
        {!! Form::number("days", null, ['class' => 'form-control form-control-sm', 'placeholder' => __('planilla.days'), 
        'id' => 'days', 'required'])!!}
      </div>
    </div>

	<div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
		<div class="form-group">
		  <label>@lang('planilla.percentage')</label> <span class="text-danger">*</span>
		  {!! Form::number("percentage", null, ['class' => 'form-control form-control-sm', 'placeholder' => __('planilla.percentage'), 
		  'id' => 'percentage', 'required'])!!}
		</div>
	  </div>

  </div>
</div>
<div class="modal-footer">
  <input type="hidden" name="_token" value="{{ csrf_token() }}" id="token">
  <button type="button" class="btn btn-primary" id="btn_add_bonus_calculation">@lang('planilla.add')</button>
  <button type="button" class="btn btn-danger" data-dismiss="modal" onClick="closeModal()">@lang( 'messages.cancel'
    )</button>
</div>
{!! Form::close() !!}
<script>
  $( document ).ready(function() {
		$.fn.modal.Constructor.prototype.enforceFocus = function() {};
		$('.select2').select2();
	});


	$("#btn_add_bonus_calculation").click(function() {
		route = "/bonus-calculation";    
		token = $("#token").val();

		var form = $("#form_add_bonus_calculation");
		var formData = new FormData(form[0]);
		console.log(formData);
		$.ajax({
			url: route,
			headers: {'X-CSRF-TOKEN': token},
			type: 'POST',
			processData: false,
			contentType: false,       
			data: formData,
			success:function(result) {
				if(result.success == true) {
					Swal.fire
					({
						title: result.msg,
						icon: "success",
						timer: 1000,
						showConfirmButton: false,
					});
					$("#bonus-calculation-table").DataTable().ajax.reload(null, false);
					$('#modal_add').modal( 'hide' ).data( 'bs.modal', null );
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
					title: "@lang('planilla.error_list')",
					icon: "error",
					html: "<ul>"+ errormessages+ "</ul>",
				});
			}
		});
  });

	function closeModal(){
		$('#modal_add').modal( 'hide' ).data( 'bs.modal', null );
	}
</script>