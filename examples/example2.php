<!doctype html>
<html>
<head>
    <title>Zebra_Pagination, database example</title>
    <meta charset="utf-8">
    <?php if (isset($_GET['bootstrap']) && $_GET['bootstrap'] == 3): ?>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
    <?php elseif (isset($_GET['bootstrap']) && $_GET['bootstrap'] == 4): ?>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" integrity="sha384-WskhaSGFgHYWDcbwN70/dfYBj47jz9qbsMId/iRN3ewGhXQFZCSftd1LZCfmhktB" crossorigin="anonymous">
    <?php else: ?>
    <link rel="stylesheet" href="../public/css/zebra_pagination.css" type="text/css">
    <?php endif; ?>
    <link rel="stylesheet" href="style.css" type="text/css">
</head>
<body>

    <h2>Zebra_Pagination, database example</h2>

    <p>For this example, you need to first import the <strong>countries.sql</strong> file from the examples folder
    and to edit the <strong>example2.php file and change your database connection related settings.</strong></p>

    <p>Show next/previous page links on the <a href="example1.php?navigation_position=left<?php echo isset($_GET['bootstrap']) ? '&bootstrap=' . $_GET['bootstrap'] : ''; ?>">left</a> or on the
    <a href="example1.php?navigation_position=right<?php echo isset($_GET['bootstrap']) ? '&bootstrap=' . $_GET['bootstrap'] : ''; ?>">right</a>. Or revert to the <a href="example1.php<?php echo isset($_GET['bootstrap']) ? '?bootstrap=' . $_GET['bootstrap'] : ''; ?>">default style</a>.<br>
    Pagination links can be shown in <a href="example1.php<?php echo isset($_GET['bootstrap']) ? '?bootstrap=' . $_GET['bootstrap'] : ''; ?>">natural</a> or <a href="example1.php?reversed=1<?php echo isset($_GET['bootstrap']) ? '&bootstrap=' . $_GET['bootstrap'] : ''; ?>">reversed</a> order.<br>
    See the <a href="example1.php">default</a> looks, the <a href="example1.php?bootstrap=3">Bootstrap 3</a> looks or the <a href="example1.php?bootstrap=4">Bootstrap 4</a> looks<br>
    <em>(when using Bootstrap you don't need to include the zebra_pagination.css file anymore)</em>
    <?php if (isset($_GET['bootstrap']) && $_GET['bootstrap'] == 4): ?>
    <br><em>For Bootstrap 4, for centering the pagination links you will have to set <code>justify-content: center;</code> for the <code>.pagination</code> class</em>
    <?php endif; ?></p>

    <?php

    // database connection details
    $MySQL_host     = '';
    $MySQL_username = '';
    $MySQL_password = '';
    $MySQL_database = '';

    // if could not connect to database
    if (!($connection = @mysqli_connect($MySQL_host, $MySQL_username, $MySQL_password, $MySQL_database)))

        // stop execution and display error message
        die('Error connecting to the database!<br>Make sure you have specified correct values for host, username, password and database.');

    // how many records should be displayed on a page?
    $records_per_page = 10;

    // include the pagination class
    require '../Zebra_Pagination.php';

    // instantiate the pagination object
    $pagination = new Zebra_Pagination();

    // if we want to show records in reversed order
    if (isset($_GET['reversed'])) {

        // show records in reversed order
        $pagination->reverse(true);

        // when showing records in reversed order, we need to call the "records" and "records_per_page" method
        // before calling the "get_page" method

        $result = mysqli_query($connection, 'SELECT COUNT(id) AS records FROM countries') or die (mysqli_error($connection));
        $total = mysqli_fetch_assoc($result);

        // pass the total number of records to the pagination class
        $pagination->records($total['records']);

        // records per page
        $pagination->records_per_page($records_per_page);

    }

    // set position of the next/previous page links
    $pagination->navigation_position(isset($_GET['navigation_position']) && in_array($_GET['navigation_position'], array('left', 'right')) ? $_GET['navigation_position'] : 'outside');

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
        ORDER BY
            country
        LIMIT
            ' . (($pagination->get_page() - 1) * $records_per_page) . ', ' . $records_per_page . '
    ';

    // if query could not be executed
    if (!($result = @mysqli_query($connection, $MySQL)))

        // stop execution and display error message
        die(mysqli_error($connection));

    // fetch the total number of records in the table
    $rows = mysqli_fetch_assoc(mysqli_query($connection, 'SELECT FOUND_ROWS() AS rows'));

    // if we are not showing records in reversed order
    // (if we are, we already set these)
    if (!isset($_GET['reversed'])) {

        // pass the total number of records to the pagination class
        $pagination->records($rows['rows']);

        // records per page
        $pagination->records_per_page($records_per_page);

    }

    ?>

    <table class="countries" border="1">
        <thead>
            <tr><th>Country</th></tr>
        </thead>
        <tbody>

        <?php $index = 0; while ($row = mysqli_fetch_assoc($result)): ?>
        <tr<?php echo $index++ % 2 ? ' class="even"' : ''; ?>>
            <td><?php echo $row['country']; ?></td>
        </tr>
        <?php endwhile; ?>

        </tbody>
    </table>

    <div class="text-center">

    <?php

    // render the pagination links
    $pagination->render();

    ?>

    </div>

</body>
</html>
