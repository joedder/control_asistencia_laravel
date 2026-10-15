<?php

use App\Http\Middleware\HasAccessAdmin;
use App\Http\Middleware\Admin\HandleInertiaAdminRequests;
use Inertia\Inertia;

Route::group([
    'namespace' => 'App\Http\Controllers\Admin',
    'prefix' => config('admin.prefix'),
    'middleware' => ['auth', HasAccessAdmin::class, HandleInertiaAdminRequests::class],
    'as' => 'admin.',
], function () {
    Route::get('/', function () {
        return Inertia::render('Admin/Dashboard');
    })->name('dashboard');    
    Route::resource('user', 'UserController');
    Route::middleware('permission:teacher create')->group(function () {
        Route::resource('teacher', 'TeacherController')->only(['create', 'store']);
    });
    Route::middleware('permission:teacher list')->group(function () {
        Route::resource('teacher', 'TeacherController')->only(['index', 'show']);
    });
    Route::middleware('permission:teacher edit')->group(function () {
        Route::resource('teacher', 'TeacherController')->only(['edit', 'update']);
    });
    Route::delete('teacher/{teacher}', 'TeacherController@destroy')->middleware('permission:teacher delete')->name('teacher.destroy');

    Route::middleware('permission:category-group create')->group(function () {
        Route::resource('category-group', 'CategoryGroupController')->only(['create', 'store']);
    });
    Route::middleware('permission:category-group list')->group(function () {
        Route::resource('category-group', 'CategoryGroupController')->only(['index', 'show']);
    });
    Route::middleware('permission:category-group edit')->group(function () {
        Route::resource('category-group', 'CategoryGroupController')->only(['edit', 'update']);
    });
    Route::delete('category-group/{category_group}', 'CategoryGroupController@destroy')->middleware('permission:category-group delete')->name('category-group.destroy');

    Route::middleware('permission:level create')->group(function () {
        Route::resource('level', 'LevelController')->only(['create', 'store']);
    });
    Route::middleware('permission:level list')->group(function () {
        Route::resource('level', 'LevelController')->only(['index', 'show']);
    });
    Route::middleware('permission:level edit')->group(function () {
        Route::resource('level', 'LevelController')->only(['edit', 'update']);
    });
    Route::delete('level/{level}', 'LevelController@destroy')->middleware('permission:level delete')->name('level.destroy');

    Route::middleware('permission:group create')->group(function () {
        Route::resource('group', 'GroupController')->only(['create', 'store']);
    });
    Route::middleware('permission:group list')->group(function () {
        Route::resource('group', 'GroupController')->only(['index', 'show']);
    });
    Route::middleware('permission:group edit')->group(function () {
        Route::resource('group', 'GroupController')->only(['edit', 'update']);
    });
    Route::delete('group/{group}', 'GroupController@destroy')->middleware('permission:group delete')->name('group.destroy');

    Route::middleware('permission:student create')->group(function () {
        Route::resource('student', 'StudentController')->only(['create', 'store']);
    });
    Route::middleware('permission:student list')->group(function () {
        Route::resource('student', 'StudentController')->only(['index', 'show']);
    });
    Route::middleware('permission:student edit')->group(function () {
        Route::resource('student', 'StudentController')->only(['edit', 'update']);
    });
    Route::delete('student/{student}', 'StudentController@destroy')->middleware('permission:student delete')->name('student.destroy');

    Route::get('attending/export-pdf', 'AttendingController@exportReportPdf')->middleware('permission:attending export')->name('attending.export.pdf');
    Route::get('attending/{attending}/pdf', 'AttendingController@exportSinglePdf')->middleware('permission:attending export single')->name('attending.pdf');
    Route::middleware('permission:attending create')->group(function () {
        Route::resource('attending', 'AttendingController')->only(['create', 'store']);
    });
    Route::middleware('permission:attending list')->group(function () {
        Route::resource('attending', 'AttendingController')->only(['index', 'show']);
    });
    Route::middleware('permission:attending edit')->group(function () {
        Route::resource('attending', 'AttendingController')->only(['edit', 'update']);
    });
    Route::delete('attending/{attending}', 'AttendingController@destroy')->middleware('permission:attending delete')->name('attending.destroy');
    Route::resource('role', 'RoleController');
    Route::resource('permission', 'PermissionController');
    Route::middleware('permission:history-movement create')->group(function () {
        Route::resource('history-movement', 'HistoryMovementController')->only(['create', 'store']);
    });
    Route::middleware('permission:history-movement list')->group(function () {
        Route::resource('history-movement', 'HistoryMovementController')->only(['index', 'show']);
    });
    Route::middleware('permission:history-movement edit')->group(function () {
        Route::resource('history-movement', 'HistoryMovementController')->only(['edit', 'update']);
    });
    Route::delete('history-movement/{history_movement}', 'HistoryMovementController@destroy')->middleware('permission:history-movement delete')->name('history-movement.destroy');
    Route::resource('menu', 'MenuController')->except([
        'show',
    ]);
    Route::resource('menu.item', 'MenuItemController')->except([
        'show',
    ]);

    Route::get('edit-account-info', 'UserController@accountInfo')->name('account.info');
    Route::post('edit-account-info', 'UserController@accountInfoStore')->name('account.info.store');
    Route::post('change-password', 'UserController@changePasswordStore')->name('account.password.store');
});
