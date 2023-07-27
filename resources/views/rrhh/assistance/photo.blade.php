<div class="modal-header">
    <h4 class="modal-title" id="formModal">@lang('rrhh.photo') | <span style="color: gray">{{ $employee->first_name }}
        {{ $employee->last_name }}</span>
		<button type="button" class="close" data-dismiss="modal" aria-label="Close" onClick="closeModal()">
			<span aria-hidden="true">&times;</span>
		</button>
	</h4>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12">
            <img id="photo-{{ $idAssistance }}" width="100%" height="100%" class="img-responsive">
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        loadImage();
    });

    function loadImage(){
        var idAssistance = {!! json_encode($idAssistance) !!};
        var routeApi = {!! json_encode($routeApi) !!};
        let response1 = fetch(routeApi+""+idAssistance)
        .then(response => {
            const codes = response.url;
            if (response.status === 200) {
                const imageBlob = response.url
                const imageObjectURL = imageBlob;
                const image = document.getElementById("photo-"+idAssistance);
                image.src = imageObjectURL;
            }
            else {
                console.log("HTTP-Error: " + response.status)
            }
        });
    }

    function closeModal(){
		$('#assistance_modal').modal({backdrop: 'static'});
		$('#modal_photo').modal( 'hide' ).data( 'bs.modal', null );
	}
</script>