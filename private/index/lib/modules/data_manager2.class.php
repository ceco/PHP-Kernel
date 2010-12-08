<?

    /**
    * Data manager modes
    * @const string modes
    */

    define( 'MODE_LIST',          'list');
    define( 'MODE_VIEW',          'view');
    define( 'MODE_PRINT',         'print');
    define( 'MODE_PDF',           'pdf');
    define( 'MODE_XML',           'xml');
    define( 'MODE_XLS',           'xls');

    define( 'MODE_ADD',           'add');
    define( 'MODE_INSERT',        'insert');
    define( 'MODE_EDIT',          'edit');
    define( 'MODE_CELL',          'cell');
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

    class data_manager2 {

        public    $kernel = null;
        public    $mode   = null;
        protected $mode_method = null;
        protected $table  = null;

        public    $filter = array();
        public    $orders = array();
        public    $pager  = array();
        public    $list   = array();
        public    $row    = array();

        public function __construct ( $kernel ){

            $this->kernel = $kernel;

        } // end __construct

        ////// Main methods

        public function set_mode ($mode){
            $this->mode = $mode;
            $this->mode_method = "mode_{$mode}";
        }

        public function input (){
            $this->set_mode($this->kernel->params["mode"]);
        }

        public function validate (){

            // Validate mode
            if( !method_exists( $this, $this->mode_method ) ){
                $this->set_mode(MODE_LIST);
            }

            // Validate acl if loaded
            if( $this->kernel->acl )
                $this->kernel->acl_can_do( $this->kernel->user_info["group_id"] , $this->kernel->app, $this->kernel->object, $this->mode );

            // Validate form

        }

        public function process (){

            $this->input();
            $this->validate();
            $this->{$this->mode_method}();
            return $this->output();

        }

        public function output (){

            return array($this->list, $this->pager, $this->mode);

        }

        ////// Modes

        public function mode_list (){

            $this->filter();
            $this->list = $this->getAll();
            $this->pager();

        }

        /**
        * View single record mode
        * @name mode_view
        *
        */
        public function mode_view (){
            extract( $this->kernel->params_sql );

            if( $this->row = $this->getOne( $id ) ){
                $this->kernel->params_html = array_merge( $this->kernel->params_html, $this->kernel->html_special_chars($this->row) );
                $this->kernel->templ->assign("params", $this->kernel->params_html);
            }

        } // end mode_view

        /**
        * Edit single record mode
        * @name mode_edit
        *
        */
        public function mode_edit (){
            extract( $this->kernel->params_sql );

            if( $this->row = $this->getOne( $id ) ){
                $this->kernel->params_html = array_merge( $this->kernel->params_html, $this->kernel->html_special_chars($this->row) );
                $this->kernel->templ->assign("params", $this->kernel->params_html);
            } else {
                $this->set_mode(MODE_LIST);
                $this->mode_list();
            }

        } // end mode_edit

        /**
        * Update single record mode
        * @name mode_update
        *
        */
        public function mode_update (){
            extract( $this->kernel->params_sql );

            if( $this->row = $this->getOne( $id ) ){
                if( $this->validation() ){
                    $this->kernel->templ->assign("result_message", "record_updated" );
                    $this->update();
                } else {
                    $this->kernel->templ->assign("result_message", "data_manager_validation_error");
                    $this->kernel->params_html = array_merge( $this->kernel->html_special_chars($this->row), $this->kernel->params_html );
                    $this->kernel->templ->assign("params", $this->kernel->params_html);
                    return;
                }
            } // end if record exists

            $this->set_mode(MODE_LIST);
            $this->mode_list();

        } // end mode_update

        /**
        * Add record mode
        * @name mode_add
        *
        */
        public function mode_add (){
            $this->set_mode(MODE_EDIT);
        } // end mode_view


        /**
        * Insert record mode
        * @name mode_insert
        *
        */
        public function mode_insert (){

            if( !$this->record_exists() ) {
                if( $this->validation() ){
                    $this->kernel->templ->assign("result_message", "record_inserted" );
                    $this->insert();
                } else {
                    $this->kernel->templ->assign("result_message", "data_manager_validation_error");
                    $this->kernel->params_html = array_merge( $this->kernel->html_special_chars($this->row), $this->kernel->params_html );
                    $this->kernel->templ->assign("params", $this->kernel->params_html);
                    $this->set_mode(MODE_LIST);
                    return;
                }
            } // end if record does not exists

            $this->set_mode(MODE_LIST);
            $this->mode_list();

        } // end mode_insert

        /**
        * Delete single record mode
        * @name mode_delete
        *
        */
        public function mode_delete (){

            if( !$this->record_in_use() ){
                $this->kernel->templ->assign("result_message", "record_deleted" );
                $this->remove();
            } else
                $this->kernel->templ->assign("result_message", "record_in_use" );

            $this->set_mode(MODE_LIST);
            $this->mode_list();

        } // end mode_delete

        /**
        * Update table cell
        * @name mode_cell
        *
        */
        public function mode_cell () {
            extract( $this->kernel->params_sql );

            $this->row = $this->getOne( $id );

            if( $id and $field_name and $this->row and array_key_exists( $field_name, $this->row ) ){
                $this->kernel->db->query("UPDATE `{$this->table}` SET `{$field_name}` = '{$value}' WHERE id = '{$id}'");
                $this->row = $this->getOne( $id );
            }

            $this->kernel->templ->assign("value", $this->row[$field_name] );

        } // end mode_cell

        public function mode_xls (){

        } // end mode_xls

        public function mode_pdf (){

        } // end mode_pdf

        ////// Data methods

        public function getAll (){
            extract( $this->kernel->params_sql );

            $list = $this->kernel->db->getAll("SELECT SQL_CALC_FOUND_ROWS * FROM `{$this->table}` WHERE {$this->filter["where"]} {$this->filter["group_by"]} {$this->filter["order_by"]} LIMIT {$this->filter["start"]}, {$this->filter["per_page"]}");
            $this->filter["total_items"] = $this->kernel->db->getOne("SELECT FOUND_ROWS()");

            return $list;
        }

        public function getOne ($id){
            return $this->kernel->db->getRow("SELECT * FROM `{$this->table}` WHERE id = '$id'");
        }

        protected function insert (){

        }

        protected function update (){

        }

        protected function remove (){
            extract( $this->kernel->params_sql );

            if( $id and preg_match('|^[\d,]+$|', $id ) )
                $this->kernel->db->query("DELETE FROM {$this->table} WHERE id IN ($id)");
        }

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

        ////// Helpers

        public function validation() { 

            return true; 

        }

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

        public function filter (){
            extract( $this->kernel->params_sql );

            // Set page
            $page = is_numeric($page) ? abs((int)$page) : 1;
            $per_page = $this->kernel->user_info["show_num_records"] ? (int)$this->kernel->user_info["show_num_records"] : $this->kernel->INITRD["PAGINATION_RECORDS_PER_PAGE"];

            // Set filter
            $this->filter = array( "where" => " 1 ", "group_by" => "", "order_by" => "", "page" => $page, "per_page" => $per_page, "start" => ($page - 1) * $per_page );
        }

        public function pager (){

            // Initializations
            $float_val = $this->filter["total_items"] / $this->filter["per_page"];
            $int_val = (int)$float_val;
            $last_page_calc = round( $this->filter["per_page"] * ($float_val - $int_val) );
            $totalPages = $int_val + ($last_page_calc >= 1 ? 1 : 0);
            
            $currentPage = $this->filter["page"] ? $this->filter["page"] : 1;
            $visiblePages = $this->kernel->INITRD["PAGINATION_RANGE"];
            $startPage = 1;
            $endPage = $totalPages;

            // Bottom bounds
            if( $totalPages == 0 ) $totalPages = 1;
            if( $currentPage > $totalPages ) $currentPage = $totalPages;

            // Calculations
            $startPage = $currentPage - (int)($visiblePages / 2);
            if( $startPage < 1 ) $startPage = 1;
            $endPage = $startPage + $visiblePages - 1;

            // Upper bounds
            if( $endPage > $totalPages ) $endPage = $totalPages;
            if( $endPage < $startPage ) $endPage = $startPage;

            // Output variables
            $this->pager["startPage"] = $startPage;
            $this->pager["endPage"] = $endPage;
            $this->pager["currentPage"] = $currentPage;

            $this->pager["prevPage"] = $currentPage > 1 ? $currentPage - 1 : 0;
            $this->pager["nextPage"] = $currentPage < $totalPages ? $currentPage + 1 : 0;
            $this->pager["firstPage"] = $startPage > 1 ? 1 : 0;
            $this->pager["lastPage"] = $endPage < $totalPages ? $totalPages : 0;

            $this->pager["visiblePages"] = $visiblePages;
            $this->pager["totalPages"] = $totalPages;

            $this->pager["itemsStart"] = ($currentPage - 1) * $this->filter["per_page"];
            $this->pager["totalItems"] = $this->filter["total_items"];
            $this->pager["itemsPerPage"] = $this->filter["per_page"];
        }

    } // end class data_manager2

?>