<?

    /**
    * @name rss.class.php
    * @date 2007/05/25
    * @author Tsvetan Filev <tsvetan.filev@gmail.com>
    *
    */
    class rss {

        /**
        * @var object
        */
//         private $kernel;

        public function __construct ( $kernel ) {

//             $this->kernel = $kernel;

        } // end __construct

        /**
        * Generate rss
        *
        * @name rss
        * @param array $rss
        *
        */
        public function rss ( $rss ) {

            header('Content-type: text/xml');

?><rss version="2.0">
<channel>
<title><?=$rss["title"]?></title>
<description><?=$rss["description"]?></description>
<link><?=$rss["link"]?></link>
<copyright><?=$rss["copyright"]?></copyright>
<?
foreach( $rss["items"] as $item ){
?>
<item>
    <title><?=$item["title"]?></title>
    <description><?=$item["description"]?></description>
    <link><?=$item["link"]?></link>
    <pubDate><?=$item["pubDate"]?></pubDate>
</item>
<?
} // end foreach
?>
</channel>
</rss>
<?

        } // end rss

        /**
        * Generate rss
        *
        * @name rss
        * @param string $url
        *
        * @return $rss array
        *
        */
        public function client ( $url ) {

            $xml = simplexml_load_file($url);

            $rss["title"] = (string)$xml->channel->item->title;
            $rss["description"] = (string)$xml->channel->item->description;
            $rss["link"] = (string)$xml->channel->item->link;
            $rss["copyright"] = (string)$xml->channel->item->copyright;

            foreach( $xml->channel->item as $item ){
                $item = array( "title" => (string)$item->title,"link" => (string)$item->link,"description" => (string)$item->description,"pubDate" => (string)$item->pubDate  );
                $rss["items"][] = $item;
            }

            return $rss;

        } // end rss

    } // end class rss

?>