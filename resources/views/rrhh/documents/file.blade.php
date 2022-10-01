<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<div class="modal-body">

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