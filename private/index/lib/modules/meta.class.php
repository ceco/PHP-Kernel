<?

    /**
    * @name meta.class.php
    * @date 2009/09/20
    * @author Tsvetan Filev <tsvetan.filev@gmail.com>
    *
    */
    class meta {
        /**
        * @var object
        */
        private $kernel;
        public $title, $description, $keywords;

        public function __construct ( $kernel ) {
            $this->kernel = $kernel;
        } // end __construct

        public function load( $app = "index", $object = "index", $lang = "en" ){

            $row = $this->kernel->cache_db("SELECT * FROM ".METATAGS_MAP_TABLE." mm LEFT JOIN ".METATAGS_TABLE." m ON mm.meta_id = m.id WHERE mm.lang_name = '$lang' AND mm.app_name = '$app' AND mm.object_name = '$object'","metatags" .DIRECTORY_SEPARATOR. "meta_{$lang}_{$app}_{$object}.txt", 86400, "getRow");
            $this->title = $row["title"];
            $this->description = $row["description"];
            $this->keywords = $row["keywords"];

        } // end load

        public function generate($num_spaces = 4){

            $meta = "";
            if( $this->title )
                $meta .= str_repeat(" ", $num_spaces)."<title>{$this->title}</title>
";
            if( $this->description )
                $meta .= str_repeat(" ", $num_spaces)."<meta name=\"Description\" content=\"{$this->description}\" />
";
            if( $this->keywords )
                $meta .= str_repeat(" ", $num_spaces)."<meta name=\"Keywords\" content=\"{$this->keywords}\" />";
            return $meta;

        } // end generate

    } // end class meta

?>