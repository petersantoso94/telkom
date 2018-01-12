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
    Route::any('showDashboard', array('as' => 'showDashboard', 'uses' => 'InventoryController@showDashboard'));
    Route::any('showInventory', array('as' => 'showInventory', 'uses' => 'InventoryController@showInventory'));
    Route::any('showInsertInventory', array('as' => 'showInsertInventory', 'uses' => 'InventoryController@showInsertInventory'));
    Route::any('showConsignment', array('as' => 'showConsignment', 'uses' => 'InventoryController@showConsignment'));
    Route::any('showReturnInventory', array('as' => 'showReturnInventory', 'uses' => 'InventoryController@showReturnInventory'));
    Route::any('showInventoryShipout', array('as' => 'showInventoryShipout', 'uses' => 'InventoryController@showInventoryShipout'));
    Route::any('showWarehouseInventory', array('as' => 'showWarehouseInventory', 'uses' => 'InventoryController@showWarehouseInventory'));
    Route::any('showChange', array('as' => 'showChange', 'uses' => 'InventoryController@showChange'));
    Route::any('showInsertReporting', array('as' => 'showInsertReporting', 'uses' => 'InventoryController@showInsertReporting'));
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
    Route::get('inventoryDataBackupCons/{start}', array('as' => 'inventoryDataBackupCons', 'before' => '', 'uses' => 'InventoryController@inventoryDataBackupCons'));
    Route::post('inventoryDataBackupCons', array('as' => 'inventoryDataBackupCons', 'before' => '', 'uses' => 'InventoryController@inventoryDataBackupCons'));
    Route::get('getSN/{msi}', array('as' => 'getSN', 'before' => '', 'uses' => 'InventoryController@getSN'));
    Route::get('exportExcel/{start}', array('as' => 'exportExcel', 'before' => '', 'uses' => 'InventoryController@exportExcel'));
    Route::post('exportExcel', array('as' => 'exportExcel', 'before' => '', 'uses' => 'InventoryController@exportExcel'));
    Route::post('addInv', array('as' => 'addInv', 'before' => '', 'uses' => 'InventoryController@addInv'));
    Route::any('delInv', array('as' => 'delInv', 'before' => '', 'uses' => 'InventoryController@delInv'));
    Route::post('getSN', array('as' => 'getSN', 'before' => '', 'uses' => 'InventoryController@getSN'));
    Route::post('getForm', array('as' => 'getForm', 'before' => '', 'uses' => 'InventoryController@getForm'));
    Route::post('getShipout', array('as' => 'getShipout', 'before' => '', 'uses' => 'InventoryController@getShipout'));
    Route::post('postMissing', array('as' => 'postMissing', 'before' => '', 'uses' => 'InventoryController@postMissing'));
    Route::post('postConsStat', array('as' => 'postConsStat', 'before' => '', 'uses' => 'InventoryController@postConsStat'));
    Route::post('postNewAgent', array('as' => 'postNewAgent', 'before' => '', 'uses' => 'InventoryController@postNewAgent'));
    Route::post('postFormSeries', array('as' => 'postFormSeries', 'before' => '', 'uses' => 'InventoryController@postFormSeries'));
    Route::post('postWarehouse', array('as' => 'postWarehouse', 'before' => '', 'uses' => 'InventoryController@postWarehouse'));
    Route::get('getFS', array('as' => 'getFS', 'before' => '', 'uses' => 'InventoryController@getFS'));
    Route::post('postFS', array('as' => 'postFS', 'before' => '', 'uses' => 'InventoryController@postFS'));
    Route::any('getPDFShipout', array('as' => 'getPDFShipout', 'before' => '', 'uses' => 'InventoryController@getPDFShipout'));
    Route::any('getPDFReturn', array('as' => 'getPDFReturn', 'before' => '', 'uses' => 'InventoryController@getPDFReturn'));
    Route::any('getPDFCons', array('as' => 'getPDFCons', 'before' => '', 'uses' => 'InventoryController@getPDFCons'));
    Route::any('getPDFInv', array('as' => 'getPDFInv', 'before' => '', 'uses' => 'InventoryController@getPDFInv'));
    Route::post('postAvail', array('as' => 'postAvail', 'before' => '', 'uses' => 'InventoryController@postAvail'));
    Route::post('changeFB', array('as' => 'changeFB', 'before' => '', 'uses' => 'InventoryController@changeFB'));
    Route::post('getSubAgent', array('as' => 'getSubAgent', 'before' => '', 'uses' => 'InventoryController@getSubAgent'));
});
