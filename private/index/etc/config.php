<?

    /**
    * Main configuration file
    * @name config.php
    * @date 2009/10/10
    * @author Tsvetan Filev <tsvetan.filev@gmail.com>
    *
    */

    /**
    * Debug and output settings
    *
    */
    define( 'APPLICATION_ENVIRONMENT',  'development' ); // development, test, production
    //define( 'DEBUG', 1 );
    //define( 'APP_DEBUG', 1 );
    //define( 'PARAMS_DEBUG', 1 );
    //define( 'CACHE_OUTPUT', 1 );
    //define( 'VALIDATION_DEBUG', 1 );
    //error_reporting( 0 );
    error_reporting( E_ALL ^ E_NOTICE );

    /**
    * Private directory
    * @const string directory where private contents is located
    */
    define( 'PRIVATE_DIRECTORY_NAME', 'private' );
    define( 'PRIVATE_DIRECTORY', '.'.DIRECTORY_SEPARATOR.PRIVATE_DIRECTORY_NAME );

    /**
    * Default values
    * @const string default system settings
    */
    // initial call constants
    define( 'DEFAULT_APPLICATION',  'index' );
    define( 'DEFAULT_OBJECT',       'index' );
    define( 'DEFAULT_LANGUAGE',     'en' );
    define( 'DEFAULT_TARGET_DIV',   'contents' );
    define( 'DEFAULT_DATABASE',     'kernel');

    // app constants
    define( 'APP_CODE',             'bin' );
    define( 'APP_API',              'lib' );
    define( 'APP_LIB',              'lib' );
    define( 'APP_TEMPLATE',         'template' );
    define( 'APP_ECHO_ON',          1 );
    define( 'APP_ECHO_OFF',         0 );
    // timezone
    date_default_timezone_set('Europe/Sofia'); 

    /**
    * Set paths
    */
    // include path
    set_include_path( get_include_path() . PATH_SEPARATOR . PRIVATE_DIRECTORY . DIRECTORY_SEPARATOR . DEFAULT_APPLICATION . DIRECTORY_SEPARATOR . APP_LIB );
    // sessions path
    session_save_path(PRIVATE_DIRECTORY.DIRECTORY_SEPARATOR.DEFAULT_APPLICATION.DIRECTORY_SEPARATOR.'var'.DIRECTORY_SEPARATOR.'sessions');

    /**
    * Include and enable kernel modules used in the project
    */
    // Modules settings
    $INITRD = array (

        // List with kernel modules to load (!!! include the module code for speed )
        'MODPROBE' => array(
            'dbms',
            'template',
            'acl',
            'statistics',
            'serialize',
            'cache',
            'meta',
            'soap',
            'xmlrpc',
            'rss',
//         'lookup'
        ),

        // database configuration
        // database access information (mysql://{username}:{password}@{host}/{database})
        'DB_ACCESS' => 'mysql://ceco:qwerty@localhost/'.DEFAULT_DATABASE,
        // default connection encoding
        //'DB_SET_NAMES' => 'UTF8',

        // smarty template constants
        'SMARTY_LANGUAGE_DIR' => PRIVATE_DIRECTORY.DIRECTORY_SEPARATOR.DEFAULT_APPLICATION.DIRECTORY_SEPARATOR.'usr'.DIRECTORY_SEPARATOR.'lang',
        'SMARTY_COMPILE_DIR' => PRIVATE_DIRECTORY.DIRECTORY_SEPARATOR.DEFAULT_APPLICATION.DIRECTORY_SEPARATOR.'var'.DIRECTORY_SEPARATOR.'templates',
        'SMARTY_GLOBAL_LANGUAGE_DIR' => '..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.DEFAULT_APPLICATION.DIRECTORY_SEPARATOR.'usr'.DIRECTORY_SEPARATOR.'lang'.DIRECTORY_SEPARATOR.'lang.txt',

        // data_manager configuration
        'PAGINATION_RANGE' => 10,
        'PAGINATION_RECORDS_PER_PAGE' => 20,

        // cache dir
        'CACHE_DIR' => PRIVATE_DIRECTORY.DIRECTORY_SEPARATOR.DEFAULT_APPLICATION.DIRECTORY_SEPARATOR.'var'.DIRECTORY_SEPARATOR.'cache',

        // Access control
        'PUBLIC_APPLICATIONS' => '',
        'PRIVATE_APPLICATIONS' => '',

    ); // end array $INITRD

    // Load external code
    require('DB.php');                         // Database access
    require('Smarty/libs/Smarty.class.php');   // html templates

    // Load modules code (not mandatory, for speed only)
    require('modules/dbms.class.php');         // Database management
    require('modules/template.class.php');     // Templates
    require('modules/acl.class.php');          // Access control
    require('modules/serialize.class.php');   // Statistics
    require('modules/statistics.class.php');   // Statistics
    require('modules/cache.class.php');        // Disk cache
    require('modules/meta.class.php');         // Meta data
    require('modules/soap.class.php');         // Soap
    require('modules/xmlrpc.class.php');       // Xml rpc
    require('modules/rss.class.php');          // Rss
//    require('modules/lookup.class.php');       // Lookup mechanism
//    require('modules/manage_record.class.php');// Manage records
//    require('modules/data_manager.class.php');// Data manager

    /**
    * table names constants
    * @const string names of tables
    */
    define( 'USERS_TABLE',          'users' );
    define( 'USERGROUP_TABLE',      'groups' );
    define( 'PERMISSIONS_TABLE',    'acl' );
    define( 'LOOKUP_TABLE',         '_lookup' );
    define( 'CUSTOM_TABLE',         'custom' );
    define( 'LOGGING_TABLE',        'logging' );

    /**
    * Module names
    * @const string module names
    */
    define( 'INVOICES',             'invoices');
    define( 'GOODS_RECEIVING',      'goods_receiving');
    define( 'SERVER_BUILDING',      'server_building');
    define( 'SERVICE',              'service');

    /**
    * Information constants
    * @const string strings representing certain actions
    */
    define( 'RECORD_NO_ACCESS',     'no_access');
    define( 'RECORD_EXISTS',        'record_exists');
    define( 'RECORD_INSERTED',      'record_inserted');
    define( 'RECORD_UPDATED',       'record_updated');
    define( 'RECORD_DELETED',       'record_deleted');

    /**
    * User access environment constants
    * @const string types of environments supported
    */
    define( 'ENVIRONMENT_NO_ENV',    'noenv' );     // No environment detected
    define( 'ENVIRONMENT_SHELL',     'shell' );     // Shell access
    define( 'ENVIRONMENT_GTK',         'gtk' );     // GTK access
    define( 'ENVIRONMENT_WWW',         'www' );     // Web interface
    define( 'ENVIRONMENT_SOA',         'soa' );     // SOA Web Services
    define( 'ENVIRONMENT_XML_RPC', 'xml_rpc' );     // SOA Web Services XML-RPC
    define( 'ENVIRONMENT_SOAP',       'soap' );     // SOA Web Services SOAP
    define( 'ENVIRONMENT_RSS',         'rss' );     // RSS Web query
    define( 'ENVIRONMENT_SERIALIZE', 'serialize' ); // Web query with serialized result
    define( 'ENVIRONMENT_WWW_PDA',     'pda' );     // Web query from a PDA
    define( 'ENVIRONMENT_WWW_SMART', 'smart' );     // Web query from a smart phone (symbian)
    define( 'ENVIRONMENT_WWW_WAP',     'wap' );     // Web query from a wap phone
    define( 'ENVIRONMENT_WWW_VOICE', 'voice' );     // Voice access

    /**
    * Set tmp dir
    * @const string tmp dir
    */
    define( 'TMP_DIRECTORY', PRIVATE_DIRECTORY.DIRECTORY_SEPARATOR.DEFAULT_APPLICATION.DIRECTORY_SEPARATOR.'var'.DIRECTORY_SEPARATOR.'tmp');

    /**
    * Logs dir
    * @const string logs dir
    */
    define( 'LOGS_DIRECTORY', PRIVATE_DIRECTORY.DIRECTORY_SEPARATOR.DEFAULT_APPLICATION.DIRECTORY_SEPARATOR.'var'.DIRECTORY_SEPARATOR.'logs');

?>