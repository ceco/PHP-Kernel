<?

    /**
    * @name testcase
    * @date
    * @author
    * @expires
    *
    */

    /**
    * @name testcase_index
    * @global object $kernel
    *
    */
    function testcase_index (){
        global $kernel;

        return $kernel->templ->fetch("index.tpl");
    } // end testcase_index

?>