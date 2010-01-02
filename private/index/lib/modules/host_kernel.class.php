<?

    class host_kernel {

        protected $url;
        protected $url_dest;

        public function __construct( $url = null ){

            $this->url = $url;
            $this->url_dest = "http://localhost/";

        } // end __construct

        public function dest ( $url ){

            $this->url_dest = $url;

        } // dest

        public function __call( $method, $vars ){

            $vars_url = urlencode( serialize( $vars ) );

            print_r( file_get_contents("{$this->url}?app=controler&object=kernel_call&method=$method&vars=$vars_url&content_only=1") );
            // call remote method
//            $xml = @simplexml_load_file("{$this->url}?app=controler&method=$method&$vars");
//            print_r( $xml );

        } // end __call

        public function __get($nm){
            print_r( file_get_contents("{$this->url}?app=controler&object=get&var=$nm&content_only=1") );

//             $xml = @simplexml_load_file("{$this->url}?app=controler&object=get&var=$nm");
//             print_r( $xml );
        } // end __get

        public function __set($nm, $val){
            $xml = @simplexml_load_file("{$this->url}?app=controler&object=set&var=$nm&value=$val");
            print_r( $xml );
        } // end __set

        public function __isset($nm){
            $xml = @simplexml_load_file("{$this->url}?app=controler&object=get&var=$nm");
            print_r( $xml );
            return true;
            //return isset($this->x[$nm]);
        } // end __isset

        public function __unset($nm){
            $xml = @simplexml_load_file("{$this->url}?app=controler&object=unset&var=$nm");
            print_r( $xml );
        } // end __unset

    } // end class host_kernel

?>