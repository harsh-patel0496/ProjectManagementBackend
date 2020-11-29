<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::group([

    'middleware' => ['api']

], function ($router) {

    Route::group(['prefix' => 'users'], function(){
        Route::get('/assembly','UserController@assembly');
        Route::post('/login','UserController@login');
        Route::post('/signup','UserController@signup');
        Route::post('/resetPassword','UserController@resetPassword');
        Route::post('/getEmailAddress','UserController@getEmailAddress');
        Route::post('/changePassword','UserController@changePassword');
    });
    Route::apiResource('users','UserController');
    

    Route::apiResource('clients','ClientController');
    Route::apiResource('employees','EmployeeController');
    Route::apiResource('companyTypes','CompanyTypeController');
    
    Route::group(['prefix' => 'teams'], function(){
        Route::get('/getListOfEmployee','TeamController@getListOfEmployee');
        Route::post('/validateTeamName','TeamController@validateTeamName');
        Route::post('/detachEmployee','TeamController@detachEmployee');
    });
    Route::apiResource('teams','TeamController');

    Route::group(['prefix' => 'projects'], function(){
        Route::get('/getListOfTeamsAndClients','ProjectController@getListOfTeamsAndClients');
        Route::get('/getProjectWithTaskForDashboard','ProjectController@getProjectWithTaskForDashboard');
    });
    Route::apiResource('projects','ProjectController');

    Route::group(['prefix' => 'comments'], function(){
        Route::get('/{project_id}','CommentController@index');
    });
    Route::apiResource('comments','CommentController');

    Route::group(['prefix' => 'tasks'], function(){
        Route::get('/{project_id}/{view?}','TaskController@index');
    });
    Route::apiResource('tasks','TaskController');

    Route::group(['prefix' => 'messages'], function(){
        
        Route::get('/getFriendList','MessageController@getFriendList');
        Route::get('/{active_chat}/{isTeam?}','MessageController@index');
        Route::post(
            '/inviteFriendsForChat',
            'MessageController@inviteFriendsForChat'
        );
        Route::put(
            '/acceptInvite/{friend_id}',
            'MessageController@acceptInvite'
        );
    });
    
    Route::apiResource('messages','MessageController');
    Route::apiResource('items','ItemController');
});
