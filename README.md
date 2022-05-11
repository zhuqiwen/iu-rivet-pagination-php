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
**NOTE:** If not specified in `$options['rivet']`, the lib generates [Pagination of Rivet V2](https://v2.rivet.iu.edu/docs/components/pagination/).

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

### Advanced
#### V1 pagination
```PHP
// not required
$v1Settings = [
    'position' => 'center', // or 'right'
    'size' => 'small'
];

$options = [
    'rivetVersion' => 'v1', // required if V1 pagination is desired 
    'rivetV1Settings' => $v1Settings // it not set or empty, default style of V1 will be applied
];
$pagination = new \Edu\IU\VPCM\Rivet\Pagination(150, $options);
echo $pagination->render();
```

#### Set pagination length/width
```PHP
/**
*   default
*   protected $paginationLength = 5;
 */
$totalNumItems = 100;
$options = ['paginationLength' => 10]
$pagination = new Pagination($totalNumItems, $options);
echo $pagination->render();
```

#### Set the number of items to display per page
```PHP
/**
*   default
*   protected $perPage = 9;
 */
$totalNumItems = 100;
$options = ['perPage' => 10]
$pagination = new Pagination($totalNumItems, $options);
echo $pagination->render();
```

#### Set which key in $_GET indicates page number
```PHP
/**
*   default
*   protected $pageKeyInGet = 'page';
 */
$totalNumItems = 100;
$options = ['pageKeyInGet' => 'myPage']
$pagination = new Pagination($totalNumItems, $options);
echo $pagination->render();
```

#### If custom query string is needed
```PHP
/**
*   default
*   protected $queryString = $_SERVER['QUERY_STRING];
 */
$totalNumItems = 100;
$options = ['queryString' => '?tom=jerry&mj=forever'] // the '?' can be omitted 
$pagination = new Pagination($totalNumItems, $options);
echo $pagination->render();
```

