<?

    setConf("filters", "tags", "submit", array(
        "tag" => "input",                               // new tag name (old tag name in index of array)
        "attributes" => array(                          // rewrite this attributes
            "value" => "{original}|{content}|" . lng('Submit'),     
            "type" => "submit",
        ),
        "allowAttributes" => "",                        // remove all any attributes
        "denyAttributes" => "",                         // remove this attributes
        "withAttributes" => "",                         // rewrite only with this attributes
        "notAttributes" => "",                          // rewrite if this attributes not present
        "notRewrite" => "remove",                       // if not rewrite then remove
        "longTag" => false,
        "removeContent" => true                         // if short tag then true
    ));
    
    setConf("filters", "tags", "js", array(
        "tag" => "script",
        "attributes" => array(
            "src" => "{original}|/js/",     
            "type" => "text/javascript",
        ),
        "attributesFilters" => array(
            "src{1}" => "^((prototype)|(scriptaculous)|(mootools)|(jquery)|(phpjs))$->/js/$1/",
            "src{2}" => "^(.*)$->" . SITEURI . "$1",
        ),
        "allowAttributes" => "src|type",
        "notRewrite" => "remove",
        "longTag" => true,
        "removeContent" => true
    ));

    setConf("filters", "tags", "css", array(
        "tag" => "link",
        "attributes" => array(
            "href" => "{original}|{attr_src}|/css/",     
            "type" => "text/css",
            "rel" => "stylesheet"
        ),
        "attributesFilters" => array(
            "href" => "^(.*)$->" . SITEURI . "$1",
        ),
        "allowAttributes" => "href|type|rel",
        "notRewrite" => "remove",
        "longTag" => false,
        "removeContent" => true
    ));
    setConf("filters", "tags", "nofloat", array(
        "tag" => "div",                               // new tag name (old tag name in index of array)
        "attributes" => array(                          // rewrite this attributes
            "class" => "nofloat"
        ),
        "allowAttributes" => "class",                        // remove all any attributes
        "denyAttributes" => "",                         // remove this attributes
        "withAttributes" => "",                         // rewrite only with this attributes
        "notAttributes" => "",                          // rewrite if this attributes not present
        "notRewrite" => "remove",                       // if not rewrite then remove
        "longTag" => true,
        "removeContent" => true                         // if short tag then true
    ));
    

    function filterSetTags($content) {
        foreach(array_keys(getConf("filters", "tags")) as $tag) {
            $content = preg_replace_callback("/<({$tag})\s*([^>]*?)(?:(?:\/>)|(?:>(.*?)<\/{$tag}>))/is", "filterSetTagsCallback", $content);
        }
        return $content;
    }
    
    function filterSetTagsCallback($matches) {
        $conf = getConf("filters", "tags", strtolower($matches[1]));

        $result = (($conf["notRewrite"] == "ignore") ? $matches[0] : "");
        
        if(!isset($matches[3])) $matches[3] = false;
        
        $attrs = Array();
        if(preg_match_all('/(\w+)=[\"\'](.*?)[\"\']/', $matches[2], $matches2)) {
            for($i=0; $i<count($matches2[1]); $i++){
                $attrs[$matches2[1][$i]] = $matches2[2][$i];
            }
        }

        if(isset($conf["withAttributes"]) && !empty($conf["withAttributes"])) {
            foreach(explode("|", $conf["withAttributes"]) as $name) {
                if(empty($name)) continue;
                if(!array_key_exists($name, $attrs)) return $result;
            }
        }

        if(isset($conf["notAttributes"]) && !empty($conf["notAttributes"])) {
            foreach(explode("|", $conf["notAttributes"]) as $name) {
                if(empty($name)) continue;
                if(array_key_exists($name, $attrs)) return $result;
            }
        }

        if(isset($conf["attributes"]) && !empty($conf["attributes"])) {
            foreach($conf["attributes"] as $name => $values) {
                foreach(explode("|", $values) as $value) {
                    if($value == "{original}") {
                        if(isset($attrs[$name])) break; 
                        else continue;
                    }
                    if($value == "{content}") {
                        if($matches[3] !== false && !empty($matches[3])) $value = htmlspecialchars($matches[3]);
                        else continue;
                    }
                    if(preg_match("/^{attr_.*?}$/is", $value)) {
                        if(isset($attrs[preg_replace("/^{attr_(.*?)}$/is", "$1", $value)])) $value = $attrs[preg_replace("/^{attr_(.*?)}$/is", "$1", $value)];
                        else continue;
                    }
                    $attrs[$name] = $value;
                    break;
                }
            }
        }
        
        if(isset($conf["allowAttributes"]) && !empty($conf["allowAttributes"])) {
            $conf["allowAttributes"] = explode("|", $conf["allowAttributes"]);
            $newAttrs = array();
            foreach($attrs as $name => $value) {
                if(in_array($name, $conf["allowAttributes"])) $newAttrs[$name] = $value;
            }
            $attrs = $newAttrs;
        }
        
        if(isset($conf["denyAttributes"]) && !empty($conf["denyAttributes"])) {
            foreach(explode("|", $conf["denyAttributes"]) as $name) {
                if(empty($name)) continue;
                if(array_key_exists($name, $attrs)) unset($attrs[$name]);
            }
        }
        
        if(isset($conf["attributesFilters"]) && !empty($conf["attributesFilters"])) {
            foreach($conf["attributesFilters"] as $name => $filter) {
                $name = preg_replace("/\{\d+\}$/is", "", $name);
                if(!array_key_exists($name, $attrs)) continue;
                $filter = explode("->", $filter);
                $attrs[$name] = preg_replace("/{$filter[0]}/is", $filter[1], $attrs[$name]);
            }
        }
        
        $content = "";
        if(isset($matches[3])) $content = $matches[3];
        if(isset($conf["removeContent"]) && $conf["removeContent"] == true) $content = "";
        
        $attrsLine = "";
        foreach($attrs as $name => $value) {
            $attrsLine .= ' ' . $name . '="' . $value . '"';
        }
        
        $result = "<" . $conf["tag"] . $attrsLine . (($conf["longTag"] == true) ? ">{$content}</{$conf["tag"]}>" : " />");
        
        return  $result;
    }
