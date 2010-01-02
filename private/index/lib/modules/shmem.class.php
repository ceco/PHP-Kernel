<?

    /**
    * @name shmem.class.php
    * @date 2008/10/24
    * @author Tsvetan Filev <tsvetan.filev@gmail.com>
    *
    */
    class shmem {
        /**
        * @var object
        */
        private $kernel = null;
        private $shmem_enabled = false;

        public function __construct ( $kernel ) {
            $this->kernel = $kernel;
            if( !function_exists('shmop_open') )
                print("Warning: Shmem not installed!");
        } // end __construct

        /**
        * Store and retrieve shmem
        *
        * @name data
        * @param string $data data to shmem
        * @param string $key shmem key
        * @param string $expire time to expire in seconds default 24 hours
        *
        * @return string $data restored data
        *
        * Example:
        * if( !$array = $main->shmem_data($array, $key, 300) ){
        *     $array = code;
        *     $main->shmem_data( $array, "UNIQUE_ID" );
        * }
        */
        public function data ( $data, $key, $expire = 86400 ){
            // If data create shmem
            if( $data ) $this->save( $data, $key );
            // Get the records stored in shmem
            else {
                ob_start();
                $shm_id = @shmop_open(ftok('.',$key), 'a', 0, 0);
                ob_end_clean();
                if ( $shm_id && $size = shmop_size($shm_id) ){
                    $data = unserialize(shmop_read($shm_id, 0, $size));
                    //echo time() - $data["mtime"];
                    if( $data["mtime"] > (time() - $expire)) return $data["body"];
                }
            }

            return false;
        } // end data

        /**
        * Save data into shmem
        *
        * @name save
        * @param string $data data to save
        * @param string $key shmem key name
        *
        */
        public function save ( $data, $key ){
            echo "No cache";
            $this->clear($key);
            $data_str = serialize( array( "mtime" => time(), "body" => $data ) );
            $len = strlen($data_str);
            $shm_id = shmop_open(ftok('.',$key), "c", 0644, $len);
            if (!$shm_id) return false;
            $shm_bytes_written = shmop_write($shm_id, $data_str, 0);
            if ($shm_bytes_written != $len) return false;
        } // end save

        /**
        * Restore data from shmem
        *
        * @name restore
        * @param string $id shmem id name
        *
        * @return mixed $result extracted from shmem
        *
        */
        public function restore ( $id ){

            $shm_size = shmop_size($id);
            $data = unserialize( shmop_read($id, 0, $shm_size) );

            return $data["body"];
        } // end restore

        /**
        * Removes shmem id
        *
        * @name clear
        * @param string $key shmem key name
        *
        */
        public function clear ( $key ){
            ob_start();
            $shm_id = @shmop_open(ftok('.',$key), "a", 0, 0);
            ob_end_clean();
            if( $shm_id ){
                $res = shmop_delete($shm_id);
                shmop_close($shm_id);
                return $res;
            }
            return true;
        } // end clear

        /**
        * Store and retrieve database results
        *
        * @name db
        * @param string $sql sql query
        * @param string $key shmem key name
        * @param string $expire time to expire in seconds default 24 hours
        * @param string $method db method to execute
        *
        * @return string $array extracted from database
        *
        */
        public function db ( $sql, $key, $expire = 86400, $method = "getAll" ){

            $data = array();
            ob_start();
            $shm_id = @shmop_open(ftok('.',$key), 'a', 0644, 100);
            ob_end_clean();

            if ( $shm_id && $size = shmop_size($shm_id) ){
                $data_shm = unserialize(shmop_read($shm_id, 0, $size));
                //echo time() - $data_shm["mtime"];
                if( $data_shm["mtime"] > (time() - $expire)) $data = $data_shm["body"];
            }

            if( !$data ) {  // If no shmem create it
                $sql_safe = $sql;
                if( stristr( $sql, "limit" ) ){
                    $sqls = preg_split("|limit|i", $sql );
                    $sql_safe = $sqls[0];
                }
                $data = $this->db_save( $sql_safe, $key, $method );
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
        * Save database result into shmem
        *
        * @name db_save
        * @param string $sql sql query
        * @param string $key shmem key name
        * @param string $method db method to execute
        *
        * @return mixed $data extracted from database
        *
        * Example:
        * $array = $main->shmem_db("select * from table", 's', 300);
        */
        public function db_save ( $sql, $key, $method = "getAll" ){

            $data = $this->kernel->db->$method( $sql );
            $this->save( $data, $key );
            return $data;

        } // end db_save

    } // end class shmem

?>