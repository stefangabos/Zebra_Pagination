<img src="https://github.com/stefangabos/zebrajs/blob/master/docs/images/logo.png" alt="zebrajs" align="right">

# Zebra_Pagination

*A generic, Twitter Bootstrap compatible, pagination library that automatically generates navigation links*

[![Latest Stable Version](https://poser.pugx.org/stefangabos/zebra_pagination/v/stable)](https://packagist.org/packages/stefangabos/zebra_pagination) [![Total Downloads](https://poser.pugx.org/stefangabos/zebra_pagination/downloads)](https://packagist.org/packages/stefangabos/zebra_pagination) [![Monthly Downloads](https://poser.pugx.org/stefangabos/zebra_pagination/d/monthly)](https://packagist.org/packages/stefangabos/zebra_pagination) [![Daily Downloads](https://poser.pugx.org/stefangabos/zebra_pagination/d/daily)](https://packagist.org/packages/stefangabos/zebra_pagination) [![License](https://poser.pugx.org/stefangabos/zebra_pagination/license)](https://packagist.org/packages/stefangabos/zebra_pagination)

A generic, [Twitter Bootstrap](http://getbootstrap.com) compatible (versions 3, 4 and 5), pagination script that automatically generates navigation links as well as next/previous page links, given the total number of records and the number of records to be shown per page. Useful for breaking large sets of data into smaller chunks, reducing network traffic and, at the same time, improving readability, aesthetics and usability.

Adheres to pagination best practices (provides large clickable areas, doesn't use underlines, the selected page is clearly highlighted, page links are spaced out, provides "previous page" and "next page" links, provides "first page" and "last page" links - as outlined in an article by Faruk Ates from 2007, which can now be found [here](https://gist.github.com/622561), can generate links both in natural as well as in reverse order, can be easily, localized, supports different positions for next/previous page buttons, supports page propagation via GET or via URL rewriting, is SEO-friendly, and the appearance is easily customizable through CSS.

> Please note that this is a *generic* pagination script, meaning that it does not display any records and it does not have any dependencies on database connections or SQL queries, making it very flexible! It is up to the developer to fetch the actual data and display it based on the information returned by this pagination script. The advantage is that it can be used to paginate over records coming from any source like arrays or databases.

The code is heavily commented and generates no warnings/errors/notices when PHP's error reporting level is set to E_ALL.

:books: Check out the [awesome documentation](https://stefangabos.github.io/Zebra_Pagination/Zebra_Pagination/Zebra_Pagination.html)!

## Support the development of this library

[![Donate](https://www.paypalobjects.com/en_US/i/btn/btn_donate_LG.gif)](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=SKXN7C6PPH6FL)

## Features

- it is a generic library: can be used to paginate records both from an array or from a database
- it automatically generates navigation links, given the total number of items and the number of items per page (examples of best practices are also included)
- navigation links can be generated in natural or in reverse order
- it is SEO-friendly - it solves the problem of duplicate content on the first page without navigation and the first page having the page number in the URL
- appearance is easily customizable through CSS
- compatible with [Twitter Bootstrap](http://getbootstrap.com) versions 3, 4 and 5
- code is heavily commented and generates no warnings/errors/notices when PHP's error reporting level is set to E_ALL
- has [awesome documentation](https://stefangabos.github.io/Zebra_Pagination/Zebra_Pagination/Zebra_Pagination.html)

## Requirements

PHP 5+

## Installation

You can install Zebra_Pagination via [Composer](https://packagist.org/packages/stefangabos/zebra_pagination)

```bash
# get the latest stable release
composer require stefangabos/zebra_pagination

# get the latest commit
composer require stefangabos/zebra_pagination:dev-master
```

Or you can install it manually by downloading the latest version, unpacking it, and then including it in your project

```php
require_once 'path/to/Zebra_Pagination.php';
```

## How to use

Make sure that in the <head> of your page you have

```html
<!-- you don't need this if you're using Twitter Bootstrap -->
<link rel="stylesheet" href="path/to/zebra_pagination.css" type="text/css">
```

If you want to preserve hashes in the URL, also include the JavaScript file – simply including it will suffice;
(jQuery needs to also be loaded before loading this file)

```javascript
<script src="path/to/zebra_pagination.js"></script>
```

Paginate data from an array:

**The PHP**

```php
<?php

// let's paginate data from an array...
$countries = array(
    // array of countries
);

// how many records should be displayed on a page?
$records_per_page = 10;

// include the pagination class
require 'path/to/Zebra_Pagination.php';

// instantiate the pagination object
$pagination = new Zebra_Pagination();

// the number of total records is the number of records in the array
$pagination->records(count($countries));

// records per page
$pagination->records_per_page($records_per_page);

// here's the magic: we need to display *only* the records for the current page
$countries = array_slice(
    $countries,
    (($pagination->get_page() - 1) * $records_per_page),
    $records_per_page
);

?>

<table>
    <thead>
    <tr>
        <th>Country</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($countries as $index => $country): ?>
    <tr<?php echo $index % 2 ? ' class="even"' : ''; ?>>
        <td><?php echo $country; ?></td>
    </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<?php

// render the pagination links
$pagination->render();
```

Would result is something like

![Zebra_Pagination](https://github.com/stefangabos/Zebra_Pagination/blob/master/docs/media/example-natural.png?raw=true)

You can set the navigation links' position to the left or to the right of the pagination links using the [navigation_position()](https://stefangabos.github.io/Zebra_Pagination/Zebra_Pagination/Zebra_Pagination.html#methodnavigation_position) method:

```php
$pagination->navigation_position('left');
```
![Zebra_Pagination](https://github.com/stefangabos/Zebra_Pagination/blob/master/docs/media/example-buttons-left.png?raw=true)

```php
$pagination->navigation_position('right');
```
![Zebra_Pagination](https://github.com/stefangabos/Zebra_Pagination/blob/master/docs/media/example-buttons-right.png?raw=true)

Labels for "Previous" and "Next" links can be changed with the [labels()](https://stefangabos.github.io/Zebra_Pagination/Zebra_Pagination/Zebra_Pagination.html#methodlabels) method:

```php
$pagination->labels('Previous', 'Next');
```
![Zebra_Pagination](https://github.com/stefangabos/Zebra_Pagination/blob/master/docs/media/example-labels.png?raw=true)

You can also have HTML markup as labels making it easy to include font icons like the ones from [Font Awesome](https://fontawesome.com/)

```php
$pagination->labels('<i class="fa fa-arrow-left"></i>', '<i class="fa fa-arrow-right"></i>');
```
![Zebra_Pagination](https://github.com/stefangabos/Zebra_Pagination/blob/master/docs/media/example-labels-icon-font.png?raw=true)

Paginate data from MySQL:

```php
<?php

// connect to a database
$connection = mysqli_connect($host, $username, $password, $database);

// how many records should be displayed on a page?
$records_per_page = 10;

// include the pagination class
require 'path/to/Zebra_Pagination.php';

// instantiate the pagination object
$pagination = new Zebra_Pagination();

// the MySQL statement to fetch the rows
$sql = '
    SELECT
        country
    FROM
        countries
    LIMIT
        ' . (($pagination->get_page() - 1) * $records_per_page) . ', ' . $records_per_page . '
';

// execute the MySQL query
// (you will use mysqli or PDO here, but you get the idea)
$result = mysqli_query($connection, $sql) or die(mysqli_error($connection));

// fetch the total number of records in the table
$rows = mysqli_fetch_assoc(mysqli_query($connection, 'SELECT COUNT(*) AS rows FROM countries'));

// pass the total number of records to the pagination class
$pagination->records($rows['rows']);

// records per page
$pagination->records_per_page($records_per_page);

?>

<table>
    <thead>
    <tr>
        <th>Country</th>
    </tr>
    </thead>
    <tbody>
    <?php $index = 0; while ($row = mysqli_fetch_assoc($result)): ?>
    <tr<?php echo $index++ % 2 ? ' class="even"' : ''; ?>>
        <td><?php echo $row['country']; ?></td>
    </tr>
    <?php endwhile; ?>
    </tbody>
</table>

<?php

// render the pagination links
$pagination->render();
```

Paginate data from MySQL in reverse order:

```php
<?php

// connect to a database
$connection = mysqli_connect($host, $username, $password, $database);

// how many records should be displayed on a page?
$records_per_page = 10;

// include the pagination class
require 'path/to/Zebra_Pagination.php';

// instantiate the pagination object
$pagination = new Zebra_Pagination();

// show records in reverse order
$pagination->reverse(true);

// when showing records in reverse order, we need to know the total number
// of records from the beginning
$result = mysqli_query($connection, 'SELECT COUNT(*) AS rows FROM countries'))) or die (mysqli_error());

// pass the total number of records to the pagination class
$pagination->records(array_pop(mysqli_fetch_assoc($result)));

// records per page
$pagination->records_per_page($records_per_page);

// the MySQL statement to fetch the rows
// note the LIMIT - use it exactly like that!
// also note that we're ordering data descendingly - most important when we're
// showing records in reverse order!
$sql = '
    SELECT
        country
    FROM
        countries
    ORDER BY
        country DESC
    LIMIT
        ' . (($pagination->get_pages() - $pagination->get_page()) * $records_per_page) . ', ' . $records_per_page . '
';

// run the query
mysqli_query($connection. $sql) or die(mysqli_error($connection));

?>

<table>
    <thead>
    <tr>
        <th>Country</th>
    </tr>
    </thead>
    <tbody>
    <?php $index = 0; while ($row = mysqli_fetch_assoc($result)): ?>
    <tr<?php echo $index++ % 2 ? ' class="even"' : ''; ?>>
        <td><?php echo $row['country']; ?></td>
    </tr>
    <?php endwhile; ?>
    </tbody>
</table>

<?php

// render the pagination links
$pagination->render();
```

Would result in something like

![Zebra_Pagination](https://github.com/stefangabos/Zebra_Pagination/blob/master/docs/media/example-reversed.png?raw=true)

:books: Check out the [awesome documentation](https://stefangabos.github.io/Zebra_Pagination/Zebra_Pagination/Zebra_Pagination.html)!
