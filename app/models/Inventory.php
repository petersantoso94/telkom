<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class Inventory extends Eloquent implements UserInterface, RemindableInterface {

    use UserTrait,
        RemindableTrait;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'm_inventory';
    protected $timestamp = true;
    protected $primaryKey = 'SerialNumber';

    const CREATED_AT = 'dtRecord';
    const UPDATED_AT = 'dtModified';

    public static function showInventory() {
        return Inventory::all();
    }

    public function history() {
        return $this->hasMany('History', 'SerialNumber', 'SN');
    }
    
    public function laststatus() {
        return $this->hasMany('History', 'LastStatusID', 'ID');
    }
}
