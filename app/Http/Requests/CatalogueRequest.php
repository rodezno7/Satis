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
        if($parent == 0)
        {
            if($prefijo != null)
            {
                $prefijo = $prefijo->code;
                return [
                    'code' => 'required|integer|unique:catalogues|regex:/^('.$prefijo.')/',
                    'name' => 'required',
                    'type' => 'required',
                    'parent' => 'unique:accounting_entries_details,account_id',
                ];
            }
            else
            {
                return [
                   'code' => 'required|integer|digits:1|unique:catalogues',
                   'name' => 'required',
                   'type' => 'required',
                   'parent' => 'unique:accounting_entries_details,account_id',
                   'name' => 'required',
               ];
           }
       }
       else
       {
            if($prefijo != null)
            {
                $prefijo = $prefijo->code;
                return [
                    'code' => 'required|integer|unique:catalogues|regex:/^('.$prefijo.')/',
                    'name' => 'required',
                    'type' => 'required',
                    'parent' => 'unique:accounting_entries_details,account_id',
                ];
            }
            else
            {
                return [
                    'code' => 'required|integer|unique:catalogues',
                    'name' => 'required',
                    'type' => 'required',
                    'parent' => 'unique:accounting_entries_details,account_id',
                ];
            }
       }
   }
}