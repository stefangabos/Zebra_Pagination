# Zebra_Pagination

#### A generic, Twitter Bootstrap compatible, pagination library that automatically generates navigation links

----

[Packagist](https://packagist.org/) stats

[![Latest Stable Version](https://poser.pugx.org/stefangabos/zebra_pagination/v/stable)](https://packagist.org/packages/stefangabos/zebra_pagination) [![Total Downloads](https://poser.pugx.org/stefangabos/zebra_pagination/downloads)](https://packagist.org/packages/stefangabos/zebra_pagination) [![Monthly Downloads](https://poser.pugx.org/stefangabos/zebra_pagination/d/monthly)](https://packagist.org/packages/stefangabos/zebra_pagination) [![Daily Downloads](https://poser.pugx.org/stefangabos/zebra_pagination/d/daily)](https://packagist.org/packages/stefangabos/zebra_pagination) [![License](https://poser.pugx.org/stefangabos/zebra_pagination/license)](https://packagist.org/packages/stefangabos/zebra_pagination)

A generic pagination script that automatically generates navigation links as well as next/previous page links, given the total number of records and the number of records to be shown per page. Useful for breaking large sets of data into smaller chunks, reducing network traffic and, at the same time, improving readability, aesthetics and usability.

Adheres to pagination best practices (provides large clickable areas, doesn't use underlines, the selected page is clearly highlighted, page links are spaced out, provides "previous page" and "next page" links, provides "first page" and "last page" links - as outlined in an article by Faruk Ates from 2007, which can now be found [here](https://gist.github.com/622561), can generate links both in natural as well as in reverse order, can be easily, localized, supports different positions for next/previous page buttons, supports page propagation via GET or via URL rewriting, is SEO-friendly, and the appearance is easily customizable through CSS.

The library is compatible with [Twitter Bootstrap](http://getbootstrap.com).

Please note that this is a *generic* pagination script, meaning that it does not display any records and it does not have any dependencies on database connections or SQL queries, making it very flexible! It is up to the developer to fetch the actual data and display it based on the information returned by this pagination script. The advantage is that it can be used to paginate over records coming from any source like arrays or databases.

The code is heavily commented and generates no warnings/errors/notices when PHP's error reporting level is set to E_ALL.

##Features

- it is a generic library: can be used to paginate records both from an array or from a database
- it automatically generates navigation links, given the total number of items and the number of items per page (examples of best practices are also included)
- navigation links can be generated in natural or in reverse order
- is SEO-friendly – it uses rel=”next” and rel=”prev” and solves the problem of duplicate content on the first page without navigation and the first page having the page number in the URL
- appearance is easily customizable through CSS
- compatible with [Twitter Bootstrap](http://getbootstrap.com)
- code is heavily commented and generates no warnings/errors/notices when PHP’s error reporting level is set to E_ALL
- has comprehensive documentation

## Requirements

PHP 5+

## How to use

Make sure that in the <head> of your page you have

```html
<!-- you don't need this if you're using Twitter Bootstrap -->
<link rel="stylesheet" href="path/to/zebra_pagination.css" type="text/css">
```

If you want to preserve hashes in the URL, also include the JavaScript file – simply including it will suffice;
(jQuery needs to also be loaded before loading this file)

```javascript
<script type="text/javascript" src="path/to/zebra_pagination.js"></script>
```

Paginate data from an array:

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

// here's the magick: we need to display *only* the records for the current page
$countries = array_slice(
    $countries,
    (($pagination->get_page() - 1) * $records_per_page),
    $records_per_page
);

?>

<table>

    <tr><th>Country</th></tr>

    <?php foreach ($countries as $index => $country):?>

    <tr<?php echo $index % 2 ? ' class="even"' : '')?>>
        <td><?php echo $country?></td>
    </tr>

    <?php endforeach?>

</table>

<?php

// render the pagination links
$pagination->render();

?>
```

Paginate data from MySQL:

```php
<?php
// how many records should be displayed on a page?
$records_per_page = 10;

// include the pagination class
require 'path/to/Zebra_Pagination.php';

// instantiate the pagination object
$pagination = new Zebra_Pagination();

// the MySQL statement to fetch the rows
// note how we build the LIMIT
// also, note the "SQL_CALC_FOUND_ROWS"
// this is to get the number of rows that would've been returned if there was no LIMIT
// see http://dev.mysql.com/doc/refman/5.0/en/information-functions.html#function_found-rows
$MySQL = '
    SELECT
        SQL_CALC_FOUND_ROWS
        country
    FROM
        countries
    LIMIT
        ' . (($pagination->get_page() - 1) * $records_per_page) . ', ' . $records_per_page . '
';

// if query could not be executed
if (!($result = @mysql_query($MySQL))) {

    // stop execution and display error message
    die(mysql_error());

}

// fetch the total number of records in the table
$rows = mysql_fetch_assoc(mysql_query('SELECT FOUND_ROWS() AS rows'));

// pass the total number of records to the pagination class
$pagination->records($rows['rows']);

// records per page
$pagination->records_per_page($records_per_page);

?>

<table class="countries" border="1">

    <tr><th>Country</th></tr>

    <?php $index = 0?>

    <?php while ($row = mysql_fetch_assoc($result)):?>

    <tr<?php echo $index++ % 2 ? ' class="even"' : ''?>>
        <td><?php echo $row['country']?></td>
    </tr>

    <?php endwhile?>

</table>

<?php

// render the pagination links
$pagination->render();

?>
```

Paginate data from MySQL in reverse order:

```php
<?php
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
if (!($result = @mysql_query('SELECT COUNT(id) AS records FROM countries')))

    die (mysql_error());

// pass the total number of records to the pagination class
$pagination->records(array_pop(mysql_fetch_assoc($result)));

// records per page
$pagination->records_per_page($records_per_page);

// the MySQL statement to fetch the rows
// note the LIMIT - use it exactly like that!
// also note that we're ordering data descendingly - most important when we're
// showing records in reverse order!
$MySQL = '
    SELECT
        country
    FROM
        countries
    ORDER BY
        country DESC
    LIMIT
        ' . (($pagination->get_pages() - $pagination->get_page()) * $records_per_page) . ', ' . $records_per_page . '
';

// if query could not be executed
if (!($result = @mysql_query($MySQL)))

    // stop execution and display error message
    die(mysql_error());

?>

<table class="countries" border="1">

    <tr><th>Country</th></tr>

    <?php $index = 0?>

    <?php while ($row = mysql_fetch_assoc($result)):?>

    <tr<?php echo $index++ % 2 ? ' class="even"' : ''?>>
        <td><?php echo $row['country']?></td>
    </tr>

    <?php endwhile?>

</table>

<?php

// render the pagination links
$pagination->render();

?>
```

Visit the **[project's homepage](http://stefangabos.ro/php-libraries/zebra-pagination/)** for more information.