<div class="modal-header">
    <button type="button" class="close no-print" data-dismiss="modal" aria-label="Close"><span
            aria-hidden="true">&times;</span></button>
    <h4 class="modal-title">@lang('rrhh.assistance_detail')</h4>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            @lang('rrhh.employee'): <span style="color: gray">{{ $employee->first_name }}
                {{ $employee->last_name }}</span>
            <br>
            <br>
            <table class="table table-responsive table-condensed table-text-center" style="font-size: inherit;"
                id="documents-table">
                <thead>
                    <tr class="active">
                        <th width="25%">{{ __('rrhh.photo') }}</th>
                        <th>{{ __('rrhh.date') }}</th>
                        <th>Ip</th>
                        <th width="30%">{{ __('rrhh.location') }}</th>
                        <th>{{ __('rrhh.type') }}</th>
                    </tr>
                </thead>
                <tbody id="referencesItems">
                    @if (count($assistances) > 0)
                        @foreach ($assistances as $key => $item)
                            <tr>
                                <td class="text-center">
                                    <img alt="" id="imagen-{{ $assistancesIds[$key]->id }}" width="100%"
                                        height="100%" onClick="viewFile({{ $item->id }})">
                                </td>
                                <td>
                                    {{ @format_date($item->date) }} {{ @format_time($item->time) }}
                                </td>
                                <td>
                                    {{ $item->ip }}
                                </td>
                                <td>
                                    <b>{{ __('rrhh.country') }}:</b> {{ $item->country }} <br>
                                    <b>{{ __('rrhh.city') }}:</b> {{ $item->city }} <br>
                                    <b>{{ __('rrhh.latitude') }}:</b> {{ $item->latitude }} <br>
                                    <b>{{ __('rrhh.longitude') }}:</b> {{ $item->longitude }}
                                </td>
                                <td>
                                    {{ $item->type }}
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="4" class="text-center">@lang('lang_v1.no_records')</td>
                        </tr>
                    @endif
                </tbody>
            </table>
            <input type="hidden" name="_employee_id" value="{{ $employee->id }}" id="_employee_id">
            <input type="hidden" name="_token" value="{{ csrf_token() }}" id="token">
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        loadImage();
    });

    function loadImage() {
        var assistances = {!! json_encode($assistancesIds) !!};
        var routeApi = {!! json_encode($routeApi) !!};
        assistances.forEach(function(assistance) {
            let response1 = fetch(routeApi + "" + assistance.id)
                .then(response => {
                    const codes = response.url;
                    if (response.status === 200) {
                        const imageBlob = response.url
                        const imageObjectURL = imageBlob;
                        const image = document.getElementById("imagen-" + assistance.id);
                        image.src = imageObjectURL;
                    } else {
                        console.log("HTTP-Error: " + response.status)
                    }
                });
        });
    }

    function viewFile(id) {
        $("#modal_content_photo").html('');
        var url = "{!! URL::to('/rrhh-assistances-viewImage/:id') !!}";
        url = url.replace(':id', id);
        $.get(url, function(data) {
            $("#modal_content_photo").html(data);
            $('#modal_photo').modal({
                backdrop: 'static'
            });
        });

        $('#assistance_modal').modal( 'hide' ).data( 'bs.modal', null );
    }
</script>
