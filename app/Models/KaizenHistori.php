<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KaizenHistori extends Model
{
     protected $fillable = ['kaizen_project_id',
      'old_status',
      'new_status',
      'changed_by',
      'created_at'];
      public function user()
        {
            return $this->belongsTo(User::class);
        }
       public function KaizenProject()
        {
            return $this->belongsTo(KaizenProject::class);
        }


}
