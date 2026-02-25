<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KaizenReview extends Model
{
    protected $fillable = [
        'kaizen_project_id',
        'user_id',
        'comment',
        'action',
    ];

    public $timestamps = false; // DB only has created_at with useCurrent()

    public function kaizenProject()
    {
        return $this->belongsTo(KaizenProject::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
