<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;
    protected $fillable = ['id', 'title', 'body', 'date', 'url'];
    protected $with = ['type', 'creator'];

    // 1st param passed by laravel
    public function scopeFilter($query, array $args)  
    {  
        $query->when($args['search'] ?? false, function($query, $search){
            $query
                ->where('title', 'like', '%' . $search . '%')
                ->orWhere('body', 'like', '%' . $search . '%');
            });
        $query->when($args['type'] ?? false, function($query, $type) {
            $query->whereHas('type', function($query) {
                $query->where('slug', $type);
            });
        });
    }

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
