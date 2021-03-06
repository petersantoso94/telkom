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
    Route::get('showLogout', array('as' => 'showLogout', 'uses' => 'LoginController@showLogout'));
//ajax
    Route::get('inventoryDataBackup/{start}', array('as' => 'inventoryDataBackup', 'before' => '', 'uses' => 'InventoryController@inventoryDataBackup'));
    Route::post('inventoryDataBackup', array('as' => 'inventoryDataBackup', 'before' => '', 'uses' => 'InventoryController@inventoryDataBackup'));
    Route::get('getFS', array('as' => 'getFS', 'before' => '', 'uses' => 'InventoryController@getFS'));
    Route::any('getIVR', array('as' => 'getIVR', 'before' => '', 'uses' => 'InventoryController@getIVR'));
    Route::any('getProductive', array('as' => 'getProductive', 'before' => '', 'uses' => 'InventoryController@getProductive'));
    Route::any('getRemaining', array('as' => 'getRemaining', 'before' => '', 'uses' => 'InventoryController@getRemaining'));
    Route::any('getRemainingWarehouse', array('as' => 'getRemainingWarehouse', 'before' => '', 'uses' => 'InventoryController@getRemainingWarehouse'));
    Route::any('getSumService', array('as' => 'getSumService', 'before' => '', 'uses' => 'InventoryController@getSumService'));
    Route::any('getPayload', array('as' => 'getPayload', 'before' => '', 'uses' => 'InventoryController@getPayload'));
    Route::any('getInternetVsNon', array('as' => 'getInternetVsNon', 'before' => '', 'uses' => 'InventoryController@getInternetVsNon'));
    Route::any('getPayloadPerUser', array('as' => 'getPayloadPerUser', 'before' => '', 'uses' => 'InventoryController@getPayloadPerUser'));
    Route::any('getVouchersTopUp', array('as' => 'getVouchersTopUp', 'before' => '', 'uses' => 'InventoryController@getVouchersTopUp'));
    Route::any('geteVouchersTopUp', array('as' => 'geteVouchersTopUp', 'before' => '', 'uses' => 'InventoryController@geteVouchersTopUp'));
    Route::any('getVouchers300TopUp', array('as' => 'getVouchers300TopUp', 'before' => '', 'uses' => 'InventoryController@getVouchers300TopUp'));
    Route::any('getMSISDNTopUp', array('as' => 'getMSISDNTopUp', 'before' => '', 'uses' => 'InventoryController@getMSISDNTopUp'));
    Route::any('getSubsriberTopUp', array('as' => 'getSubsriberTopUp', 'before' => '', 'uses' => 'InventoryController@getSubsriberTopUp'));
    Route::any('getCHURN', array('as' => 'getCHURN', 'before' => '', 'uses' => 'InventoryController@getCHURN'));
    Route::any('getCHURN2', array('as' => 'getCHURN2', 'before' => '', 'uses' => 'InventoryController@getCHURN2'));
    Route::any('getSubsriber', array('as' => 'getSubsriber', 'before' => '', 'uses' => 'InventoryController@getSubsriber'));
    Route::any('getChurnDetail', array('as' => 'getChurnDetail', 'before' => '', 'uses' => 'InventoryController@getChurnDetail'));
    Route::any('getChannel', array('as' => 'getChannel', 'before' => '', 'uses' => 'InventoryController@getChannel'));
    Route::any('getChannelShipoutSim', array('as' => 'getChannelShipoutSim', 'before' => '', 'uses' => 'InventoryController@getChannelShipoutSim'));
    Route::any('getChannelShipoutVoc', array('as' => 'getChannelShipoutVoc', 'before' => '', 'uses' => 'InventoryController@getChannelShipoutVoc'));
    Route::any('getChannelChurn', array('as' => 'getChannelChurn', 'before' => '', 'uses' => 'InventoryController@getChannelChurn'));
    Route::any('getShipoutSim', array('as' => 'getShipoutSim', 'before' => '', 'uses' => 'InventoryController@getShipoutSim'));
    Route::any('getShipoutVoc', array('as' => 'getShipoutVoc', 'before' => '', 'uses' => 'InventoryController@getShipoutVoc'));
    Route::post('getSubAgent', array('as' => 'getSubAgent', 'before' => '', 'uses' => 'InventoryController@getSubAgent'));
    Route::post('delST', array('as' => 'delST', 'before' => '', 'uses' => 'InventoryController@delST'));
    Route::post('postWarehouse', array('as' => 'postWarehouse', 'before' => '', 'uses' => 'InventoryController@postWarehouse'));
    Route::post('postFormSeries', array('as' => 'postFormSeries', 'before' => '', 'uses' => 'InventoryController@postFormSeries'));

//Admin role
    Route::group(array('before' => 'admin'), function() {

//Admin role
        Route::group(array('before' => 'superadmin'), function() {
            Route::any('showResetReporting', array('as' => 'showResetReporting', 'uses' => 'InventoryController@showResetReporting'));
            Route::any('showAddAdmin', array('as' => 'showAddAdmin', 'uses' => 'InventoryController@showAddAdmin'));
        });
        Route::any('showInventory', array('as' => 'showInventory', 'uses' => 'InventoryController@showInventory'));
        Route::any('showInsertInventory', array('as' => 'showInsertInventory', 'uses' => 'InventoryController@showInsertInventory'));
        Route::any('showConsignment', array('as' => 'showConsignment', 'uses' => 'InventoryController@showConsignment'));
        Route::any('showReturnInventory', array('as' => 'showReturnInventory', 'uses' => 'InventoryController@showReturnInventory'));
        Route::any('showInventoryShipout', array('as' => 'showInventoryShipout', 'uses' => 'InventoryController@showInventoryShipout'));
        Route::any('showWarehouseInventory', array('as' => 'showWarehouseInventory', 'uses' => 'InventoryController@showWarehouseInventory'));
        Route::any('showChange', array('as' => 'showChange', 'uses' => 'InventoryController@showChange'));
        Route::any('showUncat', array('as' => 'showUncat', 'uses' => 'InventoryController@showUncat'));
        Route::any('showInsertReporting', array('as' => 'showInsertReporting', 'uses' => 'InventoryController@showInsertReporting'));
//        Route::any('showResetReporting', array('as' => 'showResetReporting', 'uses' => 'InventoryController@showResetReporting'));
//ajax
//        Route::get('inventoryDataBackup/{start}', array('as' => 'inventoryDataBackup', 'before' => '', 'uses' => 'InventoryController@inventoryDataBackup'));
//        Route::post('inventoryDataBackup', array('as' => 'inventoryDataBackup', 'before' => '', 'uses' => 'InventoryController@inventoryDataBackup'));
        Route::get('inventoryDataBackupOut/{start}', array('as' => 'inventoryDataBackupOut', 'before' => '', 'uses' => 'InventoryController@inventoryDataBackupOut'));
        Route::post('inventoryDataBackupOut', array('as' => 'inventoryDataBackupOut', 'before' => '', 'uses' => 'InventoryController@inventoryDataBackupOut'));
        Route::get('inventoryDataBackupWare/{start}', array('as' => 'inventoryDataBackupWare', 'before' => '', 'uses' => 'InventoryController@inventoryDataBackupWare'));
        Route::post('inventoryDataBackupWare', array('as' => 'inventoryDataBackupWare', 'before' => '', 'uses' => 'InventoryController@inventoryDataBackupWare'));
        Route::get('inventoryDataBackupReturn/{start}', array('as' => 'inventoryDataBackupReturn', 'before' => '', 'uses' => 'InventoryController@inventoryDataBackupReturn'));
        Route::post('inventoryDataBackupReturn', array('as' => 'inventoryDataBackupReturn', 'before' => '', 'uses' => 'InventoryController@inventoryDataBackupReturn'));
        Route::get('inventoryDataBackupCons/{start}', array('as' => 'inventoryDataBackupCons', 'before' => '', 'uses' => 'InventoryController@inventoryDataBackupCons'));
        Route::post('inventoryDataBackupCons', array('as' => 'inventoryDataBackupCons', 'before' => '', 'uses' => 'InventoryController@inventoryDataBackupCons'));
        Route::any('inventoryDataBackupDashboard', array('as' => 'inventoryDataBackupDashboard', 'before' => '', 'uses' => 'InventoryController@inventoryDataBackupDashboard'));
        Route::any('inventoryDataBackupUncat', array('as' => 'inventoryDataBackupUncat', 'before' => '', 'uses' => 'InventoryController@inventoryDataBackupUncat'));
        Route::any('inventoryDataBackupAnomalies', array('as' => 'inventoryDataBackupAnomalies', 'before' => '', 'uses' => 'InventoryController@inventoryDataBackupAnomalies'));
        Route::get('getSN/{msi}', array('as' => 'getSN', 'before' => '', 'uses' => 'InventoryController@getSN'));
        Route::get('exportExcel/{start}', array('as' => 'exportExcel', 'before' => '', 'uses' => 'InventoryController@exportExcel'));
        Route::post('exportExcel', array('as' => 'exportExcel', 'before' => '', 'uses' => 'InventoryController@exportExcel'));
        Route::post('exportExcelDashboard', array('as' => 'exportExcelDashboard', 'before' => '', 'uses' => 'InventoryController@exportExcelDashboard'));
        Route::any('exportExcelShipoutDashboard', array('as' => 'exportExcelShipoutDashboard', 'before' => '', 'uses' => 'InventoryController@exportExcelShipoutDashboard'));
        Route::any('exportExcelShipinDashboard', array('as' => 'exportExcelShipinDashboard', 'before' => '', 'uses' => 'InventoryController@exportExcelShipinDashboard'));
        Route::any('exportExcelUsageDashboard', array('as' => 'exportExcelUsageDashboard', 'before' => '', 'uses' => 'InventoryController@exportExcelUsageDashboard'));
        Route::any('exportExcelUserDashboard', array('as' => 'exportExcelUserDashboard', 'before' => '', 'uses' => 'InventoryController@exportExcelUserDashboard'));
        Route::any('exportExcelSubAgentDashboard', array('as' => 'exportExcelSubAgentDashboard', 'before' => '', 'uses' => 'InventoryController@exportExcelSubAgentDashboard'));
        Route::any('exportExcelWeeklyDashboard', array('as' => 'exportExcelWeeklyDashboard', 'before' => '', 'uses' => 'InventoryController@exportExcelWeeklyDashboard'));
        Route::post('exportExcelSIM1Dashboard', array('as' => 'exportExcelSIM1Dashboard', 'before' => '', 'uses' => 'InventoryController@exportExcelSIM1Dashboard'));
        Route::post('addInv', array('as' => 'addInv', 'before' => '', 'uses' => 'InventoryController@addInv'));
        Route::any('delInv', array('as' => 'delInv', 'before' => '', 'uses' => 'InventoryController@delInv'));
        Route::post('getSN', array('as' => 'getSN', 'before' => '', 'uses' => 'InventoryController@getSN'));
        Route::post('getForm', array('as' => 'getForm', 'before' => '', 'uses' => 'InventoryController@getForm'));
        Route::post('getShipout', array('as' => 'getShipout', 'before' => '', 'uses' => 'InventoryController@getShipout'));
        Route::post('postMissing', array('as' => 'postMissing', 'before' => '', 'uses' => 'InventoryController@postMissing'));
        Route::post('postShipin', array('as' => 'postShipin', 'before' => '', 'uses' => 'InventoryController@postShipin'));
        Route::post('postSemuaSN', array('as' => 'postSemuaSN', 'before' => '', 'uses' => 'InventoryController@postSemuaSN'));
        Route::any('postRemark', array('as' => 'postRemark', 'before' => '', 'uses' => 'InventoryController@postRemark'));
        Route::post('postConsStat', array('as' => 'postConsStat', 'before' => '', 'uses' => 'InventoryController@postConsStat'));
        Route::post('postNewAgent', array('as' => 'postNewAgent', 'before' => '', 'uses' => 'InventoryController@postNewAgent'));
        Route::post('postNewWh', array('as' => 'postNewWh', 'before' => '', 'uses' => 'InventoryController@postNewWh'));
//        Route::post('postFormSeries', array('as' => 'postFormSeries', 'before' => '', 'uses' => 'InventoryController@postFormSeries'));
//        Route::post('postWarehouse', array('as' => 'postWarehouse', 'before' => '', 'uses' => 'InventoryController@postWarehouse'));
        Route::any('postDashboard', array('as' => 'postDashboard', 'before' => '', 'uses' => 'InventoryController@postDashboard'));
        Route::any('postShipoutDashboard', array('as' => 'postShipoutDashboard', 'before' => '', 'uses' => 'InventoryController@postShipoutDashboard'));
        Route::any('postUsageDashboard', array('as' => 'postUsageDashboard', 'before' => '', 'uses' => 'InventoryController@postUsageDashboard'));
        Route::any('postShipinDashboard', array('as' => 'postShipinDashboard', 'before' => '', 'uses' => 'InventoryController@postShipinDashboard'));
        Route::post('postST', array('as' => 'postST', 'before' => '', 'uses' => 'InventoryController@postST'));
//        Route::post('delST', array('as' => 'delST', 'before' => '', 'uses' => 'InventoryController@delST'));
//        Route::get('getFS', array('as' => 'getFS', 'before' => '', 'uses' => 'InventoryController@getFS'));
//        Route::any('getIVR', array('as' => 'getIVR', 'before' => '', 'uses' => 'InventoryController@getIVR'));
//        Route::any('getProductive', array('as' => 'getProductive', 'before' => '', 'uses' => 'InventoryController@getProductive'));
//        Route::any('getSumService', array('as' => 'getSumService', 'before' => '', 'uses' => 'InventoryController@getSumService'));
//        Route::any('getPayload', array('as' => 'getPayload', 'before' => '', 'uses' => 'InventoryController@getPayload'));
//        Route::any('getInternetVsNon', array('as' => 'getInternetVsNon', 'before' => '', 'uses' => 'InventoryController@getInternetVsNon'));
//        Route::any('getPayloadPerUser', array('as' => 'getPayloadPerUser', 'before' => '', 'uses' => 'InventoryController@getPayloadPerUser'));
//        Route::any('getVouchersTopUp', array('as' => 'getVouchersTopUp', 'before' => '', 'uses' => 'InventoryController@getVouchersTopUp'));
//        Route::any('geteVouchersTopUp', array('as' => 'geteVouchersTopUp', 'before' => '', 'uses' => 'InventoryController@geteVouchersTopUp'));
//        Route::any('getVouchers300TopUp', array('as' => 'getVouchers300TopUp', 'before' => '', 'uses' => 'InventoryController@getVouchers300TopUp'));
//        Route::any('getMSISDNTopUp', array('as' => 'getMSISDNTopUp', 'before' => '', 'uses' => 'InventoryController@getMSISDNTopUp'));
//        Route::any('getSubsriberTopUp', array('as' => 'getSubsriberTopUp', 'before' => '', 'uses' => 'InventoryController@getSubsriberTopUp'));
//        Route::any('getCHURN', array('as' => 'getCHURN', 'before' => '', 'uses' => 'InventoryController@getCHURN'));
//        Route::any('getCHURN2', array('as' => 'getCHURN2', 'before' => '', 'uses' => 'InventoryController@getCHURN2'));
//        Route::any('getSubsriber', array('as' => 'getSubsriber', 'before' => '', 'uses' => 'InventoryController@getSubsriber'));
//        Route::any('getChurnDetail', array('as' => 'getChurnDetail', 'before' => '', 'uses' => 'InventoryController@getChurnDetail'));
//        Route::any('getChannel', array('as' => 'getChannel', 'before' => '', 'uses' => 'InventoryController@getChannel'));
//        Route::any('getChannelChurn', array('as' => 'getChannelChurn', 'before' => '', 'uses' => 'InventoryController@getChannelChurn'));
        Route::any('getPDFShipout', array('as' => 'getPDFShipout', 'before' => '', 'uses' => 'InventoryController@getPDFShipout'));
        Route::any('getPDFReturn', array('as' => 'getPDFReturn', 'before' => '', 'uses' => 'InventoryController@getPDFReturn'));
        Route::any('getPDFCons', array('as' => 'getPDFCons', 'before' => '', 'uses' => 'InventoryController@getPDFCons'));
        Route::any('getPDFInv', array('as' => 'getPDFInv', 'before' => '', 'uses' => 'InventoryController@getPDFInv'));
//        Route::post('getSubAgent', array('as' => 'getSubAgent', 'before' => '', 'uses' => 'InventoryController@getSubAgent'));
        Route::post('postFS', array('as' => 'postFS', 'before' => '', 'uses' => 'InventoryController@postFS'));
        Route::post('postAvail', array('as' => 'postAvail', 'before' => '', 'uses' => 'InventoryController@postAvail'));
        Route::post('changeFB', array('as' => 'changeFB', 'before' => '', 'uses' => 'InventoryController@changeFB'));
//filter user
        Route::any('postUserResetFilter', array('as' => 'postUserResetFilter', 'before' => '', 'uses' => 'InventoryController@postUserResetFilter'));
        Route::any('postUserFilterActive', array('as' => 'postUserFilterActive', 'before' => '', 'uses' => 'InventoryController@postUserFilterActive'));
        Route::any('postUserFilterv300', array('as' => 'postUserFilterv300', 'before' => '', 'uses' => 'InventoryController@postUserFilterv300'));
        Route::any('postUserFilterv100', array('as' => 'postUserFilterv100', 'before' => '', 'uses' => 'InventoryController@postUserFilterv100'));
        Route::any('postUserFilterService', array('as' => 'postUserFilterService', 'before' => '', 'uses' => 'InventoryController@postUserFilterService'));
    });
});
