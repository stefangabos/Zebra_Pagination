<?php

/**
 *  A generic pagination script that automatically generates navigation links as well as next/previous page links, given
 *  the total number of records and the number of records to be shown per page. Useful for breaking large sets of data
 *  into smaller chunks, reducing network traffic and, at the same time, improving readability, aesthetics and usability.
 *
 *  Adheres to pagination best practices (provides large clickable areas, doesn't use underlines, the selected page is
 *  clearly highlighted, page links are spaced out, provides "previous page" and "next page" links, provides "first page"
 *  and "last page" links - as outlined in an article by Faruk Ates from 2007, which can now be found
 *  {@link https://gist.github.com/622561 here}), can generate links both in natural as well as in reverse order, can be
 *  easily, localized, supports different positions for next/previous page buttons, supports page propagation via GET or
 *  via URL rewriting, is SEO-friendly, and the appearance is easily customizable through CSS.
 *
 *  The library is compatible with {@link http://getbootstrap.com/ Twitter Bootstrap}.
 *
 *  Please note that this is a *generic* pagination script, meaning that it does not display any records and it does not
 *  have any dependencies on database connections or SQL queries, making it very flexible! It is up to the developer to
 *  fetch the actual data and display it based on the information returned by this pagination script. The advantage is
 *  that it can be used to paginate over records coming from any source like arrays or databases.
 *
 *  The code is heavily commented and generates no warnings/errors/notices when PHP's error reporting level is set to
 *  E_ALL.
 *
 *  Visit {@link http://stefangabos.ro/php-libraries/zebra-pagination/} for more information.
 *
 *  For more resources visit {@link http://stefangabos.ro/}
 *
 *  @author     Stefan Gabos <contact@stefangabos.ro>
 *  @version    2.2.1 (last revision: February 19, 2016)
 *  @copyright  (c) 2009 - 2016 Stefan Gabos
 *  @license    http://www.gnu.org/licenses/lgpl-3.0.txt GNU LESSER GENERAL PUBLIC LICENSE
 *  @package    Zebra_Pagination
 */

class Zebra_Pagination
{

    // set defaults and initialize some private variables
    private $_properties = array(

        // should the "previous page" and "next page" links be always visible
        'always_show_navigation'    =>  true,

        // should we avoid duplicate content
        'avoid_duplicate_content'   =>  true,

        // default method for page propagation
        'method'                    =>  'get',

        // string for "next page"
        'next'                      =>  '&raquo;',

        // by default, prefix page number with zeroes
        'padding'                   =>  true,

        // the default starting page
        'page'                      =>  1,

        // a flag telling whether current page was set manually or determined from the URL
        'page_set'                  =>  false,

        'navigation_position'       =>  'outside',

        // a flag telling whether query strings in base_url should be kept or not
        'preserve_query_string'     =>  0,

        // string for "previous page"
        'previous'                  =>  '&laquo;',

        // by default, we assume there are no records
        // we expect this number to be set after the class is instantiated
        'records'                   =>  '',

        // records per page
        'records_per_page'          =>  '',

        // should the links be displayed in reverse order
        'reverse'                   =>  false,

        // number of selectable pages
        'selectable_pages'          =>  11,

        // will be computed later on
        'total_pages'               =>  0,

        // trailing slashes are added to generated URLs
        // (when "method" is "url")
        'trailing_slash'            =>  true,

        // this is the variable name to be used in the URL for propagating the page number
        'variable_name'             =>  'page',

    );

    /**
     *  Constructor of the class.
     *
     *  Initializes the class and the default properties.
     *
     *  @return void
     */
    function __construct()
    {

        // set the default base url
        $this->base_url();

    }

    /**
     *  By default, the "previous page" and "next page" links are always shown.
     *
     *  By disabling this feature the "previous page" and "next page" links will only be shown if there are more pages
     *  than set through {@link selectable_pages()}.
     *
     *  <code>
     *  // show "previous page" / "next page" only if there are more pages
     *  // than there are selectable pages
     *  $pagination->always_show_navigation(false);
     *  </code>
     *
     *  @param  boolean     $show   (Optional) If set to FALSE, the "previous page" and "next page" links will only be
     *                              shown if there are more pages than set through {@link selectable_pages()}.
     *
     *                              Default is TRUE.
     *
     *  @since  2.0
     *
     *  @return void
     */
    public function always_show_navigation($show = true)
    {

        // set property
        $this->_properties['always_show_navigation'] = $show;

    }

    /**
     *  When you first access a page with navigation you have the same content as you have when you access the first page
     *  from navigation. For example http://www.mywebsite.com/list will have the same content as http://www.mywebsite.com/list?page=1.
     *
     *  From a search engine's point of view these are 2 distinct URLs having the same content and your pages will be
     *  penalized for that.
     *
     *  So, by default, in order to avoid this, the library will have for the first page (or last, if you are displaying
     *  links in {@link reverse() reverse} order) the same path as you have for when you are accessing the page for the
     *  first (unpaginated) time.
     *
     *  If you want to disable this behaviour call this method with its argument set to FALSE.
     *
     *  <code>
     *  // don't avoid duplicate content
     *  $pagination->avoid_duplicate_content(false);
     *  </code>
     *
     *  @param  boolean     $avoid_duplicate_content    (Optional) If set to FALSE, the library will have for the first
     *                                                  page (or last, if you are displaying links in {@link reverse() reverse}
     *                                                  order) a different path than the one you have when you are accessing
     *                                                  the page for the first (unpaginated) time.
     *
     *                                                  Default is TRUE.
     *
     *  @return void
     *
     *  @since  2.0
     */
    function avoid_duplicate_content($avoid_duplicate_content = true)
    {

        // set property
        $this->_properties['avoid_duplicate_content'] = $avoid_duplicate_content;

    }

    /**
     *  The base URL to be used when generating the navigation links.
     *
     *  This is helpful for the case when, for example, the URL where the records are paginated may have parameters that
     *  are needed only once and need to be removed for any subsequent requests generated by pagination.
     *
     *  For example, suppose some records are paginated at <i>http://yourwebsite/mypage/</i>. When a record from the list
     *  is updated, the URL could become something like <i>http://youwebsite/mypage/?action=updated</i>. Based on the
     *  value of <i>action</i> a message would be shown to the user.
     *
     *  Because of the way this script works, the pagination links would become:
     *
     *  -   <i>http://youwebsite/mypage/?action=updated&page=[page number]</i> when {@link method()} is "get" and
     *      {@link variable_name()} is "page";
     *
     *  -   <i>http://youwebsite/mypage/page[page number]/?action=updated</i> when {@link method()} is "url" and
     *      {@link variable_name()} is "page").
     *
     *  Because of this, whenever the user would paginate, the message would be shown to him again and again because
     *  <i>action</i> will be preserved in the URL!
     *
     *  The solution is to set the <i>base_url</i> to <i>http://youwebsite/mypage/</i> and in this way, regardless of
     *  however will the URL be changed, the pagination links will always be in the form of
     *
     *  -   <i>http://youwebsite/mypage/?page=[page number]</i> when {@link method()} is "get" and {@link variable_name()}
     *      is "page";
     *
     *  -   <i>http://youwebsite/mypage/page[page number]/</i> when {@link method()} is "url" and {@link variable_name()} is "page").
     *
     *  Of course, you may still have query strings in the value of the $base_url if you wish so, and these will be
     *  preserved when paginating.
     *
     *  <samp>If you want to preserve the hash in the URL, make sure you load the zebra_pagination.js file!</samp>
     *
     *  @param  string      $base_url                   (Optional) The base URL to be used when generating the navigation
     *                                                  links
     *
     *                                                  Defaults is whatever returned by
     *                                                  {@link http://www.php.net/manual/en/reserved.variables.server.php $_SERVER['REQUEST_URI']}
     *
     *  @param  boolean     $preserve_query_string      (Optional) Indicates whether values in query strings, other than
     *                                                  those set in $base_url, should be preserved
     *
     *                                                  Default is TRUE
     *
     *  @return void
     */
    public function base_url($base_url = '', $preserve_query_string = true)
    {

        // set the base URL
        $base_url = ($base_url == '' ? $_SERVER['REQUEST_URI'] : $base_url);

        // parse the URL
        $parsed_url = parse_url($base_url);

        // cache the "path" part of the URL (that is, everything *before* the "?")
        $this->_properties['base_url'] = $parsed_url['path'];

        // cache the "query" part of the URL (that is, everything *after* the "?")
        $this->_properties['base_url_query'] = isset($parsed_url['query']) ? $parsed_url['query'] : '';

        // store query string as an associative array
        parse_str($this->_properties['base_url_query'], $this->_properties['base_url_query']);

        // should query strings (other than those set in $base_url) be preserved?
        $this->_properties['preserve_query_string'] = $preserve_query_string;

    }

    /**
     *  Returns the current page's number.
     *
     *  <code>
     *  // echoes the current page
     *  echo $pagination->get_page();
     *  </code>
     *
     *  @return integer     Returns the current page's number
     */
    public function get_page()
    {

        // unless page was not specifically set through the "set_page" method
        if (!$this->_properties['page_set']) {

            // if
            if (

                // page propagation is SEO friendly
                $this->_properties['method'] == 'url' &&

                // the current page is set in the URL
                preg_match('/\b' . preg_quote($this->_properties['variable_name']) . '([0-9]+)\b/i', $_SERVER['REQUEST_URI'], $matches) > 0

            )

                // set the current page to whatever it is indicated in the URL
                $this->set_page((int)$matches[1]);

            // if page propagation is done through GET and the current page is set in $_GET
            elseif (isset($_GET[$this->_properties['variable_name']]))

                // set the current page to whatever it was set to
                $this->set_page((int)$_GET[$this->_properties['variable_name']]);

        }

        // if showing records in reverse order we must know the total number of records and the number of records per page
        // *before* calling the "get_page" method
        if ($this->_properties['reverse'] && $this->_properties['records'] == '') trigger_error('When showing records in reverse order you must specify the total number of records (by calling the "records" method) *before* the first use of the "get_page" method!', E_USER_ERROR);

        if ($this->_properties['reverse'] && $this->_properties['records_per_page'] == '') trigger_error('When showing records in reverse order you must specify the number of records per page (by calling the "records_per_page" method) *before* the first use of the "get_page" method!', E_USER_ERROR);

        // get the total number of pages
        $this->_properties['total_pages'] = $this->get_pages();

        // if there are any pages
        if ($this->_properties['total_pages'] > 0) {

            // if current page is beyond the total number pages
            /// make the current page be the last page
            if ($this->_properties['page'] > $this->_properties['total_pages']) $this->_properties['page'] = $this->_properties['total_pages'];

            // if current page is smaller than 1
            // make the current page 1
            elseif ($this->_properties['page'] < 1) $this->_properties['page'] = 1;

        }

        // if we're just starting and we have to display links in reverse order
        // set the first to the last one rather then first
        if (!$this->_properties['page_set'] && $this->_properties['reverse']) $this->set_page($this->_properties['total_pages']);

        // return the current page
        return $this->_properties['page'];

    }

    /**
     *  Returns the total number of pages, based on the total number of records and the number of records to be shown
     *  per page.
     *
     *  <code>
     *  // get the total number of pages
     *  echo $pagination->get_pages();
     *  </code>
     *
     *  @since  2.1
     *
     *  @return integer     Returns the total number of pages, based on the total number of records and the number of
     *                      records to be shown per page.
     */
    public function get_pages()
    {

        // return the total number of pages based on the total number of records and number of records to be shown per page
        return @ceil($this->_properties['records'] / $this->_properties['records_per_page']);

    }

    /**
     *  Change the labels for the "previous page" and "next page" links.
     *
     *  <code>
     *  // change the default labels
     *  $pagination->labels('Previous', 'Next');
     *  </code>
     *
     *  @param  string  $previous   (Optional) The label for the "previous page" link.
     *
     *                              Default is "Previous page".
     *
     *  @param  string  $next       (Optional) The label for the "next page" link.
     *
     *                              Default is "Next page".
     *  @return void
     *
     *  @since  2.0
     */
    public function labels($previous = '&laquo;', $next = '&raquo;')
    {

        // set the labels
        $this->_properties['previous'] = $previous;
        $this->_properties['next'] = $next;

    }

    /**
     *  Set the method to be used for page propagation.
     *
     *  <code>
     *  // set the method to the SEO friendly way
     *  $pagination->method('url');
     *  </code>
     *
     *  @param  string  $method     (Optional) The method to be used for page propagation.
     *
     *                              Values can be:
     *
     *                              - <b>url</b> - page propagation is done in a SEO friendly way;
     *
     *                              This method requires the {@link http://httpd.apache.org/docs/current/mod/mod_rewrite.html mod_rewrite}
     *                              module to be enabled on your Apache server (or the equivalent for other web servers);
     *
     *                              When using this method, the current page will be passed in the URL as
     *                              <i>http://youwebsite.com/yourpage/[variable name][page number]/</i> where
     *                              <i>[variable name]</i> is set by {@link variable_name()} and <i>[page number]</i>
     *                              represents the current page.
     *
     *                              - <b>get</b> - page propagation is done through GET;
     *
     *                              When using this method, the current page will be passed in the URL as
     *                              <i>http://youwebsite.com/yourpage?[variable name]=[page number]</i> where
     *                              <i>[variable name]</i> is set by {@link variable_name()} and <i>[page number]</i>
     *                              represents the current page.
     *
     *                              Default is "get".
     *
     *  @returns void
     */
    public function method($method = 'get')
    {

        // set the page propagation method
        $this->_properties['method'] = (strtolower($method) == 'url' ? 'url' : 'get') ;

    }

    /**
     *  By default, next/previous page links are shown on the outside of the links to individual pages.
     *
     *  These links can also be shown both on the left or on the right of the links to individual pages by setting the
     *  method's argument to "left" or "right" respectively.
     *
     *  @param  string  $position   Setting this argument to "left" or "right" will instruct the script to show next/previous
     *                              page links on the left or on the right of the links to individual pages.
     *
     *                              Allowed values are "left", "right" and "outside".
     *
     *                              Default is "outside".
     *
     *  @since  2.1
     *
     *  @return void
     */
    function navigation_position($position)
    {

        // set the positioning of next/previous page links
        $this->_properties['navigation_position'] = (in_array(strtolower($position), array('left', 'right')) ? strtolower($position) : 'outside') ;

    }

    /**
     *  Sets whether page numbers should be prefixed by zeroes.
     *
     *  This is useful to keep the layout consistent by having the same number of characters for each page number.
     *
     *  <code>
     *  // disable padding numbers with zeroes
     *  $pagination->padding(false);
     *  </code>
     *
     *  @param  boolean     $enabled    (Optional) Setting this property to FALSE will disable padding rather than
     *                                  enabling it.
     *
     *                                  Default is TRUE.
     *
     *  @return void
     */
    public function padding($enabled = true)
    {

        // set padding
        $this->_properties['padding'] = $enabled;

    }

    /**
     *  Sets the total number of records that need to be paginated.
     *
     *  Based on this and on the value of {@link records_per_page()}, the script will know how many pages there are.
     *
     *  The total number of pages is given by the fraction between the total number records (set through {@link records()})
     *  and the number of records that are shown on a page (set through {@link records_per_page()}).
     *
     *  <code>
     *  // tell the script that there are 100 total records
     *  $pagination->records(100);
     *  </code>
     *
     *  @param  integer     $records    The total number of records that need to be paginated
     *
     *  @return void
     */
    public function records($records)
    {

        // the number of records
        // make sure we save it as an integer
        $this->_properties['records'] = (int)$records;

    }

    /**
     *  Sets the number of records that are displayed on one page.
     *
     *  Based on this and on the value of {@link records()}, the script will know how many pages there are: the total
     *  number of pages is given by the fraction between the total number of records and the number of records that are
     *  shown on one page.
     *
     *  <code>
     *  //  tell the class that there are 20 records displayed on one page
     *  $pagination->records_per_page(20);
     *  </code>
     *
     *  @param  integer     $records_per_page   The number of records displayed on one page.
     *
     *                      Default is 10.
     *
     *  @return void
     */
    public function records_per_page($records_per_page)
    {

        // the number of records displayed on one page
        // make sure we save it as an integer
        $this->_properties['records_per_page'] = (int)$records_per_page;

    }

    /**
     *  Generates the output.
     *
     *  <i>Make sure your script references the CSS file!</i>
     *
     *  <code>
     *  //  generate output but don't echo it
     *  //  but return it instead
     *  $output = $pagination->render(true);
     *  </code>
     *
     *  @param  boolean     $return_output      (Optional) Setting this argument to TRUE will instruct the script to
     *                                          return the generated output rather than outputting it to the screen.
     *
     *                                          Default is FALSE.
     *
     *  @return void
     */
    public function render($return_output = false)
    {

        // get some properties of the class
        $this->get_page();

        // if there is a single page, or no pages at all, don't display anything
        if ($this->_properties['total_pages'] <= 1) return '';

        // start building output
        $output = '<div class="Zebra_Pagination"><ul class="pagination">';

        // if we're showing records in reverse order
        if ($this->_properties['reverse']) {

            // if "next page" and "previous page" links are to be shown to the left of the links to individual pages
            if ($this->_properties['navigation_position'] == 'left')

                // first show next/previous and then page links
                $output .= $this->_show_next() . $this->_show_previous() . $this->_show_pages();

            // if "next page" and "previous page" links are to be shown to the right of the links to individual pages
            elseif ($this->_properties['navigation_position'] == 'right')

                $output .= $this->_show_pages() . $this->_show_next() . $this->_show_previous();

            // if "next page" and "previous page" links are to be shown on the outside of the links to individual pages
            else $output .= $this->_show_next() . $this->_show_pages() . $this->_show_previous();

        // if we're showing records in natural order
        } else {

            // if "next page" and "previous page" links are to be shown to the left of the links to individual pages
            if ($this->_properties['navigation_position'] == 'left')

                // first show next/previous and then page links
                $output .= $this->_show_previous() . $this->_show_next() . $this->_show_pages();

            // if "next page" and "previous page" links are to be shown to the right of the links to individual pages
            elseif ($this->_properties['navigation_position'] == 'right')

                $output .= $this->_show_pages() . $this->_show_previous() . $this->_show_next();

            // if "next page" and "previous page" links are to be shown on the outside of the links to individual pages
            else $output .= $this->_show_previous() . $this->_show_pages() . $this->_show_next();

        }

        // finish generating the output
        $output .= '</ul></div>';

        // if $return_output is TRUE
        // return the generated content
        if ($return_output) return $output;

        // if script gets this far, print generated content to the screen
        echo $output;

    }

    /**
     *  By default, pagination links are shown in natural order, from 1 to the number of total pages.
     *
     *  Calling this method with the argument set to TRUE will generate links in reverse order, from the number of total
     *  pages to 1.
     *
     *  <code>
     *  // show pagination links in reverse order rather than in natural order
     *  $pagination->reverse(true);
     *  </code>
     *
     *  @param  boolean     $reverse    (Optional) Set it to TRUE to generate navigation links in reverse order.
     *
     *                                  Default is FALSE.
     *
     *  @return void
     *
     *  @since  2.0
     */
    public function reverse($reverse = false)
    {

        // set how the pagination links should be generated
        $this->_properties['reverse'] = $reverse;

    }

    /**
     *  Sets the number of links to be displayed at once (besides the "previous page" and "next page" links)
     *
     *  <code>
     *  // display links to 15 pages
     *  $pagination->selectable_pages(15);
     *  </code>
     *
     *  @param  integer     $selectable_pages   The number of links to be displayed at once (besides the "previous page"
     *                                          and "next page" links).
     *
     *                                          <i>You should set this to an odd number so that the same number of links
     *                                          will be shown to the left and to the right of the current page.</i>
     *
     *                                          Default is 11.
     *
     *  @return void
     */
    public function selectable_pages($selectable_pages)
    {

        // the number of selectable pages
        // make sure we save it as an integer
        $this->_properties['selectable_pages'] = (int)$selectable_pages;

    }

    /**
     *  Sets the current page.
     *
     *  <code>
     *  // sets the fifth page as the current page
     *  $pagination->set_page(5);
     *  </code>
     *
     *  @param  integer     $page           The page's number.
     *
     *                                      A number lower than <b>1</b> will be interpreted as <b>1</b>, while a number
     *                                      greater than the total number of pages will be interpreted as the last page.
     *
     *                                      The total number of pages is given by the fraction between the total number
     *                                      records (set through {@link records()}) and the number of records that are
     *                                      shown on one page (set through {@link records_per_page()}).
     *
     *  @return void
     */
    public function set_page($page)
    {

        // set the current page
        // make sure we save it as an integer
        $this->_properties['page'] = (int)$page;

        // if the number is lower than one
        // make it '1'
        if ($this->_properties['page'] < 1) $this->_properties['page'] = 1;

        // set a flag so that the "get_page" method doesn't change this value
        $this->_properties['page_set'] = true;

    }

    /**
     *  Enables or disabled trailing slash on the generated URLs when {@link method} is "url".
     *
     *  Read more on the subject on {@link http://googlewebmastercentral.blogspot.com/2010/04/to-slash-or-not-to-slash.html Google Webmasters's official blog}.
     *
     *  <code>
     *  // disables trailing slashes on generated URLs
     *  $pagination->trailing_slash(false);
     *  </code>
     *
     *  @param  boolean     $enabled    (Optional) Setting this property to FALSE will disable trailing slashes on generated
     *                                  URLs when {@link method} is "url".
     *
     *                                  Default is TRUE (trailing slashes are enabled by default).
     *
     *  @return void
     */
    public function trailing_slash($enabled)
    {

        // set the state of trailing slashes
        $this->_properties['trailing_slash'] = $enabled;

    }

    /**
     *  Sets the variable name to be used for page propagation.
     *
     *  <code>
     *  //  sets the variable name to "foo"
     *  //  now, in the URL, the current page will be passed either as "foo=[page number]" (if method is "get") or
     *  //  as "/foo[page number]" (if method is "url")
     *  $pagination->variable_name('foo');
     *  </code>
     *
     *  @param  string  $variable_name      A string representing the variable name to be used for page propagation.
     *
     *                                      Default is "page".
     *
     *  @return void
     */
    public function variable_name($variable_name)
    {

        // set the variable name
        $this->_properties['variable_name'] = strtolower($variable_name);

    }

    /**
     *  Generate the link for the page given as argument.
     *
     *  @access private
     *
     *  @return void
     */
    private function _build_uri($page)
    {

        // if page propagation method is through SEO friendly URLs
        if ($this->_properties['method'] == 'url') {

            // see if the current page is already set in the URL
            if (preg_match('/\b' . $this->_properties['variable_name'] . '([0-9]+)\b/i', $this->_properties['base_url'], $matches) > 0) {

                // build string
                $url = str_replace('//', '/', preg_replace(

                    // replace the currently existing value
                    '/\b' . $this->_properties['variable_name'] . '([0-9]+)\b/i',

                    // if on the first page, remove it in order to avoid duplicate content
                    ($page == 1 ? '' : $this->_properties['variable_name'] . $page),

                    $this->_properties['base_url']

                ));

            // if the current page is not yet in the URL, set it, unless we're on the first page
            // case in which we don't set it in order to avoid duplicate content
            } else $url = rtrim($this->_properties['base_url'], '/') . '/' . ($this->_properties['variable_name'] . $page);

            // handle trailing slash according to preferences
            $url = rtrim($url, '/') . ($this->_properties['trailing_slash'] ? '/' : '');

            // if values in the query string - other than those set through base_url() - are not to be preserved
            // preserve only those set initially
            if (!$this->_properties['preserve_query_string']) $query = implode('&', $this->_properties['base_url_query']);

            // otherwise, get the current query string
            else $query = $_SERVER['QUERY_STRING'];

            // return the built string also appending the query string, if any
            return $url . ($query != '' ? '?' . $query : '');

        // if page propagation is to be done through GET
        } else {

            // if values in the query string - other than those set through base_url() - are not to be preserved
            // preserve only those set initially
            if (!$this->_properties['preserve_query_string']) $query = $this->_properties['base_url_query'];

            // otherwise, get the current query string, if any, and transform it to an array
            else parse_str($_SERVER['QUERY_STRING'], $query);

            // if we are avoiding duplicate content and if not the first/last page (depending on whether the pagination links are shown in natural or reversed order)
            if (!$this->_properties['avoid_duplicate_content'] || ($page != ($this->_properties['reverse'] ? $this->_properties['total_pages'] : 1)))

                // add/update the page number
                $query[$this->_properties['variable_name']] = $page;

            // if we are avoiding duplicate content, don't use the "page" variable on the first/last page
            elseif ($this->_properties['avoid_duplicate_content'] && $page == ($this->_properties['reverse'] ? $this->_properties['total_pages'] : 1))

                unset($query[$this->_properties['variable_name']]);

            // make sure the returned HTML is W3C compliant
            return htmlspecialchars(html_entity_decode($this->_properties['base_url']) . (!empty($query) ? '?' . urldecode(http_build_query($query)) : ''));

        }

    }

    /**
     *  Generates the "next page" link, depending on whether the pagination links are shown in natural or reversed order.
     *
     *  @access private
     */
    private function _show_next()
    {

        $output = '';

        // if "always_show_navigation" is TRUE or
        // if the total number of available pages is greater than the number of pages to be displayed at once
        // it means we can show the "next page" link
        if ($this->_properties['always_show_navigation'] || $this->_properties['total_pages'] > $this->_properties['selectable_pages'])

            // if we're on the last page, the link is disabled
            $output = '<li' . ($this->_properties['page'] == $this->_properties['total_pages'] ? ' class="disabled"' : '') . '><a href="' .

                // the href is different if we're on the last page
                ($this->_properties['page'] == $this->_properties['total_pages'] ? 'javascript:void(0)' : $this->_build_uri($this->_properties['page'] + 1)) . '"' .

                // good for SEO
                // http://googlewebmastercentral.blogspot.de/2011/09/pagination-with-relnext-and-relprev.html
                ' rel="next">' .

                ($this->_properties['reverse'] ? $this->_properties['previous'] : $this->_properties['next']) . '</a></li>';

        // return the resulting string
        return $output;

    }

    /**
     *  Generates the pagination links (minus "next" and "previous"), depending on whether the pagination links are shown
     *  in natural or reversed order.
     *
     *  @access private
     */
    private function _show_pages()
    {

        $output = '';

        // if the total number of pages is lesser than the number of selectable pages
        if ($this->_properties['total_pages'] <= $this->_properties['selectable_pages']) {

            // iterate ascendingly or descendingly depending on whether we're showing links in reverse order or not)
            for (

                $i = ($this->_properties['reverse'] ? $this->_properties['total_pages'] : 1);
                ($this->_properties['reverse'] ? $i >= 1 : $i <= $this->_properties['total_pages']);
                ($this->_properties['reverse'] ? $i-- : $i++)

            )

                // render the link for each page making sure to highlight the currently selected page
                $output .= '<li' . ($this->_properties['page'] == $i ? ' class="active"' : '') . '><a href="' . $this->_build_uri($i) . '">' .

                    // apply padding if required
                    ($this->_properties['padding'] ? str_pad($i, strlen($this->_properties['total_pages']), '0', STR_PAD_LEFT) : $i) .

                    '</a></li>';

        // if the total number of pages is greater than the number of selectable pages
        } else {

            // start with a link to the first or last page, depending if we're displaying links in reverse order or not
            // highlight if the page is currently selected
            $output .= '<li' . ($this->_properties['page'] == ($this->_properties['reverse'] ? $this->_properties['total_pages'] : 1) ? ' class="active"' : '') . '><a href="' . $this->_build_uri($this->_properties['reverse'] ? $this->_properties['total_pages'] : 1) . '">' .

                // if padding is required
                ($this->_properties['padding'] ?

                    // apply padding
                    str_pad(($this->_properties['reverse'] ? $this->_properties['total_pages'] : 1), strlen($this->_properties['total_pages']), '0', STR_PAD_LEFT) :

                    // show the page number
                    ($this->_properties['reverse'] ? $this->_properties['total_pages'] : 1)) .

                '</a></li>';

            // compute the number of adjacent pages to display to the left and right of the currently selected page so
            // that the currently selected page is always centered
            $adjacent = floor(($this->_properties['selectable_pages'] - 3) / 2);

            // this number must be at least 1
            if ($adjacent == 0) $adjacent = 1;

            // find the page number after we need to show the first "..."
            // (depending on whether we're showing links in reverse order or not)
            $scroll_from = ($this->_properties['reverse'] ?

                $this->_properties['total_pages'] - ($this->_properties['selectable_pages'] - $adjacent) + 1 :

                $this->_properties['selectable_pages'] - $adjacent);

            // get the page number from where we should start rendering
            // if displaying links in natural order, then it's "2" because we have already rendered the first page
            // if we're displaying links in reverse order, then it's total_pages - 1 because we have already rendered the last page
            $starting_page = ($this->_properties['reverse'] ? $this->_properties['total_pages'] - 1 : 2);

            // if the currently selected page is past the point from where we need to scroll,
            // we need to adjust the value of $starting_page
            if (

                ($this->_properties['reverse'] && $this->_properties['page'] <= $scroll_from) ||
                (!$this->_properties['reverse'] && $this->_properties['page'] >= $scroll_from)

            ) {

                // by default, the starting_page should be whatever the current page plus/minus $adjacent
                // depending on whether we're showing links in reverse order or not
                $starting_page = $this->_properties['page'] + ($this->_properties['reverse'] ? $adjacent : -$adjacent);

                // but if that would mean displaying less navigation links than specified in $this->_properties['selectable_pages']
                if (

                    ($this->_properties['reverse'] && $starting_page < ($this->_properties['selectable_pages'] - 1)) ||
                    (!$this->_properties['reverse'] && $this->_properties['total_pages'] - $starting_page < ($this->_properties['selectable_pages'] - 2))

                )

                    // adjust the value of $starting_page again
                    if ($this->_properties['reverse']) $starting_page = $this->_properties['selectable_pages'] - 1;

                    else $starting_page -= ($this->_properties['selectable_pages'] - 2) - ($this->_properties['total_pages'] - $starting_page);

                // put the "..." after the link to the first/last page
                // depending on whether we're showing links in reverse order or not
                $output .= '<li><span>&hellip;</span></li>';

            }

            // get the page number where we should stop rendering
            // by default, this value is the sum of the starting page plus/minus (depending on whether we're showing links
            // in reverse order or not) whatever the number of $this->_properties['selectable_pages'] minus 3 (first page,
            // last page and current page)
            $ending_page = $starting_page + (($this->_properties['reverse'] ? -1 : 1) * ($this->_properties['selectable_pages'] - 3));

            // if we're showing links in natural order and ending page would be greater than the total number of pages minus 1
            // (minus one because we don't take into account the very last page which we output automatically)
            // adjust the ending page
            if ($this->_properties['reverse'] && $ending_page < 2) $ending_page = 2;

            // or, if we're showing links in reverse order, and ending page would be smaller than 2
            // (2 because we don't take into account the very first page which we output automatically)
            // adjust the ending page
            elseif (!$this->_properties['reverse'] && $ending_page > $this->_properties['total_pages'] - 1) $ending_page = $this->_properties['total_pages'] - 1;

            // render pagination links
            for ($i = $starting_page; $this->_properties['reverse'] ? $i >= $ending_page : $i <= $ending_page; $this->_properties['reverse'] ? $i-- : $i++)

                // also highlight the currently selected page
                $output .= '<li' . ($this->_properties['page'] == $i ? ' class="active"' : '') . '><a href="' . $this->_build_uri($i) . '">' .

                    // apply padding if required
                    ($this->_properties['padding'] ? str_pad($i, strlen($this->_properties['total_pages']), '0', STR_PAD_LEFT) : $i) .

                    '</a></li>';

            // if we have to, place another "..." at the end, before the link to the last/first page (depending on whether
            // we're showing links in reverse order or not)
            if (

                ($this->_properties['reverse'] && $ending_page > 2) ||
                (!$this->_properties['reverse'] && $this->_properties['total_pages'] - $ending_page > 1)

            ) $output .= '<li><span>&hellip;</span></li>';

            // put a link to the last/first page (depending on whether we're showing links in reverse order or not)
            // also, highlight if it is the currently selected page
            $output .= '<li' . ($this->_properties['page'] == $i ? ' class="active"' : '') . '><a href="' . $this->_build_uri($this->_properties['reverse'] ? 1 : $this->_properties['total_pages']) . '">' .

                // also, apply padding if necessary
                ($this->_properties['padding'] ? str_pad(($this->_properties['reverse'] ? 1 : $this->_properties['total_pages']), strlen($this->_properties['total_pages']), '0', STR_PAD_LEFT) : ($this->_properties['reverse'] ? 1 : $this->_properties['total_pages'])) .

                '</a></li>';

        }

        // return the resulting string
        return $output;

    }

    /**
     *  Generates the "previous page" link, depending on whether the pagination links are shown in natural or reversed order.
     *
     *  @access private
     */
    private function _show_previous()
    {

        $output = '';

        // if "always_show_navigation" is TRUE or
        // if the number of total pages available is greater than the number of selectable pages
        // it means we can show the "previous page" link
        if ($this->_properties['always_show_navigation'] || $this->_properties['total_pages'] > $this->_properties['selectable_pages'])

            // if we're on the first page, the link is disabled
            $output = '<li' . ($this->_properties['page'] == 1 ? ' class="disabled"' : '') . '><a href="' .

                // the href is different if we're on the first page
                ($this->_properties['page'] == 1 ? 'javascript:void(0)' : $this->_build_uri($this->_properties['page'] - 1)) . '"' .

                // good for SEO
                // http://googlewebmastercentral.blogspot.de/2011/09/pagination-with-relnext-and-relprev.html
                ' rel="prev">' .

                ($this->_properties['reverse'] ? $this->_properties['next'] : $this->_properties['previous']) . '</a></li>';

        // return the resulting string
        return $output;

    }

}

?>