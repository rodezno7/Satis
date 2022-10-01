function AddDiscount() {
 		var discount =  document.getElementById("value_").value;
		var data = discount;
		var form = $('#updateDiscount');
		var url = form.attr('action');
		var data = form.serialize();


		$.post(url,data,function(result){
				toastr.success('Descuento Actualizado');
				location.reload();
		}).fail(function(){
			toastr.success('Error al actualizar');
		});
}