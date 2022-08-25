<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $with = ['type', 'creator'];

    // 1st param passed by laravel
    public function scopeFilter($query, array $args)  
    {  
           $query->when($args['search'] ?? false, fn($query, $search) =>
            $query->where(fn($query) =>
                $query->where('title', 'like', '%' . $search . '%')
                    ->orWhere('body', 'like', '%' . $search . '%')
            )
        );
         
        $query->when($args['type'] ?? false, fn($query, $type) =>
                $query->whereHas('type', fn ($query) =>
                    $query->where('slug', $type)
                )
                );
                                
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
