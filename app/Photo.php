<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class Photo extends Model
{
    protected $table = 'rooms_photos';
    public $timestamps = false;

    public $rules = [
        'photo' => 'dimensions:min_width=300,min_height=300|max:2000',
    ];

    public $messages = [
        'dimensions' => ':attribute dimension minimum of 300x300',
        'max' => ':attribute size must not exceed to 2MB',
    ];

    public function validate($data, $rules = false)
    {
        if (!$rules) {
            $rules = $this->rules;
        }
        // make a new validator object
        $v = Validator::make($data, $rules, $this->messages);
        // return the result
        return $v;
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function($model)
        {
            
        });

        static::updating(function($model)
        {
            //
        });

        static::deleting(function($photo)
        {
            if ($photo->file) {
                foreach($photo->file as $key => $file) {
                    \File::delete(public_path() . $file);
                }
            }
        });
    }


    public function getFileAttribute($value) {
        if (is_array($value)) {
            return $value;
        }
        return json_decode($value, true);
    }

    public function setFileAttribute($value)
    {
        $this->attributes['file'] = json_encode($value);
    }
}
