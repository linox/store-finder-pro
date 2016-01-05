<?php
/*
 |--------------------------------------------------------------------------
 | Installation check (database, permissions)
 |--------------------------------------------------------------------------
 */

\StoreFinder\Controller\InstallationController::check();

// ----------------------------------------------------------------------------
// General

$aUrl = parse_url(URL::current());
Former::framework('TwitterBootstrap3');

// ----------------------------------------------------------------------------
// API

if(isset($aUrl['path']) && strpos($aUrl['path'], '/api/v1') !== false)
{
	Route::controller('/api/v1/auth',            'StoreFinder\Controller\AuthController');
	Route::controller('/api/v1/app',             'StoreFinder\Controller\AppController');
	Route::controller('/api/v1/category',        'StoreFinder\Controller\CategoryController');
	Route::controller('/api/v1/item',            'StoreFinder\Controller\ItemController');
	Route::controller('/api/v1/option',          'StoreFinder\Controller\OptionController');
	Route::controller('/api/v1/import',          'StoreFinder\Controller\ImportController');
	Route::controller('/api/v1/installation',    'StoreFinder\Controller\InstallationController');
};

// ----------------------------------------------------------------------------
// Main site routes

Route::get('/', function()
{
	return View::make('site.main');
});

if(isset($aUrl['path']))
{
	// ----------------------------------------------------------------------------
	// Map route

	Route::get('/map', function()
	{
		$cat = \StoreFinder\Core\CategoryHelpers::parseLink(Request::get('m'));
		$language = \StoreFinder\Core\CategoryHelpers::getLanguage('', false, $cat['id']);
	
		App::setLocale($language);
	
		return View::make('map.main')->with('cat', $cat);
	});

	// ----------------------------------------------------------------------------
	// App routes

	Route::group(array('before' => 'auth'), function()
	{
		Route::get( '/dashboard',                             'StoreFinder\Controller\DashboardController@showDashboard');
		Route::get( '/dashboard/user/settings',               'StoreFinder\Controller\UserController@showUserSettings');
		Route::get( '/dashboard/items',                       'StoreFinder\Controller\ItemController@showItems');
		Route::get( '/dashboard/item',                        'StoreFinder\Controller\ItemController@showItem');
		Route::get( '/dashboard/options',                     'StoreFinder\Controller\OptionController@showOptions');
		Route::get( '/dashboard/option',                      'StoreFinder\Controller\OptionController@showOption');
		Route::get( '/dashboard/category',                    'StoreFinder\Controller\CategoryController@showCategory');
		Route::get( '/dashboard/import',                      'StoreFinder\Controller\ImportController@showImport');
	
		// Superadmin views
		Route::group(array('before' => 'superadmin'), function()
		{
			if(Config::get('system.user_management'))
			{
				Route::get( '/dashboard/users',                      'StoreFinder\Controller\UserController@showUsers');
				Route::get( '/dashboard/users/user',                 'StoreFinder\Controller\UserController@showUser');
				Route::get( '/dashboard/settings',                   'StoreFinder\Controller\SettingsController@showSettings');
			}
		});
	});

	// ----------------------------------------------------------------------------
	// Auth

	Route::get( '/login',                                     'StoreFinder\Controller\AuthController@showLogin');
	Route::get( '/signup',                                    'StoreFinder\Controller\AuthController@showSignup');
	Route::get( '/reminder',                                  'StoreFinder\Controller\AuthController@showReminder');
	Route::get( '/reset/{token}',                             'StoreFinder\Controller\AuthController@showReset');
	Route::get( '/activate/{token}',                          'StoreFinder\Controller\AuthController@showActivate');
	Route::get( '/logout',                                    'StoreFinder\Controller\AuthController@doLogout');

	// ----------------------------------------------------------------------------
	// 404
	
	App::missing(function($exception)
	{
		return Response::view('app.errors.404', array(), 404);
	});
}