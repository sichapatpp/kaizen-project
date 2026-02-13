<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KaizenIndiator extends Model
{
    
       protected $fillable = [
       'kaizen_project_id',
       'indicator_neme', 
       'before_value', 
       'after_value', 
       'unit', 
       'created_at'];

       public function Kaizenproject()
        {
            return $this->belongsTo(KaizenProject::class);
        }
}
