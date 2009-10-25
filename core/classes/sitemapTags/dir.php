<?

class softenSitemapTagDir extends softenSitemapTagSitemap {
    function start() {
        $var = $this->attributes->getNamedItem('var')->nodeValue;
        $this->parser->basepath = $var;        
        clDebug("sitemapTagDir", "set dir", $var);
        return SITEMAP_IN_PROCESS;
    }
}

?>
