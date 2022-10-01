<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes;
    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    public static function forDropdown($business_id, $prepend_none = true, $prepend_all = false){
        $all_cat = Category::where('business_id', $business_id)
        ->orderBy('name');
        $all_cat = $all_cat->pluck('name', 'id');

        if($prepend_none){
            $all_cat = $all_cat->prepend(__("lang_v1.none"), '');
        }

        if($prepend_all){
            $all_cat = $all_cat->prepend(__("report.all"), '');
        }

        return $all_cat;
    }

    /**
     * Combines Category and sub-category
     *
     * @param int $business_id
     * @return array
     */
    public static function catAndSubCategories($business_id)
    {
        $categories = Category::where('business_id', $business_id)
                        ->where('parent_id', 0)
                        ->orderBy('name', 'asc')
                        ->get()
                        ->toArray();

        if (empty($categories)) {
            return [];
        }

        $sub_categories = Category::where('business_id', $business_id)
                            ->where('parent_id', '!=', 0)
                            ->orderBy('name', 'asc')
                            ->get()
                            ->toArray();
        $sub_cat_by_parent = [];

        if (!empty($sub_categories)) {
            foreach ($sub_categories as $sub_category) {
                if (empty($sub_cat_by_parent[$sub_category['parent_id']])) {
                    $sub_cat_by_parent[$sub_category['parent_id']] = [];
                }

                $sub_cat_by_parent[$sub_category['parent_id']][] = $sub_category;
            }
        }

        foreach ($categories as $key => $value) {
            if (!empty($sub_cat_by_parent[$value['id']])) {
                $categories[$key]['sub_categories'] = $sub_cat_by_parent[$value['id']];
            }
        }

        return $categories;
    }

    public function catalogue(){
        return $this->belongsTo('App\Catalogue');
    }
}
