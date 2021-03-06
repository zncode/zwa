<?php
use App\User;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', 'IndexController@index');

// 认证路由...
Route::get('auth/login', 'Auth\LoginController@getLogin');
Route::post('auth/login', 'Auth\LoginController@login');
Route::get('auth/logout', 'Auth\LoginController@logout');

// 注册路由...
Route::get('auth/register', 'Auth\RegisterController@getRegister');
Route::post('auth/register', 'Auth\RegisterController@postRegister');

// 密码重置链接的路由...
Route::get('password/email', 'Auth\ResetPasswordController@getEmail');
Route::post('password/email', 'Auth\ResetPasswordController@postEmail');

// 密码重置的路由...
Route::get('password/reset/{token}', 'Auth\ResetPasswordController@getReset');
Route::post('password/reset', 'Auth\ResetPasswordController@postReset');



//用户
Route::resource('users', 'UserController');

//公会
Route::get('group_founders', 'GroupController@groupFounderList');
Route::get('groups', 'GroupController@groupList');


//应用
Route::get('applications', 'AppController@appList');
Route::get('application_blacklist', 'AppController@appBlacklist');

//角色
Route::resource('roles', 'RoleController');

//模块
Route::resource('modules', 'ModuleController');
Route::get('functions/{cid}', 'FunctionController@index');
Route::get('functions/create/{cid}', 'FunctionController@create');
Route::resource('functions', 'FunctionController');

//权限
Route::resource('permissions', 'PermissionController');

//会长审核&授权
Route::get('chairmans',                                     'ChairmanController@index');
Route::get('chairmans/audit_form/{id}',                     'ChairmanController@audit_form');
Route::post('chairmans/audit_form_submit',                  'ChairmanController@audit_form_submit');
Route::get('chairmans/game_authorization_form/{id}',        'ChairmanController@game_authorization_form');
Route::post('chairmans/game_authorization_form_submit',     'ChairmanController@game_authorization_form_submit');
Route::post('chairmans/index_ajax',                         'ChairmanController@index_ajax');
Route::get('chairmans/game_authorization',                  'ChairmanController@game_authorization');
Route::get('chairmans/blacklist',                           'ChairmanController@blacklist');
Route::post('chairmans/blacklist_ajax',                     'ChairmanController@blacklist_ajax');
Route::get('chairmans/blacklist_join_form/{id}',            'ChairmanController@blacklist_join_form');
Route::post('chairmans/blacklist_join_form_submit',         'ChairmanController@blacklist_join_form_submit');
Route::get('chairmans/blacklist_out_form/{id}',             'ChairmanController@blacklist_out_form');
Route::post('chairmans/blacklist_out_form_submit',          'ChairmanController@blacklist_out_form_submit');


//开发者
Route::get('developers',                    'DeveloperController@index');
Route::post('developers/index_ajax',        'DeveloperController@index_ajax');
Route::get('developers/audit_form/{id}',    'DeveloperController@audit_form');
Route::post('developers/audit_form_submit', 'DeveloperController@audit_form_submit');

//游戏
Route::get('games',                                 'GameController@index');
Route::post('games/index_ajax',                     'GameController@index_ajax');
Route::get('games/audit_form/{id}',                 'GameController@audit_form');
Route::post('games/audit_form_submit',              'GameController@audit_form_submit');
Route::get('games/types',                           'GameController@types');
Route::post('games/types_ajax',                     'GameController@types_ajax');
Route::get('games/types_add',                       'GameController@types_add');
Route::post('games/types_add_submit',               'GameController@types_add_submit');
Route::get('games/types_edit/{id}',                 'GameController@types_edit');
Route::post('games/types_edit_submit',              'GameController@types_edit_submit');
Route::get('games/types_delete/{id}',               'GameController@types_delete');
Route::get('games/types/classes/{id?}',             'GameController@types_classes');
Route::post('games/types/classes_ajax',             'GameController@types_classes_ajax');
Route::get('games/types/classes_add/{id?}',         'GameController@types_classes_add');
Route::post('games/types/classes_add_submit',       'GameController@types_classes_add_submit');
Route::get('games/types/classes_edit/{id}',         'GameController@types_classes_edit');
Route::post('games/types/classes_edit_submit',      'GameController@types_classes_edit_submit');
Route::get('games/types/classes_delete/{id}',       'GameController@types_classes_delete');

//游戏包
Route::get('apks',                                  'ApkController@index');
Route::post('apks/index_ajax',                      'ApkController@index_ajax');
Route::get('apks/audit_form/{id}',                  'ApkController@audit_form');
Route::post('apks/audit_form_submit',               'ApkController@audit_form_submit');

//日志
Route::get('logs',                                  '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');
Route::get('logs/{controller}/{method}',            'LogController@index');
Route::post('logs/index_ajax',                      'LogController@index_ajax');

//财务
Route::get('finances',                                  'FinanceController@index');
Route::post('finances/index_ajax',                      'FinanceController@index_ajax');
Route::get('finances/recharge_form',                    'FinanceController@recharge_form');
Route::post('finances/recharge_form_submit',            'FinanceController@recharge_form_submit');
Route::get('finances/discount_form',                    'FinanceController@discount_form');
Route::post('finances/discount_form_submit',            'FinanceController@discount_form_submit');


//统计
Route::get('statistics/channels',                               'StatisticController@channel');
Route::post('statistics/channels_ajax',                         'StatisticController@channel_ajax');
Route::get('statistics/games_income',                           'StatisticController@game_income');
Route::post('statistics/games_income_ajax',                     'StatisticController@game_income_ajax');
Route::get('statistics/games_users',                             'StatisticController@game_user');
Route::post('statistics/games_users_ajax',                       'StatisticController@game_user_ajax');
Route::get('statistics/points',                                 'StatisticController@point');
Route::post('statistics/points_ajax',                           'StatisticController@point_ajax');
