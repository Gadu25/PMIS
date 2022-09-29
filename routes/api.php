<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\DivisionController;
use App\Http\Controllers\Api\ProgramController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\WorkshopController;
use App\Http\Controllers\Api\TagController;

Route::controller(AuthController::class)->group(function(){
    Route::post('login', 'login');
    Route::post('register', 'register');
});

Route::prefix('/user')->group(function(){
    Route::middleware('auth:sanctum')->get('/', [UserController::class, 'index']);
    Route::middleware('auth:sanctum')->post('/', [UserController::class, 'store']);
    Route::middleware('auth:sanctum')->get('/auth', [UserController::class, 'authUser']);
    Route::middleware('auth:sanctum')->get('/title', [UserController::class, 'getTitles']);
    Route::middleware('auth:sanctum')->put('/{id}', [UserController::class, 'update']);
    Route::middleware('auth:sanctum')->get('/division/{id}', [UserController::class, 'getByDivision']);

    Route::prefix('/notification')->group(function(){
        Route::middleware('auth:sanctum')->put('/{id}', [UserController::class, 'updateNotification']);
    });

    Route::prefix('/role')->group(function(){
        Route::middleware('auth:sanctum')->get('/sidebaritems', [UserController::class, 'getSidebarItems']);
        Route::middleware('auth:sanctum')->post('/', [UserController::class, 'storeSidebarRole']);
        Route::middleware('auth:sanctum')->put('/{id}', [UserController::class, 'updateSidebarRole']);
        Route::middleware('auth:sanctum')->delete('/{id}', [UserController::class, 'destroySidebarRole']);
    });

    Route::prefix('/staff')->group(function(){
        Route::middleware('auth:sanctum')->get('/', [UserController::class, 'getStaffs']);
    });
});

Route::prefix('/division')->group(function(){
    Route::middleware('auth:sanctum')->get('/', [DivisionController::class, 'index']);
});

Route::prefix('/program')->group(function(){
    Route::middleware('auth:sanctum')->get('/', [ProgramController::class, 'index']);
});

Route::prefix('/project')->group(function(){
    Route::middleware('auth:sanctum')->get('/', [ProjectController::class, 'index']);
    Route::middleware('auth:sanctum')->get('/{id}', [ProjectController::class, 'show']);
    Route::middleware('auth:sanctum')->get('/sort/{selected}', [ProjectController::class, 'showSortedProjects']);
    Route::middleware('auth:sanctum')->post('/', [ProjectController::class, 'store']);
    Route::middleware('auth:sanctum')->post('/multiple', [ProjectController::class, 'storeMultiple']);
    Route::middleware('auth:sanctum')->put('/{id}', [ProjectController::class, 'update']);
    Route::middleware('auth:sanctum')->put('/portfolio/{id}', [ProjectController::class, 'updatePortfolio']);
    Route::middleware('auth:sanctum')->delete('/{id}', [ProjectController::class, 'destroy']);
});

Route::prefix('/workshop')->group(function(){
    Route::prefix('/annex-e')->group(function(){
        Route::middleware('auth:sanctum')->post('/display', [WorkshopController::class, 'getAnnexE']);
        Route::middleware('auth:sanctum')->post('/', [WorkshopController::class, 'storeAnnexE']);
        Route::middleware('auth:sanctum')->put('/other/{id}', [WorkshopController::class, 'updateOtherIndicatorDetails']);
        Route::middleware('auth:sanctum')->put('/{id}', [WorkshopController::class, 'updateAnnexE']);
        Route::middleware('auth:sanctum')->delete('/{id}', [WorkshopController::class, 'destroyAnnexE']);
        Route::middleware('auth:sanctum')->get('/{id}', [WorkshopController::class, 'showAnnexE']);
    });

    Route::prefix('/annex-f')->group(function(){
        Route::middleware('auth:sanctum')->post('/display', [WorkshopController::class, 'getAnnexF']);
        Route::middleware('auth:sanctum')->post('/', [WorkshopController::class, 'storeAnnexF']);
        Route::middleware('auth:sanctum')->put('/{id}', [WorkshopController::class, 'updateAnnexF']);
        Route::middleware('auth:sanctum')->delete('/{id}', [WorkshopController::class, 'destroyAnnexF']);
        Route::middleware('auth:sanctum')->get('/{id}', [WorkshopController::class, 'showAnnexF']);
    });

    Route::prefix('/annex-one')->group(function(){
        Route::middleware('auth:sanctum')->post('/', [WorkshopController::class, 'storeAnnexOne']);
        Route::middleware('auth:sanctum')->put('/{id}', [WorkshopController::class, 'updateAnnexOne']);
        Route::middleware('auth:sanctum')->delete('/{id}', [WorkshopController::class, 'destroyAnnexOne']);
        Route::middleware('auth:sanctum')->get('/{workshopId}', [WorkshopController::class, 'getAnnexOne']);
        Route::middleware('auth:sanctum')->post('/publish/{workshopId}', [WorkshopController::class, 'publishAnnexOneProjects']);
    });

    Route::prefix('/common-indicator')->group(function(){
        Route::middleware('auth:sanctum')->post('/', [WorkshopController::class, 'storeCommonIndicator']);
        Route::middleware('auth:sanctum')->put('/{id}', [WorkshopController::class, 'updateCommonIndicator']);
        Route::middleware('auth:sanctum')->delete('/{id}', [WorkshopController::class, 'destroyCommonIndicator']);
        Route::middleware('auth:sanctum')->get('/{workshopId}', [WorkshopController::class, 'getCommonIndicator']);
    });

    Route::middleware('auth:sanctum')->get('/options/{workshopId}/{annex}', [WorkshopController::class, 'getOptions']);
    Route::middleware('auth:sanctum')->get('/', [WorkshopController::class, 'index']);
    Route::middleware('auth:sanctum')->post('/', [WorkshopController::class, 'store']);
    Route::middleware('auth:sanctum')->get('/{id}', [WorkshopController::class, 'show']);
    Route::middleware('auth:sanctum')->put('/{id}', [WorkshopController::class, 'update']);
    Route::middleware('auth:sanctum')->delete('/{id}', [WorkshopController::class, 'destroy']);
});

Route::prefix('/tag')->group(function(){
    Route::middleware('auth:sanctum')->get('/', [TagController::class, 'index']);
    Route::middleware('auth:sanctum')->post('/', [TagController::class, 'store']);
    Route::middleware('auth:sanctum')->get('/{type}', [TagController::class, 'getTagsByType']);
    Route::middleware('auth:sanctum')->put('/{id}', [TagController::class, 'update']);
    Route::middleware('auth:sanctum')->delete('/{id}', [TagController::class, 'destroy']);
});

Route::get('/export/{workshopId}/{annex}/{status}/{type}/{id1}/{id2}/{id}', [WorkshopController::class, 'exportxlsx']);