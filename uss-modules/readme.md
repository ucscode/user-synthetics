# How to Make Modules

- Create a new folder in the `uss-modules` directory

- Add an `index.php` file into the folder

That's it! You can now write your code within the `index.php` file of your module.

# Code Sample

```php
// Focus on the home page

Uss::route('', function() {

    // Render HTML Content

    Uss::view(function() {

        echo "<h1>Bootstrap is enabled</h1>";

    });

})
```

***

Please refer to the [Documentation](http://uss.ucscode.me) for more information
