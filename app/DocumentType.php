<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DocumentType extends Model
{
      protected $guarded = ['id'];

      protected $table ='document_types';

      protected $fillable = [
            'document_name',
            'short_name',
            'print_format',
            'is_active',
            'tax_inc',
            'tax_exempt',
            'business_id',
            'is_default',
            'is_document_sale',
            'is_document_purchase',
            'is_return_document',
            'max_operation',
            'document_class_id',
            'document_type_number'
      ];

	public $timestamps = false;

      public static function forDropdown($business_id, $prepend_none = true, $preprend_all = false){
            $all_docs = DocumentType::where('business_id', $business_id)->where('is_active', 1);
            $all_docs = $all_docs->pluck('document_name', 'id');

            if ($prepend_none){
                  $all_docs = $all_docs->prepend(__('correlatives.none_correlatives'), '');
            }

            if ($preprend_all){
                  $all_docs = $all_docs->prepend(__('report.all'), '');
            }

            return $all_docs;
     }

}
