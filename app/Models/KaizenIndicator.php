<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KaizenIndicator extends Model
{
    protected $table = 'kaizen_indicators';

    public $timestamps = false;

    protected $fillable = [
        'kaizen_project_id',
        'indicator_name',
        'before_value',
        'after_value',
        'unit',
    ];

    protected $casts = [
        // 'before_value' => 'float',
        // 'after_value' => 'float',
    ];
    
    public function project()
    {
        return $this->belongsTo(KaizenProject::class, 'kaizen_project_id');
    }
}