# php-route
Simple package to handle routes

# Use
Create routes like these:

```php
Route::get('/', 'MainController@index');
Route::get('/view', 'ViewController@index');

Route::get(['set' => '/edit', 'as' => 'edit'], 'ViewController@edit');
Route::get(['set' => '/show/{id}', 'as' => 'show'], 'ViewController@show');

Route::get('/test/one/two/tree', function() {
    echo '<a href="' . Route::translate('show', ['id' => 1]) . '">Show record 1</a>';
});
```

# Inspiration
This work was inspired by the fantastic article [Construir um sistema de rotas para MVC, de Alexandre Barbosa](https://alexandrebbarbosa.wordpress.com/2019/04/17/phpconstruir-um-sistema-de-rotas-para-mvc-primeira-parte/)
