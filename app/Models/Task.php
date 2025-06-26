<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'due_date',
        'priority',
        'completed',
        'file_path',
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function scopeFilter($query, array $filters)
{
    $query->when($filters['priority'] ?? false, fn($query, $priority) => 
        $query->where('priority', $priority)
    );
    
    $query->when($filters['search'] ?? false, fn($query, $search) => 
        $query->where(fn($query) => 
            $query->where('title', 'like', '%'.$search.'%')
                  ->orWhere('description', 'like', '%'.$search.'%')
        )
    );
}
}