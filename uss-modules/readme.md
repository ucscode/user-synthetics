# How to Create a Modules

- Create a new folder inside the `uss-modules` directory

- Add an `index.php` file into the folder

That's it! You can now write your code within the `index.php` file of your module.

## Code Sample

```php
// Focus on the home page

Uss::route('', function() {

    // Render HTML Document

    Uss::view(function() {
        
        // Create a template tag

        Uss::tag('template', "Bootstrap");

        // Print your HTML Content

        echo "<h1>%{template} is enabled</h1>";

    });

})
```

***

Please refer to the [Documentation](http://uss.ucscode.me) for more information

## Author

Uchenna Ajah &mdash; &lt;Ucscode&gt;
