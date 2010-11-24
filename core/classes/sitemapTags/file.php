<?

class softenSitemapTagFile extends softenSitemapTagSitemap {
    function start() {
        $src = $this->attributes->getNamedItem('src')->nodeValue;
        $var = $this->attributes->getNamedItem('var')->nodeValue;
        $dir = $this->attributes->getNamedItem('dir')->nodeValue;
        if(!$var || empty($var))$var = null;
        if(!$dir || empty($dir))$dir = null;

        $func = 'file_get_contents';
        switch($dir) {
            case "css": $dir = CSS_DIR . '/'; break;
            case "img": $dir = IMG_DIR . '/'; break;
            case "js": $dir = JS_DIR . '/'; break;
            case "data": $dir = DATA_DIR . '/'; $func = 'include'; break;
            default: $dir = ""; $func = 'include'; break;
        }

        clDebug("sitemapTagFile", "in process", Array("src"=>$src,"var"=>$var,"dir"=>$dir));
        
        $files = glb(correctPath($this->parser->basepath . '/' . $dir . $src));
        
        if(strpos($var, '[') === false) {
            $operand = '.=';
            $$var = '';
        } else {
            $operand = '=';
            $v = substr($var, 0, strpos($var, '['));
            $$v = array();
        }
        
        foreach ($files as $file) {
            $v = ((strpos($var, '$')===0)?'':'$') . str_replace("[]", "['{$file}']", $var);
            eval("{$v} = {$func}('{$file}');");
        }
        
        $var = str_replace("[]","",$var);

        if(!isset($this->parser->vars[$var])) $this->parser->vars[$var] = array();
        if(is_array($$var)) {
            $this->parser->vars[$var] = array_merge($this->parser->vars[$var], $$var);
        } else {
            $this->parser->vars[$var] = $$var;
        }
        
        return SITEMAP_IN_PROCESS;
    }
}

?>
