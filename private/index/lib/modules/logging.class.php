<?

    /**
    * Class: logging
    *
    */
    class logging {

        protected $kernel;

        protected $file;

        /**
        * Constructor
        * @name __construct
        * @param object kernel kernel object
        * @param string file log file name
        *
        */
        public function __construct ( $kernel, $file = "access.log" ) {
            $this->kernel = $kernel;
            $this->file = $file;
        } // end __construct

        /**
        * Adds a record to the file log
        * @name log_file
        * @param string app app name
        * @param string object object name
        *
        */
        public function log_file ( $app, $object, $text = null ) {
            $fp = fopen( PRIVATE_DIRECTORY.DIRECTORY_SEPARATOR.DEFAULT_APPLICATION.DIRECTORY_SEPARATOR."var".DIRECTORY_SEPARATOR."logs".DIRECTORY_SEPARATOR.$this->file, "a" );
            if( !$fp ) return;
            fwrite( $fp, date("r").": {$this->kernel->user_info["username"]}@{$_SERVER["REMOTE_ADDR"]} -> {$_SERVER["REQUEST_URI"]} $text\n" );
            fclose( $fp );
        } // end log_file

        public function arch_file (){
            $cmd = "cd ". PRIVATE_DIRECTORY.DIRECTORY_SEPARATOR.DEFAULT_APPLICATION.DIRECTORY_SEPARATOR."var".DIRECTORY_SEPARATOR."logs; tar zcvf access_".date("Ymd").".tgz {$this->file}";
            `$cmd`;
            unlink(PRIVATE_DIRECTORY.DIRECTORY_SEPARATOR.DEFAULT_APPLICATION.DIRECTORY_SEPARATOR."var".DIRECTORY_SEPARATOR."logs".DIRECTORY_SEPARATOR.$this->file);
        } // end arch_file

        /**
        * Adds a record to the database
        * @name log_db
        * @param string app app name
        * @param string object object name
        *
        */
        public function log_db ( $app, $object, $text = null ) {
            $this->kernel->db->query("INSERT INTO ".LOGGING_TABLE." (app, object, request_uri, username, remote_ip, description) VALUES('$app', '$object', '{$_SERVER["REQUEST_URI"]}', '{$this->kernel->user_info["username"]}', '{$_SERVER["REMOTE_ADDR"]}', '$text')");
        } // end log_db

    } // end class logging

?>