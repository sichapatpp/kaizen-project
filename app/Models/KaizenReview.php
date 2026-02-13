<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KaizenReview extends Model
{
   protected $fillable = ['kaizen_project_id', 
   'reviewer_id', 
   'comment', 
   'action', 
   'created_at'];

public function Kaizenproject()
        {
            return $this->belongsTo(KaizenProject::class);
        }


}
