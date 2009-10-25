<?

class softenSitemapTagMatch extends softenSitemapTagSitemap {
    var $matches;
    function __construct(&$parser, $attributes) {
        $this->parser = &$parser;
        $this->matches = &$parser->matches[count($parser->matches)];
        $this->attributes = $attributes;
    }
    
    function start() {
        $pattern = $this->attributes->getNamedItem('pattern')->nodeValue;
        $mode = $this->attributes->getNamedItem('mode')->nodeValue;
        if(!$mode || empty($mode)) $mode = 'is';
        $pattern = "<^{$pattern}$>{$mode}";
        $debugLine = "pattern: {$pattern}; uri: {$_REQUEST["uri"]}";
        if(preg_match($pattern, $_REQUEST["uri"], $this->matches)){
            clDebug("sitemapTagMatch", "in process", Array("pattern"=>$pattern, "uri"=>$_REQUEST["uri"]));
            return SITEMAP_IN_PROCESS;
        } else {
            clDebug("sitemapTagMatch", "skip", Array("pattern"=>$pattern, "uri"=>$_REQUEST["uri"]));
            return SITEMAP_SKIP_TAG;
        }
        return SITEMAP_IN_PROCESS;
    }
    
    function end() {
        unset($this->matches);
        unset($this->parser->matches[count($this->parser->matches)-1]);
    }
}

?>
