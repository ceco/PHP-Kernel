<?

    /**
    * class pager
    * pagination
    * @name pager
    * @author Tsvetan Filev <tsvetan.filev@gmail.com>
    * @date 11/30/2007
    *
    */
    class pager {
        /**
        * @var array
        */
        protected $pagination;
        /**
        * @var array
        */
        protected $filter;
        /**
        * Constructor for pager
        * @name __construct
        *
        */
        public function __construct ( $main ){

        } // end __construct

        /**
        * Generate conditons array used for different operations
        * @name filter
        *
        */
        public function filter ( $total_items, $per_page, $start ){

            $this->filter["total_items"] = $total_items;
            $this->filter["per_page"] = $per_page;
            $this->filter["start"] = $start;

        } // end filter
        /**
        * Paginate records
        * @name pagination
        *
        * @param base_url : The links will refer to this url, don't include arguments
        * @param total_items : The total of items for the list
        * @param items_per_page : Number of items to show per page
        * @param start_item : Item to start at (start counting at 0, not 1!)
        * @param page_range : The range of pages to show, can be 1 to infinite
        * @param pages_text : The type for the page text to the left of the pagination:
        * @param total : Only shows total pages
        * @param page : Only shows current page
        * @param pageoftotal : Shows current page and total
        *- FALSE (bool) : Shows nothing
        * @param prevnext_links : The type for the previous and next links:
        * @param num : Shows number for previous and next page
        * @param nump : Only shows number for previous page
        * @param numn : Only shows number for next page
        *- TRUE (bool) : Show, but no numbers
        *- FALSE (bool) : Shows nothing
        * @param prevnext_always : Always show previous and next links, depending on prevnext_links
        * @param firstlast_links : The type for the first and last links:
        * @param num : Shows number for first and last page
        * @param numf : Only shows number for first page
        * @param numl : Only shows number for last page
        *- TRUE (bool) : Show, but no numbers
        *- FALSE (bool) : Show nothing
        * @param firstlast_always : Always show first and last links, depending on firstlast_links
        *   http://www.phpfreaks.com/quickcode/code/282.php
        *
        */
        function pagination($range = PAGINATION_RANGE, $pages = 'total', $prevnext = TRUE, $prevnext_always = FALSE, $firstlast = TRUE, $firstlast_always = FALSE) {

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

        } // end pagination

    } // end class pager

?>