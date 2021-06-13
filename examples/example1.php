<?php ini_set('display_errors', 1); error_reporting(E_ALL); ?>
<?php require 'common.php'; ?>
<!doctype html>
<html>
<head>
    <title>Zebra Pagination, array example</title>
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

    <h2>Zebra Pagination, array example</h2><br>

    <p>

        Show next/previous page links on the
        <a href="example1.php<?php echo prep_query_string(array('navigation_position' => 'left')); ?>"<?php echo isset($_GET['navigation_position']) && $_GET['navigation_position'] == 'left' ? ' class="active"' : ''; ?>>left</a> or on the
        <a href="example1.php<?php echo prep_query_string(array('navigation_position' => 'right')); ?>"<?php echo isset($_GET['navigation_position']) && $_GET['navigation_position'] == 'right' ? ' class="active"' : ''; ?>>right</a>, or use the
        <a href="example1.php<?php echo prep_query_string(array('navigation_position' => '')); ?>"<?php echo !isset($_GET['navigation_position']) || !in_array($_GET['navigation_position'], array('left', 'right')) ? ' class="active"' : ''; ?>>default style</a>.

        <br>

        Show pagination links in
        <a href="example1.php<?php echo prep_query_string(array('reversed' => '')); ?>"<?php echo !isset($_GET['reversed']) || $_GET['reversed'] != 1 ? ' class="active"' : ''; ?>>natural</a> or
        <a href="example1.php<?php echo prep_query_string(array('reversed' => 1)); ?>"<?php echo isset($_GET['reversed']) && $_GET['reversed'] == 1 ? ' class="active"' : ''; ?>>reversed</a> order.

        <br>

        Show
        <a href="example1.php<?php echo prep_query_string(array('condensed' => 1)); ?>"<?php echo isset($_GET['condensed']) && $_GET['condensed'] == 1 ? ' class="active"' : ''; ?>>condensed</a>,
        <a href="example1.php<?php echo prep_query_string(array('condensed' => 2)); ?>"<?php echo isset($_GET['condensed']) && $_GET['condensed'] == 2 ? ' class="active"' : ''; ?>>very condensed</a> or the
        <a href="example1.php<?php echo prep_query_string(array('condensed' => '')); ?>"<?php echo !isset($_GET['condensed']) || !in_array($_GET['condensed'], array(1, 2)) ? ' class="active"' : ''; ?>>default</a> style.

        <br>

        See the
        <a href="example1.php<?php echo prep_query_string(array('bootstrap' => '')); ?>"<?php echo !isset($_GET['bootstrap']) || !in_array($_GET['bootstrap'], array(3, 4)) ? ' class="active"' : ''; ?>>default</a> looks, the
        <a href="example1.php<?php echo prep_query_string(array('bootstrap' => 3)); ?>"<?php echo isset($_GET['bootstrap']) && $_GET['bootstrap'] == 3 ? ' class="active"' : ''; ?>>Bootstrap 3</a> looks, the
        <a href="example1.php<?php echo prep_query_string(array('bootstrap' => 4)); ?>"<?php echo isset($_GET['bootstrap']) && $_GET['bootstrap'] == 4 ? ' class="active"' : ''; ?>>Bootstrap 4</a> looks, or the
        <a href="example1.php<?php echo prep_query_string(array('bootstrap' => 5)); ?>"<?php echo isset($_GET['bootstrap']) && $_GET['bootstrap'] == 5 ? ' class="active"' : ''; ?>>Bootstrap 5</a> looks
        <br>

        <br><small><em>(when using Bootstrap you don't need to include the <code>zebra_pagination.css</code> file anymore)</em></small>

        <?php if (isset($_GET['bootstrap']) && $_GET['bootstrap'] == 4): ?>
        <br><small><em>For Bootstrap 4, for centering the pagination links you have to set <code>justify-content: center;</code> for the <code>.pagination</code> class</em></small>
        <?php endif; ?>

    </p>

    <?php

    // let's paginate data from an array...
    $countries = array(

        'Afghanistan', 'Aland Islands', 'Albania', 'Algeria', 'American Samoa', 'Andorra', 'Angola', 'Anguilla', 'Antarctica',
        'Antigua And Barbuda', 'Argentina', 'Armenia', 'Aruba', 'Australia', 'Austria', 'Azerbaijan', 'Bahamas', 'Bahrain',
        'Bangladesh', 'Barbados', 'Belarus', 'Belgium', 'Belize', 'Benin', 'Bermuda', 'Bhutan', 'Bolivia', 'Bosnia And
        Herzegowina', 'Botswana', 'Bouvet Island', 'Brazil', 'British Indian Ocean Territory', 'Brunei Darussalam', 'Bulgaria',
        'Burkina Faso', 'Burundi', 'Cambodia', 'Cameroon', 'Canada', 'Cape Verde', 'Cayman Islands', 'Central African
        Republic', 'Chad', 'Chile', 'China', 'Christmas Island', 'Cocos (Keeling) Islands', 'Colombia', 'Comoros', 'Congo',
        'Congo, The Democratic Republic Of The', 'Cook Islands', 'Costa Rica', 'Cote D\'Ivoire', 'Croatia', 'Cuba', 'Cyprus',
        'Czech Republic', 'Denmark', 'Djibouti', 'Dominica', 'Dominican Republic', 'Ecuador', 'Egypt', 'El Salvador',
        'Equatorial Guinea', 'Eritrea', 'Estonia', 'Ethiopia', 'Falkland Islands (Malvinas)', 'Faroe Islands', 'Fiji',
        'Finland', 'France', 'French Guiana', 'French Polynesia', 'French Southern Territories', 'Gabon', 'Gambia', 'Georgia',
        'Germany', 'Ghana', 'Gibraltar', 'Greece', 'Greenland', 'Grenada', 'Guadeloupe', 'Guam', 'Guatemala', 'Guinea',
        'Guinea-Bissau', 'Guyana', 'Haiti', 'Heard And Mc Donald Islands', 'Holy See (Vatican City State)', 'Honduras', 'Hong
        Kong', 'Hungary', 'Iceland', 'India', 'Indonesia', 'Iran, Islamic Republic Of', 'Iraq', 'Ireland', 'Israel', 'Italy',
        'Jamaica', 'Japan', 'Jordan', 'Kazakhstan', 'Kenya', 'Kiribati', 'Korea, Democratic People\'S Republic Of', 'Korea,
        Republic Of', 'Kuwait', 'Kyrgyzstan', 'Lao People\'S Democratic Republic', 'Latvia', 'Lebanon', 'Lesotho', 'Liberia',
        'Libyan Arab Jamahiriya', 'Liechtenstein', 'Lithuania', 'Luxembourg', 'Macau', 'Macedonia, The Former Yugoslav
        Republic Of', 'Madagascar', 'Malawi', 'Malaysia', 'Maldives', 'Mali', 'Malta', 'Marshall Islands', 'Martinique',
        'Mauritania', 'Mauritius', 'Mayotte', 'Mexico', 'Micronesia, Federated States Of', 'Moldova, Republic Of', 'Monaco',
        'Mongolia', 'Montserrat', 'Morocco', 'Mozambique', 'Myanmar', 'Namibia', 'Nauru', 'Nepal', 'Netherlands', 'Netherlands
        Antilles', 'New Caledonia', 'New Zealand', 'Nicaragua', 'Niger', 'Nigeria', 'Niue', 'Norfolk Island', 'Northern
        Mariana Islands', 'Norway', 'Oman', 'Pakistan', 'Palau', 'Palestinian Territory, Occupied', 'Panama', 'Papua New
        Guinea', 'Paraguay', 'Peru', 'Philippines', 'Pitcairn', 'Poland', 'Portugal', 'Puerto Rico', 'Qatar', 'Reunion',
        'Romania', 'Russian Federation', 'Rwanda', 'Saint Helena', 'Saint Kitts And Nevis', 'Saint Lucia', 'Saint Pierre And
        Miquelon', 'Saint Vincent And The Grenadines', 'Samoa', 'San Marino', 'Sao Tome And Principe', 'Saudi Arabia',
        'Senegal', 'Serbia And Montenegro', 'Seychelles', 'Sierra Leone', 'Singapore', 'Slovakia', 'Slovenia', 'Solomon
        Islands', 'Somalia', 'South Africa', 'South Georgia And The South Sandwich Islands', 'Spain', 'Sri Lanka', 'Sudan',
        'Suriname', 'Svalbard And Jan Mayen Islands', 'Swaziland', 'Sweden', 'Switzerland', 'Syrian Arab Republic', 'Taiwan,
        Province Of China', 'Tajikistan', 'Tanzania, United Republic Of', 'Thailand', 'Timor-Leste', 'Togo', 'Tokelau',
        'Tonga', 'Trinidad And Tobago', 'Tunisia', 'Turkey', 'Turkmenistan', 'Turks And Caicos Islands', 'Tuvalu', 'Uganda',
        'Ukraine', 'United Arab Emirates', 'United Kingdom', 'United States', 'United States Minor Outlying Islands',
        'Uruguay', 'Uzbekistan', 'Vanuatu', 'Venezuela', 'Viet Nam', 'Virgin Islands, British', 'Virgin Islands, U.S.',
        'Wallis And Futuna', 'Western Sahara', 'Yemen', 'Zambia', 'Zimbabwe',

    );

    // how many records should be displayed on a page?
    $records_per_page = 10;

    // include the pagination class
    require '../Zebra_Pagination.php';

    // instantiate the pagination object
    $pagination = new Zebra_Pagination();

    // set position of the next/previous page links
    $pagination->navigation_position(isset($_GET['navigation_position']) && in_array($_GET['navigation_position'], array('left', 'right')) ? $_GET['navigation_position'] : 'outside');

    // if we have to show records in reversed order
    if (isset($_GET['reversed']) && $_GET['reversed'] == 1) $pagination->reverse(true);

    // if we have to show condensed links
    if (isset($_GET['condensed']) && ($_GET['condensed'] == 1 || $_GET['condensed'] == 2)) $pagination->condensed($_GET['condensed'] == 2 ? true : '');

    // the number of total records is the number of records in the array
    $pagination->records(count($countries));

    // records per page
    $pagination->records_per_page($records_per_page);

    // here's the magick: we need to display *only* the records for the current page
    $countries = array_slice(
        $countries,                                             //  from the original array we extract
        (($pagination->get_page() - 1) * $records_per_page),    //  starting with these records
        $records_per_page                                       //  this many records
    );

    ?>

    <table class="countries table" border="1">
        <thead>
            <tr><th>Country</th></tr>
        </thead>
        <tbody>

        <?php foreach ($countries as $index => $country): ?>
        <tr<?php echo $index % 2 ? ' class="even"' : ''; ?>>
            <td><?php echo $country; ?></td>
        </tr>
        <?php endforeach; ?>

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
