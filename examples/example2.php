<?php ini_set('display_errors', 1); error_reporting(E_ALL); ?>
<?php require 'common.php'; ?>
<!doctype html>
<html>
<head>
    <title>Zebra Pagination, database example</title>
    <meta charset="utf-8">
    <?php if (isset($_GET['bootstrap']) && $_GET['bootstrap'] == 3): ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/css/bootstrap.min.css">
    <?php elseif (isset($_GET['bootstrap']) && $_GET['bootstrap'] == 4): ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
    <?php elseif (isset($_GET['bootstrap']) && $_GET['bootstrap'] == 5): ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css">
    <?php else: ?>
    <link rel="stylesheet" href="../public/css/zebra_pagination.css" type="text/css">
    <?php endif; ?>
    <link rel="stylesheet" href="style.css" type="text/css">
</head>
<body>

    <h2>Zebra Pagination, database example</h2>

    <p>For this example, you need to first import the <code>countries.sql</code> file from the <code>examples</code> folder <br>and edit the <code>example2.php</code> file to enter your database connection related settings.</p>

    <p>

        Show next/previous page links on the
        <a href="example2.php<?php echo prep_query_string(array('navigation_position' => 'left')); ?>"<?php echo isset($_GET['navigation_position']) && $_GET['navigation_position'] == 'left' ? ' class="active"' : ''; ?>>left</a> or on the
        <a href="example2.php<?php echo prep_query_string(array('navigation_position' => 'right')); ?>"<?php echo isset($_GET['navigation_position']) && $_GET['navigation_position'] == 'right' ? ' class="active"' : ''; ?>>right</a>, or use the
        <a href="example2.php<?php echo prep_query_string(array('navigation_position' => '')); ?>"<?php echo !isset($_GET['navigation_position']) || !in_array($_GET['navigation_position'], array('left', 'right')) ? ' class="active"' : ''; ?>>default style</a>.

        <br>

        Show pagination links in
        <a href="example2.php<?php echo prep_query_string(array('reversed' => '')); ?>"<?php echo !isset($_GET['reversed']) || $_GET['reversed'] != 1 ? ' class="active"' : ''; ?>>natural</a> or
        <a href="example2.php<?php echo prep_query_string(array('reversed' => 1)); ?>"<?php echo isset($_GET['reversed']) && $_GET['reversed'] == 1 ? ' class="active"' : ''; ?>>reversed</a> order.

        <br>

        Show
        <a href="example2.php<?php echo prep_query_string(array('condensed' => 1)); ?>"<?php echo isset($_GET['condensed']) && $_GET['condensed'] == 1 ? ' class="active"' : ''; ?>>condensed</a>,
        <a href="example2.php<?php echo prep_query_string(array('condensed' => 2)); ?>"<?php echo isset($_GET['condensed']) && $_GET['condensed'] == 2 ? ' class="active"' : ''; ?>>very condensed</a> or the
        <a href="example2.php<?php echo prep_query_string(array('condensed' => '')); ?>"<?php echo !isset($_GET['condensed']) || !in_array($_GET['condensed'], array(1, 2)) ? ' class="active"' : ''; ?>>default</a> style.

        <br>

        See the
        <a href="example1.php<?php echo prep_query_string(array('bootstrap' => '')); ?>"<?php echo !isset($_GET['bootstrap']) || !in_array($_GET['bootstrap'], array(3, 4)) ? ' class="active"' : ''; ?>>default</a> looks, the
        <a href="example1.php<?php echo prep_query_string(array('bootstrap' => 3)); ?>"<?php echo isset($_GET['bootstrap']) && $_GET['bootstrap'] == 3 ? ' class="active"' : ''; ?>>Bootstrap 3</a> looks, the
        <a href="example1.php<?php echo prep_query_string(array('bootstrap' => 4)); ?>"<?php echo isset($_GET['bootstrap']) && $_GET['bootstrap'] == 4 ? ' class="active"' : ''; ?>>Bootstrap 4</a> looks, or the
        <a href="example1.php<?php echo prep_query_string(array('bootstrap' => 5)); ?>"<?php echo isset($_GET['bootstrap']) && $_GET['bootstrap'] == 5 ? ' class="active"' : ''; ?>>Bootstrap 5</a> looks
        <br>

        <br><small><em>When using Bootstrap you don't need to include the <code>zebra_pagination.css</code> file anymore.</em></small>

        <?php if (isset($_GET['bootstrap']) && $_GET['bootstrap'] == 4): ?>
        <br><small><em>For Bootstrap 4, for centering the pagination links you have to set <code>justify-content: center;</code> for the <code>.pagination</code> class</em></small>
        <?php endif; ?>

    </p>

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

    // if we have to show condensed links
    if (isset($_GET['condensed']) && ($_GET['condensed'] == 1 || $_GET['condensed'] == 2)) $pagination->condensed($_GET['condensed'] == 2 ? true : '');

    // the MySQL statement to fetch the rows
    $MySQL = '
        SELECT
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
    $rows = mysqli_query($connection, 'SELECT COUNT(*) AS `rows` FROM countries') or die(mysqli_error($connection));
    $rows = mysqli_fetch_assoc($rows);

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
