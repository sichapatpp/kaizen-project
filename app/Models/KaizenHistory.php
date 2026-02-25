<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KaizenHistory extends Model
{
    protected $fillable = [
        'kaizen_project_id',
        'old_status',
        'new_status',
        'user_id',
    ];

    public $timestamps = false;
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function KaizenProject()
    {
        return $this->belongsTo(KaizenProject::class);
    }


}
