# Simple ADMIN & API GENERATOR
this package is require [ametsuramet/simple_orm](https://github.com/ametsuramet/simple_orm)

## Installation

Begin by pulling in the package through Composer.

```bash
composer require ametsuramet/amet_simple_admin_api
```

Or add in the `require` key of `composer.json` file manually

``` json
"ametsuramet/amet_simple_admin_api": "dev-master"
```



Register the ServiceProvider in `config/app.php`

```php
'providers' => [
    // ...
    Amet\SimpleORM\ModelBuilderServiceProvider::class,
    Amet\SimpleAdminAPI\SimpleAdminAPIServiceProvider::class,
],
```


## Defining Models

The easiest way to create a ADMIN & API is using the `simple_admin_api:generate` Artisan command:

```bash
php artisan simple_admin_api:generate
```

## Publish Vendor
```bash
php artisan vendor:publish --tag=simple_admin_api --force
```
