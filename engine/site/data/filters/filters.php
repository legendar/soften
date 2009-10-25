<?
    
    function filterFormSetAction($content) {
        return preg_replace_callback('/<form(.*?)>/is', 'filterFormSetActionCallback', $content);
    }
    function filterFormSetActionCallback($matches) {
        
        $attrs = Array();
        
        if(preg_match_all('/(\w+)=[\"\'](.*?)[\"\']/', $matches[1], $matches2)) {
            for($i=0; $i<count($matches2[1]); $i++){
                $attrs[$matches2[1][$i]] = $matches2[2][$i];
            }
        }
        
        if(!isset($attrs["action"]) && isset($attrs["name"]) && !empty($attrs["name"])) {
            $attrs["action"] = SITEURI . '/forms/' . $attrs["name"] . '/do/';
            if(!isset($attrs["onsubmit"])) {
                $attrs["onsubmit"] = "return checkForm(this, '{$attrs["name"]}');";
            }
        }
        if(!isset($attrs["method"])) $attrs["method"] = "POST";

        $code = "<form ";
        foreach($attrs as $name => $value) {
            $code = "{$code}{$name}=\"{$value}\" ";
        }
        $code .= " >";

        return $code;
    }
    
    function filterSetURLs($content) {
        $content = preg_replace("/\{SITEURI\}/is",SITEURI,$content);
        $content = preg_replace("/\{SITEURL\}/is",SITEURL,$content);
        return $content;
    }
    
    function filterInsertMessages($content) {
        $errors = cl("sitemap")->templates["errors"];
        if(!empty($errors)) {
            if(preg_match("/<\/body>/is",$content))
                $content = preg_replace("/<\/body>/is",implode("",$errors)."</body>",$content);
            else $content .= implode("",$errors);
        }
        return $content;
    }
    
?>