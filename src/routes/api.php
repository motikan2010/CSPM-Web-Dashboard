<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AccountController;
use App\Http\Controllers\Api\AuthController;


Route::middleware(['auth:sanctum'])->group(function (){

    Route::prefix('user')->group(function () {
        Route::get('', function (Request $request) {
            return $request->user();
        });
    });

    // 検査対象クラウド
    Route::prefix('cloud')->group(function () {
        Route::get('/list', [App\Http\Controllers\Api\CloudController::class, 'list'])->name('cloud.list');

        // 検査対象の詳細
        Route::get('/detail', [App\Http\Controllers\Api\CloudController::class, 'detail'])->name('cloud.detail');

        // 検査対象の登録
        Route::post('/new', [App\Http\Controllers\Api\CloudController::class, 'new'])->name('cloud.new');

        // 検査対象を削除
        Route::delete('/', [App\Http\Controllers\Api\CloudController::class, 'delete'])->name('cloud.delete');
    });

    // CSPM
    Route::prefix('cspm')->group(function () {
        Route::get('status', [App\Http\Controllers\Api\CspmController::class, 'status'])->name('cspm.status');
        Route::get('result', [App\Http\Controllers\Api\CspmController::class, 'result'])->name('cspm.result');
        Route::get('all-result', [App\Http\Controllers\Api\CspmController::class, 'allResult'])->name('cspm.allResult');

        // 検査を実行
        Route::post('/run', [App\Http\Controllers\Api\CspmController::class, 'run'])->name('cspm.run');
    });

    // ユーザ
    Route::prefix('account')->group(function () {
        Route::get('', [App\Http\Controllers\Api\AccountController::class, 'detail'])->name('account.detail');
        Route::put('', [App\Http\Controllers\Api\AccountController::class, 'edit'])->name('account.edit');
        Route::put('password', [App\Http\Controllers\Api\AccountController::class, 'changePassword'])->name('account.changePassword');
    });

});

// ユーザー登録
Route::post('/register', [AccountController::class, 'register']);

// ログイン
Route::post('/login', [AuthController::class, 'login']);

// ログアウト
Route::post('/logout', [AuthController::class, 'logout']);
