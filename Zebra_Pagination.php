<?php

/**
 *  A generic, Twitter Bootstrap compatible (versions 3, 4 and 5), pagination script that automatically generates navigation links
 *  as well as next/previous page links, given the total number of records and the number of records to be shown per page.
 *  Useful for breaking large sets of data into smaller chunks, reducing network traffic and, at the same time, improving
 *  readability, aesthetics and usability.
 *
 *  Read more {@link https://github.com/stefangabos/Zebra_Pagination/ here}
 *
 *  @author     Stefan Gabos <contact@stefangabos.ro>
 *  @version    2.4.2 (last revision: September 06, 2021)
 *  @copyright  © 2009 - 2021 Stefan Gabos
 *  @license    https://www.gnu.org/licenses/lgpl-3.0.txt GNU LESSER GENERAL PUBLIC LICENSE
 *  @package    Zebra_Pagination
 */
class Zebra_Pagination {

    // set defaults and initialize some private variables
    private $_properties = array(

        // should the "previous page" and "next page" links be always visible
        'always_show_navigation'    =>  true,

        // should we avoid duplicate content
        'avoid_duplicate_content'   =>  true,

        // whether pagination links should be removed leaving only the next and previous buttons,
        // links to the first and last pages, as well as a label showing the current page and
        // the total available pages
        // if set to 1 will also remove the links to the first and last page
        'condensed'                 =>  false,

        // in condensed mode, the string indicating the current page
        'condensed_progress'        =>  '%d / %d',

        // CSS classes to assign to the list, list item and to the anchor
        'css_classes'   =>  array(
            'list'      =>  'pagination',
            'list_item' =>  'page-item',
            'anchor'    =>  'page-link',
        ),

        // default method for page propagation
        'method'                    =>  'get',

        // string for "next page"
        'next'                      =>  '&raquo;',

        // by default, prefix page number with zeros
        'padding'                   =>  true,

        // the default starting page
        'page'                      =>  1,

        // a flag telling whether current page was set manually or determined from the URL
        'page_set'                  =>  false,

        // where should the "next" / "previous" links should be placed, relative to the navigation links
        'navigation_position'       =>  'outside',

        // a flag telling whether query strings in base_url should be kept or not
        'preserve_query_string'     =>  0,

        // string for "previous page"
        'previous'                  =>  '&laquo;',

        // by default, we assume there are no records
        // we expect this number to be set after the class is instantiated
        'records'                   =>  0,

        // records per page
        'records_per_page'          =>  0,

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
    public function __construct() {

        // set the default base url
        $this->base_url();

    }

    /**
     *  By default, the *previous page* and *next page* links are always shown.
     *
     *  By disabling this feature, the *previous page* and *next page* links will only be shown if there are more pages
     *  than {@link selectable_pages selectable pages}.
     *
     *  <code>
     *  // show "previous page" / "next page" only if there are more pages
     *  // than there are selectable pages
     *  $pagination->always_show_navigation(false);
     *  </code>
     *
     *  @param  boolean     $status (Optional) If set to `FALSE`, the *previous page* and *next page* links will only be
     *                              shown if there are more pages than {@link selectable_pages selectable pages}.
     *
     *                              Default is `TRUE`.
     *
     *  @since  2.0
     *
     *  @return void
     */
    public function always_show_navigation($status = true) {

        // set property
        $this->_properties['always_show_navigation'] = $status;

    }

    /**
     *  From a search engine's point of view URL `https://www.mywebsite.com/list` points to a different place than where
     *  `https://www.mywebsite.com/list?page=1` points to (because of the added query string in the second URL), but because
     *  both have the same content, your page will get an SEO penalization.
     *
     *  In order to avoid this, the library will have for the first page (or last, if you are displaying links in {@link reverse}
     *  order) the same path as you have for when you are accessing the page for the first (un-paginated) time.
     *
     *  If you want to disable this behavior call this method with its argument set to `FALSE`.
     *
     *  <code>
     *  // don't avoid duplicate content
     *  $pagination->avoid_duplicate_content(false);
     *  </code>
     *
     *  @param  boolean     $status     (Optional) If set to `FALSE`, the library will have for the first page (or last,
     *                                  if you are displaying links in {@link reverse} order) a different path than the
     *                                  one you have when you are accessing the page for the first (un-paginated) time.
     *
     *                                  Default is `TRUE`.
     *
     *  @return void
     *
     *  @since  2.0
     */
    public function avoid_duplicate_content($status = true) {

        // set property
        $this->_properties['avoid_duplicate_content'] = $status;

    }

    /**
     *  The base URL to be used when generating the navigation links.
     *
     *  This is helpful for the case when the URL where the records are paginated may have parameters that are not needed
     *  for subsequent requests generated by pagination.
     *
     *  For example, suppose some records are paginated at `https://yourwebsite/mypage/`. When a record from the list is
     *  updated, the URL could become something like `https://youwebsite/mypage/?action=updated`. Based on the value of
     *  `action` a message would be shown to the user.
     *
     *  Because of the way this script works, the pagination links would become
     *
     *  `https://youwebsite/mypage/?action=updated&page=[page number]`
     *
     *  when {@link method} is `get` and {@link variable_name} is `page`
     *
     *  `https://youwebsite/mypage/page[page number]/?action=updated`
     *
     *  when {@link method} is `url` and {@link variable_name} is `page`
     *
     *  As a result, whenever the user would paginate, the message would be shown to him again and again because
     *  `action` will be preserved in the URL!
     *
     *  The solution is to set the `base_url` to `https://youwebsite/mypage/` and in this way, regardless of how the URL
     *  changes, the pagination links will always be in the form of
     *
     *  `https://youwebsite/mypage/?page=[page number]`
     *
     *  when {@link method} is `get` and {@link variable_name} is `page`
     *
     *  `https://youwebsite/mypage/page[page number]/`
     *
     *  when {@link method} is `url` and {@link variable_name} is `page`
     *
     *  Of course, you may still have query strings in the value of the `base_url` if you wish so, and these will be
     *  preserved when paginating.
     *
     *  >   If you need to preserve the hash in the URL, make sure to include the zebra_pagination.js file in your page!
     *
     *  @param  string      $base_url                   (Optional) The base URL to be used when generating the navigation
     *                                                  links
     *
     *                                                  Defaults is whatever returned by
     *                                                  {@link https://www.php.net/manual/en/reserved.variables.server.php $_SERVER['REQUEST_URI']}
     *
     *  @param  boolean     $preserve_query_string      (Optional) Indicates whether values in query strings, other than
     *                                                  those set in `base_url`, should be preserved
     *
     *                                                  Default is `TRUE`
     *
     *  @return void
     */
    public function base_url($base_url = '', $preserve_query_string = true) {

        // we'll need this in case "variable_name" is an empty string
        // (when "base_url" must be explicitly declared)
        $this->_properties['base_url_explicit'] = $base_url !== '';

        // set the base URL
        $base_url = ($base_url == '' ? $_SERVER['REQUEST_URI'] : $base_url);

        // parse the URL
        $parsed_url = parse_url($base_url);

        // cache the "path" part of the URL (that is, everything *before* the "?")
        $this->_properties['base_url'] = rtrim($parsed_url['path'], '/');

        // cache the "query" part of the URL (that is, everything *after* the "?")
        $this->_properties['base_url_query'] = isset($parsed_url['query']) ? $parsed_url['query'] : '';

        // store query string as an associative array
        parse_str($this->_properties['base_url_query'], $this->_properties['base_url_query']);

        // should query strings (other than those set in $base_url) be preserved?
        $this->_properties['preserve_query_string'] = $preserve_query_string;

    }

    /**
     *  Removes pagination links leaving only the *next* and *previous* buttons, links to the *first* and *last* pages,
     *  as well as a label showing the current page and the total available pages.
     *
     *  >   Setting {@link selectable_pages selectable pages} to a value lower than `5` will automatically turn
     * {@link condensed} mode on.
     *
     *  @param  boolean $extra_condensed    Turning *extra condensed* mode on will also remove the links to the first
     *                                      and last pages.
     *
     *                                      Default is `FALSE`.
     *
     *  @since  2.4.0
     *
     *  @return void
     */
    public function condensed($extra_condensed = false) {

        $this->_properties['condensed'] = $extra_condensed ? true : 1;

    }

    /**
     *  Allows defining of custom CSS class names to be applied to the HTML markup.
     *
     *  @param  array   $css_classes    An associative array with one or more or all of the following keys:
     *
     *                                  -   **list**, for setting the CSS class name to be used for the ordered list (`<kbd><ol></kbd>`)
     *                                  -   **list_item**, for setting the CSS class name to be used for the list item (`<kbd><li></kbd>`)
     *                                  -   **anchor**, for setting the CSS class name to be used for the anchor (`<kbd><a></kbd>`)
     *
     *                                  The default generated HTML markup looks like below:
     *
     *                                  <code>
     *                                  <div class="Zebra_Pagination">
     *                                      <ol class="pagination">
     *                                          <li class="page-item">
     *                                              <a href="path/to/first/page/" class="page-link">1</a>
     *                                          </li>
     *                                          <li class="page-item">
     *                                              <a href="path/to/second/page/" class="page-link">2</a>
     *                                          </li>
     *                                          ...the other pages...
     *                                      </ol>
     *                                  </div>
     *                                  </code>
     *
     *                                  Calling this method with the following argument...
     *
     *                                  <code>
     *                                  $pagination->css_classes(array(
     *                                      'list'      =>  'foo',
     *                                      'list_item' =>  'bar',
     *                                      'anchor'    =>  'baz',
     *                                  ));
     *                                  </code>
     *
     *                                  ...would result in the following markup:
     *
     *                                  <code>
     *                                  <div class="Zebra_Pagination">
     *                                      <ol class="foo">
     *                                          <li class="bar">
     *                                              <a href="path/to/first/page/" class="baz">1</a>
     *                                          </li>
     *                                          <li class="bar">
     *                                              <a href="path/to/second/page/" class="baz">2</a>
     *                                          </li>
     *                                          ...the other pages...
     *                                      </ol>
     *                                  </div>
     *                                  </code>
     *
     *                                  Default values are:
     *
     *                                  <code>
     *                                  $pagination->css_classes(array(
     *                                      'list'      =>  'pagination',
     *                                      'list_item' =>  'page-item',
     *                                      'anchor'    =>  'page-link',
     *                                  ));
     *                                  </code>
     *
     *                                  These values make the resulting markup to be compatible with versions 3, 4 and
     *                                  5 of Twitter Bootstrap.
     *
     *  @return void
     */
    public function css_classes($css_classes) {

        // if argument is invalid
        if (!is_array($css_classes) || empty($css_classes) || array_keys($css_classes) != array_filter(array_keys($css_classes), function($value) { return in_array($value, array('list', 'list_item', 'anchor'), true); }))

            // stop execution
            trigger_error('Invalid argument. Method <strong>classes()</strong> accepts as argument an associative array with one or more of the following keys: <em>list, list_item, anchor</em>' , E_USER_ERROR);

        // merge values with the default ones
        $this->_properties['css_classes'] = array_merge($this->_properties['css_classes'], $css_classes);

    }

    /**
     *  Returns the number of the currently selected page.
     *
     *  <code>
     *  // echoes the current page
     *  echo $pagination->get_page();
     *  </code>
     */
    public function get_page() {

        // unless page was not specifically set through the "set_page" method
        if (!$this->_properties['page_set']) {

            // if
            if (

                // page propagation is SEO friendly
                $this->_properties['method'] == 'url' &&

                // the current page is set in the URL
                preg_match('/\b' . str_replace('/', '\/', preg_quote(($this->_properties['variable_name'] === '' ? $this->_properties['base_url'] . '/' : '') . $this->_properties['variable_name'])) . '([0-9]+)\b/i', $_SERVER['REQUEST_URI'], $matches) > 0

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
     *  Returns the total number of available pages.
     *
     *  The value is computed based on the {@link records() total number of records} and the {@link records_per_page() number of records to be shown per page}.
     *
     *  <code>
     *  // get the total number of pages
     *  echo $pagination->get_pages();
     *  </code>
     *
     *  @since  2.1
     */
    public function get_pages() {

        // return the total number of pages based on the total number of records and number of records to be shown per page
        return $this->_properties['records_per_page'] > 0 ? ceil($this->_properties['records'] / $this->_properties['records_per_page']) : 0;

    }

    /**
     *  Change the labels for the *previous page* and *next page* links as well as the label used to indicate progress
     *  when in {@link condensed} mode.
     *
     *  <code>
     *  // change the default labels
     *  $pagination->labels('Previous', 'Next', 'Page %d of %d pages');
     *  </code>
     *
     *  @param  string  $previous   (Optional) The label for the *previous page* link.
     *
     *                              Default is `&laquo;` (which looks like `«`)
     *
     *  @param  string  $next       (Optional) The label for the *next page* link.
     *
     *                              Default is `&raquo;` (which looks like `»`).
     *
     *  @param  string  $progress   (Optional) The label for showing the current progress when in {@link condensed} mode.
     *
     *                              Default is `%d / %d`
     *
     *                              First `%d` will be replaced with the current page while the second one with the
     *                              number of total pages.
     *
     *  @return void
     *
     *  @since  2.0
     */
    public function labels($previous = '&laquo;', $next = '&raquo;', $progress = '%d / %d') {

        // set the labels
        $this->_properties['previous'] = $previous;
        $this->_properties['next'] = $next;
        $this->_properties['condensed_progress'] = $progress;

    }

    /**
     *  Sets the method to be used for page propagation.
     *
     *  <code>
     *  // set the method to the SEO friendly way
     *  $pagination->method('url');
     *  </code>
     *
     *  @param  string  $method     (Optional) The method to be used for page propagation.
     *
     *                              Valid values are:
     *
     *                              - `url` - page propagation is done in a SEO friendly way
     *
     *                              This method requires the {@link https://httpd.apache.org/docs/current/mod/mod_rewrite.html mod_rewrite}
     *                              module to be enabled on your Apache server (or the equivalent for other web servers).
     *
     *                              When using this method, the current page will be passed in the URL as
     *
     *                              `https://youwebsite.com/yourpage/[variable name][page number]/`
     *
     *                              where `variable name` is set through {@link variable_name} and `page number`
     *                              represents the current page.
     *
     *                              - `get` - page propagation is done through `GET`
     *
     *                              When using this method, the current page will be passed in the URL as
     *
     *                              `https://youwebsite.com/yourpage?[variable name]=[page number]`
     *
     *                              where `variable name` is set through {@link variable_name} and `page number`
     *                              represents the current page.
     *
     *                              Default is `get`.
     *
     *  @returns void
     */
    public function method($method = 'get') {

        // set the page propagation method
        $this->_properties['method'] = (strtolower($method) == 'url' ? 'url' : 'get') ;

    }

    /**
     *  Sets the position of the *next* and *previous* page, relative to the links to individual pages.
     *
     *  @param  string  $position   By default, the links for the *next* and *previous* page are shown on the outside
     *                              (to the left and right) of the links to individual pages.
     *
     *                              These links can also be shown both on the left or both on the right of the links to
     *                              individual pages by setting this argument to `left` or `right` respectively.
     *
     *                              Valid values are `left`, `right` and `outside`.
     *
     *                              Default is `outside`.
     *
     *  @since  2.1
     *
     *  @return void
     */
    public function navigation_position($position) {

        // set the positioning of next/previous page links
        $this->_properties['navigation_position'] = (in_array(strtolower($position), array('left', 'right')) ? strtolower($position) : 'outside') ;

    }

    /**
     *  Sets whether page numbers should be prefixed with zeros.
     *
     *  This is useful to keep the layout consistent by having the same number of characters for each page number.
     *
     *  <code>
     *  // disable padding numbers with zeros
     *  $pagination->padding(false);
     *  </code>
     *
     *  @param  boolean     $status     (Optional) Setting this property to `FALSE` will disable padding.
     *
     *                                  Default is `TRUE`.
     *
     *  @return void
     */
    public function padding($status = true) {

        // set padding
        $this->_properties['padding'] = $status;

    }

    /**
     *  Defines the total number of records that need to be paginated.
     *
     *  Based on this and on the {@link records_per_page number of records to be shown per page}, the script will know
     *  how many pages there are.
     *
     *  <code>
     *  // tell the script that there are 100 total records
     *  $pagination->records(100);
     *  </code>
     *
     *  @param  integer     $records    The total number of records that need to be paginated.
     *
     *  @return void
     */
    public function records($records) {

        // the number of records
        // make sure we save it as an integer
        $this->_properties['records'] = (int)$records;

    }

    /**
     *  Defines the number of records that are displayed on a single page.
     *
     *  Based on this and on the {@link records total number of records}, the script will know how many pages there are.
     *
     *  <code>
     *  // tell the class that there are 20 records displayed on one page
     *  $pagination->records_per_page(20);
     *  </code>
     *
     *  @param  integer     $records_per_page   The number of records displayed on a single page.
     *
     *                      Default is `10`.
     *
     *  @return void
     */
    public function records_per_page($records_per_page) {

        // the number of records displayed on one page
        // make sure we save it as an integer
        $this->_properties['records_per_page'] = (int)$records_per_page;

    }

    /**
     *  Generates the HTML markup for the pagination.
     *
     *  <code>
     *  // generate output
     *  // don't echo it but return it instead
     *  $output = $pagination->render(true);
     *  </code>
     *
     *  >   If {@link https://getbootstrap.com/ Twitter Bootstrap} is not present on the page, make sure to load the
     *  default styles by including the `zebra_pagination.css` file.
     *
     *  @param  boolean     $return_output      (Optional) Setting this argument to `TRUE` will instruct the script to
     *                                          return the generated output rather than outputting it to the screen.
     *
     *                                          Default is `FALSE`.
     *
     *  @return void
     */
    public function render($return_output = false) {

        // "base_url" must be explicitly declared if "variable_name" is an empty string and "method" is "url"
        if ($this->_properties['variable_name'] === '' && $this->_properties['method'] == 'url' && !$this->_properties['base_url_explicit'])

            trigger_error('<strong>base_url</strong> must be explicitly declared if <strong>variable_name</strong> is <strong>an empty string</strong>' , E_USER_ERROR);

        // "variable_name" cannot be an empty string when "method" is "get"
        if ($this->_properties['variable_name'] === '' && $this->_properties['method'] == 'get')

            trigger_error('<strong>variable_name</strong> must not be an empty when <strong>method</strong> is <strong>get</strong>' , E_USER_ERROR);

        // get some properties of the class
        $this->get_page();

        // if there is a single page or no pages at all, and we don't have to always display navigation, don't display anything
        if ($this->_properties['total_pages'] <= 1 && !$this->_properties['always_show_navigation']) return '';

        // start building output
        $output = '<div class="Zebra_Pagination"><ol' . ($this->_properties['css_classes']['list'] != '' ? ' class="' . trim($this->_properties['css_classes']['list']) . '"' : '') . '>';

        // if we're showing records in reverse order
        if ($this->_properties['reverse']) {

            // if "next page" and "previous page" links need to be shown to the left of the links to individual pages
            if ($this->_properties['navigation_position'] == 'left')

                // first show next/previous and then page links
                $output .= $this->_show_next() . $this->_show_previous() . $this->_show_pages();

            // if "next page" and "previous page" links need to be shown to the right of the links to individual pages
            elseif ($this->_properties['navigation_position'] == 'right')

                $output .= $this->_show_pages() . $this->_show_next() . $this->_show_previous();

            // if "next page" and "previous page" links need to be shown on the outside of the links to individual pages
            else $output .= $this->_show_next() . $this->_show_pages() . $this->_show_previous();

        // if we're showing records in natural order
        } else {

            // if "next page" and "previous page" links need to be shown to the left of the links to individual pages
            if ($this->_properties['navigation_position'] == 'left')

                // first show next/previous and then page links
                $output .= $this->_show_previous() . $this->_show_next() . $this->_show_pages();

            // if "next page" and "previous page" links need to be shown to the right of the links to individual pages
            elseif ($this->_properties['navigation_position'] == 'right')

                $output .= $this->_show_pages() . $this->_show_previous() . $this->_show_next();

            // if "next page" and "previous page" links need to be shown on the outside of the links to individual pages
            else $output .= $this->_show_previous() . $this->_show_pages() . $this->_show_next();

        }

        // finish generating the output
        $output .= '</ol></div>';

        // if $return_output is TRUE
        // return the generated content
        if ($return_output) return $output;

        // if script gets this far, print generated content to the screen
        echo $output;

    }

    /**
     *  By default, pagination links are shown in natural order, from 1 to the number of total pages.
     *
     *  Calling this method with the `TRUE` argument will generate links in reverse order, from the number of total pages
     *  down to 1.
     *
     *  <code>
     *  // show pagination links in reverse order rather than in natural order
     *  $pagination->reverse(true);
     *  </code>
     *
     *  @param  boolean     $reverse    (Optional) Set it to `TRUE` to generate navigation links in reverse order.
     *
     *                                  Default is `FALSE`.
     *
     *  @return void
     *
     *  @since  2.0
     */
    public function reverse($reverse = false) {

        // set how the pagination links should be generated
        $this->_properties['reverse'] = $reverse;

    }

    /**
     *  Defines the number of pagination links to be displayed at once besides *previous page* and *next page* links.
     *
     *  <code>
     *  // display links to 15 pages
     *  $pagination->selectable_pages(15);
     *  </code>
     *
     *  @param  integer     $selectable_pages   The number of pagination links to be displayed at once besides *previous
     *                                          page* and *next page*  links.
     *
     *                                          >   For optimal results this should be an odd value so that the number of
     *                                          links shown to the left and right of the current page is the same.
     *
     *                                          Setting this to a value lower than `5` will automatically turn {@link condensed}
     *                                          mode on.
     *
     *                                          Default is `11`.
     *
     *  @return void
     */
    public function selectable_pages($selectable_pages) {

        // the number of selectable pages
        // make sure we save it as an integer
        $this->_properties['selectable_pages'] = (int)$selectable_pages;

        // if less than 5 selectable pages, turn "condensed" mode on
        if ($this->_properties['selectable_pages'] < 5) $this->condensed();

    }

    /**
     *  Sets the current page.
     *
     *  <code>
     *  // sets the fifth page as the current page
     *  $pagination->set_page(5);
     *  </code>
     *
     *  @param  integer     $page   The page's number.
     *
     *                              A number lower than `1` will be interpreted as `1`, while a number greater than the
     *                              total number of pages will be interpreted as the last page.
     *
     *  @return void
     */
    public function set_page($page) {

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
     *  Enables or disables trailing slash on the generated URLs when {@link method} is `url`.
     *
     *  From an SEO perspective, a page with trailing slash is considered different than the same page without the trailing
     *  slash. Read more on the subject at {@link https://webmasters.googleblog.com/2010/04/to-slash-or-not-to-slash.html Google Webmaster's official blog}.
     *
     *  <code>
     *  // disables trailing slashes on generated URLs
     *  $pagination->trailing_slash(false);
     *  </code>
     *
     *  @param  boolean     $status     (Optional) Setting this property to `FALSE` will disable trailing slashes on generated
     *                                  URLs when {@link method} is `url`.
     *
     *                                  Default is `TRUE` (trailing slashes are enabled by default).
     *
     *  @return void
     */
    public function trailing_slash($status = true) {

        // set the state of trailing slashes
        $this->_properties['trailing_slash'] = $status;

    }

    /**
     *  Sets the variable name to be used for page propagation.
     *
     *  <code>
     *  // sets the variable name to "foo"
     *  // now, in the URL, the current page will be passed either as
     *  // "foo=[page number]" (if method is "get") or as
     *  // "/foo[page number]" (if method is "url")
     *  $pagination->variable_name('foo');
     *  </code>
     *
     *  @param  string  $variable_name      A string representing the variable name to be used for page propagation.
     *
     *                                      Default is `page`.
     *
     *  @return void
     */
    public function variable_name($variable_name) {

        // set the variable name
        $this->_properties['variable_name'] = strtolower($variable_name);

    }

    /**
     *  Generate the link for the page given as argument.
     *
     *  @return void
     */
    private function _build_uri($page) {

        // if page propagation method is through SEO friendly URLs
        if ($this->_properties['method'] == 'url') {

            // see if the current page is already set in the URL
            // when "variable_name" is an empty string we'll also factor in "base_url" (which is mandatory in this case)
            if (preg_match(
                '/\b' . str_replace('/', '\/', preg_quote(($this->_properties['variable_name'] === '' ? $this->_properties['base_url'] . '/' : '') . $this->_properties['variable_name'])) . '([0-9]+)\b/i',
                $this->_properties['variable_name'] === '' ? $_SERVER['REQUEST_URI'] : $this->_properties['base_url']
            ) > 0) {

                // build string
                $url = str_replace('//', '/', preg_replace(

                    // replace the currently existing value
                    // (also handle the case when "variable_name" is an empty string)
                    '/\b' . str_replace('/', '\/', preg_quote(($this->_properties['variable_name'] === '' ? $this->_properties['base_url'] . '/' : '') . $this->_properties['variable_name'])) . '([0-9]+)\b/i',

                    // if on the first page and we are avoiding duplicate content, remove page number
                    // (also handle the case when "variable_name" is an empty string)
                    ($this->_properties['variable_name'] === '' ? $this->_properties['base_url'] . '/' : '') . ($page == 1 && $this->_properties['avoid_duplicate_content'] ? '' : $this->_properties['variable_name'] . $page),

                    // handle the case when "variable_name" is an empty string
                    $this->_properties['variable_name'] === '' ? $_SERVER['REQUEST_URI'] : $this->_properties['base_url']

                ));

            // if the current page is not yet in the URL, set it, unless we're on the first page
            // case in which we don't set it in order to avoid duplicate content
            } else $url = ($this->_properties['variable_name'] !== '' ? $this->_properties['base_url'] . '/' : '') . $this->_properties['variable_name'] . $page;

            // handle trailing slash according to preferences
            $url = rtrim($url, '/') . ($this->_properties['trailing_slash'] ? '/' : '');

            // if values in the query string - other than those set through base_url() - are not to be preserved
            // preserve only those set initially
            if (!$this->_properties['preserve_query_string']) $query = implode('&', $this->_properties['base_url_query']);

            // otherwise, get the current query string
            else $query = $_SERVER['QUERY_STRING'];

            // return the built string also appending the query string, if any
            $uri =  $url . ($query != '' ? '?' . $query : '');

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
            $uri = htmlspecialchars(html_entity_decode($this->_properties['base_url']) . (!empty($query) ? '?' . http_build_query(array_map('urldecode', $query)) : ''));

        }

        // if for whatever reason the URI is an empty string it means it should be pointing to the root ("/")
        // we can't leave this as an empty string or it will point to whatever URL is currently open in the browser
        return $uri !== '' ? $uri : '/';

    }

    /**
     *  Generates the *next page* link, depending on whether the pagination links are shown in natural or reversed order.
     */
    private function _show_next() {

        $output = '';

        // if "always_show_navigation" is TRUE or
        // if the total number of available pages is greater than the number of pages to be displayed at once
        // it means we can show the "next page" link
        if ($this->_properties['always_show_navigation'] || $this->_properties['total_pages'] > $this->_properties['selectable_pages']) {

            // CSS classes to be applied to the list item, if any
            $css_classes = isset($this->_properties['css_classes']['list_item']) && $this->_properties['css_classes']['list_item'] != '' ? array(trim($this->_properties['css_classes']['list_item'])) : array();

            // if we're on the last page, the link is disabled
            if ($this->_properties['page'] == $this->_properties['total_pages']) $css_classes[] = 'disabled';

            // generate markup
            $output = '<li' .

                // add CSS classes to the list item, if necessary
                (!empty($css_classes) ? ' class="' . implode(' ', $css_classes) . '"' : '') . '><a href="' .

                // the href is different if we're on the last page
                ($this->_properties['page'] == $this->_properties['total_pages'] ? 'javascript:void(0)' : $this->_build_uri($this->_properties['page'] + 1)) . '"' .

                // add CSS classes to the anchor, if necessary
                (isset($this->_properties['css_classes']['anchor']) && $this->_properties['css_classes']['anchor'] != '' ? ' class="' . trim($this->_properties['css_classes']['anchor']) . '"' : '') . '>' .

                // reverse arrows if necessary
                ($this->_properties['reverse'] ? $this->_properties['previous'] : $this->_properties['next']) . '</a></li>';

        }

        // return the resulting string
        return $output;

    }

    /**
     *  Generates the pagination links (minus "next" and "previous"), depending on whether the pagination links are shown
     *  in natural or reversed order.
     */
    private function _show_pages() {

        $output = '';

        // if the total number of pages is lesser than the number of selectable pages and we are not in "condensed" mode
        if ($this->_properties['total_pages'] <= $this->_properties['selectable_pages'] && !$this->_properties['condensed'])

            // iterate ascendingly or descendingly, depending on whether we're showing links in reverse order or not
            for (

                $i = ($this->_properties['reverse'] ? $this->_properties['total_pages'] : 1);
                ($this->_properties['reverse'] ? $i >= 1 : $i <= $this->_properties['total_pages']);
                ($this->_properties['reverse'] ? $i-- : $i++)

            ) {

                // CSS classes to be applied to the list item, if any
                $css_classes = isset($this->_properties['css_classes']['list_item']) && $this->_properties['css_classes']['list_item'] != '' ? array(trim($this->_properties['css_classes']['list_item'])) : array();

                // if this the currently selected page, highlight it
                if ($this->_properties['page'] == $i) $css_classes[] = 'active';

                // generate markup
                $output .= '<li' .

                    // add CSS classes to the list item, if necessary
                    (!empty($css_classes) ? ' class="' . implode(' ', $css_classes) . '"' : '') . '><a href="' . $this->_build_uri($i) . '"' .

                    // add CSS classes to the anchor, if necessary
                    (isset($this->_properties['css_classes']['anchor']) && $this->_properties['css_classes']['anchor'] != '' ? ' class="' . trim($this->_properties['css_classes']['anchor']) . '"' : '') . '>' .

                    // apply padding if required
                    ($this->_properties['padding'] ? str_pad($i, strlen($this->_properties['total_pages']), '0', STR_PAD_LEFT) : $i) .

                    '</a></li>';

            }

        // if the total number of pages is greater than the number of selectable pages, or we are in "condensed" mode
        else {

            // start with a link to the first page (or last, if we are displaying links in reverse order)

            // if we are not in "extra condensed" mode
            if ($this->_properties['condensed'] !== true) {

                // CSS classes to be applied to the list item, if any
                $css_classes = isset($this->_properties['css_classes']['list_item']) && $this->_properties['css_classes']['list_item'] != '' ? array(trim($this->_properties['css_classes']['list_item'])) : array();

                // highlight if the page is currently selected
                if ($this->_properties['page'] == ($this->_properties['reverse'] ? $this->_properties['total_pages'] : 1)) $css_classes[] = 'active';

                // generate markup
                $output .= '<li' .

                    // add CSS classes to the list item, if necessary
                    (!empty($css_classes) ? ' class="' . implode(' ', $css_classes) . '"' : '') . '><a href="' . $this->_build_uri($this->_properties['reverse'] ? $this->_properties['total_pages'] : 1) . '"' .

                    // add CSS classes to the anchor, if necessary
                    (isset($this->_properties['css_classes']['anchor']) && $this->_properties['css_classes']['anchor'] != '' ? ' class="' . trim($this->_properties['css_classes']['anchor']) . '"' : '') . '>' .

                    // if padding is required
                    ($this->_properties['padding'] ?

                        // apply padding
                        str_pad(($this->_properties['reverse'] ? $this->_properties['total_pages'] : 1), strlen($this->_properties['total_pages']), '0', STR_PAD_LEFT) :

                        // show the page number
                        ($this->_properties['reverse'] ? $this->_properties['total_pages'] : 1)) .

                    '</a></li>';

            }

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

            // if
            if (

                // we are not in "condensed" mode
                !$this->_properties['condensed'] &&

                // the currently selected page is past the point from where we need to scroll,
                (($this->_properties['reverse'] && $this->_properties['page'] <= $scroll_from) ||
                (!$this->_properties['reverse'] && $this->_properties['page'] >= $scroll_from))

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

                // put the "..." after the link to the first/last page, depending on whether we're showing links in reverse order or not
                $output .= '<li' .

                    // add CSS classes to the list item, if necessary
                    (isset($this->_properties['css_classes']['list_item']) && $this->_properties['css_classes']['list_item'] != '' ? ' class="' . $this->_properties['css_classes']['list_item'] . '"' : '') . '>' .

                    // add CSS classes to the span element, if necessary
                    '<span' . (isset($this->_properties['css_classes']['anchor']) && $this->_properties['css_classes']['anchor'] != '' ? ' class="' . trim($this->_properties['css_classes']['anchor']) . '"' : '') . '>' .

                    '&hellip;</span></li>';

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

            // if we are not in "condensed" mode
            if (!$this->_properties['condensed'])

                // render pagination links
                for ($i = $starting_page; $this->_properties['reverse'] ? $i >= $ending_page : $i <= $ending_page; $this->_properties['reverse'] ? $i-- : $i++) {

                    // CSS classes to be applied to the list item, if any
                    $css_classes = isset($this->_properties['css_classes']['list_item']) && $this->_properties['css_classes']['list_item'] != '' ? array(trim($this->_properties['css_classes']['list_item'])) : array();

                    // highlight the currently selected page
                    if ($this->_properties['page'] == $i) $css_classes[] = 'active';

                    // generate markup
                    $output .= '<li' .

                        // add CSS classes to the list item, if necessary
                        (!empty($css_classes) ? ' class="' . implode(' ', $css_classes) . '"' : '') . '><a href="' . $this->_build_uri($i) . '"' .

                        // add CSS classes to the anchor, if necessary
                        (isset($this->_properties['css_classes']['anchor']) && $this->_properties['css_classes']['anchor'] != '' ? ' class="' . trim($this->_properties['css_classes']['anchor']) . '"' : '') . '>' .

                        // apply padding if required
                        ($this->_properties['padding'] ? str_pad($i, strlen($this->_properties['total_pages']), '0', STR_PAD_LEFT) : $i) .

                        '</a></li>';

                }

            // if we are in "condensed" mode
            if ($this->_properties['condensed']) {

                // CSS classes to be applied to the list item, if any
                $css_classes = isset($this->_properties['css_classes']['list_item']) && $this->_properties['css_classes']['list_item'] != '' ? array(trim($this->_properties['css_classes']['list_item'])) : array();

                // add the "disabled" class so it behaves nicely with Bootstrap
                $css_classes[] = 'disabled';

                // generate markup
                $output .= '<li' .

                    // add CSS classes to the list item, if necessary
                    (!empty($css_classes) ? ' class="' . implode(' ', $css_classes) . '"' : '') . '><span' .

                    // add CSS classes to the anchor, if necessary
                    (isset($this->_properties['css_classes']['anchor']) && $this->_properties['css_classes']['anchor'] != '' ? ' class="' . trim($this->_properties['css_classes']['anchor']) . '"' : '') . '>' .

                    // show current page
                    sprintf($this->_properties['condensed_progress'], $this->_properties['page'], $this->_properties['total_pages']) . '</span></li>';

            }

            // if we have to, place another "..." at the end, before the link to the last/first page (depending on whether we're showing links in reverse order or not)
            if (

                // if we are not in "condensed" mode
                !$this->_properties['condensed'] &&

                (($this->_properties['reverse'] && $ending_page > 2) ||
                (!$this->_properties['reverse'] && $this->_properties['total_pages'] - $ending_page > 1))

            )

                // generate markup
                $output .= '<li' .

                    // add CSS classes to the list item, if necessary
                    (isset($this->_properties['css_classes']['list_item']) && $this->_properties['css_classes']['list_item'] != '' ? ' class="' . $this->_properties['css_classes']['list_item'] . '"' : '') . '>' .

                    // add CSS classes to the span element, if necessary
                    '<span' . (isset($this->_properties['css_classes']['anchor']) && $this->_properties['css_classes']['anchor'] != '' ? ' class="' . trim($this->_properties['css_classes']['anchor']) . '"' : '') . '>' .

                    '&hellip;</span></li>';

            // now we put a link to the last page (or first if we are showing links in reverse order)

            // if we are not in "extra condensed" mode
            if ($this->_properties['condensed'] !== true) {

                // CSS classes to be applied to the list item, if any
                $css_classes = isset($this->_properties['css_classes']['list_item']) && $this->_properties['css_classes']['list_item'] != '' ? array(trim($this->_properties['css_classes']['list_item'])) : array();

                // highlight if the page is currently selected
                if ($this->_properties['page'] == ($this->_properties['reverse'] ? 1 : $this->_properties['total_pages'])) $css_classes[] = 'active';

                // generate markup
                $output .= '<li' .

                    // add CSS classes to the list item, if necessary
                    (!empty($css_classes) ? ' class="' . implode(' ', $css_classes) . '"' : '') . '><a href="' . $this->_build_uri($this->_properties['reverse'] ? 1 : $this->_properties['total_pages']) . '"' .

                    // add CSS classes to the anchor, if necessary
                    (isset($this->_properties['css_classes']['anchor']) && $this->_properties['css_classes']['anchor'] != '' ? ' class="' . trim($this->_properties['css_classes']['anchor']) . '"' : '') . '>' .

                    // also, apply padding if necessary
                    ($this->_properties['padding'] ? str_pad(($this->_properties['reverse'] ? 1 : $this->_properties['total_pages']), strlen($this->_properties['total_pages']), '0', STR_PAD_LEFT) : ($this->_properties['reverse'] ? 1 : $this->_properties['total_pages'])) .

                    '</a></li>';

            }

        }

        // return the resulting string
        return $output;

    }

    /**
     *  Generates the *previous page* link, depending on whether the pagination links are shown in natural or reversed order.
     */
    private function _show_previous() {

        $output = '';

        // if "always_show_navigation" is TRUE or
        // if the number of total pages available is greater than the number of selectable pages
        // it means we can show the "previous page" link
        if ($this->_properties['always_show_navigation'] || $this->_properties['total_pages'] > $this->_properties['selectable_pages']) {

            // CSS classes to be applied to the list item, if any
            $css_classes = isset($this->_properties['css_classes']['list_item']) && $this->_properties['css_classes']['list_item'] != '' ? array(trim($this->_properties['css_classes']['list_item'])) : array();

            // if we're on the first page, the link is disabled
            if ($this->_properties['page'] == 1) $css_classes[] = 'disabled';

            // generate markup
            $output = '<li' .

                // add CSS classes to the list item, if necessary
                (!empty($css_classes) ? ' class="' . implode(' ', $css_classes) . '"' : '') . '><a href="' .

                // the href is different if we're on the first page
                ($this->_properties['page'] == 1 ? 'javascript:void(0)' : $this->_build_uri($this->_properties['page'] - 1)) . '"' .

                // add CSS classes to the anchor, if necessary
                (isset($this->_properties['css_classes']['anchor']) && $this->_properties['css_classes']['anchor'] != '' ? ' class="' . trim($this->_properties['css_classes']['anchor']) . '"' : '') . '>' .

                ($this->_properties['reverse'] ? $this->_properties['next'] : $this->_properties['previous']) . '</a></li>';

        }

        // return the resulting string
        return $output;

    }

}
