<?

class softenSitemapTagIf extends softenSitemapTagSitemap {
    function start() {
        $src = $this->attributes->getNamedItem('src')->nodeValue;
        $condition = $this->attributes->getNamedItem('condition')->nodeValue;

        if(empty($src)&&empty($condition)) {
            clDebug("sitemapTagIf", "skip", "no params");
            return SITEMAP_SKIP_TAG;
        } else if(empty($src)) {
            if(eval('return '.$condition.';')) {
                clDebug("sitemapTagIf", "in process", Array("condition"=>$condition));
                return SITEMAP_IN_PROCESS;
            } else {
                clDebug("sitemapTagIf", "skip", Array("condition"=>$condition));
                return SITEMAP_SKIP_TAG;
            }
        }
        
        /*$SITEMAP = Array();
        for($i=0;$i<$attributes->length;$i++) {
            $name = $attributes->item($i)->nodeName;
            $value = $attributes->item($i)->nodeValue;
            if($name == 'src') continue;
            $$name = $SITEMAP[$name] = $value;
        }
        
        $TEMPLATE = &$this->parser->templates[$this->type];
        $DATA = &$this->parser->data;*/
        $files = glb(correctPath($this->parser->basepath . '/'. DATA_DIR .'/' . $src));
        if(count($files)>0) $result = true;
        else $result = false;
        foreach ($files as $file) {
            //$r = include($file);
            $r = $this->parser->includeFile($file, 'include', false, $this->attributes, true);
            if(!$r) $result = false;
        }
        if(!$result) {
            clDebug("sitemapTagIf", "skip", Array("src"=>$src));
            return SITEMAP_SKIP_TAG;
        } else {
            clDebug("sitemapTagIf", "in process", Array("src"=>$src));
            return SITEMAP_IN_PROCESS;
        }
        
        return SITEMAP_IN_PROCESS;
    }
}

?>
