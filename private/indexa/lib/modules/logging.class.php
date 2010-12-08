<?

    class logging {

        protected $kernel;
        protected $file;

        public function __construct ( $kernel, $file = "access.log" ) {
            $this->kernel = $kernel;
            $this->file = $file;
        } // end __construct


        public function log_file ( $app, $object ) {
            $fp = fopen( PRIVATE_DIRECTORY.DIRECTORY_SEPARATOR.DEFAULT_APPLICATION.DIRECTORY_SEPARATOR."var".DIRECTORY_SEPARATOR."logs".DIRECTORY_SEPARATOR.$this->file, "a" );
            fwrite( $fp, date("r").": {$app}_{$object} ({$_SERVER["REQUEST_URI"]}) - {$this->kernel->user_info["username"]}\n" );
            fclose( $fp );
        } // end log_file

        public function arch_file (){
            $cmd = "cd ". PRIVATE_DIRECTORY.DIRECTORY_SEPARATOR.DEFAULT_APPLICATION.DIRECTORY_SEPARATOR."var".DIRECTORY_SEPARATOR."logs; tar zcvf access_".date("Ymd").".tgz {$this->file}";
            `$cmd`;
            unlink($this->file);
        } // end arch_file

        public function log_db ( $app, $object ) {
            $this->kernel->db->query("INSERT INTO ".LOGGING_TABLE." (`event_date`,`app`,`object`,`request_uri`,`username`) VALUES(now(),'$app','$object','{$_SERVER["REQUEST_URI"]}','{$this->kernel->user_info["username"]}')");
        } // end log_db

    } // end class logging

?>