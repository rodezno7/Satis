@extends('layouts.app')
@section('title', 'Test print')
@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
	
</section>
<!-- Main content -->
<section class="content">
	<div class="box">
		<div class="box-body">
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">	
				{!! Form::open(['id'=>'form_print']) !!}
				<input type="hidden" name="_token" value="{{ csrf_token() }}" id="token">	
				
				<div class="row">


					



					
				</div>

				<div class="row">

					


					

					<div class="form-group float-left col-lg-3 col-md-3 col-sm-3 col-xs-12">
						<input type="button" id="button_print" class="btn btn-primary" value="@lang('accounting.generate')" style="margin-top: 20px;">
					</div>

				</div>



				


				{!! Form::close() !!}
			</div>



		</div>
	</div>


</section>
<!-- /.content -->
@endsection
@section('javascript')
<script type="text/javascript">




	$("#button_print").click(function(event) {

		$("#button_print").prop('disabled', true);
		var data = $("#form_print").serialize();
		route = "/print_pos";
		token = $("#token").val();
		$.ajax({
			url: route,
			headers: {
				'X-CSRF-TOKEN': token
			},
			type: 'POST',
			dataType: 'json',
			data: data,
			success: function(result) {
				if (result.success == true) {

					/*Swal.fire({
						title: 'Listo para imprimir',
						icon: "success",
					});*/
					/*$.ajax({
						url: 'http://127.0.0.1:9100/htbin/kp.py',
						type: 'GET',
						data: {
							p:'EPSON_L375',
							data: result.data
						},
						success:function(bytes){
							console.log(bytes)
						}
					});*/

					var rawdat = result.data;
					var xhttp = new XMLHttpRequest();

					url = 'http://127.0.0.1:9100/htbin/kp.py';
					xhttp.open("POST", url, false);
					xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

					xhttp.onreadystatechange = function() {

						if(this.readyState == 4 && this.status == 200)
						{
							//alert(this.responseText);
							Swal.fire({
								title: 'Impreso exitosamente',
								icon: "success",
								timer: 2000,
								showConfirmButton: false,
							});
						}

					}

					xhttp.send("p=EPSON TM-U950 Slip&data="+rawdat);

					$("#button_print").prop('disabled', false);

				} else {
					$("#button_print").prop('disabled', false);

					Swal.fire({
						title: result.msg,
						icon: "error",
					});
				}
			},
			error: function(msj) {

				$("#button_print").prop('disabled', false);
				var errormessages = "";
				$.each(msj.responseJSON.errors, function(i, field) {
					errormessages += "<li>" + field + "</li>";
				});
				Swal.fire({
					title: "{{ __('quote.errors') }}",
					icon: "error",
					html: "<ul>" + errormessages + "</ul>",
				});
			}
		});
	});



	

	


</script>
@endsection