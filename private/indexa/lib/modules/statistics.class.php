<?

    class statistics {

        /**
        * @var integer
        */
        protected $mtime = null;

        /**
        * @var object
        */
        private $kernel;

        public function __construct ( $kernel ) {

            $this->kernel = $kernel;

        } // end __construct

        /**
        * Saves timestamp
        *
        * @name save_time
        *
        */
        public function save_time () {
            // Get current time
            $mtime = microtime();
            $mtime = explode(' ',$mtime);
            $this->mtime = $mtime[1] + $mtime[0];
        } // end save_time

        /**
        * Calculate time difference
        *
        * @name calc_time
        *
        * @return float totaltime
        *
        */
        public function calc_time () {
            // Calculate the running time and display in title
            $mtime_e = microtime();
            $mtime_e = explode(' ',$mtime_e);
            $mtime_e = $mtime_e[1] + $mtime_e[0];
            $totaltime = ($mtime_e - $this->mtime);

            return $totaltime;
        } // end calc_time
        /////////////////  END STATISTICS  /////////////////////////////////

    } // end class statistics

?>