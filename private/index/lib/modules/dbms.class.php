<?

    /**
    * Database management system access
    * @name dbms
    * @author Tsvetan Filev <tsvetan.filev@gmail.com>
    * @date 2009/10/10
    *
    */
    class dbms {

        public $kernel;

        public function __construct( $kernel ) {

            $this->kernel = $kernel;

            $this->kernel->db = DB::connect( $this->kernel->INITRD["DB_ACCESS"] );
            $this->kernel->db->setFetchMode( DB_FETCHMODE_ASSOC );

            // Set default connection encoding
            if( $this->kernel->INITRD["DB_SET_NAMES"] )
                $this->kernel->db->query("SET NAMES ".$this->kernel->INITRD["DB_SET_NAMES"] );

        } // end __construct

    } // end class dbms
/*
    // http://www.phpied.com/db-2-mdb2/
    //
    // db, mdb2
    class db extends mdb2 {

        public function query (){

        }

        public function getAll (){

        }
        public function getRow (){

        }
        public function getCol (){

        }
        public function getOne (){

        }

    } // end class db

    class rdb extends db {

        public function query (){

        }

        public function getAll (){

        }
        public function getRow (){

        }
        public function getCol (){

        }
        public function getOne (){

        }

    } // end class rdb
*/

?>