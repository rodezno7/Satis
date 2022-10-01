<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>@lang('credit.credit_request')</title>    
  <style>
    body
    {
      font-family: 'Helvetica', 'Arial', sans-serif;
      color: #000000;
      font-size: 7pt;
    }
    h3, h4
    {
      text-align: center;        
    }
    .table1
    {
      border: 0px;
    }
    .table2
    {
      border-collapse: collapse;
      border: 0.25px solid black;
    }
    .td2
    {
      border: 0px;            
    }
    td
    {
      border: 0.25px solid black;
      padding: 4px;
      text-align: left;
    }
    th
    {
      border: 0.25px solid black;
      padding: 4px;
      text-align: center;
    }
    .alnright { text-align: right; }
    .alnleft { text-align: left; }
    .alncenter { text-align: center; }
    @page{
      margin-bottom: 75px;
    }

    #header,
    #footer {
      position: fixed;
      left: 0;
      right: 0;
      color: #000000;
      font-size: 0.9em;
    }
    #header {
      top: 0;
      border-bottom: 0.1pt solid #aaa;
    }
    #footer {
      bottom: 0;
      border-top: 0.1pt solid #aaa;
    }
    .page-number:before {
      content: "Página " counter(page);
    }
    tr.no-border-tr td {
      border-top: 0.25px solid white;
      border-right: 0.25px solid white;
      border-bottom: 0.25px solid white;
    }
    tr.no-border-tr td.yes-border-r {
      border-right: 0.25px solid black;
    }
    td.no-border-b {
      border-bottom: 0.25px solid white;
    }
    tr.yes-border-b td {
      border-bottom: 0.25px solid black;
    }
  </style>   
</head>
<body>
  <div id="footer">
    <div class="page-number"></div>
  </div>

  <table class="table2" style=" width: 100%;">
    <tr>
      <td class="alncenter"><img src="{{ asset('uploads/business_logos/'.$logo) }}" width="140px" height="60px"></td>
      <td colspan="3" class="alncenter"><strong>SOLICITUD DE CRÉDITO</strong></td>
    </tr>

    <tr>
     <td colspan="4" class="alnleft"><strong>A. PARA PERSONAS JURÍDICAS</strong></td>
   </tr>

   <tr>
     <td colspan="4" class="alnleft">@lang('credit.social_reason'): {{ $credit->business_name }}</td>
   </tr>

   <tr>
     <td class="alnleft" style="width: 25%;">@lang('credit.trade_name'): {{ $credit->trade_name }}</td>
     <td class="alnleft" style="width: 25%;"></td>
     <td class="alnleft" style="width: 25%;"></td>
     <td class="alnleft" style="width: 25%;">@lang('credit.nrc'): {{ $credit->nrc }}</td>
   </tr>

   <tr>
     <td class="alnleft">@lang('credit.nit'): {{ $credit->nit_business }}</td>
     <td class="alnleft" colspan="3">@lang('credit.business_type'): {{ $credit->business_type }}</td>
   </tr>

   <tr>
     <td colspan="4" class="alnleft">@lang('credit.address'): {{ $credit->address }}</td>
   </tr>

   <tr>
     <td class="alnleft">@lang('credit.category'): {{ $credit->category_business }}</td>
     <td class="alnleft"></td>
     <td class="alnleft">@lang('credit.phone'): {{ $credit->phone_business }}</td>
     <td class="alnleft">@lang('credit.fax'): {{ $credit->fax_business }}</td>
   </tr>

   <tr>
     <td class="alnleft" colspan="3">@lang('credit.legal_representative'): {{ $credit->legal_representative }}</td>
     <td class="alnleft">@lang('credit.dui'): {{ $credit->dui_legal_representative }}</td>
   </tr>

   <tr>
     <td class="alnleft" colspan="3">@lang('credit.purchasing_agent'): {{ $credit->purchasing_agent }}</td>
     <td class="alnleft"></td>
   </tr>

   <tr>
     <td class="alnleft">@lang('credit.phone'): {{ $credit->phone_purchasing_agent }}</td>
     <td class="alnleft"></td>
     <td class="alnleft">@lang('credit.fax'): {{ $credit->fax_purchasing_agent }}</td>
     <td class="alnleft">@lang('credit.email'): {{ $credit->email_purchasing_agent }}</td>
   </tr>

   <tr>
     <td class="alnleft" colspan="2">@lang('credit.payment_manager'): {{ $credit->payment_manager }}</td>
     <td class="alnleft">@lang('credit.phone'): {{ $credit->phone_payment_manager }}</td>
     <td class="alnleft">@lang('credit.email'): {{ $credit->email_payment_manager }}</td>
   </tr>

   <tr>
     <td class="alnleft">@lang('credit.amount_request'): US$ {{ $credit->amount_request_business }}</td>
     <td class="alnleft">@lang('credit.term'): {{ $credit->term_business }}</td>
     <td class="alnleft"></td>
     <td class="alnleft">@lang('credit.warranty'): {{ $credit->warranty_business }}</td>
   </tr>

   <tr>
     <td colspan="4"></td>
   </tr>

   <tr>
     <td colspan="4" class="alnleft"><strong>B. PARA PERSONAS NATURALES</strong></td>
   </tr>

   <tr>
     <td class="alnleft" colspan="3">@lang('credit.name_by_dui'): {{ $credit->name_natural }}</td>
     <td class="alnleft">@lang('credit.dui'): {{ $credit->dui_natural }}</td>
   </tr>

   <tr>
     <td class="alnleft">@lang('credit.age'): {{ $credit->age }}</td>
     <td class="alnleft" colspan="3">@lang('credit.birthday'): {{ $credit->birthday }}</td>
   </tr>

   <tr>
     <td class="alnleft">@lang('credit.phone'): {{ $credit->phone_natural }}</td>
     <td class="alnleft" colspan="2">@lang('credit.category'): {{ $credit->category_natural }}</td>
     <td class="alnleft">@lang('credit.nit'): {{ $credit->nit_natural }}</td>
   </tr>

   <tr>
     <td class="alnleft" colspan="4">Dirección: {{ $credit->address_natural }}</td>
   </tr>

   <tr>
     <td class="alnleft">@lang('credit.amount_request'): US$ {{ $credit->amount_request_natural }}</td>
     <td class="alnleft">@lang('credit.term'): {{ $credit->term_natural }}</td>
     <td class="alnleft"></td>
     <td class="alnleft">@lang('credit.warranty'): {{ $credit->warranty_natural }}</td>
   </tr>

   <tr>
     <td class="alnleft" colspan="4"></td>
   </tr>

   <tr>
     <td class="alnleft" colspan="4"><strong>NEGOCIO PROPIO</strong></td>
   </tr>

   <tr>
     <td class="alnleft" colspan="4">@lang('credit.own_business_name'): {{ $credit->own_business_name }}</td>
   </tr>

   <tr>
     <td class="alnleft" colspan="4">@lang('credit.own_business_address'): {{ $credit->own_business_address }}</td>
   </tr>

   <tr>
    <td class="alnleft" colspan="2"></td>
    <td class="alnleft" colspan="2">@lang('credit.own_business_time'): {{ $credit->own_business_time }}</td>
  </tr>

  <tr>
    <td class="alnleft">@lang('credit.phone'): {{ $credit->own_business_phone }}</td>
    <td class="alnleft"></td>
    <td class="alnleft">@lang('credit.fax'): {{ $credit->own_business_fax }}</td>
    <td class="alnleft">@lang('credit.email'): {{ $credit->own_business_email }}</td>
  </tr>

  <tr>
    <td class="alnleft" colspan="2">@lang('credit.average_monthly_income'): US$ {{ $credit->average_monthly_income }}</td>
    <td class="alnleft" colspan="2"></td>
  </tr>

  <tr>
    <td class="alnleft" colspan="4"></td>
  </tr>

  <tr>
    <td class="alnleft" colspan="4"><strong>DATOS DEL CÓNYUGE</strong></td>
  </tr>

  <tr>
    <td class="alnleft" colspan="3">@lang('credit.spouse_name'): {{ $credit->spouse_name }}</td>
    <td class="alnleft">@lang('credit.dui'): {{ $credit->spouse_dui }}</td>
  </tr>

  <tr>
    <td class="alnleft" colspan="3">@lang('credit.spouse_work_address'): {{ $credit->spouse_work_address }}</td>
    <td class="alnleft">@lang('credit.phone'): {{ $credit->spouse_phone }}</td>
  </tr>

  <tr>
    <td class="alnleft">@lang('credit.spouse_income_date'): {{ $credit->spouse_income_date }}</td>
    <td class="alnleft" colspan="2">@lang('credit.spouse_position'): {{ $credit->spouse_position }}</td>
    <td class="alnleft">@lang('credit.spouse_salary'): US$ {{ $credit->spouse_salary }}</td>
  </tr>

  <tr>
    <td class="alnleft" colspan="4"></td>
  </tr>

  <tr>
    <td class="alnleft">@lang('credit.order_purchase'):</td>
    @if ($credit->order_purchase)
    <td class="alnleft">Si: X</td>
    <td class="alnleft">No:</td>
    @else
    <td class="alnleft">Si:</td>
    <td class="alnleft">No: X</td>
    @endif

    @if ($credit->order_via_fax)
    <td class="alnleft">@lang('credit.order_via_fax'): X</td>
    @else
    <td class="alnleft">@lang('credit.order_via_fax'):</td>
    @endif
  </tr>

  <tr>
    <td colspan="4"></td>
  </tr>

  <tr>
    <td class="alnleft" colspan="4"><strong>C. REFERENCIAS COMERCIALES</strong></td>
  </tr>
  <tr>
    <td class="alnleft" colspan="4">Menciones los nombres de las Casas Comerciales con las que tiene o haya tenido crédito:</td>
  </tr>

  <tr>
    <td class="alnleft" colspan="4">
      <table class="table2" width="100%">

        <tr>
          <td class="alncenter"><strong>Nombre</strong></td>
          <td class="alncenter"><strong>Teléfono</strong></td>
          <td class="alncenter"><strong>Monto</strong></td>
          <td class="alncenter"><strong>Cancelado en Fecha</strong></td>
        </tr>



        @foreach($references as $item)
        <tr>
          <td class="alnleft"> {{ $item->name }} </td>
          <td class="alnleft"> {{ $item->phone }} </td>
          <td class="alnright"> {{ number_format($item->amount, 2) }} </td>
          <td class="alnleft"> {{ $item->date_cancelled }} </td>
        </tr>
        @endforeach


      </table>
    </td>
  </tr>

  <tr>
    <td colspan="4"></td>
  </tr>

  <tr>
    <td colspan="4"><strong>D. PARIENTES CERCANOS DEL SOLICITANTE </strong></td>
  </tr>

  <tr>
    <td class="alnleft" colspan="4">
      <table class="table2" width="100%">

        <tr>
          <td class="alncenter"><strong>Nombre</strong></td>
          <td class="alncenter"><strong>Parentezco</strong></td>
          <td class="alncenter"><strong>Teléfono</strong></td>
          <td class="alncenter"><strong>Domicilio</strong></td>
        </tr>



        @foreach($relationships as $item)
        <tr>
          <td class="alnleft"> {{ $item->name }} </td>
          <td class="alnleft"> {{ $item->relationship }} </td>
          <td class="alnright"> {{ $item->phone }} </td>
          <td class="alnleft"> {{ $item->address }} </td>
        </tr>
        @endforeach


      </table>
    </td>
  </tr>

  <tr>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>

  <tr>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>

  <tr>
    <td colspan="4" class="alncenter" style="background-color: gray;"><strong>AUTORIZACIÓN DEL CONSUMIDOR</strong></td>
  </tr>

  <tr>
    <td colspan="4"></td>
  </tr>

  <tr>
    <td colspan="4">
      Yo: _____________________________________________________
      con Documento Único de Identidad número: _______________________
      en mi calidad de Consumidor o Cliente, autorizo expresamente a:
      
    </td>
  </tr>
  <tr>
    <td colspan="4" class="alnleft"><strong>HEBER JOB GUARDADO RODRIGUEZ Y/ O LUXOR GT S.A DE C.V</strong></td>
  </tr>

  <tr>
    <td class="alnleft" colspan="4">
      A. Para que consulte, e investigue mi Historial de Crédito, ya sea con los proveedores que me ha otorgado dichos
      créditos o en las Agencias de Información de Datos sobre el Historia de Crédito de las Personas.
    </td>
  </tr>

  <tr>
    <td class="alnleft" colspan="4">
      B. Para que pueda reportar y compartir mi información personal y crediticia, independientemente de cuál sea el
      estado de esta última, con otros proveedores y con las Agencias de Información de Datos sobre el historial de Crédito
      de las Personas, en los términos señalados por la ley.
    </td>
  </tr>

  <tr>
    <td class="alnleft" colspan="4">
      C. Para que pueda apoyarse en su gestión de cobro, de las herramientas que de que dispone la Agencia de
      Información de Datos sobre el Historial de crédito de las Personas , para la localización y recuperación de cualquier
      saldo que les adecuare en el futuro, siempre que se proceda acorde a los dispuesto por la ley de la materia.
    </td>
  </tr>

  <tr>
    <td class="alnleft" colspan="4">
      D. Para que se proceda por la vía de la conciliación (Art. 111 de La Ley de Protección al Consumidor), cuando exista
      algún tipo de controversia.
    </td>
  </tr>

  <tr>
    <td class="alnleft" colspan="4">
      Entiendo y reconozco que el reportar mi información crediticia a las Agencias de Información de Datos sobre el
      Historial de Crédito de las Personas, contribuye a fortalecer mis referencias de crédito, lo que permitirá a futuro
      acceder oportunamente a nuevos créditos”.
    </td>
  </tr>

  <tr class="no-border-tr">
    <td></td>
    <td></td>
    <td></td>
    <td class="yes-border-r"></td>
  </tr>
  <tr class="no-border-tr">
    <td></td>
    <td></td>
    <td></td>
    <td class="yes-border-r"></td>
  </tr>
  <tr class="no-border-tr">
    <td></td>
    <td></td>
    <td></td>
    <td class="yes-border-r"></td>
  </tr>
  <tr class="no-border-tr">
    <td></td>
    <td></td>
    <td></td>
    <td class="yes-border-r"></td>
  </tr>
  <tr class="no-border-tr">
    <td></td>
    <td></td>
    <td></td>
    <td class="yes-border-r"></td>
  </tr>
  <tr class="no-border-tr">
    <td></td>
    <td></td>
    <td></td>
    <td class="yes-border-r"></td>
  </tr>
  <tr class="no-border-tr">
    <td></td>
    <td></td>
    <td></td>
    <td class="yes-border-r"></td>
  </tr>

  <tr>
    <td colspan="4" class="alnleft no-border-b">

      {{ $footer }}
      
    </td>
  </tr>

  <tr class="no-border-tr">
    <td></td>
    <td></td>
    <td></td>
    <td class="yes-border-r"></td>
  </tr>
  <tr class="no-border-tr">
    <td></td>
    <td></td>
    <td></td>
    <td class="yes-border-r"></td>
  </tr>
  <tr class="no-border-tr">
    <td></td>
    <td></td>
    <td></td>
    <td class="yes-border-r"></td>
  </tr>
  <tr class="no-border-tr">
    <td></td>
    <td></td>
    <td></td>
    <td class="yes-border-r"></td>
  </tr>
  <tr class="no-border-tr">
    <td></td>
    <td></td>
    <td></td>
    <td class="yes-border-r"></td>
  </tr>

  <tr>
    <td colspan="4" class="alncenter no-border-b">
      <br>

      ________________________________________________________<br>
      Firma y sello del Representante Legal o Títular
      
    </td>
    
  </tr>

  <tr class="no-border-tr">
    <td></td>
    <td></td>
    <td></td>
    <td class="yes-border-r"></td>
  </tr>
  <tr class="no-border-tr">
    <td></td>
    <td></td>
    <td></td>
    <td class="yes-border-r"></td>
  </tr>
  <tr class="no-border-tr">
    <td></td>
    <td></td>
    <td></td>
    <td class="yes-border-r"></td>
  </tr>
  <tr class="no-border-tr">
    <td></td>
    <td></td>
    <td></td>
    <td class="yes-border-r"></td>
  </tr>
  <tr class="no-border-tr">
    <td></td>
    <td></td>
    <td></td>
    <td class="yes-border-r"></td>
  </tr>
  <tr class="no-border-tr yes-border-b">
    <td></td>
    <td></td>
    <td></td>
    <td class="yes-border-r"></td>
  </tr>

  <tr>
    <td colspan="4" class="alncenter"><strong>Favor anexar a la presente solicitud:</strong></td>
  </tr>

  <tr>
    <td class="alnleft" colspan="2"><strong>Persona Jurídica</strong></td>
    <td class="alnleft" colspan="2"><strong>Personas Naturales:</strong></td>
  </tr>

  <tr>
    <td class="alnleft" colspan="2">Fotocopia Escritura de Constitución</td>
    <td class="alnleft" colspan="2">Fotocopia de DUI y NIT</td>
  </tr>

  <tr>
    <td class="alnleft" colspan="2">Modificaciones al Pacto Social si las hubiere</td>
    <td class="alnleft" colspan="2">Fotocopia de Tarjeta de Contribuyente IVA</td>
  </tr>

  <tr>
    <td class="alnleft" colspan="2">Fotocopia de Credencial Vigente (Representante Legal)</td>
    <td class="alnleft" colspan="2">Fotocopia de ULTIMAS 3 DECLARACIONES DE IVA</td>
  </tr>

  <tr>
    <td class="alnleft" colspan="2">Fotocopia DUI y NIT del Representante Legal</td>
    <td class="alnleft" colspan="2"><strong>Para Instituciones Gubernamentales</strong></td>
  </tr>

  <tr>
    <td class="alnleft" colspan="2">Fotocopia de NRC Y NIT de la Empresa</td>
    <td class="alnleft" colspan="2">Ordenanza Municipal</td>
  </tr>

  <tr>
    <td class="alnleft" colspan="2">Fotocopia de ULTIMAS 3 DECLARACIONES DE IVA</td>
    <td class="alnleft" colspan="2">Punto de Acta donde especifique los limites Autorizados</td>
  </tr>

  <tr>
    <td class="alnleft" colspan="2">Pagare firmado y sellado por el representante legal</td>
    <td class="alnleft" colspan="2">Acuerdo de Consejo Directivo en el que se autoriza la compra</td>
  </tr>

  <tr>
    <td class="alnleft" colspan="2"></td>
    <td class="alnleft" colspan="2">Orden de Compra</td>
  </tr>

  <tr>
    <td class="alnleft" colspan="2"></td>
    <td class="alnleft" colspan="2">DUI y NIT de Personas Autorizadas para Firmar Creditos</td>
  </tr>

</table>

</body>
</html>