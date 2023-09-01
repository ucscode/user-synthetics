# How to Create a Modules

- Create a new folder inside the `uss-modules` directory

- Add an `index.php` file into the folder

That's it! You can now write your code within the `index.php` file of your module.

## Code Sample

```php
// Focus on the home page

Uss::instance()->route('', function() {

    // Render HTML Document

    Uss::instance()->view(function() {

        // Print your HTML Content

        echo "<h1>Bootstrap is enabled</h1>";

    });

})
```

***

Please refer to the [Documentation](http://uss.ucscode.me) for more information

## Author

Uchenna Ajah &mdash; &lt;[Ucscode](http://ucscode.me)&gt;
