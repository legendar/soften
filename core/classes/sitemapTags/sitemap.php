<?

class softenSitemapTagSitemap {
    
    protected $parser;
    protected $attributes;
    
    public function __construct(& $parser, $attributes, & $el) {
        $this->parser = & $parser;
        $this->attributes = $attributes;
        $this->el = & $el;
    }
    
    public function start() {
        return SITEMAP_IN_PROCESS;
    }

    public function end() {
        return $this->status;
    }
}

?>
