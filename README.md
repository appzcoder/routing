# URL Request Router
URL Request Router


## Installation

1. Run 
    ```
    composer require appzcoder/routing:"dev-master"
    ```
    
2. Add bellow lines to your script
	```php
	require 'vendor/autoload.php';
	```

## Usage

```php
// Make Route alias of Route Facade for static instance calling
class_alias('Appzcoder\Routing\RouterFacade', 'Route');

// Set your own controller namespace (optional)
Route::setControllerNamespace("App\\Controllers\\");

// GET Route with anonymous function
Route::get('/', function () {
    return 'Hello World';
});

// Route with name parameter
Route::get('/demo/hello/{name}', 'MyController#getHello');

// Route with name and id both parameters
Route::get('/demo/hello/{id}/me/{name}', 'MyController#getHello');

// POST Verb Route
Route::post('/demo', 'MyController#getIndex');

// Controller Route
Route::controller('/my', 'MyController');

// RESTfull Resource Route
Route::resource('/person', 'PersonController');

// Group Route
Route::group(['prefix' => 'admin', 'namespace' => 'Admin'], function () {
    Route::get('/users', function () {
        return "/admin/users";
    });

    Route::get('/teachers', function () {
        return "/admin/teachers";
    });

    Route::get('/dashboard', 'AdminController#getIndex');
});

// Finally execute or dispatch your route to your desire cotroller method or callback
Route::execute();
```

##Author

[Sohel Amin](http://www.sohelamin.com)
