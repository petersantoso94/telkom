<?php

/*
  |--------------------------------------------------------------------------
  | Application Routes
  |--------------------------------------------------------------------------
  |
  | Here is where you can register all of the routes for an application.
  | It's a breeze. Simply tell Laravel the URIs it should respond to
  | and give it the Closure to execute when that URI is requested.
  |
 */


Route::any('/', array('as' => '/', 'uses' => 'LoginController@showLogin'));
Route::group(array('before' => 'auth'), function() {
    Route::any('showInventory', array('as' => 'showInventory', 'uses' => 'InventoryController@showInventory'));
    Route::any('showInsertInventory', array('as' => 'showInsertInventory', 'uses' => 'InventoryController@showInsertInventory'));
    Route::any('showReturnInventory', array('as' => 'showReturnInventory', 'uses' => 'InventoryController@showReturnInventory'));
    Route::any('showInventoryShipout', array('as' => 'showInventoryShipout', 'uses' => 'InventoryController@showInventoryShipout'));
    Route::any('showWarehouseInventory', array('as' => 'showWarehouseInventory', 'uses' => 'InventoryController@showWarehouseInventory'));
    Route::any('showChange', array('as' => 'showChange', 'uses' => 'InventoryController@showChange'));
    Route::get('showLogout', array('as' => 'showLogout', 'uses' => 'LoginController@showLogout'));

    //ajax
    Route::get('inventoryDataBackup/{start}', array('as' => 'inventoryDataBackup', 'before' => '', 'uses' => 'InventoryController@inventoryDataBackup'));
    Route::post('inventoryDataBackup', array('as' => 'inventoryDataBackup', 'before' => '', 'uses' => 'InventoryController@inventoryDataBackup'));
    Route::get('inventoryDataBackupOut/{start}', array('as' => 'inventoryDataBackupOut', 'before' => '', 'uses' => 'InventoryController@inventoryDataBackupOut'));
    Route::post('inventoryDataBackupOut', array('as' => 'inventoryDataBackupOut', 'before' => '', 'uses' => 'InventoryController@inventoryDataBackupOut'));
    Route::get('inventoryDataBackupWare/{start}', array('as' => 'inventoryDataBackupWare', 'before' => '', 'uses' => 'InventoryController@inventoryDataBackupWare'));
    Route::post('inventoryDataBackupWare', array('as' => 'inventoryDataBackupWare', 'before' => '', 'uses' => 'InventoryController@inventoryDataBackupWare'));
    Route::get('inventoryDataBackupReturn/{start}', array('as' => 'inventoryDataBackupReturn', 'before' => '', 'uses' => 'InventoryController@inventoryDataBackupReturn'));
    Route::post('inventoryDataBackupReturn', array('as' => 'inventoryDataBackupReturn', 'before' => '', 'uses' => 'InventoryController@inventoryDataBackupReturn'));
    Route::get('getSN/{msi}', array('as' => 'getSN', 'before' => '', 'uses' => 'InventoryController@getSN'));
    Route::post('getSN', array('as' => 'getSN', 'before' => '', 'uses' => 'InventoryController@getSN'));
    Route::post('getForm', array('as' => 'getForm', 'before' => '', 'uses' => 'InventoryController@getForm'));
    Route::post('postMissing', array('as' => 'postMissing', 'before' => '', 'uses' => 'InventoryController@postMissing'));
    Route::post('postAvail', array('as' => 'postAvail', 'before' => '', 'uses' => 'InventoryController@postAvail'));
    Route::post('getSubAgent', array('as' => 'getSubAgent', 'before' => '', 'uses' => 'InventoryController@getSubAgent'));
});
