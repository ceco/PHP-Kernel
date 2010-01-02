<?

    class securedb {

        protected $main;

        public function __construct ( $main ) {

            $this->main = $main;

        } // end __construct

        public function encrypt( $value, $key ) {

            return "aes_encrypt('$value','$key')";

        } // end encrypt

        public function encrypt_all( $fields = array(), $values = array(), $key ){
            $sql = array();

            foreach( $fields as $row => $field )
                $sql[] = " $field = ".$this->encrypt(  $values[$row], $key );

            return join( ",", $sql );
        } // end encrypt_all

        public function decrypt( $column, $key ) {

            return "aes_decrypt( $column, '$key' ) AS $column";

        } // end decrypt

        public function decrypt_all( $fields = array(), $key ){
            $sql = array();

            foreach( $fields as $field )
                $sql[] = $this->decrypt( $field, $key );

            return join( ",", $sql );
        } // end decrypt_all

        public function search( $columns = array(), $key ) {

            return "aes_decrypt( concat( ".join( ",", $columns )."), '$key' )";

        } // end search

    } // end class securedb

?>