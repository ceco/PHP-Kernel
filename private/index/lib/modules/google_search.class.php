<?

    class google_search {

        public $kernel;

        public function __construct( $kernel ){

            $this->kernel = $kernel;

        } // end __construct

        public function google_search($query, $lang = 'bg' ){

            $result = file_get_contents('http://www.google.bg/search?hl='.$lang.'&q='.urlencode($query).'&meta=&ie=UTF-8');
            preg_match("|class=p>(.*?)</a>|", $result, $m );
            preg_match("|class=p>(.*?)\$|", $m[1], $m );
            $text = iconv( 'CP1251', 'UTF-8',$m[1]);
            return $text ? array( $text, str_replace( array("<b>","<i>","</b>","</i>") , array("","","",""), $text) ) : null;

        } // end search

    } // end google_search

?>