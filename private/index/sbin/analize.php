<? 

    /**
    * @name analize.php
    * @date 11/04/2007
    * @author Tsvetan Filev <tsvetan.filev@gmail.com>
    * Script for environment analizis
    *
    */

    session_start();

    $_SESSION["sessions"]++;

    //require_once("../etc/config.php");

    echo "
    <html>
    <style>
        pre { font-size: 11px; color: red; }
    </style>
    <body>
    <table border='1'>";
    echo '<tr><td>Current PHP version: </td><td>' . phpversion();
    if( phpversion() < 5.0 ) echo " (Try AddHandler application/x-httpd-php5 .php in .htaccess)";
    echo '</td></tr>';

    echo '<tr><td>OS:  </td><td>'. PHP_OS. "</td></tr>";
    echo '<tr><td><a href="http://www.php.net/manual/en/function.php-sapi-name.php">Server API interface:</a> </td><td>'.php_sapi_name() . '</td></tr>';
    echo '<tr><td>/proc/cpuinfo:  </td><td><pre>'. `cat /proc/cpuinfo`."\n".`cpufreq-info`. "</pre></td></tr>";
    echo '<tr><td>df -h:  </td><td><pre>'. `df -h`. "</pre></td></tr>";
    echo '<tr><td>free -m:  </td><td><pre>'. `free -m`. "</pre></td></tr>";
    echo '<tr><td>ps x:  </td><td><pre>'. `ps x`. "</pre></td></tr>";
    echo '<tr><td>date:  </td><td><pre>'. `date`. "</pre></td></tr>";
    echo '<tr><td>ls bin/* sbin/*:  </td><td><pre>'. `ls /bin/ /sbin/`. "</pre></td></tr>";

//    echo '<tr><td>Server:  </td><td><pre>'; print_r( $_SERVER ); echo "</pre></td></tr>";
//     echo '<tr><td>Environment:  </td><td><pre>'; print_r( $_ENV ); echo "</pre></td></tr>";
    echo '<tr><td>Include path:  </td><td>- '.str_replace(":","<br /> - ",get_include_path()) . '</td></tr>';

    echo '<tr><td>Safe mode:  </td><td>'. (ini_get("safe_mode") ? "Yes" : "No") . "</td></tr>";

    echo '<tr><td>tar -help:  </td><td><pre>'. `tar 2>&1`. "</pre></td></tr>";
    echo '<tr><td>unzip:  </td><td><pre>'. `unzip 2>&1`. "</pre></td></tr>";
    echo '<tr><td>mysqladmin:  </td><td><pre>'. `mysqladmin 2>&1`. "</pre></td></tr>";

    echo '<tr><td>get_loaded_extensions();  </td><td>';
    $extensions = get_loaded_extensions();
    if( in_array( "Zend Optimizer", $extensions ) ) echo "<span style='color:red'>Zend Optimizer Found!</span><br /><br />\n";
    echo "<pre style='color:black'>";
    print_r( $extensions );
    echo '</pre></td></tr>';

    echo '<tr><td>Max upload filesize:</td><td>';
    echo ini_get( "upload_max_filesize" );
    echo '</td></tr>';
    echo '<tr><td>Max post size:</td><td>';
    echo ini_get( "post_max_size" );
    echo '</td></tr>';

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

    echo '<tr><td>session:  </td><td>'. $_SESSION["sessions"]. "</td></tr>";

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

    echo '<tr><td>mail("tsvetan.filev@gmail.com")</td><td><form method="post"><input type="hidden" name="send" value="1" />email:<input type="text" name="email" value="tsvetan.filev@gmail.com" /><input type="submit" value="Send"></form>';
    if( $_REQUEST["send"] )
        echo mail($_REQUEST["email"],'Subject','Body');
    echo '</td></tr>';

    echo '<tr><td><a href="?phpinfo=1">View phpinfo():  </a></td><td>';
    if( $_GET["phpinfo"] )
        phpinfo();
    echo '</td></tr>';
    echo '</table>
    </body>
    </html>';

?>