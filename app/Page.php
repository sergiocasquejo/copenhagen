<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class Page extends Model
{
    protected $table = 'pages';
    
	public function user() {
		return $this->belongsTo('App\User', 'author');
	}

	public $rules = array(
        'title' => 'required'
    );

    protected $appends = [
        'slug',
        'author_name'];

    public function getAuthorNameAttribute()
    {
        return $this->user->username;
    }

    public function getSlugAttribute()
    {
        return $this->seo->slug;
    }



    public function validate($data, $rules = false)
    {
        if (!$rules) {
            $rules = $this->rules;
        }
        // make a new validator object
        $v = Validator::make($data, $rules);
        // return the result
        return $v;
    }

    public function seo()
    {
        return $this->hasOne('App\Seo', 'seoableId','id')
                    ->where('seoableType', 'page');
    }
}
