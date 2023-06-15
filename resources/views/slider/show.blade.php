<div class="modal-dialog modal-xl" role="document">
    <div class="modal-content" style="border-radius: 20px;">
        <div class="modal-content" style="border-radius: 10px;">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">@lang('carrousel.uploaded_image')</h4>
            </div>
            <div class="modal-body">
                <img src="{{asset('uploads/slides/'.$path)}}" style="width: 100% !important; height: 300px !important;">
            </div>
        </div>
    </div>
</div>
