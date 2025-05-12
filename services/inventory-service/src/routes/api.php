<?php

use App\Http\Controllers\Api\InventoryController;
use Illuminate\Support\Facades\Route;


Route::prefix('v1')->group(function(){
    Route::controller(InventoryController::class)->group(function(){
        Route::get('inventories','all');
    });
});

Route::get('hi',function(){
    return "hi";
});
