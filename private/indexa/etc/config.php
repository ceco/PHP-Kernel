<?

    /**
    * @name config.php
    * @date 2007/04/25
    * @author Tsvetan Filev <tsvetan.filev@gmail.com>
    *
    */

    /**
    * Debug and output settings
    *
    */
    //define( "DEBUG", 1 );
    //define( "APP_DEBUG", 1 );
    //define( "PARAMS_DEBUG", 1 );
    //define( "CACHE_OUTPUT", 1 );
    error_reporting( E_ALL ^ E_NOTICE );

    /**
    * private directory
    * @const string directory where private contents is located
    */
    define( "PRIVATE_DIRECTORY", ".".DIRECTORY_SEPARATOR."private" );

    /**
    * Default application
    * @const string default applicaiton name
    */
    define( "DEFAULT_APPLICATION",    "indexa" );

    /**
    * Sets include path
    */
    set_include_path( get_include_path() . PATH_SEPARATOR . PRIVATE_DIRECTORY.DIRECTORY_SEPARATOR.DEFAULT_APPLICATION.DIRECTORY_SEPARATOR."lib" );
    // echo get_include_path();

    /**
    * Sets sessions path
    */
    session_save_path(PRIVATE_DIRECTORY.DIRECTORY_SEPARATOR.DEFAULT_APPLICATION.DIRECTORY_SEPARATOR."var".DIRECTORY_SEPARATOR."sessions");

    /**
    * database access (mysql://{username}:{password}@{host}/{database})
    * @const string contains information for database access
    */
    define( "DB_ACCESS", "mysql://ceco:qwerty@localhost/info" );

    /**
    * environment constants
    * @const string types of environments supported
    */
    define( "ENVIRONMENT_NO_ENV",    "noenv" );     // No environment detected
    define( "ENVIRONMENT_SHELL",     "shell" );     // Shell access
    define( "ENVIRONMENT_GTK",         "gtk" );     // GTK access
    define( "ENVIRONMENT_WWW",         "www" );     // Web interface
    define( "ENVIRONMENT_SOA",         "soa" );     // SOA Web Services
    define( "ENVIRONMENT_XML_RPC", "xml_rpc" );     // SOA Web Services XML-RPC
    define( "ENVIRONMENT_SOAP",       "soap" );     // SOA Web Services SOAP
    define( "ENVIRONMENT_RSS",         "rss" );     // RSS Web query
    define( "ENVIRONMENT_WWW_PDA",     "pda" );     // Web query from a PDA
    define( "ENVIRONMENT_WWW_SMART", "smart" );     // Web query from a smart phone (symbian)
    define( "ENVIRONMENT_WWW_WAP",     "wap" );     // Web query from a wap phone
    define( "ENVIRONMENT_WWW_VOICE", "voice" );     // Voice access

    /**
    * app constants
    * @const string app obejcts
    */
    define( "APP_CODE",           "code" );
    define( "APP_API",            "api" );
    define( "APP_TEMPLATE",       "template" );

    define( "APP_ECHO_ON",                1 );
    define( "APP_ECHO_OFF",               0 );

    /**
    * smarty constants
    * @const string language directory
    */
    define( "SMARTY_LANGUAGE_DIR",    PRIVATE_DIRECTORY.DIRECTORY_SEPARATOR.DEFAULT_APPLICATION.DIRECTORY_SEPARATOR."usr".DIRECTORY_SEPARATOR."lang" );
    define( "SMARTY_COMPILE_DIR",     PRIVATE_DIRECTORY.DIRECTORY_SEPARATOR.DEFAULT_APPLICATION.DIRECTORY_SEPARATOR."var".DIRECTORY_SEPARATOR."templates" );
    define( "SMARTY_GLOBAL_LANGUAGE_DIR", "..".DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR.DEFAULT_APPLICATION.DIRECTORY_SEPARATOR."usr".DIRECTORY_SEPARATOR."lang".DIRECTORY_SEPARATOR."lang.txt");

    /**
    * default language
    * @const string language
    */
    define( "DEFAULT_LANGUAGE",       "en" );

    /**
    * Soap urn
    * @const string soap urn
    */
    define( "SOAP_URN",             "application" );

    /**
    * databases names constants
    * @const string names of databases used in sms
    */
    define( "DBN_APPLICATION",   "application");

    /**
    * table names constants
    * @const string names of tables
    */
    //define('USERS_TABLE','users');
    define('CUSTOM_TABLE',          'custom');
    define('USERGROUP_TABLE',       'usergroup2');
    define('PERMISSIONS_TABLE',     'userpermissions2');

    /**
    * Default target div
    * @const string div name
    */
    define( "DEFAULT_TARGET_DIV",           "contents" );

    /**
    * Module names
    * @const string module names
    */
    define( "INVOICES",            "invoices");
    define( "GOODS_RECEIVING",     "goods_receiving");
    define( "SERVER_BUILDING",     "server_building");
    define( "SERVICE",             "service");

    /**
    * Submit constants
    * @const string names of form actions 
    */
    define("SUBMIT_VIEW","View");
    define("SUBMIT_INSERT","Insert");
    define("SUBMIT_UPDATE","Update");
    define("SUBMIT_DELETE","Delete");
    define("SUBMIT_UPLOAD","Upload");
    define("SUBMIT_SEARCH","Search");

    /**
    * Information constants
    * @const string strings representing certain actions
    */
    define("RECORD_EXISTS",   "Record exists!");
    define("RECORD_INSERTED", "Record inserted!");
    define("RECORD_UPDATED",  "Record updated!");
    define("RECORD_DELETED",  "Record deleted!");

    /**
    * Modules constants
    * @const int number of pages
    */
    define("PAGINATION_RANGE",10);
    define("PAGINATION_RECORDS_PER_PAGE",20);

?>