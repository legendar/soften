<?

class softenSitemapTagFilter extends softenSitemapTagSitemap {
    function start() {
        $name = $this->attributes->getNamedItem('name')->nodeValue;
        $name = 'filter'.ucwords($name);
        $this->parser->filters[] = $name;
        return SITEMAP_IN_PROCESS;
    }
}

?>
