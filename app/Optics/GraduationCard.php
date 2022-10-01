<?php

namespace App\Optics;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GraduationCard extends Model
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
    	'patient_id',
        'sphere_os',
        'sphere_od',
        'cylindir_os',
        'cylindir_od',
        'axis_os',
        'axis_od',
        'base_os',
        'base_od',
        'addition_os',
        'addition_od',
        'di',
        'ao',
        'invoice',
        'attended_by',
        'optometrist',
        'observations',
        'is_prescription',
        'dnsp_os',
        'dnsp_od',
        'ap',
        'balance_os',
        'balance_od',
        'document'
    ];

    /**
     * Gets the patient to which the graduation card belongs.
     *
     * @return \Illuminate\Database\Eloquent\Concerns\HasRelationships
     */
    public function patient()
    {
        return $this->belongsTo('App\Optics\Patient');
    }

    /**
     * Retrieves documents path if exists
     */
    public function getDocumentPathAttribute()
    {
        $path = ! empty($this->document) ? asset('/uploads/documents/' . $this->document) : null;
        
        return $path;
    }

    /**
     * Removes timestamp from document name
     */
    public function getDocumentNameAttribute()
    {
        $document_name = ! empty(explode("_", $this->document, 2)[1]) ? explode("_", $this->document, 2)[1] : $this->document;

        return $document_name;
    }
}
