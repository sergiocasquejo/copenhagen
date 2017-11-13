<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * Get the customer record associated with the user.
     */
    public function customer()
    {
        return $this->belongsTo('App\Customer');
    }

    public function pages()
    {
        return $this->hasMany('App\Page');
    }

    public $rules = [
        'username' => 'required|min:5|unique:users,username',
        'password2' => 'same:password',
        'email' => 'required|unique:users,email',
        'password' => 'required|min:8',
    ];

    public $messages = [
        'required' => ':attribute is required',
        'unique' => ':attribute must be unique',
        'required'  => ':attribute is required',
        'numeric'  => ':attribute must be numeric',
        'min' => ':attribute minimum of :min character',
        'same'    => 'The :attribute and :other must match.',
    ];

    public function validate($data, $rules = false)
    {
        if (!$rules) {
            $rules = $this->rules;
        }
        // make a new validator object
        $v = \Validator::make($data, $rules, $this->messages);
        // return the result
        return $v;
    }
}
