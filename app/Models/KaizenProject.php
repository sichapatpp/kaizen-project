<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;
use App\Models\KaizenFile;
use App\Models\KaizenParticipant;

class KaizenProject extends Model
{
    use HasFactory;

    protected $table = 'kaizen_projects';

    protected $fillable = [
        'fiscalyear',
        'title',
        'problem',
        'improvement',
        'result',
        'user_id',
        'status',
        'improvement_types',
    ];

    protected $casts = [
        'improvement_types' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function participants()
    {
        return $this->hasMany(KaizenParticipant::class, 'kaizen_project_id');
    }

    public function files()
    {
        return $this->hasMany(KaizenFile::class, 'kaizen_project_id');
    }

    public function histories()
    {
        return $this->hasMany(KaizenHistori::class, 'kaizen_project_id');
    }

    public function indicators()
    {
        return $this->hasMany(KaizenIndiator::class, 'kaizen_project_id');
    }

    public function reviews()
    {
        return $this->hasMany(KaizenReview::class, 'kaizen_project_id');
    }
}