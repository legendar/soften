<?

class softenSitemapTagRedirect extends softenSitemapTagSitemap {
    function start() {
        $src = $this->attributes->getNamedItem('src')->nodeValue;
        if(!$src) $src = "/";
        redirect((AJAX_REQ ? ('/' . AJAX_KEY) : '') . $src);
        return SITEMAP_IN_PROCESS;
    }
}

?>
