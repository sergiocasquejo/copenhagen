<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Photo extends Model
{
    protected $table = 'rooms_photos';
    public $timestamps = false;

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
