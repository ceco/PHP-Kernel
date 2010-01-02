<?

    /**
    * template
    * @name template
    * @author Tsvetan Filev <tsvetan.filev@gmail.com>
    * @date 2009/10/10
    *
    */

    class template {

        public $kernel;

        public function __construct( $kernel ) {

            $this->kernel = $kernel;

            $this->kernel->templ = new Smarty();
            $this->kernel->templ->config_dir = $this->kernel->INITRD["SMARTY_LANGUAGE_DIR"];
            $this->kernel->templ->compile_dir = $this->kernel->INITRD["SMARTY_COMPILE_DIR"];
            $this->kernel->templ->assign("SMARTY_GLOBAL_LANGUAGE_DIR", $this->kernel->INITRD["SMARTY_GLOBAL_LANGUAGE_DIR"] );

        } // end __construct

        public function init($app, $app_prefix)
        {
            $this->kernel->templ->assign( "app", $app );
            // Set include paths
            $this->kernel->templ->template_dir = $app_prefix . DIRECTORY_SEPARATOR . "usr" . DIRECTORY_SEPARATOR . "templates" . DIRECTORY_SEPARATOR . $this->kernel->environment;
            $this->kernel->templ->compile_id = "{$app}_{$this->kernel->environment}_";
            $this->kernel->templ->config_dir = $app_prefix . DIRECTORY_SEPARATOR . "usr" . DIRECTORY_SEPARATOR . "lang";
        } // end init

    } // end class template

?>