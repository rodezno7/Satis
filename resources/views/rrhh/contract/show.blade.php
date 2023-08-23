<div class="modal-header">
    <h4 class="modal-title" id="formModal">@lang('rrhh.file') |
        @if ($contract->contract_status == 'Vigente')
            <span class="badge" style="background: #449D44">{{ __('rrhh.current') }}</span>
        @else
            @if ($contract->contract_status == 'Finalizado')
                <span class="badge" style="background: #4e58b6">{{ __('rrhh.finalized') }}</span>
            @else
                <span class="badge" style="background: #C9302C">{{ __('rrhh.defeated') }}</span>
            @endif
        @endif
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" onClick="closeModal()">
            <span aria-hidden="true">&times;</span>
        </button>
    </h4>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12">
            <style>
                .pdfobject-container {
                    width: 87rem;
                    height: 49rem;
                    border: 1rem solid rgba(0, 0, 0, .1);
                }
            </style>

            <div id="pdf">

            </div>
            <script>
                PDFObject.embed("{{ asset($route) }}", "#pdf");
            </script>
        </div>
    </div>
</div>
<script>
    function closeModal() {
        $('#modal_action').modal({
            backdrop: 'static'
        });
        $('#modal_show').modal('hide').data('bs.modal', null);
    }
</script>
