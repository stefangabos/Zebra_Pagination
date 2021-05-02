<?php

function prep_query_string($parameters, $query_string = '') {

    // make an array out of the query string given as argument or from QUERY_STRING otherwise
    parse_str($query_string != '' ? $query_string : (isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : ''), $query);

    // iterate through the given parameters
    foreach ($parameters as $name => $value)

        // if value is not an empty string
        if ($value != '')

            // add/update existing parameters
            $query[$name] = $value;

        // if value is an empty string
        else

            // remove the parameter
            unset($query[$name]);

    // transform the array back to string
    $result = http_build_query($query);

    // prefix the result with "?" if it is not an empty string
    return ($result != '' ? '?' : '') . $result;

}
