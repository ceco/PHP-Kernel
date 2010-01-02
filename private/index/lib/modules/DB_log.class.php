<?

    // Example in DB connect: log://user:pass@localhost/db
    //
    class DB_log extends DB_mysql {

        function &query($query, $params = array()){
            $fp = fopen( LOGS_DIRECTORY.DIRECTORY_SEPARATOR.'sql.log', "a" );
            fwrite( $fp, date("r").": query:$query\n" );
            fclose( $fp );
            return parent::query($query);
        } // end query

        function &getAll($query, $params = array(), $fetchmode = DB_FETCHMODE_DEFAULT){
             $fp = fopen( LOGS_DIRECTORY.DIRECTORY_SEPARATOR.'sql.log', "a" );
             fwrite( $fp, date("r").": getAll:$query\n" );
             fclose( $fp );
             return parent::getAll($query);
        } // end getAll

        function &getRow($query, $params = array(), $fetchmode = DB_FETCHMODE_DEFAULT){
            $fp = fopen( LOGS_DIRECTORY.DIRECTORY_SEPARATOR.'sql.log', "a" );
            fwrite( $fp, date("r").": getRow:$query\n" );
            fclose( $fp );
            return parent::getRow($query);
        } // end getRow

        function &getCol($query, $col = 0, $params = array()){
            $fp = fopen( LOGS_DIRECTORY.DIRECTORY_SEPARATOR.'sql.log', "a" );
            fwrite( $fp, date("r").": getCol:$query\n" );
            fclose( $fp );
            return parent::getCol($query);
        } // end getCol

        function &getOne($query, $params = array())
        {
            $fp = fopen( LOGS_DIRECTORY.DIRECTORY_SEPARATOR.'sql.log', "a" );
            fwrite( $fp, date("r").": getOne:$query\n" );
            fclose( $fp );
            return parent::getOne($query);
        } // end getOne

    } // end class DB_log

?>