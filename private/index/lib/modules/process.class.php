<?

    class process {

        protected $pids = array();
        protected $resources = array();
        protected $stdin = array();
        protected $stdout = array();
        protected $stderr = array();

        public function open (){

            // $process = proc_open('php', $descriptorspec, $pipes, $cwd, $env);

        } // end open

        public function read () {

        }

        public function write (){

        }

        public function close (){

            // $return_value = proc_close($process);

        } // end close

        public function idle (){

        } // end idle

    } // end class process

?>