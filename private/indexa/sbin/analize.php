<? 

    /**
    * @name analize.php
    * @date 11/04/2007
    * @author Tsvetan Filev <tsvetan.filev@gmail.com>
    * Script for environment analizis
    *
    */

    require_once("../etc/config.php");

    echo "<table border='1'>";
    echo '<tr><td>Current PHP version: </td><td>' . phpversion();
    if( phpversion() < 5.0 ) echo " (Try AddHandler application/x-httpd-php5 .php in .htaccess)";
    echo '</td></tr>';
    echo '<tr><td>OS:  </td><td>'. PHP_OS. "</td></tr>";
    echo '<tr><td><a href="http://www.php.net/manual/en/function.php-sapi-name.php">Server API interface:</a> </td><td>'.php_sapi_name() . '</td></tr>';
//    echo '<tr><td>Server:  </td><td><pre>'; print_r( $_SERVER ); echo "</pre></td></tr>";
//     echo '<tr><td>Environment:  </td><td><pre>'; print_r( $_ENV ); echo "</pre></td></tr>";
    echo '<tr><td>Include path:  </td><td>- '.str_replace(":","<br /> - ",get_include_path()) . '</td></tr>';

    echo '<tr><td>Safe mode:  </td><td>'. (ini_get("safe_mode") ? "Yes" : "No") . "</td></tr>";

    echo '<tr><td>tar -help:  </td><td>'. `tar 2>&1`. "</td></tr>";
    echo '<tr><td>unzip:  </td><td>'. `unzip 2>&1`. "</td></tr>";

    echo '<tr><td>get_loaded_extensions();  </td><td><pre>';
    $extensions = get_loaded_extensions();
    if( in_array( "Zend Optimizer", $extensions ) ) echo "<span style='color:red'>Zend Optimizer Found!</span><br />\n";
    print_r( $extensions );
    echo '</pre></td></tr>';

    echo '<tr><td>PEAR.php:  </td><td>';
    echo @include_once('PEAR.php');
    echo '</td></tr>';
    echo '<tr><td>DB.php:  </td><td>';
    echo @include_once('DB.php');
    echo '</td></tr>';
    echo '<tr><td>DB/mysql.php:  </td><td>';
    echo @include_once('DB/mysql.php');
    echo '</td></tr>';
    echo '<tr><td>MDB2.php:  </td><td>';
    echo @include_once('MDB2.php');
    echo '</td></tr>';
    echo '<tr><td>MDB2/Driver/mysql.php:  </td><td>';
    echo @include_once('MDB2/Driver/mysql.php');
    echo '</td></tr>';

    echo '<tr><td>Smarty.class.php:  </td><td>';
    echo @include_once('Smarty/libs/Smarty.class.php');
    echo '</td></tr>';

    echo '<tr><td>mysql connect:  </td><td><form method="post">U:<input type="text" name="user" value="'.$_REQUEST["user"].'" /> P:<input type="password" name="pass" value="'.$_REQUEST["pass"].'" />H:<input type="text" name="host" value="'.$_REQUEST["host"].'" /><input type="submit" value="Connect"></form>';
    if( $_REQUEST["user"] and  $_REQUEST["pass"] and $_REQUEST["host"] ){
         if( !mysql_connect( $_REQUEST["host"], $_REQUEST["user"], $_REQUEST["pass"] ) ) print( mysql_error() );
         else {
            echo "Connected<br />";
            $result = mysql_query("select version()");
            $result = mysql_fetch_array($result);
            echo "Version: $result[0] <br />";
            echo "Databases:<br/>";
            $result = mysql_query("show databases");
            while( $db = mysql_fetch_assoc($result) ){
                echo "- {$db["Database"]} <br />";
            }
         }
    }
    echo '</td></tr>';

    echo '<tr><td>mcrypt_decrypt();  </td><td>';
    echo function_exists('mcrypt_decrypt');
    echo '</td></tr>';

    echo '<tr><td><a href="?send=1">mail("tsvetan.filev@gmail.com")</a></td><td>';
    if( $_REQUEST["send"] )
        echo mail('tsvetan.filev@gmail.com','Subject','Body');
    echo '</td></tr>';

    echo '<tr><td>phpinfo():  </td><td>';
    phpinfo();
    echo '</td></tr>';
    echo '</table>';

?>