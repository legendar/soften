<?

class softenSitemapTagEnd extends softenSitemapTagSitemap {
    function start() {
        $tpl = $this->attributes->getNamedItem('tpl')->nodeValue;
        if(!$tpl || empty($tpl)) $tpl = AJAX_REQ ? 'ajax' : 'default';
        $this->parser->tpl = $tpl;
        return SITEMAP_IN_PROCESS;
    }
    function end() {return SITEMAP_END_PARSE;}
}

?>
