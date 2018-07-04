## version 2.3 (July 04, 2018)

- a new [css_classes](https://stefangabos.github.io/Zebra_Pagination/Zebra_Pagination/Zebra_Pagination.html#methodcss_classes) method was added for setting the CSS classes used in the markup; additionally, the library is now also compatible with [Twitter Bootstrap 4](http://getbootstrap.com/)
- updated example to use the `mysqli` extension instead of the `mysql` extension, fixing [#6](https://github.com/stefangabos/Zebra_Pagination/issues/6) - thank you [Elra Ghifary](https://github.com/elraghifary)
- fixed some typos in the examples (see [#9](https://github.com/stefangabos/Zebra_Pagination/pull/9))

## version 2.2.3 (May 20, 2017)

- unnecessary files are no more included when downloading from GitHub or via Composer

## version 2.2.2 (May 17, 2017)

- minor source code tweaks
- documentation is now available in the repository and on GitHub
- the home of the library is now exclusively on GitHub

## version 2.2.1 (February 19, 2016)

- fixed an issue when using the library with Composer

## version 2.2.0 (January 04, 2016)

- the library is now compatible with [Twitter Bootstrap](http://getbootstrap.com/)

## version 2.1.1 (July 15, 2013)

- fixed a bug where wrong class name was used in the JavaScript files
- project is now available on [GitHub](https://github.com/stefangabos/Zebra_Pagination/) and as a [package for Composer](https://packagist.org/packages/stefangabos/zebra_pagination)

## version 2.1 (February 06, 2013)

- fixed some bugs with having pagination links shown in reverse order; thanks to **marcin** for all the feedback
- position of next/previous links can now be changed so instead of being on the outside of the links to individual pages these can now be shown also either to the left or to the right; see the newly added [navigation_position](https://stefangabos.github.io/Zebra_Pagination/Zebra_Pagination/Zebra_Pagination.html#methodnavigation_position) method

## version 2.0 (January 28, 2013)

- dropped support for PHP4; the library now requires PHP5+
- fixed a bug where if parts of the URL had HTML entities, these would wrongly be encoded again by the library when generating the pagination links; thanks to **Phil**
- "next page" and "previous page" links will now show their associated labels by default (label were previously hidden from the library's stylesheet file); labels can be changed/localized with the newly added [labels](https://stefangabos.github.io/Zebra_Pagination/Zebra_Pagination/Zebra_Pagination.html#methodlabels) method; also, the "next page" and "previous page" links are now always visible instead of just when there were more pages than the value of [selectable pages](https://stefangabos.github.io/Zebra_Pagination/Zebra_Pagination/Zebra_Pagination.html#methodselectable_pages)
- pagination links can now be generated also in reverse order; use the newly added [reverse](https://stefangabos.github.io/Zebra_Pagination/Zebra_Pagination/Zebra_Pagination.html#methodreverse) method for that; thanks to **marcin** for suggesting
- everything is now centered by default
- changed the "next" and "previous" icons
- tweaked the CSS file

## version 1.4 (September 03, 2012)

- in order to make the library more SEO friendly and to indicate the relationship between component URLs in a paginated series, *rel="prev"* and *rel="next"* were added to previous/next links; read more about [pagination with rel="next" and rel="prev"](http://googlewebmastercentral.blogspot.de/2011/09/pagination-with-relnext-and-relprev.html); thanks to **Igor**
- corrections and additions to the documentation; thanks to **Roberto Gomes**

## version 1.3 (May 03, 2012)

- the URL specified through the "base_path" method can now contain query strings; previously query strings in this value got automatically removed; also, any query strings existing in the page's URL were *always* preserved - now the "base_path" method accepts an additional argument to disable this behavior; thanks to **Kesuma**, **Augusto Carvalho dos Santos**, **moregatest** and to all the people reporting these things
- by also including a newly added JavaScript file, hashes in the URL can now also be preserved; the simple inclusion of the JavaScript file will do the trick; not including it will mean that hashes will not be preserved; thanks to **Alan** for requesting this

## version 1.2.2 (October 14, 2011)

- fixed a bug where query strings got deleted if URLs were SEO friendly; thanks to **theyouyou**
- added a new method for setting whether the script should add a trailing slash to the URLs when generating SEO friendly URLs; read more on the subject on [Official Google Webmaster Central Blog](http://googlewebmastercentral.blogspot.com/2010/04/to-slash-or-not-to-slash.html); thanks to **theyouyou** for suggesting.

## version 1.2.1 (September 25, 2011)

- fixed a bug that appeared in version 1.2 that would remove all query string parameters from the URL, except the page-related one; thanks to **kumbing** for reporting
- when *method* is "url", the link to the first page does not include the "page" parameter anymore, in order to avoid duplicate content; previously this was true only for when *method* was "get"

## version 1.2 (September 18, 2011)

- the link to the first page does not include the "page" parameter anymore, in order to avoid duplicate content; thanks to **Sebi Popa** for suggesting
- some optimizations were made in the code

## version 1.1c (May 05, 2011)

- fixed a bug with the "next" link, when on first page; thanks to **Jan** for reporting.

## version 1.1b (April 30, 2011)

- fixed a bug where disabling the "next" and "previous" links, when on first or last page respectively, was not working properly; thanks to **Javier** for reporting.

## version 1.1 (March 20, 2011)

- fixed a bug where the "padding" method was not working (thanks to **D. Koper** for reporting)
- fixed a bug where the "set_page" method was not working correctly (thanks to **D. Koper** for reporting)
- when there is a single page available, the pagination links are not displayed anymore (thanks to **Sebi P.** for the suggestion)
- default style was tweaked a bit

## version 1.0.1 (January 08, 2011)

- entire code was audited and improved
- cleaner output
- more complete examples were added
- method names, method arguments and global properties were changed and therefore this version breaks compatibility with previous ones

## version 1.0 (June 04, 2009)

- initial release
