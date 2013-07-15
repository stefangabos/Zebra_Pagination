$(document).ready(function() {

    // get the hash in the URL (if any)
    var matches = document.location.href.match(/\#(.*)$/)

    // if a hash exists in the URL
    if (matches)

        // iterate through the pagination links
        $('.Zebra_Pagination a').each(function() {

            // reference to the element
            var $element = $(this);

            // attach the hash to every link
            // (remember that the hash is not available server-side and that's why we're doing it client-side)
            $element.attr('href', $element.attr('href') + '#' + matches[1]);

        });

});