<?

class softenSitemapTagData extends softenSitemapTagSitemap {
    var $src;
    var $once;
    function start() {
        $this->src = $this->attributes->getNamedItem('src')->nodeValue;
        $this->once = $this->attributes->getNamedItem('once')->nodeValue;
	    $this->id = $this->attributes->getNamedItem('id')->nodeValue;
        if(!$this->once) $this->once = false;
        if($this->once == "true") $this->once = true;
        if($this->once == "false") $this->once = false;
        
        $files = glb(correctPath($this->parser->basepath . '/' . DATA_DIR . '/' . $this->src));
        if(count($files) > 0) {
            clDebug("sitemapTagData", "in process", Array("src"=>$this->src,"once"=>($once==true?'true':'false')));
            foreach ($files as $file) {
                $this->parser->includeFile($file, 'include', $once, $this->attributes, true);
            }
        } else clDebug("sitemapTagData", "skip", Array("src"=>$this->src,"once"=>($once==true?'true':'false')));
        
        return SITEMAP_IN_PROCESS;
    }
}

?>
