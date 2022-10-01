<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages.
    |
    */

    'accepted'             => ':attribute debe ser aceptado.',
    'active_url'           => ':attribute no es una URL válida.',
    'after'                => ':attribute debe ser una fecha posterior a :date.',
    'after_or_equal'       => ':attribute debe ser una fecha posterior o igual a :date.',
    'alpha'                => ':attribute sólo debe contener letras.',
    'alpha_dash'           => ':attribute sólo debe contener letras, números y guiones.',
    'alpha_num'            => ':attribute sólo debe contener letras y números.',
    'array'                => ':attribute debe ser un conjunto.',
    'before'               => ':attribute debe ser una fecha anterior a :date.',
    'before_or_equal'      => ':attribute debe ser una fecha anterior o igual a :date.',
    'between'              => [
        'numeric' => ':attribute tiene que estar entre :min - :max.',
        'file'    => ':attribute debe pesar entre :min - :max kilobytes.',
        'string'  => ':attribute tiene que tener entre :min - :max caracteres.',
        'array'   => ':attribute tiene que tener entre :min - :max ítems.',
    ],
    'boolean'              => 'El campo :attribute debe tener un valor verdadero o falso.',
    'confirmed'            => 'La confirmación de :attribute no coincide.',
    'date'                 => ':attribute no es una fecha válida.',
    'date_format'          => ':attribute no corresponde al formato :format.',
    'different'            => ':attribute y :other deben ser diferentes.',
    'digits'               => ':attribute debe tener :digits dígitos.',
    'digits_between'       => ':attribute debe tener entre :min y :max dígitos.',
    'dimensions'           => 'Las dimensiones de la imagen :attribute no son válidas.',
    'distinct'             => 'El campo :attribute contiene un valor duplicado.',
    'email'                => ':attribute no es un correo válido',
    'exists'               => ':attribute es inválido.',
    'file'                 => 'El campo :attribute debe ser un archivo.',
    'filled'               => 'El campo :attribute es obligatorio.',
    'image'                => ':attribute debe ser una imagen.',
    'in'                   => ':attribute es inválido.',
    'in_array'             => 'El campo :attribute no existe en :other.',
    'integer'              => ':attribute debe ser un número entero.',
    'ip'                   => ':attribute debe ser una dirección IP válida.',
    'ipv4'                 => ':attribute debe ser un dirección IPv4 válida',
    'ipv6'                 => ':attribute debe ser un dirección IPv6 válida.',
    'json'                 => 'El campo :attribute debe tener una cadena JSON válida.',
    'max'                  => [
        'numeric' => ':attribute no debe ser mayor a :max.',
        'file'    => ':attribute no debe ser mayor que :max kilobytes.',
        'string'  => ':attribute no debe ser mayor que :max caracteres.',
        'array'   => ':attribute no debe tener más de :max elementos.',
    ],
    'mimes'                => ':attribute debe ser un archivo con formato: :values.',
    'mimetypes'            => ':attribute debe ser un archivo con formato: :values.',
    'min'                  => [
        'numeric' => 'El tamaño de :attribute debe ser de al menos :min.',
        'file'    => 'El tamaño de :attribute debe ser de al menos :min kilobytes.',
        'string'  => ':attribute debe contener al menos :min caracteres.',
        'array'   => ':attribute debe tener al menos :min elementos.',
    ],
    'not_in'               => ':attribute es inválido.',
    'numeric'              => ':attribute debe ser numérico.',
    'present'              => 'El campo :attribute debe estar presente.',
    'regex'                => 'El formato de :attribute es inválido.',
    'required'             => 'El campo :attribute es obligatorio.',
    'required_if'          => 'El campo :attribute es obligatorio cuando :other es :value.',
    'required_unless'      => 'El campo :attribute es obligatorio a menos que :other esté en :values.',
    'required_with'        => 'El campo :attribute es obligatorio cuando :values está presente.',
    'required_with_all'    => 'El campo :attribute es obligatorio cuando :values está presente.',
    'required_without'     => 'El campo :attribute es obligatorio cuando :values no está presente.',
    'required_without_all' => 'El campo :attribute es obligatorio cuando ninguno de :values estén presentes.',
    'same'                 => ':attribute y :other deben coincidir.',
    'size'                 => [
        'numeric' => 'El tamaño de :attribute debe ser :size.',
        'file'    => 'El tamaño de :attribute debe ser :size kilobytes.',
        'string'  => ':attribute debe contener :size caracteres.',
        'array'   => ':attribute debe contener :size elementos.',
    ],
    'string'               => 'El campo :attribute debe ser una cadena de caracteres.',
    'timezone'             => 'El :attribute debe ser una zona válida.',
    'unique'               => ':attribute ya ha sido registrado.',
    'uploaded'             => 'Subir :attribute ha fallado.',
    'url'                  => 'El formato :attribute es inválido.',
    'custom-messages' => [
        'quantity_not_available' => 'Solo :qty :unit disponibles',
        'this_field_is_required' => 'Esta campo es requerido'
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom'               => [
        'password' => [
            'min' => 'La :attribute debe contener más de :min caracteres',
        ],
        'email' => [
            'unique' => 'El :attribute ya ha sido registrado.',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */

    'attributes' => array(
        'code'                  => 'código',
        'type'                  => 'tipo',
        'parent'                => 'cuenta padre',
        'name'                  => 'nombre',
        'username'              => 'usuario',
        'email'                 => 'correo electrónico',
        'first_name'            => 'nombre',
        'last_name'             => 'apellido',
        'password'              => 'contraseña',
        'password_confirmation' => 'confirmación de la contraseña',
        'city'                  => 'ciudad',
        'country'               => 'país',
        'address'               => 'dirección',
        'phone'                 => 'teléfono',
        'mobile'                => 'móvil',
        'age'                   => 'edad',
        'sex'                   => 'sexo',
        'gender'                => 'género',
        'year'                  => 'año',
        'month'                 => 'mes',
        'day'                   => 'día',
        'hour'                  => 'hora',
        'minute'                => 'minuto',
        'second'                => 'segundo',
        'title'                 => 'título',
        'content'               => 'contenido',
        'body'                  => 'contenido',
        'description'           => 'descripción',
        'excerpt'               => 'extracto',
        'date'                  => 'fecha',
        'time'                  => 'hora',
        'subject'               => 'asunto',
        'message'               => 'mensaje',

        
        
        'period_id'             => 'período',
        'number'                => 'número',
        'debe'                  => 'debe',
        'total_debe'            => 'total debe',
        'total_haber'           => 'total haber',
        'haber'                 => 'haber',
        'type_entrie_id'        => 'tipo de partida',
        'business_location_id'  => 'sucursal',

        'description2'          => 'descripción',
        'debe2'                 => 'debe',
        'haber2'                => 'haber',
        'total_debe2'           => 'total debe',
        'total_haber2'          => 'total haber',
        'number2'               => 'número',
        'date2'                 => 'fecha',
        'country_id'            => 'país',
        'zone_id'               => 'zona',
        'state_id'              => 'departamento',
        'ename'                 => 'nombre',
        'correlative'           => 'correlativo',
        'claim_type'            => 'tipo de reclamo',
        'status_claim_id'       => 'estado',
        'description'           => 'descripción',
        'claim_date'            => 'fecha del reclamo',
        'suggested_closing_date'=> 'fecha de cierre sugerida',

        'amount_request_business'   => 'monto requerido',
        'amount_request_natural'    => 'monto requerido',

        'name_reference'            => "nombre de la referencia comerciales",
        'phone_reference'           => "teléfono de referencia comerciales",
        'amount_reference'          => "monto en referencias comerciales",
        'date_reference'            => "fecha de cancelación en referencias comerciales",

        'name_relationship'         => "nombre en parientes cercanos",
        'relation_relationship'     => "parentezco en parientes cercanos",
        'phone_relationship'        => "teléfono en parientes cercanos",
        'address_relationship'      => "domicilio en parientes cercanos",

        'business_name'             => 'razón social',
        'trade_name'                => 'nombre comercial',
        'nrc'                       => 'NRC',
        'nit_business'              => 'NIT',
        'business_type'             => 'giro',
        'address'                   => 'dirección',
        'category_business'         => 'categoría',
        'phone_business'            => 'teléfono',
        'fax_business'              => 'fax',
        'legal_representative'      => 'representante legal',
        'dui_legal_representative'  => 'DUI del representante legal',
        'purchasing_agent'          => 'encargado de compras',
        'phone_purchasing_agent'    => 'teléfono del encargado de compras',
        'fax_purchasing_agent'      => 'fax del encargado de compras',
        'email_purchasing_agent'    => 'correo del encargado de compras',
        'payment_manager'           => 'encargado de pagos',
        'phone_payment_manager'     => 'teléfono del encargado de pagos',
        'email_payment_manager'    => 'correo del encargado de pagos',
        'term_business'             => 'plazo',
        'warranty_business'         => 'garantía',
        'name_natural'              => 'nombre',
        'dui_natural'               => 'dui',
        'age'                       => 'edad',
        'birthday'                  => 'fecha de nacimiento',
        'phone_natural'             => 'teléfono',
        'category_natural'          => 'categoría',
        'nit_natural'               => 'NIT',
        'address_natural'           => 'dirección',
        'term_natural'              => 'plazo',
        'warranty_natural'          => 'garantía',
        'telphone'                  => 'teléfono',
        'dni'                       => 'DUI',
        'business_type_id'          => 'tipo de empresa',
        'customer_portfolio_id'     => 'cartera de clientes',
        'customer_group_id'         => 'grupo de clientes',
        'city_id'                   => 'ciudad',
        'txt-name-type'             => 'nombre',
        'txt-resolution-time-type'  => 'tiempo de resolución',
        'txt-ename-type'            => 'nombre',
        'txt-eresolution-time-type' => 'tiempo de resolución',

        'customer_id'               => 'Cliente',
        'employee_id'               => 'Vendedor',
        'document_type_id'          => 'Tipo de documento',
        'quote_date'                => 'Fecha',
        'quote_ref_no'              => 'Número de cotización',
        'customer_name'             => 'Nombre del cliente',
        'contact_name'              => 'Nombre del contacto',
        'email'                     => 'Correo electrónico',
        'mobile'                    => 'Celular',
        'address'                   => 'Dirección',
        'payment_condition'         => 'Condiciones de pago',
        'tax_detail'                => 'Detalle de impuestos',
        'validity'                  => 'Período de validéz',
        'delivery_time'             => 'Tiempo de entrega',
        'note'                      => 'Comentarios',
        'legend'                    => 'Leyenda',
        'terms_conditions'          => 'Términos y condiciones',
        'discount_type'             => 'Tipo de descuento',
        'total_before_tax'          => 'Total antes de impuestos',
        'tax_amount'                => 'Total de impuestos',
        'total_final'               => 'Total final',
        'catalogue_file'            => 'catálogo',
        'value'                     => 'nombre',
        'status'                    => 'estado',
        'birthdate'                 => 'fecha de nacimiento',
        'nationality_id'            => 'nacionalidad',
        'civil_status_id'           => 'estado civil',
        'check_payment'             => 'pago en cheque',
        'extra_hours'               => 'horas extras',
        'foreign_tax'               => 'renta internacional',
        'profession_id'             => 'profesión u oficio',
        'date_admission'            => 'fecha de ingreso',
        'salary'                    => 'salario',
        'fee'                       => 'honorarios',
        'department_id'             => 'departamento',
        'position_id'               => 'puesto',
        'afp_id'                    => 'Fondo de pensión',
        'type_id'                   => 'tipo',
        'bank_id'                   => 'banco',
        'bank_account'              => 'cuenta bancaria',
        'tax_number'                => 'NIT',
        'afp_number'                => 'Número fondo de pensión',
        'social_security_number'    => 'Número de seguro social',
        'reg_number'    => 'NRC',
        'tax_number'    => 'NIT',
        'business_line' => 'giro',
    ),

];