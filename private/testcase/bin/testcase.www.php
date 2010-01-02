<?

    require_once('simpletest/unit_tester.php');
    require_once('simpletest/reporter.php');
    require_once('KernelTest.class.php');

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

        $test = new KernelTest();
        $test->run(new HtmlReporter());

    } // end testcase_index

    function testcase_regexp (){
        global $main;

        $string = file_get_contents("/home/ceco/Documents/My Projects/Factory/projects/applications/private/app2/usr/templates/www/app2_list.tpl");

        $main->modprobe("regexp");

        $list = $main->regexp_match_all('<td>{$row.[all]}</td>',$string);
        print_r( $list );
/*
        $string = file_get_contents("/home/ceco/Documents/My Projects/Factory/projects/applications/private/app2/usr/templates/www/app2_edit.tpl");


        $list = $main->regexp_match_all('<tr>
                                [all]
                        </tr>',$string);
        print_r( $list );
*/
    } // end testcase_regexp

    function testcase_frontend () {
        global $main;

        $main->templ->display("frontend.tpl");
    } // end testcase_frontend

    function testcase_frontend_test(){
        global $main;

        echo "Now: ".date("r")." ";
        print_r( $main->params );
    } // end testcase_frontend_test

    function testcase_frontend_js(){
        global $main;

        echo "
<b>Script test</b>
<script>
var text = 'text';
function test(){
  alert( text );
}
</script>
<br />
<script>
var text1 = 'text 1';
function test1(){
  alert( text1 );
}
</script>
<br />
";
exit;

    } // end testcase_frontend_js

    function testcase_ser (){
        global $main;

        $mm = serialize( $main );
        //file_put_contents("/tmp/test.txt", $mm );
        $ll = unserialize( $mm );

        print_r( $ll->list_apps() );
    } // end testcase_ser

    function testcase_securedb () {
        global $main;

        $key = 'qwerty';

        $main->modprobe("securedb");

        //$main->db->query("INSERT INTO securedb SET ".$main->securedb_encrypt_all(array('name','description'), array('name value'.rand(),'description value'.rand()), $key ) );

        print_r( $main->db->getAll("SELECT ".$main->securedb_decrypt_all(array('name','description'),$key)." FROM securedb WHERE ". $main->securedb_search(array('name','description'), $key )." LIKE '%name%'" ) );

    } // end testcase_securedb

?>