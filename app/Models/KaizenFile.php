<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KaizenFile extends Model
{
     public $timestamps = false;

    protected $table = 'kaizen_files';

    protected $fillable = [
        'kaizen_project_id',
        'file_name',
        'file_path',
        'file_type',
        'user_id',
        'created_at',
    ];

     protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->created_at = $model->freshTimestamp();
        });
    }


    public function kaizenProject()
    {
        return $this->belongsTo(KaizenProject::class, 'kaizen_project_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

     public function getUrlAttribute()
    {
        return asset('storage/' . $this->file_path);
    }


}
