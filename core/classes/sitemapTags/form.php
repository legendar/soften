<?

class softenSitemapTagForm extends softenSitemapTagSitemap {
    function start() {
        $name = $this->attributes->getNamedItem('name')->nodeValue;
        $action = $this->attributes->getNamedItem('action')->nodeValue;
        
        if($action == 'secure') {
            return $this->secure();
        }

        $src = correctPath("{$name}/{$action}.php");
        
        $file = correctPath($this->parser->basepath . '/'. FORMS_DIR .'/' . $src);
        
        if(file_exists($file)) {
            clDebug("sitemapTagForm", "in process", $file);
            $this->parser->includeFile($file, 'include', false, $this->attributes, true);
        } else {
            clDebug("sitemapTagForm", "skip", Array("src" => $file, "status" => "file not exists"));
        }

        return SITEMAP_IN_PROCESS;
    }

    private function secure() {
        $name = $this->attributes->getNamedItem('name')->nodeValue;

        $file = correctPath($this->parser->basepath . '/'. FORMS_DIR .'/' . $name . '/secure');
        if(file_exists($file)) {
            $secure = file_get_contents($file);
        } else {
            $secure = FORMS_DEFAULT_SECURE;
        }
        $secure = explode('|', $secure);
        if(checkUserLevel($secure[0],  isset($secure[1]) ? $secure[1] : 'up', isset($secure[2]) && $secure[2] == 'true' ? true : false)) {
            return SITEMAP_IN_PROCESS;
        } else {
            return SITEMAP_BREAK_TAG;
        }
    }
}

?>
