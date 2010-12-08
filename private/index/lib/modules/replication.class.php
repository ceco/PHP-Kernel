<?

    class replication {

        public function store ( $query ){

            $fp = fopen( $file, 'a' ); // append
            fwrite( $fp, time().":$query\n" );
            fclose( $fp );

        } // end store

        public function read ( $lrtime ){
            $fp = fopen( $file, 'r' );
            while( $row = read() ){
                if ( $row[0] == $lrtime ) break;
            }
            while( $row = read() ){
                $result = $row;
            }
            close();

            return (array)$result;

        } // end read

    } // end class replication

?>