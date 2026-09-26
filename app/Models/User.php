<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $primaryKey = 'user_id';
    public $incrementing = false;
    protected $keyType = 'string';

    const CREATED_AT = 'user_createdAt';
    const UPDATED_AT = 'user_updatedAt';
    const DELETED_AT = 'user_deletedAt';

    protected $fillable = [
        'user_id', 'user_name', 'user_email', 'user_password', 'user_promptpay_no',
    ];

    protected $hidden = [
        'user_password', 'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'user_password' => 'hashed',
        ];
    }

    public function getAuthPassword()
    {
        return $this->user_password;
    }

    public function getEmailForPasswordReset()
    {
        return $this->user_email;
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->user_id)) {
                $model->user_id = strtoupper(Str::random(8));
            }
        });
    }
}