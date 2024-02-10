# How to Create a Modules

To create a module for your application, follow these steps:

1. **Create Module Directory**: Start by creating a new folder inside the `modules` directory.

2. **Copy Configuration File**: Copy the `config.json.dist` file as `config.json` into the newly created folder. 

    - Make sure to edit the `config.json` file according to your module's requirements.

3. **Add Index File**: Inside the module folder, add an `index.php` file. 

    - This file will serve as the entry point for your module.

Once these steps are completed, you can begin writing your code within the `index.php` file of your module.

## Code Sample

Register your template directory and assign a unique namespace:

```php
use Uss\Component\Uss;

Uss::instance()->filesystemLoader->addPath("/path/to/templates", 'MyTemplate');
```

Create URL Routes:

```php
use Uss\Component\Route\Route;
use Uss\Component\Route\RouteInterface;

new Route('/about', new class () implements RouteInterface 
{
    public function onload(array $context): void
    {
        // Render Twig Template
        Uss::instance()->render('@MyTemplate/base.html.twig', [
            'option' => 'value'
        ]);
    }
})
```

For more detailed information, please refer to the [Documentation](http://uss.ucscode.me).

## Author

Uchenna Ajah  
[Ucscode](http://ucscode.com)

For further assistance or inquiries, feel free to reach out.
