<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Link extends Model
{
    use HasFactory;

    public $incrementing = false;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'long_url',
        'title',
    ];

    /**
     * The tags that belong to the link.
     */
    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }
}
