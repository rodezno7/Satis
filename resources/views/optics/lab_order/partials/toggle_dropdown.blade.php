@if (auth()->user()->can('lab_order.view'))
<li>
    <a href="#" class="view-lab-order" data-lab-order-id="{{ $id }}">
        <i class="fa fa-eye"></i> @lang("messages.view")
    </a>
</li>
@endif

@if (auth()->user()->can('lab_order.print'))
<li>
    <a href="#" class="print-order" data-href="{{ action('Optics\LabOrderController@print', [$id]) }}">
        <i class="fa fa-print" aria-hidden="true"></i> @lang("messages.print")
    </a>
</li>
@endif

@if (auth()->user()->can('lab_order.update'))
<li>
    <a href="#" class="edit-lab-order" data-lab-order-id="{{ $id }}">
        <i class="fa fa-edit"></i> @lang("messages.edit")
    </a>
</li>
@endif

{{-- @if (auth()->user()->can('lab_order.delete'))
<li>
    <a href="#" onClick="deleteOrder({{ $id }})">
        <i class="fa fa-trash"></i> @lang("messages.delete")
    </a>
</li>
@endif --}}

@if (auth()->user()->can('lab_order.view') && ! empty($document))
@php
$document_name = ! empty(explode('_', $document, 2)[1]) ? explode('_', $document, 2)[1] : $document;
@endphp
<li>
    <a href="{{ url('uploads/documents/' . $document) }}" download="{{ $document_name }}">
        <i class="fa fa-download"></i> @lang("purchase.download_document")
    </a>
</li>
@endif

@if (! empty($steps) && ! $is_annulled)
<hr style="margin-top: 3px; margin-bottom: 3px;">

@foreach ($steps as $step)
@if (auth()->user()->can('status_lab_order.' . $step->step_id))
@if (! empty($step->step))
<li>
    @if ($step->step->print_order == 1)
    <a href="#" class="print-order" data-href="{{ action('Optics\LabOrderController@changeStatusAndPrint', [$id, $step->step_id]) }}">
    @elseif ($step->step->transfer_sheet == 1)
    <a href="#" class="transfer-order" data-href="{{ action('Optics\LabOrderController@changeStatusAndTransfer', [$id, $step->step_id]) }}">
    @elseif ($step->step->second_time == 1)
    <a href="#" class="copy-order" data-href="{{ action('Optics\LabOrderController@changeStatusAndCopy', [$id, $step->step_id]) }}">
    @elseif ($step->step->material_download == 1)
    <a href="#" class="edit-order" data-href="{{ action('Optics\LabOrderController@changeStatusAndEdit', [$id, $step->step_id]) }}">
    @else
    <a href="#" class="status-lab-order-change" data-order-id="{{ $id }}" data-status-id="{{ $step->step_id }}">
    @endif
        <i class="fa fa-dot-circle-o" style="color: {{ $step->step->color }}"></i> {{ $step->step->name }}
    </a>
</li>
@endif
@endif
@endforeach

@endif