# Rivet Pagination
## Installation
2 ways: using [Composer](https://getcomposer.org/), or direct `include`/`require`

### Composer
run the following in your composer project's root folder, where the `composer.json` resides.
```shell
composer require iu-vpcm/rivet2-pagination
```
### Plain PHP include/require
Download the script, name it as you wish (for example `rivet_pagination.php`) and in your scripts:
```php
require 'PATH-TO/rivet_pagination.php';
// or 
// inlcude 'PATH-TO/rivet_pagination.php'
```
## Usage
### Basic
The only required argument is the number of items that need to be paginated.

Other parameters such as the number of items per page, and the key in `$_GET` that indicates current page number, are listed below with their default values
```PHP
/**
*   protected $pageKeyInGet = 'page';
*   protected $queryString = $_SERVER['QUERY_STRING];
*   protected $perPage = 9;
*   protected $paginationLength = 5;
*   protected $rivetVersion = 2;
 */
$totalNumItems = 100;
$pagination = new Pagination($totalNumItems);
echo $pagination->render();
```

## States
There are 2 basic states of pagination:
1. when the number of pages <= pagination width
2. when it's > pagination width

### number pages <= pagination width
### number pages > pagination width
