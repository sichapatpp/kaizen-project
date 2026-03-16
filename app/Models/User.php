<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\KaizenProject;
use App\Models\Notification;
use App\Models\ActivityLog;


class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'department',
        'role_id',
        'status',
        'created_at',
        'updated_at'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
        public function kaizenProjects()
        {
            return $this->hasMany(KaizenProject::class);
        }

         public function role()
        {
            return $this->belongsTo(Role::class);
        }
        public function kaizenHistories()
        {
            return $this->hasMany(KaizenHistory::class);
        }

        public function notifications()
        {
            return $this->hasMany(Notification::class);
        }

        public function activityLogs()
        {
            return $this->hasMany(ActivityLog::class);
        }

        public function kaizenFiles()
        {
            return $this->hasMany(KaizenFile::class);
        }
            
}
