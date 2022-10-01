<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
class Catalogue extends Model
{
	protected $fillable = ['code', 'name', 'parent', 'type', 'status', 'level', 'catalogue_id'];

	public function detail()
	{
		return $this->hasMany('App\AccountingEntriesDetail');
	}

	public function child()
	{
		return $this->hasMany('App\Catalogue', 'parent')->select(['id', DB::raw("CONCAT(code, ' ', name) AS text"), 'parent']);
	}

	public function children()
	{
		return $this->child()->with('children');
	}

	public function parent()
	{
		return $this->belongsTo('App\Catalogue','parent')->select(['id', 'name', DB::raw("CONCAT(code, ' ', name) AS text"), 'parent']);
	}
	public function padre()
	{
		return $this->parent();
	}
	public function parent_rec()
	{
		return $this->parent()->with('parent_rec');
	}

	public function latestMeasure()
	{
		return $this->hasOne(Catalogue::class)->latest();
	}

	public function bankTransaction()
	{
		return $this->hasMany('App\BankTransaction');
	}

	public function category(){
		return $this->belongsTo('App\Category');
	}
}