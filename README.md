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

// Define your route as bellows
Route::get('/', function () {
    return 'Hello World';
});
Route::get('/demo/hello/{id}/me/{name}', 'MyController#getHello');
Route::get('/demo/hello/{name}', 'MyController#getHello');
Route::post('/demo', 'MyController#getIndex');
Route::controller('/my', 'MyController');
Route::resource('/person', 'PersonController');

// Finally execute or dispatch your route to your desire cotroller method or callback
Route::execute();
```

##Author

[Sohel Amin](http://www.sohelamin.com)
