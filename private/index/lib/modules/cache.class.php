<?

    /**
    * @name cache.class.php
    * @date 2007/10/05
    * @author Tsvetan Filev <tsvetan.filev@gmail.com>
    *
    */
    class cache {
        /**
        * @var object
        */
        private $kernel;

        public function __construct ( $kernel ) {
            $this->kernel = $kernel;
        } // end __construct

        /**
        * Store and retrieve cache
        *
        * @name data
        * @param string $data data to cache
        * @param string $file cache file name
        * @param string $expire time to expire in seconds default 24 hours
        *
        * @return string $data restored data
        *
        * Example:
        * if( !$array = $main->cache_data($array, "file.txt", 300) ){
        *     $array = code;
        *     $main->cache_data( $array, "file.txt" );
        * }
        */
        public function data ( $data, $file, $expire = 86400 ){
            // If data create cache
            if( $data ) $this->save( $data, $file );
            // Get the records stored in cache
            elseif ( file_exists( $this->kernel->INITRD['CACHE_DIR'] . DIRECTORY_SEPARATOR . $file ) && 
                filemtime( $this->kernel->INITRD['CACHE_DIR'] . DIRECTORY_SEPARATOR.$file) > (time() - $expire))
                return $this->restore( $file );

            return false;
        } // end data

        /**
        * Save data into cache
        *
        * @name save
        * @param string $data data to save
        * @param string $file cache file name
        *
        */
        public function save ( $data, $file ){
            file_put_contents( $this->kernel->INITRD['CACHE_DIR'] . DIRECTORY_SEPARATOR . $file, serialize( $data ) );
        } // end save

        /**
        * Restore data from cache
        *
        * @name restore
        * @param string $file cache file name
        *
        * @return mixed $result extracted from cache
        *
        */
        public function restore ( $file ){
            return unserialize( file_get_contents( $this->kernel->INITRD['CACHE_DIR'] . DIRECTORY_SEPARATOR . $file ) );
        } // end restore

        /**
        * Removes cache file
        *
        * @name clear
        * @param string $file cache file name (eider pathname or file name only)
        * @param boolean $all if true deletes all cache files
        *
        */
        public function clear ( $file, $all = false ){
            if( file_exists( $this->kernel->INITRD['CACHE_DIR'].DIRECTORY_SEPARATOR.$file ) ){
                if( !$all )
                    unlink( $this->kernel->INITRD['CACHE_DIR'].DIRECTORY_SEPARATOR.$file );
                else
                    foreach ( new DirectoryIterator($this->kernel->INITRD['CACHE_DIR'].DIRECTORY_SEPARATOR.$file) as $it )
                        if($it->isFile()) unlink( $it->getPathname() );
            } elseif( file_exists( $file ) and strstr( $file, "cache" ) ){
                if( !$all )
                    unlink( $file );
                else
                    foreach ( new DirectoryIterator($file) as $it )
                        if($it->isFile()) unlink( $it->getPathname() );
            }
        } // end clear

        /**
        * Store and retrieve database results
        *
        * @name db
        * @param string $sql sql query
        * @param string $file cache file name
        * @param string $expire time to expire in seconds default 24 hours
        * @param string $method db method to execute
        *
        * @return string $array extracted from database
        *
        * Example:
        * $array = $main->cache_db("select * from table", "file.txt", 300);
        */
        public function db ( $sql, $file, $expire = 86400, $method = "getAll" ){

            // Get the records stored in cache
            if ( file_exists( $this->kernel->INITRD['CACHE_DIR'] . DIRECTORY_SEPARATOR . $file ) && 
                filemtime($this->kernel->INITRD['CACHE_DIR'] . DIRECTORY_SEPARATOR . $file) > (time() - $expire)){
                $data = $this->restore( $file );
            }
            else {  // If no cache create it
                if( stristr( $sql, "limit" ) ){
                    $sqls = preg_split("|limit|i", $sql );
                    $sql_safe = $sqls[0];
                } else $sql_safe = $sql; // end limit
                $data = $this->db_save( $sql_safe, $file, $method );
            } // end else

            // If limit statement get only limit rows
            if( is_array( $data ) and preg_match("|LIMIT *(\d+),*(.*)|i", $sql, $m ) ){
                $start = $m[2] ? $m[1] : 0;
                $rows = $m[2] ? $m[2]: $m[1];
                for( $c = $start; $c < $start+$rows; $c++ ) $data_list[] = $data[$c];
                return $data_list;
            } // end limit

            return $data;

        } // end db

        /**
        * Save database result into cache
        *
        * @name db_save
        * @param string $sql sql query
        * @param string $file cache file name
        * @param string $method db method to execute
        *
        * @return mixed $result extracted from database
        *
        */
        public function db_save ( $sql, $file, $method = "getAll" ){

            $result = $this->kernel->db->$method( $sql );
            $this->save( $result, $file );
            return $result;

        } // end db_save

    } // end class cache

?>