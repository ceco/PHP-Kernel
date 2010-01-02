<?

    class lookup {

        /**
        * @var object
        */
        private $kernel;

        public function __construct ( $kernel ) {

            $this->kernel = $kernel;

        } // end __construct

        /**
        * Call requested function
        *
        * @name server
        * @param string $object full function name
        *
        */
        public function server ( $object ) {

            header('Content-type: text/xml');

            $result = base64_encode($this->kernel->call_from($this->kernel->app, $this->kernel->object, APP_ECHO_OFF, $environment = ENVIRONMENT_WWW));

?><rss version="2.0">
<channel>
<title>Kernel</title>
<description>Kernel response of remote request</description>
<link>http://localhost/projects/Factory/projects/kernel/</link>
<copyright>Tsvetan Filev &gt;tsvetan.filev@gmail.com&lt;</copyright>
<item>
    <title><?=$this->kernel->app?></title>
    <description><?=$result?></description>
    <link></link>
    <pubDate></pubDate>
</item>
</channel>
</rss>
<?

        } // end server

        /**
        * Call remote requested function
        *
        * @name lookup
        * @param string $object full function name
        *
        */
        public function lookup ( $object ) {
            if( defined("DEBUG") ) echo "<div style='color:blue'> 4. server_lookup </div>";

            // 1. Extract lookup data
            $lookup = $this->kernel->db->getRow("SELECT * FROM ".LOOKUP_TABLE." WHERE app = '{$this->kernel->app}'");
            $uri = parse_url( $lookup["uri"] );
            // 2. Determine host
            $host = $uri["host"] ? $uri["host"] : "localhost";
            // 3. Call local default server
            if( $host == "localhost" )
                 $this->kernel->server_default( $object );
            // Call remote application
            else {
                ini_set('user_agent', "PHP\r\nRSS-Feed: kernel");
                $xml = @simplexml_load_file($lookup["uri"]);
                if( $xml )  echo base64_decode((string)$xml->channel->item->description);
                else echo "N/A";
            } // end else

        } // end lookup

    } // end class lookup

?>