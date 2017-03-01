<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class Room extends Model
{
     protected $table = 'rooms';
     protected $appends = array('facilities', 'roomRates');
     public $rules = array(
        'name' => 'required|unique:rooms,name',
        'slug' => 'required|unique:rooms,slug',
        'totalRooms' => 'required|numeric',
        // 'minimumRate' => 'required|numeric',
        'totalPerson' => 'required|numeric',
        'location' => 'required',
        'extraBed' => 'required',
        'roomSize' => 'required|numeric',
        'bathrooms' => 'required|numeric',
        'building' => 'required',
    );

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
            
        });

        static::updating(function($model)
        {
            //
        });

        static::deleting(function($model)
        {
            $photos = $model->photos()->get();
            foreach ($photos as $i => $photo) {
                if ($photo->file) {
                    foreach($photo->file as $key => $file) {
                        \File::delete(public_path() . $file);
                    }
                }
            }
        });
    }


     public function photos() {
         return $this->hasMany('App\Photo', 'roomID', 'id');
     }

     public function aminities() {
         return $this->belongsToMany('App\Aminities', 'rooms_aminities', 'roomID', 'aminitiesID');
     }

     public function rates() {
         return $this->belongsToMany('App\Rate', 'room_rates', 'roomID', 'rateID')->withPivot('price', 'rateID', 'isActive');
     }

     public function beds() {
         return $this->hasMany('App\Bed', 'roomID', 'id');
     }

     public function calendar() {
         return $this->hasMany('App\Calendar', 'roomID', 'id');
     }

     public static function lazyLoad() {
         return self::with('photos', 'aminities', 'rates', 'beds');
     }
     

      

    public function getFacilitiesAttribute()
    {
        return $this->aminities()->pluck('id')->all();
    }

    public function getRoomRatesAttribute()
    {
        $roomRates = array();
        return $this->rates()->get()->map(function($item) use($roomRates){
            $roomRates[$item->pivot->rateID] = ['name' => $item->name, 'price' => $item->pivot->price, 'isActive' => $item->pivot->isActive];
            return $roomRates;
        });

        return $roomRates;
    }
    
}
