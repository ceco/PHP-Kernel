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
    * @global object $main
    *
    */
    function testcase_index (){
        global $main;

        return $main->templ->fetch("index.tpl");
    } // end testcase_index

?>