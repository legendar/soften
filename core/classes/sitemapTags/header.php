<?

class softenSitemapTagHeader extends softenSitemapTagSitemap {
    
    var $name;
    var $value;
    var $tpl;
    
    function start() {
        $this->name = $this->attributes->getNamedItem('name')->nodeValue;
        $this->value = $this->attributes->getNamedItem('value')->nodeValue;
        $this->tpl = $this->attributes->getNamedItem('tpl')->nodeValue;
        if(!$this->name || empty($this->name)) $this->name = null;
        if(!$this->value || empty($this->value)) $this->value = null;
        if(!$this->tpl || empty($this->tpl)) $this->tpl = 'default';
        if($this->name == null || $this->value == null) return SITEMAP_SKIP_TAG;
        
        if($this->tpl == 'all') {
            foreach($this->parser->headers as & $header) {
                $header[$this->name] = $this->value;
            }
        } else $this->parser->headers[$this->tpl][$this->name] = $this->value;
        
        return SITEMAP_IN_PROCESS;
    }
}

?>
