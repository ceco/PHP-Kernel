<?

    /**
    * @name skeleton
    * @date
    * @author
    * @expires
    *
    */

    /**
    * @name skeleton_index
    * @global object $main
    *
    */
    function skeleton_index (){
        global $main;

        return $main->templ->fetch("index.tpl");
    } // end skeleton_index

?>