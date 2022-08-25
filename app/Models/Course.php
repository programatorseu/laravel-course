<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;
    protected $fillable = ['id', 'title', 'body', 'date', 'url'];

    protected $with = ['type', 'creator'];

    public function getRouteKeyName() 
    {
        return 'url';
    }

    public function type() 
    {
        return $this->belongsTo(Type::class);
    }
    public function creator() 
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
