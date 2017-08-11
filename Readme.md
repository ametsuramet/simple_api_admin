# Simple ADMIN & API GENERATOR
this package is require [ametsuramet/simple_orm](https://github.com/ametsuramet/simple_orm)

## Installation

Begin by pulling in the package through Composer.

```bash
composer require ametsuramet/amet_simple_admin_api:dev-master
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

## Publish Vendor
```bash
php artisan vendor:publish --tag=simple_admin_api --force
```

edit your admin preference in `config/simple_admin_api.php`



## Usage
Before you generate admin views or api please make sure you have any model that generated with [ametsuramet/simple_orm](https://github.com/ametsuramet/simple_orm), you can use interactive mode:

```bash
php artisan simple_orm:interactive
```

an easiest way to create a ADMIN & API is using the `simple_admin_api:generate` Artisan command:

```bash
php artisan simple_admin_api:generate
```

## Rebuild Menu Sidebar
```bash
php artisan simple_admin_api:rebuild_menu
```

## Credits
* [AdminBSB](https://github.com/gurayyarar/AdminBSBMaterialDesign)

