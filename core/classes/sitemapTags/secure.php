<?

class softenSitemapTagSecure extends softenSitemapTagSitemap {
    function start() {
        $level = $this->attributes->getNamedItem('level')->nodeValue;
        $order = $this->attributes->getNamedItem('order')->nodeValue;
        $only = $this->attributes->getNamedItem('only')->nodeValue;

        if(!$order) $order = "up";
        if(!$only) $only = false;
        if($only == "true") $only = true;
	
        if(!checkUserLevel($level, $order, $only)) {
            clDebug("sitemapTagSecure", "skip", Array("level"=>$level, "order"=>$order, "only"=>$only?"true":"false"));
            return SITEMAP_SKIP_TAG;
        } else {
            clDebug("sitemapTagSecure", "in process", Array("level"=>$level, "order"=>$order, "only"=>$only?"true":"false"));
            return SITEMAP_IN_PROCESS;
        }
        return SITEMAP_IN_PROCESS;
    }
}

?>
