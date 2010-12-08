<?

    /**
    * Kernel module template
    * @name module_template
    * @author
    * @date
    *
    */

    class module_template {

        public $kernel;

        public function __construct( $kernel ) {

            $this->kernel = $kernel;
            // Module specific initialization code 
            // Use $MODULES_ARGUMENTS for configurations

        } // end __construct

    } // end class module_template

?>