<?

    /**
    *    Automated validation
    *
    *    @name validation
    *    @date 18/02/2008
    *    @author Tsvetan Filev <tsvetan.filev@gmail.com>
    *
    */
    class validation {
        /**
        *   @var array
        *   Params array
        */
        protected $params = null;
        /**
        *   @var object
        *   Xml object
        */
        protected $xml = null;
        /**
        *   @var array
        *   Validation rules
        */
        protected $rules = array();
        /**
        *   @var array
        *   Errors
        */
        protected $errors = array();
        /**
        *   @var String
        *   Last error params
        */
        protected $last_error_params = null;

        /**
        *   Constructor for the Validation
        *   @name __construct
        *   @param string $xml_file xml file with validation rules
        *
        */
        public function __construct(){

        } // end __construct

        /**
        *   @name setOptions
        *   @param array $options array with options 
        *
        *   Set options for validation
        *   params => input parameters to check against to
        *   file => xml file with validation rules to load
        * 
        */
        public function setOptions(array $options){

            if (array_key_exists('params',$options))
                $this->params = $options['params'];

            if (array_key_exists('file',$options))
                $this->load( $options['file'] );

        } // end setOptions

        /**
        *   Loads rules into array from xml file
        *   @name load
        *   @param string $xml_file xml file with validation rules
        *
        */
        public function load( $xml_file = null ){

            if( !file_exists($xml_file) ) return false;

            $this->xml = @simplexml_load_file( $xml_file );
            $this->isConsistent( $this->xml );
            //echo "<pre>"; print_r( $this->rules ); echo "</pre>";

            return true;

        } // end load

        /**
        *   Loads rule into rules array from xml object
        *   @name add
        *   @param array $rule
        *   @param integer $sub number of rule to which this rule is subrule (optional)
        *
        */
        public function add( $rule = array(), $sub = null ){

            if( !is_null($sub) )  $this->rules[$sub]["validator"][] = $rule;
            else  $this->rules[] = $rule;

        } // end add

        /**
        *   Validate rules from array
        *   @name validate
        *   @param string $rules array of rules to validate (default: $this->rules)
        *
        *   @return errors_found boolean
        *
        */
        public function validate( $rules = null ){

            $errors_found = false;

            $rules = $rules ? $rules : $this->rules;
            $skip = 0;

            for( $num = 0; $num < count($rules); $num++ ){

                if( $skip ){ $skip--; continue; }

                $rule = $rules[$num];

                if( method_exists( $this, $rule["type"] ) ){

                    if( $rule["type"] == "isConsistent" ){

                        $result = $this->validate( $rules[$num]["validator"] );

                        if( $result ){ $this->errors[$rule["name"]][] = $rule["type"]; $errors_found = true; }

                    } else {

                        $this->last_error_params = null;
                        $result = $this->$rule["type"]( $rules[$num] );

                        if( !$result ){ $this->errors[$rule["name"]][] = array($rule["type"], $this->last_error_params); $errors_found = true; }

                        if( $rule["flags"] ){

                            if( strstr( $rule["flags"], "L" ) ){ if( defined("VALIDATION_DEBUG" ) ) echo "Stop."; break; }
                            if( strstr( $rule["flags"], "N" ) ){ $num = 0; if( defined("VALIDATION_DEBUG" ) ) echo "Rerun from top."; }
                            if( preg_match( "|S=(\\d+)|", $rule["flags"], $m ) ){ $skip = (int)$m[1]; if( defined("VALIDATION_DEBUG" ) )  echo "Skip $m[1] rules."; }

                        } // end if flags

                    } // end else

                } // end if method exists

            } // end for rules

            return $errors_found;

        } // end validate

        public function errors (){

            return $this->errors;

        } // end errors

        public function field_errors( $field, $errors, $echo = false ){

            $result = "Errors in $field: ";
            foreach( (array)$errors as $error )
                $result .= " - $error[0] $error[1] ";
            if( $echo ) echo $result;
            return $result;

        } // end field_erros

        public function isConsistent( $validator, $sub = null ){

    //             echo "isConsistent:<br />";
    //             print_r( $validator->validator );

            foreach( $validator->validator as $rule ){

                $rule_array = (array)$rule;
                $rule_array = $rule_array['@attributes'];

                $this->add( $rule_array, $sub );

                if( (string)$rule["type"] == "isConsistent" )
                    $this->isConsistent( $rule, count($this->rules)-1 );

            } // end foreach

        } // end isConsistent

    ///////////// Validation methods  ////////////////////////////////////////

        public function isInteger( $rule ){

            if( defined("VALIDATION_DEBUG" ) )
                echo "isInteger {$rule["name"]}: ".($this->params[ $rule["name"] ] == (int)$this->params[ $rule["name"] ] && is_numeric($this->params[ $rule["name"] ]))."<br />";
            return $this->params[ $rule["name"] ] == (int)$this->params[ $rule["name"] ] && is_numeric($this->params[ $rule["name"] ]);

        } // end isInteger

        public function isFloat( $rule ){

            if( defined("VALIDATION_DEBUG" ) )
                echo "isFloat {$rule["name"]}: ".($this->params[ $rule["name"] ] == (float)$this->params[ $rule["name"] ] && is_numeric($this->params[ $rule["name"] ]))."<br />";
            return $this->params[ $rule["name"] ] == (float)$this->params[ $rule["name"] ] && is_numeric($this->params[ $rule["name"] ]);

        } // end isFloat

        public function isNumeric( $rule ){

            if( defined("VALIDATION_DEBUG" ) )
                echo "isNumeric {$rule["name"]}: ".is_numeric( $this->params[ $rule["name"] ] )."<br />";
            return is_numeric( $this->params[ $rule["name"] ] );

        } // end isNumeric

        public function isScalar( $rule ){

            if( defined("VALIDATION_DEBUG" ) )
                echo "isScalar {$rule["name"]}: ".is_scalar( $this->params[ $rule["name"] ] )."<br />";
            return is_scalar( $this->params[ $rule["name"] ] );

        } // end isScalar

        public function isBool( $rule ){

            if( defined("VALIDATION_DEBUG" ) )
                echo "isBool {$rule["name"]}: ". is_bool($this->params[ $rule["name"] ]) . "<br />";
            return is_bool($this->params[ $rule["name"] ]);

        } // end isBool

        public function isBinary( $rule ){

            if( defined("VALIDATION_DEBUG" ) )
                echo "isBinary {$rule["name"]}: ". is_binary($this->params[ $rule["name"] ]) . "<br />";
            return is_binary($this->params[ $rule["name"] ]);

        } // end isBinary

        public function isString( $rule ){

            if( defined("VALIDATION_DEBUG" ) )
                echo "isString {$rule["name"]}: ".is_string( $this->params[ $rule["name"] ] )."<br />";
            return is_string( $this->params[ $rule["name"] ] );

        } // end isString

        public function isUnicode( $rule ){

            if( defined("VALIDATION_DEBUG" ) )
                echo "isUnicode {$rule["name"]}: ".is_unicode( $this->params[ $rule["name"] ] )."<br />";
            return is_unicode( $this->params[ $rule["name"] ] );

        } // end isUnicode

        // http://www.php.net/manual/en/ref.mbstring.php
        public function isEncoding( $rule ){

            $value = $rule["encoding"];

            if( defined("VALIDATION_DEBUG" ) )
                echo "isEncoding {$rule["name"]}: ".mb_detect_encoding( $this->params[ $rule["name"] ], $value )." ($value)<br />";

            $this->last_error_params = $value;

            return mb_detect_encoding( $this->params[ $rule["name"] ], $value ) == $value;

        } // end isEncoding

        public function isRegExp( $rule ){

            $value = $rule["regexp"];
            echo "isRegExp {$rule["name"]}: ".preg_match( "|$value|", $this->params[ $rule["name"] ] )." ($value)<br />";

            return preg_match( "|$value|", $this->params[ $rule["name"] ] );

        } // end isRegExp

        public function isEmpty( $rule ){

            echo "isEmpty {$rule["name"]}: ".empty( $this->params[ $rule["name"] ] )."<br />";

            return !empty( $this->params[ $rule["name"] ] );

        } // end isEmpty

        public function isNull( $rule ){

            echo "isNull {$rule["name"]}: ".is_null( $this->params[ $rule["name"] ] )."<br />";

            return is_null( $this->params[ $rule["name"] ] );

        } // end isNull

        public function isDefined( $rule ){

            if( defined("VALIDATION_DEBUG" ) )
                echo "isDefined {$rule["name"]}: ".defined( $this->params[ $rule["name"] ] )."<br />";
            return defined( $this->params[ $rule["name"] ] );

        } // end isDefined

        public function isTrue( $rule ){

            if( defined("VALIDATION_DEBUG" ) )
                echo "isTrue {$rule["name"]}: ".( ($this->params[ $rule["name"] ] != 0) ? 1 : 0 )."<br />";
            return $this->params[ $rule["name"] ] ? true : false;

        } // end isTrue

        public function isInLength( $rule ){

            //$value = explode(",", $rule["value"] );
            $value[0] = $rule["min"];
            $value[1] = $rule["max"];
            $length = strlen( $this->params[ $rule["name"] ] );

            if( defined("VALIDATION_DEBUG" ) )
                echo "isInLength {$rule["name"]}: $length ($value[0],$value[1])<br />";

            $this->last_error_params = "$value[0],$value[1]";

            if( $value[0] and $length < (int)$value[0] ) return false;
            if( $value[1] and $length > (int)$value[1] ) return false;
            return true;

        } // end isInLength

        public function isInBounds( $rule ){

            //$value = explode(",", $rule["value"] );
            $value[0] = $rule["min"];
            $value[1] = $rule["max"];

            $param_value = $this->params[ $rule["name"] ];

            if( defined("VALIDATION_DEBUG" ) )
                echo "isInBounds {$rule["name"]}: $param_value ($value[0],$value[1])<br />";

            $this->last_error_params = "$value[0],$value[1]";

            if( $value[0] and $param_value < $value[0] ) return false;
            if( $value[1] and $param_value > $value[1] ) return false;
            return true;

        } // end isInBounds

        public function isInRange( $rule ){

            return $this->isInBounds( $rule );

        } // end isInRange

        public function isMoreThan( $rule ){

            $value = $rule["min"];

            $param_value = $this->params[ $rule["name"] ];

            if( defined("VALIDATION_DEBUG" ) )
                echo "isMoreThan {$rule["name"]}: $param_value ($value)<br />";

            $this->last_error_params = $value;

            if( $param_value <= $value ) return false;
            return true;

        } // end isMoreThan

        public function isLessThan( $rule ){

            $value = $rule["max"];

            $param_value = $this->params[ $rule["name"] ];

            if( defined("VALIDATION_DEBUG" ) )
                echo "isLessThan {$rule["name"]}: $param_value ($value)<br />";

            $this->last_error_params = $value;

            if( $param_value >= $value ) return false;
            return true;

        } // end isLessThan

        public function isInArray( $rule ){

            $array = explode(",", $rule["array"] );

            $param_value = $this->params[ $rule["name"] ];

            if( defined("VALIDATION_DEBUG" ) )
                echo "isInArray {$rule["name"]}: $param_value ({$rule["array"]})<br />";

            $this->last_error_params = $rule["array"];

            return in_array( $param_value, $array );

        } // end isInArray

        public function isEmail( $rule ){

            $value = $this->params[ $rule["name"] ];

            if( defined("VALIDATION_DEBUG" ) )
                echo "isEmail {$rule["name"]}: $value<br />";

            return preg_match('/^[a-z0-9.+_-]+@([a-z0-9-]+.)+[a-z]+$/i', $value );

        } // end isEmail

        public function isUrl( $rule ){

            $value = $this->params[ $rule["name"] ];

            if( defined("VALIDATION_DEBUG" ) )
                echo "isUrl {$rule["name"]}: $value<br />";

            return preg_match("|^http(s)?://[a-z0-9-]+(\.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i", $value );

        } // end isUrl

        public function isUri( $rule ){

            $value = $this->params[ $rule["name"] ];

            if( defined("VALIDATION_DEBUG" ) )
                echo "isUrl {$rule["name"]}: $value<br />";

            return preg_match("|^(\w+)://[a-z0-9-]+(\.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i", $value );

        } // end isUrl

        public function isFile( $rule ){

            $value = $this->params[ $rule["name"] ];

            if( defined("VALIDATION_DEBUG" ) )
                echo "isFile {$rule["name"]}: $value<br />";

            return file_exists($value) && is_file($value) ? true : false;

        } // end isFile

        public function isFolder( $rule ){

            $value = $this->params[ $rule["name"] ];

            if( defined("VALIDATION_DEBUG" ) )
                echo "isFolder {$rule["name"]}: $value<br />";

            return file_exists($value) && is_dir($value) ? true : false;

        } // end isFolder

        public function isFileSize( $rule ){

            $value = $this->params[ $rule["name"] ];
            $size = $rule["size"];
            $size_on_disk = filesize($value);

            if( defined("VALIDATION_DEBUG" ) )
                echo "isFileSize {$rule["name"]}: $value - $size_on_disk ($size)<br />";

            $this->last_error_params = $size;

            return file_exists($value) && $size_on_disk <= $size ? true : false;

        } // end isFileSize

        public function isImageSize( $rule ){

            $value = $this->params[ $rule["name"] ];
            $size = explode(",", $rule["size"]);
            $image_size = getimagesize($value);

            if( defined("VALIDATION_DEBUG" ) )
                echo "isImageSize {$rule["name"]}: $value - $image_size[0]x$image_size[1] ($size[0]x$size[1])<br />";

            $this->last_error_params = "$size[0]x$size[1]";

            return file_exists($value) && $image_size[0] <= $size[0] && $image_size[1] <= $size[1] ? true : false;

        } // end isImageSize

        public function isFileFormat( $rule ){

            $value = $this->params[ $rule["name"] ];
            $formats = explode(",", $rule["format"] );

            $extension = substr($value, strrpos($value, ".")+1);

            $this->last_error_params = $rule["format"];

            if( defined("VALIDATION_DEBUG" ) )
                echo "isFileFormat {$rule["name"]}: $extension ({$rule["format"]})<br />";

            return in_array($extension, $formats);

        } // end isFileFormat

        public function isArray( $rule ){

            if( defined("VALIDATION_DEBUG" ) )
                echo "isArray {$rule["name"]}: ".is_array( $this->params[ $rule["name"] ] )."<br />";
            return is_array( $this->params[ $rule["name"] ] );

        } // end isArray

        public function isObject( $rule ){

            if( defined("VALIDATION_DEBUG" ) )
                echo "isObject {$rule["name"]}: ".is_object( $this->params[ $rule["name"] ] )."<br />";
            return is_object( $this->params[ $rule["name"] ] );

        } // end isObject

        public function isCallable( $rule ){

            //$value = $rule["value"] ? array(${$rule["value"]}, $this->params[ $rule["name"]) : $this->params[ $rule["name"] ];
            $value = $this->params[ $rule["name"] ];

            if( defined("VALIDATION_DEBUG" ) )
                echo "isCallable {$rule["name"]}: ".is_callable( $value )." ({$rule["value"]})<br />";
            return is_callable( $value );

        } // end isCallable


        public function isDate( $rule ){

            $value = $rule["value"] ? $rule["value"] : '\d{4}-\d{2}-\d{2}';

            $param_value = $this->params[ $rule["name"] ];

            if( defined("VALIDATION_DEBUG" ) )
                echo "isDate {$rule["name"]}: $param_value ($value)<br />";

            return preg_match( "|^$value$|", $param_value );

        } // end isDate

        public function isDateTime( $rule ){

            $value = $rule["value"] ? $rule["value"] : '\d{4}-\d{2}-\d{2} \d{1,2}:\d{1,2}:\d{1,2}';

            $param_value = $this->params[ $rule["name"] ];

            if( defined("VALIDATION_DEBUG" ) )
                echo "isDateTime {$rule["name"]}: $param_value ($value)<br />";

            return preg_match( "|^$value$|", $param_value );

        } // end isDateTime

        public function isTime( $rule ){

            $value = $rule["value"] ? $rule["value"] : '\d{1,2}:\d{1,2}:\d{1,2}';

            $param_value = $this->params[ $rule["name"] ];

            if( defined("VALIDATION_DEBUG" ) )
                echo "isTime {$rule["name"]}: $param_value ($value)<br />";

            return preg_match( "|^$value$|", $param_value );

        } // end isTime

        public function isPhone( $rule ){

            $value = $rule["value"] ? $rule["value"] : '(\+*)(\d{1,4})([\d\w\*#\- ]*)';

            $param_value = $this->params[ $rule["name"] ];

            if( defined("VALIDATION_DEBUG" ) )
                echo "isPhone {$rule["name"]}: $param_value ($value)<br />";

            return preg_match( "|^$value$|", $param_value );

        } // end isPhone

        // http://ca3.php.net/manual/en/ref.pspell.php#60236
        public function isSpellCorrect( $rule ){

            $value = $rule["value"];

            $param_value = $this->params[ $rule["name"] ];

            if( defined("VALIDATION_DEBUG" ) )
                echo "isSpellCorrect {$rule["name"]}: $param_value ($value)<br />";

            return count( $this->checkSpelling( $param_value ) ) == 0;

        } // end isSpellCorrect

        /**
        * Checks spelling of $string. Whole phrases can be sent in, too, and each word will be checked.
        * Returns an associative array of mispellings and their suggested spellings
        * @param string $string Phrase to be checked
        * @return array
        * http://ca3.php.net/manual/en/ref.pspell.php#60236
        */
        protected function checkSpelling ( $string )
        {
            // Make word list based word boundries
            $wordlist = preg_split('/\s/',$string);

            // Filter words
            $words = array();
            for($i = 0; $i < count($wordlist); $i++)
            {
                $word = trim($wordlist[$i]);
                if(!preg_match('/[A-Za-z]/', $word))
                    continue;
                $word = preg_replace('/[^\w\']*(.+)/', '\1', $word);
                $word = preg_replace('/([^\W]*)[^\w\']*$/', '\1', $word);
                $word = trim($word);
                if(!in_array($word, $words, true))
                    $words[] = $word;

            }
            $misspelled = $return = array();
            $int = pspell_new('en');

            foreach ($words as $value)
                if (!pspell_check($int, $value))
                    $misspelled[] = $value;

            foreach ($misspelled as $value)
                $return[$value] = pspell_suggest($int, $value);

            return $return;
        } // end checkSpelling

    ///////////// End Validation methods  ////////////////////////////////////////

    } // end class Validation2

?>