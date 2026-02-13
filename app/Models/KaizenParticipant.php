<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KaizenParticipant extends Model
{
    protected $table = 'kaizen_participants';

    protected $fillable = [
        'kaizen_project_id',
        'participant_name',
        'participation_percent',
    ];

    public function project()
    {
        return $this->belongsTo(KaizenProject::class, 'kaizen_project_id');
    }

     public function Kaizenproject()
        {
            return $this->belongsTo(KaizenProject::class, 'kaizen_project_id');
        }




}
