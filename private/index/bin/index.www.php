<?

    function index_index (){
        global $kernel;

        $kernel->templ->display("index.tpl");
    } // end  index_index

    function index_serialize(){
        global $kernel;

        // Create a stream
        $opts = array('http'=>array('method'=>"GET",'header'=>"serialize: php\r\n"));
        $context = stream_context_create($opts);
        $result = file_get_contents("http://localhost/projects/Factory/projects/kernel/", false, $context);
        print_r( unserialize($result) );
    }

    function index_test (){
        global $kernel;
        extract( $kernel->params );

        require_once("modules/data_manager2.class.php");
        $dm = new data_manager2($kernel);
        $dm->filter();
        $dm->filter["total_items"] = $total_items; // ? $total_items : 271;
        $dm->pager();
        echo "<pre>";
        print_r( $dm->pager );
    }

?>