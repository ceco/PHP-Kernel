<?

    class statistics {

        /**
        * @var integer
        */
        protected $mtimes = array();

        /**
        * @var object
        */
        private $kernel;

        public function __construct ( $kernel, $timer = "default" ) {

            $this->kernel = $kernel;
            $this->mtimes[$timer] = 0;

        } // end __construct

        /**
        * Saves timestamp
        *
        * @name save_time
        *
        */
        public function save_time ( $timer = "default" ) {
            // Get current time
            $mtime = microtime();
            $mtime = explode(' ',$mtime);
            $this->mtimes[$timer] = $mtime[1] + $mtime[0];
        } // end save_time

        /**
        * Calculate time difference
        *
        * @name calc_time
        *
        * @return float totaltime
        *
        */
        public function calc_time ( $round = 4, $timer = "default"  ) {
            // Calculate the running time and display in title
            $mtime_e = microtime();
            $mtime_e = explode(' ',$mtime_e);
            $mtime_e = $mtime_e[1] + $mtime_e[0];
            $totaltime = ($mtime_e - $this->mtimes[$timer]);

            return sprintf( "%.{$round}f", $totaltime );
        } // end calc_time
        /////////////////  END STATISTICS  /////////////////////////////////

    } // end class statistics

?>