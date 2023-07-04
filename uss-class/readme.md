
# Working with Composer in `uss-class` Directory

To add dependencies to your project, you have a couple of options:

## Use Composer CLI (Recommended)

You can change your terminal's (Command Prompt) path to `uss-class`  directory and use the command 
```
composer require <repo-name>
```
This will automatically update the `composer.json` file and download the required package. 

## Edit composer.json

- You can manually edit the `composer.json` file in this `uss-class`. 
- Add the desired package names and versions to the `"require"` section,
- Then run `composer update` in your terminal to install the dependencies.

***

Composer's autoloading mechanism will ensure that the libraries are readily available in your project.
