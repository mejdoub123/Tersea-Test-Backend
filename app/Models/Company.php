<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Company extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function employees():HasMany {
        return $this->hasMany(User::class);
    }
    public function invitations():HasMany {
        return $this->hasMany(Invitations::class);
    }
    public function admin():BelongsTo {
        return $this->belongsTo(User::class);
    }
}
