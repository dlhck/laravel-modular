# Laravel Modular
[![Latest Stable Version](https://poser.pugx.org/davidhoeck/laravel-modular/v/stable)](https://packagist.org/packages/davidhoeck/laravel-modular)
[![Latest Unstable Version](https://poser.pugx.org/davidhoeck/laravel-modular/v/unstable)](https://packagist.org/packages/davidhoeck/laravel-modular)
[![License](https://poser.pugx.org/davidhoeck/laravel-modular/license)](https://packagist.org/packages/davidhoeck/laravel-modular)

Adds the support of a modular approach to Laravel 5.

## Agenda 
* Installation 
* Get started  
* Using Laravel Modular 

<a name="installation"></a>
### Installation

The easiest and common way is to install the package via Composer.

Add this line to your `composer.json`
```
"davidhoeck/laravel-modular": "dev-master"
```
or paste this line into your terminal.
```
composer require "davidhoeck/laravel-modular"
```
Add the following line to your `config/app.php` under the section `providers`
```
DavidHoeck\LaravelModular\ModuleServiceProvider::class
```
<a name="get_started"></a>
### Get Started 

Generate a new module with
```
php artisan make:module <module-name>
```
*** Optional Flags ***

*Generate with a base Controller* 
```
php artisan make:module <module-name> --with-controller 
```

*Generate with a base Model* 
```
php artisan make:module <module-name> --with-model
```

