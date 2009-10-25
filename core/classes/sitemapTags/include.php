<?

class softenSitemapTagInclude extends softenSitemapTagSitemap {
    function start() {
        $src = $this->attributes->getNamedItem('src')->nodeValue;
        
        if(WINDOWS && !isFullPath($src) || UNIX) {
            if(substr($src, 0, 1) == '/') {
                $src = $this->parser->basepath . DIRECTORY_SEPARATOR . SITEMAP_DIR . $src;
            } else {
                $src = dirname($this->parser->file) . DIRECTORY_SEPARATOR . $src;
            }
        }
        
        $files = glb(correctPath($src));

        clDebug("sitemapTagInclude", "start", Array("src"=>$src));
        
    	sort($files);
        foreach ($files as $file) {
            clDebug("sitemapTagInclude", "load file", Array("src"=>$file));
        
            $parser = new softenSitemap();
            $parser->set($file,$this->parser);
            $parser->parse();
            unset($parser);
            if($this->parser->status == SITEMAP_END_PARSE)
                return SITEMAP_END_PARSE;
        }
        return $this->parser->status;
    }
}

?>
