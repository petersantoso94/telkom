<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class User extends Eloquent implements UserInterface, RemindableInterface {

    use UserTrait,
        RemindableTrait;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'm_user';
    protected $primaryKey = 'ID';
    
    const CREATED_AT = 'dtRecord';
    const UPDATED_AT = 'dtModified';
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = array('Password', 'Remember_token');
    public function getAuthPassword() {
        return $this->UserPassword;
    }

}
