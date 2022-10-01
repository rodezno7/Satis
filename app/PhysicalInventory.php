<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PhysicalInventory extends Model
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

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code',
        'name',
        'start_date',
        'location_id',
        'warehouse_id',
        'status',
        'responsible',
        'business_id',
        'created_by',
        'updated_by',
        'processed_by',
        'reviewed_by',
        'authorized_by',
        'finished_by',
        'category'
    ];

    /**
     * Get business location.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function location()
    {
        return $this->belongsTo(\App\BusinessLocation::class, 'location_id');
    }

    /**
     * Get warehouse.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function warehouse()
    {
        return $this->belongsTo(\App\Warehouse::class, 'warehouse_id');
    }

    /**
     * Get user.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(\App\User::class, 'responsible');
    }

    /**
     * Get physical inventory lines.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function physical_inventory_lines()
    {
        return $this->hasMany(\App\PhysicalInventoryLine::class);
    }
}
