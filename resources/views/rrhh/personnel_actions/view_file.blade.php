<div class="modal-header">
    <h4 class="modal-title" id="formModal">@lang('rrhh.file') 
		<button type="button" class="close" data-dismiss="modal" aria-label="Close" onClick="closeModal()">
			<span aria-hidden="true">&times;</span>
		</button>
	</h4>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12">
            @if ($ext == 'pdf')
                <style>
                    .pdfobject-container {width: 55rem; height: 50rem; border: 1rem solid rgba(0,0,0,.1); }
                </style>

                <div id="pdf">

                </div>
                <script>
                    PDFObject.embed("{{ asset($route) }}", "#pdf");
                </script>
            @else
                <img src="{{ asset($route) }}" class="img-responsive">
            @endif
        </div>
    </div>
</div>
<script>
    function closeModal(){
		$('#modal_personnel_action').modal({backdrop: 'static'});
		$('#modal_photo').modal( 'hide' ).data( 'bs.modal', null );
	}
</script>