<?

    /**
    * @name dbcache.class.php
    * @date 2007/05/25
    * @author Tsvetan Filev <tsvetan.filev@gmail.com>
    *
    */
    class dbcache {

        /**
        * @var object
        */
        private $kernel;

        public function __construct ( $kernel ) {

            $this->kernel = $kernel;

        } // end __construct

        /**
        * Store and retrieve database caches
        *
        * @name db_cache
        * @param string $sql sql query
        * @param string $file cache file name
        * @param string $expire time to expire in seconds default 24 hours
        *
        * @return string $array extracted from database
        *
        */
        public function db_cache ( $sql, $file, $expire = 86400 ){

            // Get the records stored in cache 
            if ( file_exists( DB_CACHE_DIR.DIRECTORY_SEPARATOR.$file ) && 
                filemtime(DB_CACHE_DIR.DIRECTORY_SEPARATOR.$file) > (time() - $expire))
                return $this->db_cache_restore( $file );

            // If no cache create it
            return $this->db_cache_save( $sql, $file );

        } // end db_cache_expired

        /**
        * Save database result into cache
        *
        * @name db_cache_save
        * @param string $sql sql query
        * @param string $file cache file name
        *
        * @return string $array extracted from database
        *
        */
        public function db_cache_save ( $sql, $file, $method = "getAll" ){

            $result = $this->kernel->db->$method( $sql );
            $OUTPUT = serialize( $result );
            file_put_contents( DB_CACHE_DIR.DIRECTORY_SEPARATOR.$file, $OUTPUT );
            return $result;

        } // end db_cache_save

        /**
        * Restore database result from cache
        *
        * @name db_cache_restore
        * @param string $file cache file name
        *
        * @return string $array extracted from cache
        *
        */
        public function db_cache_restore ( $file ){

            $INPUT = file_get_contents( DB_CACHE_DIR.DIRECTORY_SEPARATOR.$file );
            return unserialize( $INPUT );

        } // end db_cache_restore

        /**
        * Removes cache file
        *
        * @name db_cache_clear
        * @param string $file cache file name (eider with path or file name only)
        *
        *
        */
        public function db_cache_clear ( $file ){

            if( file_exists( DB_CACHE_DIR.DIRECTORY_SEPARATOR.$file ) )
                unlink( DB_CACHE_DIR.DIRECTORY_SEPARATOR.$file );
            elseif( file_exists( $file ) and strstr( $file, "cache" ) )
                unlink( $file );

        } // end db_cache_clear

        /////////////////  END DB CACHE ///////////////////////////////////

    } // end class dbcache

?>