<?

class softenSitemapTagTemplate extends softenSitemapTagSitemap {
    
    var $src;
    var $var;
    var $type;
    
    function start() {
        $this->src = $this->attributes->getNamedItem('src')->nodeValue;
        $this->var = $this->attributes->getNamedItem('var')->nodeValue;
        $this->type = $this->attributes->getNamedItem('type')->nodeValue;
        if(!$this->var || empty($this->var)) $this->var = null;
        if(!$this->type || empty($this->type)) $this->type = 'default';

        /*$SITEMAP = Array();
        for($i=0;$i<$attributes->length;$i++) {
            $name = $attributes->item($i)->nodeName;
            $value = $attributes->item($i)->nodeValue;
            if(in_array($name,Array('src','var'))) continue;
            $$name = $SITEMAP[$name] = $value;
        }*/ 

        foreach($this->parser->vars as $name => $value) {
            $$name = $value;
        }

        $TEMPLATES = &$this->parser->templates[$this->type];
        /*$DATA = &$this->parser->data;*/
        
        $tpls = correctPath($this->parser->basepath . DIRECTORY_SEPARATOR . TEMPLATE_DIR . DIRECTORY_SEPARATOR . $this->src);
        if($this->src == "index.tpl.php" && !file_exists($tpls)) {
            $tpls = correctPath(ENGINEPATH . DIRECTORY_SEPARATOR . TEMPLATE_DIR . DIRECTORY_SEPARATOR . $this->src);
        }
        $files = glb($tpls);
        if(count($files) > 0) {
            clDebug("sitemapTagTemplate", "in process", Array("src"=>$this->src,"var"=>($this->var==null?'null':$this->var)));
            foreach ($files as $file) {
                $file = correctPath($file);
                ob_start();
                //include($file);
                $this->parser->includeFile($file, 'include', false, $this->attributes, $this->type);
                $tpl = ob_get_contents();
                ob_end_clean();

                if($this->var != null) {
                    $s = preg_replace("/[^\[]/is","",$this->var);
                    $e = preg_replace("/[^\]]/is","",$this->var);
                    if(strlen($s) != strlen($e)) return SITEMAP_SKIP_TAG;
                    preg_match_all("/(\[)([^\]]*)(\])/iUs",$this->var,$matches);
                    if(count($matches[0]) != strlen($s)) return SITEMAP_SKIP_TAG;
                    if(count($matches[0]) > 0) {
                        $var2 = '';
                        foreach($matches[2] as $match) {
                            if(!empty($match)) $var2 .= "['{$match}']";
                            else $var2 .= "[]";
                        }
                        $var1 = substr($this->var, 0, strpos($this->var, '['));
                        eval('$TEMPLATES["'.$var1.'"]'.$var2.' = $tpl;');
                    } else {
                        $TEMPLATES[$this->var] = $tpl;
                    }
                } else {
                    //if(!isset($this->parser->template[$this->type])) $this->parser->template[$this->type] = '';
                    $this->parser->template[$this->type] = $tpl;
                }
            }
        } else {
            clDebug("sitemapTagTemplate", "skip", Array("status"=>"no files","src"=>$this->src,"var"=>($this->var==null?'null':$this->var)));
        }
        return SITEMAP_IN_PROCESS;
    }
}

?>
