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
    * @global object $kernel
    *
    */
    function skeleton_index (){
        global $kernel;

        return $kernel->templ->fetch("index.tpl");
    } // end skeleton_index

?>