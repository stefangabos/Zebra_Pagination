## Paginate data from MySQL

```php
<?php
    // include the pagination class
    require 'path/to/Zebra_Pagination.php';

    // instantiate the pagination object
    $pagination = new Zebra_Pagination();

    // how many records should be displayed on a page?
    $records_per_page = 10;

    //Establish connection using mysqli api
    $conn = mysqli_connect('localhost', '', '', '');

    $sql  = 'SELECT
    *
    FROM
    countries
    ORDER BY
    country
    LIMIT
    ' . (($pagination->get_page() - 1) * $records_per_page) . ', ' . $records_per_page . '';

    $result = $conn->query($sql);

    $sql2 = "SELECT COUNT(*) AS country FROM countries";

    $result2 = $conn->query($sql2);

    $TotalRcount = $result2->fetch_assoc();

    while($row = $result->fetch_assoc()){

        echo $row['country'].'<br/>';

    }

    // pass the total number of records to the pagination class
    $pagination->records($TotalRcount['country']);

    // records per page
    $pagination->records_per_page($records_per_page);

    // render the pagination links
    $pagination->render();

    //close connection
    $conn->close();
?>
```

## Optional

Set position of the next/previous page links

```php
    $pagination->navigation_position(isset($_GET['navigation_position']) && in_array($_GET['navigation_position'], array('left', 'right')) ? $_GET['navigation_position'] : 'outside');
```

See `example.php` to view how this work.

If you want to preserve hashes in the URL, also include the JavaScript file – simply including it will suffice; (jQuery needs to also be loaded before loading this file).

```html
<script type="text/javascript" src="path/to/zebra_pagination.js"></script>
```
