<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class Seo extends Model
{
    protected $table = 'seos';
    public $timestamps = false;
    protected $fillable = [
        'seoableId', 'seoableType', 'metaTitle', 'slug', 'metaKeywords', 'metaDescription', 'h1Tag', 'redirect301', 'canonicalLinks', 'metaRobotTag', 'metaRobotFollow'
    ];

    public $rules = [
        'metaTitle' => 'required',
        'slug' => 'required|unique:seos,slug',
        'h1Tag' => 'required|unique:seos,h1Tag',
    ];

    /**
    * Get the error messages for the defined validation rules.
    *
    * @return array
    */
    public function messages()
    {
        return [
            'required' => ':attribute is required',
            'unique' => ':attribute must be unique',
            'required'  => ':attribute is required',
            'numeric'  => ':attribute must be numeric',
        ];
    }

    public function validate($data, $rules = false)
    {
        if (!$rules) {
            $rules = $this->rules;
        }
        // make a new validator object
        $v = Validator::make($data, $rules, $this->messages());
        // return the result
        return $v;
    }


    public static function boot()
    {
        parent::boot();

        static::creating(function($model)
        {
            $model->slug = str_slug($model->slug);
        });

        static::updating(function($model)
        {
            $model->slug = str_slug($model->slug);
        });

        static::deleting(function($model)
        {
        });
    }


    public function room()
    {
        return $this->belongsTo('App\Room', 'seoableId', 'id');
    }

    public function page()
    {
        return $this->belongsTo('App\Page', 'seoableId', 'id');
    }
}
