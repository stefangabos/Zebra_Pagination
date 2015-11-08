## Zebra_Pagination

#### A generic pagination library that automatically generates navigation links

A generic pagination script that automatically generates navigation links as well as next/previous page links, given the total number of records and the number of records to be shown per page. Useful for breaking large sets of data into smaller chunks, reducing network traffic and, at the same time, improving readability, aesthetics and usability.

Adheres to pagination best practices (provides large clickable areas, doesn't use underlines, the selected page is clearly highlighted, page links are spaced out, provides "previous page" and "next page" links, provides "first page" and "last page" links - as outlined in an article by Faruk Ates from 2007, which can now be found [here](https://gist.github.com/622561), can generate links both in natural as well as in reverse order, can be easily, localized, supports different positions for next/previous page buttons, supports page propagation via GET or via URL rewriting, is SEO-friendly, and the appearance is easily customizable through CSS.

Please note that this is a *generic* pagination script, meaning that it does not display any records and it does not have any dependencies on database connections or SQL queries, making it very flexible! It is up to the developer to fetch the actual data and display it based on the information returned by this pagination script. The advantage is that it can be used to paginate over records coming from any source like arrays or databases.

The code is heavily commented and generates no warnings/errors/notices when PHP's error reporting level is set to `E_ALL`.

##Features

- it is a generic library: can be used to paginate records both from an array or from a database
- it automatically generates navigation links, given the total number of items and the number of items per page (examples of best practices are also included)
- navigation links can be generated in natural or in reverse order
- is SEO-friendly – it uses `rel="next"` and `rel="prev"` and solves the problem of duplicate content on the first page without navigation and the first page having the page number in the URL
- appearance is easily customizable through CSS
- code is heavily commented and generates no warnings/errors/notices when PHP’s error reporting level is set to `E_ALL`
- has comprehensive documentation

## Requirements

PHP `>=5.2.0`

## How to use

[Docs File](docs/DOCS.md).


Visit the **[project's homepage](http://stefangabos.ro/php-libraries/zebra-pagination/)** for more information.
