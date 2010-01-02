<?

    /**
    * This class allows fast and easy manipulation of large amounts of data
    *
    * @name data_manager
    * @author Tsvetan Filev <tsvetan.filev@gmail.com>
    * @date 10/07/2008
    *
    */

    /**
    * Data manager modes
    * @const string modes
    */

    define( 'MODE_LIST',          'list');
    define( 'MODE_VIEW',          'view');
    define( 'MODE_PRINT',         'print');
    define( 'MODE_PDF',           'pdf');
    define( 'MODE_XML',           'xml');

    define( 'MODE_ADD',           'add');
    define( 'MODE_INSERT',        'insert');
    define( 'MODE_EDIT',          'edit');
    define( 'MODE_UPDATE',        'update');
    define( 'MODE_DELETE',        'delete');
    define( 'MODE_UPLOAD',        'upload');

    define( 'MODE_SEARCH',        'search');
    define( 'MODE_RESET',         'reset');
    define( 'MODE_OK',            'ok');

    define( 'MODE_START',         'start');
    define( 'MODE_STOP',          'stop');
    define( 'MODE_OPEN',          'open');
    define( 'MODE_CLOSE',         'close');
    define( 'MODE_ASSIGN',        'assign');

    class data_manager {

        /**
        * @var object
        */
        protected $kernel = null;
        /**
        * @var string
        */
        protected $table = null;
        /**
        * @var array
        */
        protected $list = array();
        /**
        * @var array
        */
        public $row = array();
        /**
        * @var array
        */
        protected $pager = array();
        /**
        * @var array
        */
        protected $filter = array();
        /**
        * @var array
        */
        protected $validation = array();
        /**
        * @var string
        */
        protected $template = null;
        /**
        * @var boolean
        */
        protected $acl = false;
        /**
        * @var array
        */
        protected $orders = array();

        /**
        * Constructor for data_manager
        * @name __construct
        * @param resource $kernel object
        *
        */
        public function __construct ( $kernel ){

            $this->kernel = $kernel;
            $this->acl = class_exists('acl') ? true : false;

        } // end __construct

        /**
        * Generate conditons array used for different operations
        * @name filter
        *
        */
        public function filter (){

            $this->filter = array();

        } // end filter

        /**
        * Generate sql search query string
        * @name search_rule
        *
        */
        public function search_rule ( $search = null, $fields, $separator = "_|||_" ) {

            if( !defined("PHP_SQL_SEARCH") ){

                $search_strings = preg_replace("|( +)|", $separator, trim($search));
                $strings_count = substr_count($search_strings, $separator)+1;
                $rule = " countStrings( concat( $fields ), '$search_strings', '$separator', $strings_count) ";
                return $rule;

            }

            $search_strings = preg_split("|( +)|", trim($search));
            foreach( $search_strings as $string )
                    $rules[] = " concat( $fields ) LIKE '%$string%' ";

            return "(".join(" OR ", $rules ).")";

        } // end search_rule

        /**
        * Validation method
        * @name validation
        *
        */
        public function validation (){

            // Validation enabled
            if( !class_exists( 'validation' ) ) return true;

            $validation = new validation( $this->kernel );

            // validation.xml exists
            if( !$validation->load( $this->kernel->templ->template_dir . DIRECTORY_SEPARATOR . "validation.xml" ) ) return true;

            $validation->setOptions( array( "params" => $this->kernel->params ) );

            // Errors in validation
            if( !$validation->validate() ) return true;

            // Get errors
            $this->validation = $validation->errors();

            // Set new template params
            $this->kernel->templ->assign("params", $this->kernel->params_html );

            // Set errors
            $this->kernel->templ->assign("errors", $this->validation );

            return false;

        } // end validation

        /**
        * Copy the vars specified in $vars from the state array to params array
        * @name get_state
        * @param array $vars array with var names to copy
        */
        public function get_state( $vars = array() ){

            foreach( $vars as $var )
                if( !isset( $this->kernel->params[$var] ) ) $this->kernel->params[$var] = $_SESSION["state"][$this->kernel->app][$this->kernel->object][$var];

            $this->kernel->set_params( $this->kernel->params );

        } // end get_state

        /**
        * Save the params array to state array
        * @name set_state
        */
        public function set_state(){

            foreach( $this->kernel->params as $key => $val )
                $_SESSION["state"][$this->kernel->app][$this->kernel->object][$key] = $val;

        } // end set_state

        /**
        * Process method that executes given mode
        * i.e. view, edit, insert, update, delete, default
        * @name process
        *
        * @return array records list, pager information, template
        */
        public function process (){
            extract( $this->kernel->params );

            if( $this->acl )
                $this->kernel->acl_can_do( $this->kernel->user_info["group_id"] , $this->kernel->app, $this->kernel->object );

            $method = "mode_$mode";

            if( !method_exists( $this, $method ) )
                $method = "mode_".MODE_LIST;

            $this->$method();

            $this->template = $this->template ? $this->template : MODE_LIST;

            return array( $this->list, $this->pager, $this->template );

        } // end process

        /**
        * Get all records
        * @name getAll
        *
        */
        public function getAll (){
            extract( $this->kernel->params );

            $list = $this->kernel->db->getAll("SELECT * FROM {$this->table} WHERE {$this->filter["where"]} $group_by $order_by LIMIT {$this->filter["start"]}, {$this->filter["per_page"]}");

            return $list;

        } // end getAll

        /**
        * Get one records
        * @name getOne
        *
        */
        public function getOne ( $id ){

            return $this->kernel->db->getRow("SELECT * FROM {$this->table} WHERE id = '$id'");

        } // end getOne

        /**
        * subclass inherits this method to insert records.
        * Also submethod can use it to make additional checks like catching existing record error
        * and etc.
        * @uses params to insert record
        * @example modules/invoice.php How to rewrite this function
        */
        protected function insert () {

        } // end insert

        /**
        * subclass inherits this method to update records.
        * Also submethod can use it to make additional checks like catching existing record error
        * and etc.
        * @uses params to update record
        * @example modules/invoice.php How to rewrite this function
        */
        protected function update () {

        } // end update

        /**
        * subclass inherits this method to remove records.
        * Also submethod can use it to make additional checks
        * @uses params to remove record
        */
        public function remove (){
            extract( $this->kernel->params );

            if( $id and preg_match('|^[\d,]+$|', $id ) )
                $this->kernel->db->query("DELETE FROM {$this->table} WHERE id IN ($id)");

        } // end remove

        /**
        * Returns the last insert id after insert sql query
        * @name last_insert_id
        */
        public function last_insert_id() {

            return $this->kernel->db->getOne("SELECT LAST_INSERT_ID()");

        } // end last_insert_id

        /**
        * Check if record already exists
        * @name record_exists
        */
        public function record_exists ( $id = null ){

            return false;

        } // end record_exists

        /**
        * Check if record is in use (trigger for delete)
        * @name record_in_use
        */
        public function record_in_use ( $id = null ){

            return false;

        } // end record_in_use

        /**
        * Paginate records
        * @name pager
        *
        * @param range : The range of pages to show, can be 1 to infinite
        * @param pages : The type for the page text to the left of the pager:
        * total : Only shows total pages
        * page : Only shows current page
        * pageoftotal : Shows current page and total
        * - FALSE (bool) : Shows nothing
        * @param prevnext : The type for the previous and next links:
        * num : Shows number for previous and next page
        * nump : Only shows number for previous page
        * numn : Only shows number for next page
        * - TRUE (bool) : Show, but no numbers
        * - FALSE (bool) : Shows nothing
        * @param prevnext_always : Always show previous and next links, depending on prevnext
        * @param firstlast : The type for the first and last links:
        * num : Shows number for first and last page
        * numf : Only shows number for first page
        * numl : Only shows number for last page
        * - TRUE (bool) : Show, but no numbers
        * - FALSE (bool) : Show nothing
        * @param firstlast_always : Always show first and last links, depending on firstlast
        *   http://www.phpfreaks.com/quickcode/code/282.php
        *
        */
        function pager($range = PAGINATION_RANGE, $pages = 'total', $prevnext = TRUE, $prevnext_always = FALSE, $firstlast = TRUE, $firstlast_always = FALSE) {
            extract( $this->kernel->params );
            $result_array = array();

            $total_items = $result_array["total_items"] = $this->filter["total_items"];
            $per_page = $this->filter["per_page"];
            $start = $this->filter["start"];

            // First, check on a few parameters to see if they're ok, we don't want negatives
            $total_items = ($total_items < 0) ? 0 : $total_items;
            $per_page = ($per_page < 1) ? 1 : $per_page;
            $range = ($range < 1) ? 1 : $range;
            $sel_page = 1;

            $float_val = $total_items / $per_page;
            $int_val = (int) $float_val;
            $reminder = $float_val - $int_val;
            $last_page_calc = round( $per_page * $reminder);
            $total_pages = $int_val + ($last_page_calc >= 1 ? 1 : 0);

            // Are there more than one pages to show? If not, this section will be skipped,
            // and only the pages_text will be shown
            if ($total_pages > 1) {
                // The page we are on
                $sel_page = round($start / $per_page) + 1;

                // The ranges indicate how many pages should be displayed before and after
                // the selected one. Here, it will check if the range is an even number,
                // and adjust the ranges appropriately. It will behave best on non-even numbers
                $range_min = ($range % 2 == 0) ? ($range / 2) - 1 : ($range - 1) / 2;
                $range_max = ($range % 2 == 0) ? $range_min + 1 : $range_min;
                $page_min = $sel_page - $range_min;

                $page_max = $sel_page + $range_max;

                // This parts checks whether the ranges are 'out of bounds'. If we're at or near
                // the 'edge' of the pagination, we will start or end there, not at the range
                $page_min = ($page_min < 1) ? 1 : $page_min;
                $page_max = ($page_max < ($page_min + $range - 1)) ? $page_min + $range - 1 : $page_max;
                if ($page_max > $total_pages) {
                    $page_min = ($page_min > 1) ? $total_pages - $range + 1 : 1;
                    $page_min = ($page_min < 1) ? 1 : $page_min;
                    $page_max = $total_pages;
                }

                // Build the links
                for ($i = $page_min;$i <= $page_max;$i++)
                    $result_array["pages"][] = array( (($i - 1) * $per_page), $i );

                // Do we got previous and next links to display?
                if (($prevnext) || (($prevnext) && ($prevnext_always))) {
                    // Aye we do, set what they will look like
                    $prev_num = (($prevnext === 'num') || ($prevnext === 'nump')) ? $sel_page - 1 : '';
                    $next_num = (($prevnext === 'num') || ($prevnext === 'numn')) ? $sel_page + 1 : '';

                    // Display previous link?
                    if (($sel_page > 1) || ($prevnext_always)) {
                        $start_at = ($sel_page - 2) * $per_page;
                        $start_at = ($start_at < 0) ? 0 : $start_at;
                        $result_array["prev_page"] = $start_at;
                    }
                    // Next link?
                    if (($sel_page < $total_pages) || ($prevnext_always)) {
                        $start_at = $sel_page * $per_page;
                        $start_at = ($start_at >= $total_items) ? $total_items - $per_page : $start_at;
                        $result_array["next_page"] = $start_at;
                    }
                }

                // This part is just about identical to the prevnext links, just a few minor
                // value differences
                if (($firstlast) || (($firstlast) && ($firstlast_always))) {
                    $first_num = (($firstlast === 'num') || ($firstlast === 'numf')) ? 1 : '';
                    $last_num = (($firstlast === 'num') || ($firstlast === 'numl')) ? $total_pages : '';

                    $first_txt = sprintf($first_txt, $first_num);
                    $last_txt = sprintf($last_txt, $last_num);

                    if ((($sel_page > ($range - $range_min)) && ($total_pages > $range)) || ($firstlast_always))
                        $result_array["first_page"] = 0;

                    if ((($sel_page < ($total_pages - $range_max)) && ($total_pages > $range)) || ($firstlast_always))
                        $result_array["last_page"] = ($total_pages - 1) * $per_page;

                }
            }

            // Display pages text?
            if ($pages) {
                $result_array["total_pages"] =  $total_pages;
                $result_array["selected_page"] =  $sel_page;
            }

            return $result_array;

        } // end pager

        ////////// MODES ////////////////////////////////////////////////

        /**
        * Default mode: lists all records
        * @name mode_default
        *
        */
        public function mode_list (){
            extract( $this->kernel->params );

            if( $this->acl and !$this->kernel->acl_can_read() ){
                $this->kernel->templ->assign("result_message", "no_access");
                return;
            }

            $this->filter();
            $this->list = $this->getAll();
            $this->pager = $this->pager();

        } // end mode_default

        /**
        * Generate xls mode
        * @name mode_xls
        *
        */
        public function mode_xls (){

            // XLS using PEAR::Spreadsheet_Excel_Writer
            // Uses: PEAR::OLE
            $this->kernel->requireonce('Spreadsheet/Excel/Writer.php');
            $this->mode_list();
            $this->template = MODE_XLS;

        } // end mode_xls

        /**
        * View single record mode
        * @name mode_view
        *
        */
        public function mode_view (){
            extract( $this->kernel->params );

            if( $this->acl and !$this->kernel->acl_can_read() ){
                $this->kernel->templ->assign("result_message", "no_access");
                return;
            }

            if( $this->row = $this->getOne( $id ) ){
                $this->kernel->params_html = array_merge( $this->kernel->params_html, $this->kernel->html_special_chars($this->row) );
                $this->kernel->templ->assign("params", $this->kernel->params_html);
            }

            $this->template = MODE_VIEW;

        } // end mode_view

        /**
        * Print single record mode
        * @name mode_print
        *
        */
        public function mode_print (){
            extract( $this->kernel->params );

            $this->mode_view();
            $this->template = MODE_PRINT;

        } // end mode_print

        /**
        * Generate pdf
        * @name mode_pdf
        *
        */
        public function mode_pdf (){
            extract( $this->kernel->params );

            $this->mode_view();
            $html = $this->kernel->templ->fetch("{$app}_print.tpl");
            $this->kernel->set_params( array( "html" => $html, "method" => "html" ) );
            echo $this->kernel->call_from("web2pdf", "index" );
            exit;

        } // end mode_pdf

        /**
        * Edit single record mode
        * @name mode_edit
        *
        */
        public function mode_edit (){
            extract( $this->kernel->params );

            if( $this->acl and !$this->kernel->acl_can_write() ){
                $this->kernel->templ->assign("result_message", "no_access");
                return;
            }

            if( $this->row = $this->getOne( $id ) ){
                $this->kernel->params_html = array_merge( $this->kernel->params_html, $this->kernel->html_special_chars($this->row) );
                $this->kernel->templ->assign("params", $this->kernel->params_html);
            } else {
                $this->mode_list();
                return;
            }

            $this->template = MODE_EDIT;

        } // end mode_edit

        /**
        * Protect edit single record mode
        * @name mode_edit_protect
        *
        */
        public function mode_edit_protect (){

            $this->template = MODE_EDIT_PROTECT;

        } // end mode_edit_protect

        /**
        * Cancel mode
        * @name mode_cancel
        *
        */
        public function mode_cancel (){

            $this->template = MODE_LIST;

        } // end mode_cancel

        /**
        * Update single record mode
        * @name mode_update
        *
        */
        public function mode_update (){
            extract( $this->kernel->params );

            if( $this->acl and !$this->kernel->acl_can_write() ){
                $this->kernel->templ->assign("result_message", "no_access");
                return;
            }

            if( $this->row = $this->getOne( $id ) ){
                if( $this->validation() ){
                    $this->kernel->templ->assign("result_message", "record_updated" );
                    $this->update();
                } else {
                    $this->kernel->templ->assign("result_message", "data_manager_validation_error");
                    $this->kernel->params_html = array_merge( $this->kernel->html_special_chars($this->row), $this->kernel->params_html );
                    $this->kernel->templ->assign("params", $this->kernel->params_html);
                    $this->template = MODE_EDIT;
                    return;
                }
            } // end if record exists

            $this->mode_list();

        } // end mode_update

        /**
        * Add record mode
        * @name mode_add
        *
        */
        public function mode_add (){

            if( $this->acl and !$this->kernel->acl_can_write() ){
                $this->kernel->templ->assign("result_message", "no_access");
                return;
            }

            $this->template = MODE_EDIT;

        } // end mode_view


        /**
        * Insert record mode
        * @name mode_insert
        *
        */
        public function mode_insert (){

            if( $this->acl and !$this->kernel->acl_can_write() ){
                $this->kernel->templ->assign("result_message", "no_access");
                return;
            }

            if( !$this->record_exists() ) {
                if( $this->validation() ){
                    $this->kernel->templ->assign("result_message", "record_inserted" );
                    $this->insert();
                } else {
                    $this->kernel->templ->assign("result_message", "data_manager_validation_error");
                    $this->kernel->params_html = array_merge( $this->kernel->html_special_chars($this->row), $this->kernel->params_html );
                    $this->kernel->templ->assign("params", $this->kernel->params_html);
                    $this->template = MODE_EDIT;
                    return;
                }
            } // end if record does not exists

            $this->mode_list();

        } // end mode_insert

        /**
        * Delete single record mode
        * @name mode_delete
        *
        */
        public function mode_delete (){

            if( $this->acl and !$this->kernel->acl_can_delete() ){
                $this->kernel->templ->assign("result_message", "no_access");
                return;
            }

            if( !$this->record_in_use() ){
                $this->kernel->templ->assign("result_message", "record_deleted" );
                $this->remove();
            } else
                $this->kernel->templ->assign("result_message", "record_in_use" );

            $this->mode_list();

        } // end mode_delete

        /**
        * Update table cell
        * @name mode_cell
        *
        */
        public function mode_cell () {
            extract( $this->kernel->params_sql );

            if( $this->acl and (!$this->kernel->acl_can_write() or !$this->kernel->acl_can_read()) ){
                $this->kernel->templ->assign("result_message", "no_access");
                return;
            }

            $this->row = $this->getOne( $id );

            if( $id and $field_name and $this->row and array_key_exists( $field_name, $this->row ) ){
                $this->kernel->db->query("UPDATE {$this->table} SET `{$field_name}` = '{$value}' WHERE id = '{$id}'");
                $this->row = $this->getOne( $id );
            }

            $this->kernel->templ->assign("value", $this->row[$field_name] );
            $this->template = MODE_CELL;

        } // end mode_cell

    } // end class data_manager

?>