<?

class softenSitemapTagBlock extends softenSitemapTagSitemap {
    function start() {
        $id = $this->attributes->getNamedItem('id')->nodeValue;
        $name = $this->attributes->getNamedItem('name')->nodeValue;
        
        if($id && !empty($id)) {
            $this->parser->blocks[$id] = & $this->el;
            return SITEMAP_SKIP_TAG;
        } else if($name && !empty($name)){
            $parser = new softenSitemap();
            $parser->set($this->parser->file, $this->parser);
            $el = $this->parser->blocks[$name];
            if($el->childNodes != NULL) {
                for($i=0;$i<$el->childNodes->length;$i++) {
                    $parser->_parse($el->childNodes->item($i));
                    if($parser->status == SITEMAP_END_PARSE) break;
                }
            }
            unset($parser);
            if($this->parser->status == SITEMAP_END_PARSE)
                return SITEMAP_END_PARSE;
        } else return SITEMAP_SKIP_TAG;
        
        return SITEMAP_IN_PROCESS;
    }
}

?>
