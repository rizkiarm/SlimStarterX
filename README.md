SlimStarterX
============

SlimStarterX is a bootstrap application built with Slim Framework in MVC architecture,
with Laravel's Eloquent as database provider (Model), Twig as template engine (View) and Phinx as a migration utility.

Additional package is Sentry as authentication provider and Slim-facade which provide easy access to underlying Slim API
with static interface like Laravel syntax (built based on Laravel's Facade).

Original project by xsanity:
<a href="https://github.com/xsanisty/SlimStarter" target="_blank">https://github.com/xsanisty/SlimStarter</a>

####Changes:
Here are few changes that has been made to the original application

* Added: Phinx as a migration utility, replacing the original migrate.php
* Added: Yii-like base Model
* Added: base Controller with CSRF support
* Added: Illuminate/Validation for Model Validation
* Added: ValidatorFacade and CsrfProtectionFacade

####Installation


#####1 Manual Install
You can manually install SlimStarterX by cloning this repo or download the zip file from this repo, and run ```composer install```.
```
$git clone https://github.com/rizkiarm/SlimStarterX.git .
$composer install
```

#####2 Install via ```composer create-project```
Alternatively, you can use ```composer create-project``` to install SlimStarterX without downloading zip or cloning this repo.

```
composer create-project rizkiarm/slim-starter-x --stability="dev"
```

#####3 Setup Permission
After composer finished install the dependencies, you need to change file and folder permission.
```
chmod -R 777 app/storage/
chmod 666 app/config/database.php
```

#####4 Configure and Setup Database
Configure your database configuration in ``app/config/database.php``. Run the user migration using the following command inside ``app`` folder
```
php phinx migrate
```


####Configuration
Configuration file of SlimStarter located in ```app/config```, edit the database.php, cookie.php and other to match your need. After you've done configuring, open ``path-to-application/public/seed`` in your browser, before you can login with the following credential.
```
email: admin@admin.com
password: password

```

####Routing
Routing configuration is located in ```app/routes.php```, it use Route facade to access underlying Slim router.
If you prefer the 'Slim' way, you can use $app to access Slim instance


Route to closure
```php
Route::get('/', function(){
    View::display('welcome.twig');
});

/** the Slim way */
$app->get('/', function() use ($app){
    $app->view->display('welcome.twig');
});
```

Route to controller method
```php
/** get method */
Route::get('/', 'SomeController:someMethod');

/** post method */
Route::post('/post', 'PostController:create');

/** put method */
Route::put('/post/:id', 'PostController:update');

/** delete method */
Route::delete('/post/:id', 'PostController:destroy');
```

Route Middleware
```php
/** route middleware */
Route::get('/admin', function(){
    //route middleware to check user login or redirect
}, 'AdminController:index');
```

Route group
```php
/** Route group to book resource */
Route::group('/book', function(){
    Route::get('/', 'BookController:index'); // GET /book
    Route::post('/', 'BookController:store'); // POST /book
    Route::get('/create', 'BookController:create'); // Create form of /book
    Route::get('/:id', 'BookController:show'); // GET /book/:id
    Route::get('/:id/edit', 'BookController:edit'); // GET /book/:id/edit
    Route::put('/:id', 'BookController:update'); // PUT /book/:id
    Route::delete('/:id', 'BookController:destroy'); // DELETE /book/:id
});
```

Route Resource
this will have same effect on route group above like Laravel Route::resource
```php
/** Route to book resource */
Route::resource('/book', 'BookController');
```

RouteController
```php
/** Route to book resource */
Route::controller('/book', 'BookController');

/**
 * GET /book will be mapped to BookController:getIndex
 * POST /book will be mapped to BookController:postIndex
 * [METHOD] /book/[path] will be mapped to BookController:methodPath
 */
```

####Model
Models are located in ```app/models``` directory, since Eloquent is used as database provider, you can write model like you
write model for Laravel, for complete documentation about eloquent, please refer to http://laravel.com/docs/eloquent

file : app/models/Book.php
```php
class Book Extends Model{}
```
>Note: Eloquent has some limitations due to dependency to some Laravel's and Symfony's components which is not included,
such as ```remember()```, ```paginate```, and validation method, which is depend on ```Illuminate\Cache```, ```Illuminate\Filesystem```,
```Symfony\Finder```, etc.

####Controller
Controllers are located in ```app/controllers``` directory, you may extends the BaseController to get access to predefined helper.
You can also place your controller in namespace to group your controller.

file : app/controllers/HomeController.php
```php
Class HomeController extends BaseController{

    public function welcome(){
        $this->data['title'] = 'Some title';
        View::display('welcome.twig', $this->data);
    }
}
```

#####Controller helper

######Get reference to Slim instance
You can access Slim instance inside your controller by accessing $app property
```php
$this->app; //reference to Slim instance
```

######Loading javascript assets or CSS assets
SlimStarter shipped with default master template with js and css asset already in place, to load your own js or css file
you can use ```loadJs``` or ```loadCss``` , ```removeJs``` or ```removeCss``` to remove js or css, ```resetJs``` or ```resetCss```
to remove all queued js or css file.

```php
/**
 * load local js file located in public/assets/js/application.js
 * by default, it will be placed in the last list,
 * to modify it, use position option in second parameter
 * array(
 *      'position' => 'last|first|after:file|before:file'
 * )
 */
$this->loadJs('application.js', ['position' => 'after:jquery.js'])

/**
 * load external js file, eg: js in CDN
 * use location option in second parameter
 * array(
 *      'location' => 'internal|external'
 * )
 */
$this->loadJs('http://code.jquery.com/jquery-1.11.0.min.js', ['location' => 'external']);

/** remove js file from the list */
$this->removeJs('user.js');

/** reset js queue, no js file will be loaded */
$this->resetJs();


/** load local css file located in public/assets/css/style.css */
$this->loadCss('style.css')

/** load external css file, eg: js in CDN */
$this->loadCss('//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css', ['location' => 'external']);

/**
```

######Publish PHP variable to javascript
You can also publish PHP variable to make it accessible via javascript (must extends master.twig)
```php
/** publish the variable */
$this->publish('user', User::find(1)->toArray());

/** remove the variable */
$this->unpublish('user');
```

the user variable will be accessible in 'global' namespace
```javascript
console.log(global.user);
```

######Default variable available in template

####View
Views file are located in ```app/views``` directory in twig format, there is master.twig with 'body' block as default master template
shipped with SlimStarer that will provide default access to published js variable.

For detailed Twig documentation, please refer to http://twig.sensiolabs.org/documentation


file : app/views/welcome.twig
```html
{% extends 'master.twig' %}
{% block body %}
    Welcome to SlimStarter
{% endblock %}

```

#####Rendering view inside controller
If your controller extends the BaseController class, you will have access to $data property which will be the placeholder for all
view's data.

```php
View::display('welcome.twig', $this->data);
```

####Hooks and Middlewares
You can still hook the Slim event, or registering Middleware to Slim instance in ```app/bootstrap/app.php```,
Slim instance is accessible in ```$app``` variable.

```php
$app->hook('slim.before.route', function(){
    //do your hook
});

$app->add(new SomeActionMiddleware());
```

You can write your own middleware class in ```app/middlewares``` directory.

file : app/middlewares/SomeActionMiddleware.php
```php
class SomeActionMiddleware extends Middleware
{
    public function call()
    {
        // Get reference to application
        $app = $this->app;

        // Run inner middleware and application
        $this->next->call();

        // do your stuff
    }
}
```

####Models
#####Rules & Messages
######Example

```

<?php

class Indomie extends \SlimStarterX\Base\Model {
    protected static function rules() {
        return [
        'name' => 'required'
    ];
    }

    //Use this for custom messages
    protected static function messages() {
        return [
        'name.required' => 'My custom message for :attribute required'
    ];
    }
}

```

######More

Example:

- app/models/ExampleModel.php

Illiminate validation official documentation:

- http://laravel.com/docs/4.2/validation

#####Find

######Example

```

Indomie::findOne('name', '=', 'sedap');

```

```

Indomie::findAll('votes', '>', 100);

```

#####Load, Validate, Save & Delete
######Example

```

// load data to model
$indomie = new Indomie;
$indomie->load(Input::get());

// you can also load data this way
$indomie = new Indomie(Input::post());

// validate model
$indomie->validate() // will return boolean

// error stuff
$indomie->hasErrors(); // return boolean
$indomie->getErrors(); // return errors

// validate and save model
$indomie->save(); // will return false if either validate, beforeSave or save is failed

// save model without enforcing validation
$indomie->forceSave();

```

######Hooks

Base Model also have hooks before some events, your Model can now implement:

```

public function beforeSave()
{
    // your code that need to be executed before model is saved
    // this function must return boolean
}


public function afterSave()
{
    // your code that need to be executed after model is saved
}

public function beforeDelete()
{
    // your code that need to be executed before model is deleted
    // this function must return boolean
}

public function afterDelete()
{
    // your code that need to be executed after model is deleted
}

```

####CSRF Protection
By default the CSRF protection are on, add this line in your controller to disable it.

```

protected $enableCsrfValidation = false;

```

To use CSRF protection, add this code inside your html form

```

{{ csrfTokenHiddenInput | raw }}

// output of those above code
<input type="hidden" name="csrf_token" value="MTQzODMxNjkyMjVnMElDWVpjS1Z1V2p5VUZCWVVEdUFiQTdwVWFKcG54">

```

Notes
----
In case autoloader cannot resolve your classes, do ```composer dump-autoload``` so composer can resolve your class location
