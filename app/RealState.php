<?php

namespace App;

use App\Services\Slug;
use Illuminate\Database\Eloquent\Model;

class RealState extends Model
{

    protected $table = 'real_state';

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'content',
        'price',
        'bedrooms',
        'bathrooms',
        'garages',
        'property_area',
        'total_property_area'
    ];

    public function setTitleAttribute($title)
    {
        $this->attributes['title'] = $title;
        $this->attributes['slug'] = (new Slug($title, $this))->createSlug();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'real_state_categories');
    }

    public function photos()
    {
        return $this->hasMany(RealStatePhoto::class);
    }
}
