<?

//http://coding.derkeiler.com/Archive/PHP/php.general/2008-08/msg00378.html
//http://php.net/manual/en/function.stream-context-create.php

class curl_cookie {
    public $headers = array(
    'Accept' => 'text/xml,application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5'
    ,'Accept-Charset' => 'ISO-8859-1,utf-8;q=0.7,*;q=0.7'
    ,'Accept-Language' => 'en-us,en;q=0.5'
    );
    public $ua = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.14) Gecko/20080404 Firefox/2.0.0.14';
    public $cookieFile = '';
    private $_lastUrl = '';

    public function __construct( $cookieFile = null ){
        $this->cookieFile = $cookieFile;
    }

    private function _headerArray(){
        foreach ($this->headers as $name => $val)
            $parts[] = $name . ': ' . $val;
        return $parts;
    }

    public function get($url){
        $curl = curl_init($url);
        return $this->_exec($curl);
    }

    public function post($url, $vars){
        if (is_array($vars))
        $vars = http_build_query($vars);
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $vars);
        return $this->_exec($curl);
    }

    private function _exec($curl){
        if ($this->ua)
            curl_setopt($curl, CURLOPT_USERAGENT, $this->ua);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $this->_headerArray());
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_AUTOREFERER, true);
        if ($this->_lastUrl)
            curl_setopt($curl, CURLOPT_REFERER, $this->_lastUrl);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_VERBOSE, true);
        curl_setopt($curl, CURLOPT_HEADER, true);
        curl_setopt($curl, CURLOPT_ENCODING, '');
        curl_setopt($curl, CURLOPT_COOKIEJAR, $this->cookieFile);
        curl_setopt($curl, CURLOPT_COOKIEFILE, $this->cookieFile);
        $response = curl_exec($curl);
        $error = curl_error($curl);
        $result = array( 'header' => '',
        'body' => '',
        'curl_error' => '',
        'http_code' => '',
        'last_url' => '');
        if ($error != "") {
            $result['curl_error'] = $error;
            curl_close($curl);
            $this->_lastUrl = '';
            return $result;
        }
        $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        $result['header'] = substr($response, 0, $header_size);
        $result['body'] = substr( $response, $header_size );
        $result['http_code'] = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $result['last_url'] = $this->_lastUrl = curl_getinfo($curl, CURLINFO_EFFECTIVE_URL);
        curl_close($curl);
        return $result;
    }

    // $user_info:
    // array( 'username' => 'user_name', 'password' => 'password' )
    // array( 'email' => 'email', 'password' => 'password' )
    public function getResult( $login_url, $url, $user_info = array() ){
            if( !file_exists( $this->cookieFile ) )
                 $this->post($login_url, $user_info );
            $res = $this->get($url);

            if( strstr( $res["body"] ,"<!-- login -->" ) ){
                 if( file_exists($this->cookieFile) )
                    unlink($this->cookieFile);
                 $this->post($login_url, $user_info );
                 $res = $this->get($url);
            }
//            return $res["body"];
            return unserialize(bzdecompress($res["body"]));
    }

} // end class Curl_Cookie

?>