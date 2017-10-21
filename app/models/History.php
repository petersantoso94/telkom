<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class History extends Eloquent implements UserInterface, RemindableInterface {

    use UserTrait,
        RemindableTrait;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'm_historymovement';
    protected $timestamp = true;
    protected $primaryKey = 'ID';

    const CREATED_AT = 'dtRecord';
    const UPDATED_AT = 'dtModified';

    public static function showHistory() {
        return History::all();
    }

    public function inventory() {
        return $this->belongsTo('Inventory', 'SN', 'SerialNumber');
    }
}
