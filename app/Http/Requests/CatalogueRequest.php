<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use App\Catalogue;
use App\AccountingEntriesDetail;
use DB;

class CatalogueRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(Request $request)
    {
        $parent = $request->input('parent');
        $prefijo = Catalogue::select('code')->where('id', $parent)->first();
        if($parent == 0) {

            if($prefijo != null) {

                $prefijo = $prefijo->code;
                return [
                    'code' => 'required|integer|regex:/^('.$prefijo.')/',
                    'name' => 'required',
                    'type' => 'required',
                    'parent' => 'unique:accounting_entries_details,account_id',
                ];
            
            } else {

                return [
                   'code' => 'required|integer|digits:1',
                   'name' => 'required',
                   'type' => 'required',
                   'parent' => 'unique:accounting_entries_details,account_id',
                   'name' => 'required',
               ];
           }
       
       } else {

            if($prefijo != null) {

                $prefijo = $prefijo->code;
                return [
                    'code' => 'required|integer|regex:/^('.$prefijo.')/',
                    'name' => 'required',
                    'type' => 'required',
                    'parent' => 'unique:accounting_entries_details,account_id',
                ];
            
            } else {

                return [
                    'code' => 'required|integer',
                    'name' => 'required',
                    'type' => 'required',
                    'parent' => 'unique:accounting_entries_details,account_id',
                ];
            }
       }
   }

   public function messages() {

        return [
            
            'parent.unique' => __('accounting.account_auxiliar'),
        ];
    }

}