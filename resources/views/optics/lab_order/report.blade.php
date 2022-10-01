<style>
  div.report_order {
    font-family: 'Helvetica', 'Arial', sans-serif;
    color: #000000;
    font-size: 10pt;
    padding: 0;
  }
  table.report_order>tbody>tr>td,
  table.report_order>tbody>tr>th,
  table.report_order>tfoot>tr>td,
  table.report_order>tfoot>tr>th,
  table.report_order>thead>tr>td,
  table.report_order>thead>tr>th {
    padding: 4px;
  }
</style>

<div class="modal-body report_order">
  <div class="row">
    <div class="col-xs-12 text-center">
      <h2 style="margin-top: 5px; margin-bottom: 5px;">{{ Session::get('business.name') }}</h2>
    </div>

    <div class="col-xs-12">
      <table style="width: 100%;">
        <tr>
          <td style="width: 40%;">
            <b>@lang('contact.customer'):</b>
            @if (!empty($lab_order->customer_name))
              {{ $lab_order->customer_name }}
            @else
              {{ $lab_order->customer_value }}
            @endif
          </td>
          <td style="width: 30%;">
            <b>@lang('lab_order.delivery'):</b> {{ $lab_order->delivery_value }}
          </td>
          <td style="width: 30%;">
            <b>@lang('accounting.location'):</b>
            @if ($lab_order->location)
            {{ $lab_order->location }}
            @else
            {{ $lab_order->blo_name }}
            @endif
          </td>
        </tr>

        <tr>
          <td>
            <b>@lang('graduation_card.patient'):</b> {{ $lab_order->patient_value }}
          </td>
          <td>
            <b>@lang('lab_order.no_order'):</b> {{ $lab_order->no_order }}
          </td>
          <td>
            <b>@lang('document_type.invoice'):</b> {{ $lab_order->correlative }}
          </td>
        </tr>
      </table>
    </div>
  </div>

  <div class="row">
    <div class="col-xs-12">
      <table class="table text-center report_order">
        <thead>
          <tr>
            <th></th>
            <th>@lang('graduation_card.sphere_abbreviation')</th>
            <th>@lang('graduation_card.cylindir_abbreviation')</th>
            <th>@lang('graduation_card.axis_mayus')</th>
            <th>@lang('graduation_card.prism_mayus')</th>
            <th>@lang('graduation_card.addition_mayus')</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <th>@lang('graduation_card.od')</th>
            <td>{{ $lab_order->sphere_od }}</td>
            <td>{{ $lab_order->cylindir_od }}</td>
            <td>{{ $lab_order->axis_od }}</td>
            <td>{{ $lab_order->base_od }}</td>
            <td>{{ $lab_order->addition_od }}</td>
          </tr>

          <tr>
            <th>@lang('graduation_card.os')</th>
            <td>{{ $lab_order->sphere_os }}</td>
            <td>{{ $lab_order->cylindir_os }}</td>
            <td>{{ $lab_order->axis_os }}</td>
            <td>{{ $lab_order->base_os }}</td>
            <td>{{ $lab_order->addition_os }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

  <div class="row">
    <div class="col-xs-12">
      <table style="width: 100%; margin-bottom: 5px;">
        <tr>
          <td style="width: 25%;">
            <b>@lang('graduation_card.dnsp'):</b>
            @if (!empty($lab_order->dnsp_od))
              <br>(@lang('graduation_card.od')) {{ $lab_order->dnsp_od }}
            @endif
            @if (!empty($lab_order->dnsp_os))
              <br>(@lang('graduation_card.os')) {{ $lab_order->dnsp_os }}
            @endif
          </td>
          <td style="width: 25%; vertical-align: top;"><b>@lang('graduation_card.di'):</b> {{ $lab_order->di }}</td>
          <td style="width: 25%; vertical-align: top;"><b>@lang('graduation_card.ao'):</b> {{ $lab_order->ao }}</td>
          <td style="width: 25%; vertical-align: top;"><b>@lang('graduation_card.ap'):</b> {{ $lab_order->ap }}</td>
        </tr>
        <tr>
          <td>
            <b>@lang('graduation_card.ring'):</b>
            @if (!empty($lab_order->hoop_value))
              {{ $lab_order->hoop_value }}
            @elseif (!empty($lab_order->is_own_hoop))
              @if (!empty($lab_order->hoop_name))
                {{ $lab_order->hoop_name }}
              @else
                (@lang('lab_order.own_hoop_text'))
              @endif
            @endif
          </td>
          <td><b>@lang('graduation_card.size'):</b> {{ $lab_order->size }}</td>
          <td><b>@lang('graduation_card.color'):</b> {{ $lab_order->color }}</td>
          <td>
            <b>@lang('lab_order.hoop_type'):</b>
            @if ($lab_order->hoop_type == 'full')
              @lang('lab_order.full')
            @elseif ($lab_order->hoop_type == 'semi_air')
              @lang('lab_order.semi_air')
            @else
              @lang('lab_order.air')
            @endif
          </td>
        </tr>
        <tr>
          <td colspan="2">
            <b>@lang('lab_order.glass'):</b>
            @if (!empty($lab_order->glass_value))
              <br>(@lang('graduation_card.vs')) {{ $lab_order->glass_value }}
            @endif
            @if (!empty($lab_order->glass_od_value))
              <br>(@lang('graduation_card.od')) {{ $lab_order->glass_od_value }}
            @endif
            @if (!empty($lab_order->glass_os_value))
              <br>(@lang('graduation_card.os')) {{ $lab_order->glass_os_value }}
            @endif
          </td>
          <td colspan="2">
            <b>@lang('lab_order.ar'):</b>
            @if ($lab_order->ar == 'green')
              @lang('lab_order.ar_green')
            @elseif ($lab_order->ar == 'blue')
              @lang('lab_order.ar_blue')
            @elseif ($lab_order->ar == 'premium')
              @lang('lab_order.ar_premium')
            @endif
          </td>
        </tr>
      </table>
    </div>
  </div>

  <div class="row">
    <div class="col-xs-12">
      <b>@lang('lab_order.job_type'):</b>
      @if (!empty($lab_order->job_type))
        <br>
        {{ $lab_order->job_type }}
      @endif
      <br>
      <b>@lang('lab_order.external_laboratory'):</b> {{ $lab_order->ext_lab_value }}
    </div>
  </div>

  {{-- Table --}}
  <div class="row">
    <div class="col-xs-12">
      <table class="table report_order">
        <thead>
          <th style="width: 12%;">@lang('order.code')</th>
          <th style="width: 56%;">@lang('order.description')</th>
          <th style="width: 16%;">@lang('order.available')</th>
          <th style="width: 16%;">@lang('order.quantity')</th>
        </thead>
        <tbody>
          @foreach ($materials as $material)
            <tr>
              <td>{{ $material->sku }}</td>
              <td>{{ $material->product_name }}</td>
              <td>{{ number_format($material->qty_available, 2, '.', ',') }}</td>
              <td>{{ number_format($material->quantity, 2, '.', ',') }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>
