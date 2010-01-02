<?

    class regexp {

        public function escape ( $regexp ){
                // Escape reserved regular expression symbols
                $regexp = str_replace( array('.','$','^','|'), array('\.','\$','\^','\|'), $regexp );
                return $regexp;
        } // end escape

        public function translate ( $regexp ){
                // Translate words to regular expression symbols
                $regexp = str_replace( array("[all]","[spaces]"), array("(.+?)", "( +)"), $regexp);
                return $regexp;
        } // end escape

        public function match_all( $regexp, $text ){
                $regexp = $this->escape( $regexp );
                $regexp = $this->translate( $regexp );
                preg_match_all("|$regexp|isx", $text, $match );
                return $match[1];
        } // end match_all

        public function match( $regexp, $text ){
                $regexp = $this->escape( $regexp );
                $regexp = $this->translate( $regexp );
                preg_match("|$regexp|isx", $text, $match );
                return $match[1];
        } // end match_all

    } // end class regexp

?>