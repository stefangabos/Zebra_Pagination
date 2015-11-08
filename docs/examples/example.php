<?php
    // include the pagination class
    require '../../Zebra_Pagination.php';
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
// set position of the next/previous page links
// OPTIONAL
        $pagination->navigation_position(isset($_GET['navigation_position']) && in_array($_GET['navigation_position'], array('left', 'right')) ? $_GET['navigation_position'] : 'outside');
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
        <title>Zebra_Pagination, database example</title>
        <!-- Latest compiled and minified CSS -->
        <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" integrity="sha512-dTfge/zgoMYpP7QbHy4gWMEGsbsdZeCXz7irItjcC3sPUFtf0kuFbDz/ixG7ArTxmDjLXDmezHubeNikyKGVyQ==" crossorigin="anonymous">
        <style>
            body{
                margin:40px
            }
        </style>
        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
            <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>
    <body>
        <div class="container">
            <div class="jumbotron">
              <h1>Zebra_Pagination, database example</h1>
                <p>For this example, you need to first import the <strong><code>countries.sql</code></strong> file from the examples folder and to edit the <strong>example2.php file and change your database connection related settings.</strong></p>
                <p>Show next/previous page links on the <a href="example2.php?navigation_position=left">left</a> or on the
        <a href="example2.php?navigation_position=right">right</a>. Or revert to the <a href="example2.php">default style</a></p>

            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-condensed">
                        <thead>
                                <tr>
                                        <th>ID</th>
                                        <th>Country Name</th>
                                        <th>ISO2</th>
                                        <th>IS3</th>
                                        <th>NOC</th>
                                </tr>
                        </thead>
                        <tbody>
                                <?php
                                    while($row = $result->fetch_assoc()){
                                ?>
                                <tr>
                                        <td><?php echo $row['id']; ?></td>
                                        <td><?php echo $row['country']; ?></td>
                                        <td><?php echo $row['iso2']; ?></td>
                                        <td><?php echo $row['iso3']; ?></td>
                                        <td><?php echo $row['noc']; ?></td>
                                </tr>
                                <?php } ?>
                        </tbody>
                </table>
            </div>
            <?php
                // pass the total number of records to the pagination class
                $pagination->records($TotalRcount['country']);
                // records per page
                $pagination->records_per_page($records_per_page);
                // render the pagination links
                $pagination->render();
                /* close connection */
                $conn->close();
            ?>
        </div>
        <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
        <script type="text/javascript" src="js/zebra_pagination.js"></script>
    </body>
</html>

