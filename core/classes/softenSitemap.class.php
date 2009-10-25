<?php

/*************************************************\
|*      soften php engine sitemap parser         *|
|*************************************************|
|* Copyright (C) 2008 legendar (legendar.org.ua) *|
\*************************************************/


define("SITEMAP_IN_PROCESS", 100);
define("SITEMAP_SKIP_TAG", 103);
define("SITEMAP_BREAK_TAG", 107);
define("SITEMAP_END_PARSE", 109);

class softenSitemap {

    private $dom;
    private $firstParser;
    public $file;
    public $xmlTitle;
    public $tags;
    public $status;
    public $basepath;
    public $tpl;           // type of template (default/ajax/...)
    public $template;
    public $templates;
    public $headers;
    public $vars;
    public $data;
    public $matches;
    public $filters;
    public $blocks;
    
    function __construct() {

        $this->dom = new domDocument('1.0',SITE_ENCODING);
        $this->dom->preserveWhiteSpace = false;
        $this->dom->validateOnParse = true;
        $this->dom->resolveExternals = true;
        $this->dom->substituteEntities = false;
        $this->dom->formatOutput = true;
        $this->dom->encoding = SITE_ENCODING;

    }
    
    public function set($file, &$defaults = null) {
        
        $this->file = correctPath($file);
        
        clDebug("sitemap","set file",$file);
        
        $this->setDefaults($defaults);
        $file = correctPath($file);

        $xmlData = file_get_contents($file);
        $xmlData = preg_replace("/^<\?xml [^>]*\?>/iUs","",$xmlData);
        $xmlData = $this->xmlTitle . $xmlData;

        $this->dom->loadXML($xmlData);
    }
    
    public function setDefaults(&$parser = null) {
        if($parser == null) {
            $this->firstParser = true;
            $this->xmlTitle = '<?xml version="1.0" encoding="'.SITE_ENCODING.'"?>';
            $this->tags = $this->loadAllowedTags();
            $this->status = SITEMAP_IN_PROCESS;
            $this->basepath = BASEPATH;
            $this->tpl = 'default';
            $this->template = Array('default' => '', 'ajax' => '');
            $this->templates = Array('default' => array(), 'ajax' => array()); 
            $this->headers = Array('default' => array(), 'ajax' => array()); 
            $this->vars = Array(); 
            $this->data = Array(); 
            $this->matches = Array(); 
            $this->filters = Array();
            $this->blocks = Array();
        } else {
            $this->firstParser = false;
            $this->xmlTitle = &$parser->xmlTitle;
            $this->tags = &$parser->tags;
            $this->status = &$parser->status;
            $this->basepath = &$parser->basepath;
            $this->tpl = &$parser->tpl;
            $this->template = &$parser->template;
            $this->templates = &$parser->templates;
            $this->headers = &$parser->headers;
            $this->vars = &$parser->vars;
            $this->data = &$parser->data;
            $this->matches = &$parser->matches;
            $this->filters = &$parser->filters;
            $this->blocks = &$parser->blocks;
        }
    }
    
    private function loadAllowedTags() {
        $tags = array();
        $coreFile = glb(correctPath(dirname(__FILE__) . "/sitemapTags/sitemap.php"));
        if(empty($coreFile)) return false;
        $files = glb(correctPath(dirname(__FILE__) . "/sitemapTags/*.php"));
        unset($files[array_search($coreFile[0], $files)]);
        $files = array_merge($coreFile, $files);
        foreach ($files as $fname) {
            require_once($fname);
            $tags[] = strtolower(basename($fname, ".php"));
        }        
        return $tags;
    }
    
    public function includeFile($file = '', $includeType = 'require', $once = true, $attributes = null, $templateType = 'default') {
        if(func_num_args() < 4) return $this->includeFile($file, $includeType, $once, $attributes, $templateType);
        if(empty($file) || !file_exists($file)) return null;
        if(!in_array($includeType, array('require', 'include'))) return null;
        
        if($templateType === true) {
            $TEMPLATES = &$this->templates;
            $HEADERS = &$this->headers;
            $TEMPLATE = &$this->template;
        } else {
            if(!isset($this->templates[$templateType])) $this->templates[$templateType] = array();
            if(!isset($this->headers[$templateType])) $this->headers[$templateType] = array();
            if(!isset($this->template[$templateType])) $this->template[$templateType] = '';
        
            $TEMPLATES = &$this->templates[$templateType];
            $HEADERS = &$this->headers[$templateType];
            $TEMPLATE = &$this->template[$templateType];
        }
        
        $DATA = &$this->data;
        $SITEMAP = Array();
        $VARS = & $this->vars;
        
        if($attributes != null) {
            for($i=0; $i < $attributes->length; $i++) {
                $name = $attributes->item($i)->nodeName;
                $value = $attributes->item($i)->nodeValue;
                $SITEMAP[$name] = $value;
            }
        }
        
        extract($SITEMAP);
        extract($this->vars);
        
        if(func_get_arg(1) == 'require') {
            if((!!func_get_arg(2)) == true) return require_once(correctPath(func_get_arg(0)));
            else return require(correctPath(func_get_arg(0)));
        } else {
            if((!!func_get_arg(2)) == true) return include_once(correctPath(func_get_arg(0)));
            else return include(correctPath(func_get_arg(0)));
        }
    }
    
    public function parse() {
        $this->_parse($this->dom->documentElement);
        if($this->firstParser) $this->filterContent();
    }

    public function _parse($el) {
        switch($el->nodeType) {
            case XML_ELEMENT_NODE:                  //<tag>...</tag>
                $tag = strtolower($el->nodeName);
                $tagClass = null;
                if(in_array($tag,$this->tags)) {
                    $tag = 'softenSitemapTag'.ucwords($tag);
                    $tagClass = new $tag($this, $this->parseAttributes($el->attributes,$el->nodeName), $el);
                    $status = $tagClass->start();
                    switch($status) {
                        case SITEMAP_SKIP_TAG: 
                            break;
                        case SITEMAP_BREAK_TAG: 
                        case SITEMAP_END_PARSE: 
                            $this->status = $status;
                            break;
                        case SITEMAP_IN_PROCESS:
                        default:
                            if($el->childNodes != NULL) {
                                for($i=0; $i<$el->childNodes->length; $i++) {
                                    $this->_parse($el->childNodes->item($i));
                                    if($this->status >= SITEMAP_BREAK_TAG) {
                                        if($this->status == SITEMAP_BREAK_TAG) {
                                            $this->status = SITEMAP_IN_PROCESS;
                                        }
                                        break;
                                    }
                                }
                                $status = $tagClass->end();
                                switch($status) {
                                    case SITEMAP_SKIP_TAG:
                                    case SITEMAP_BREAK_TAG:
                                    case SITEMAP_END_PARSE:
                                    case SITEMAP_IN_PROCESS:
                                        $this->status = $status;
                                }
                            }
                            break;
                    }
                }
                break;
            case XML_ATTRIBUTE_NODE: break;         //attr="value"
            case XML_TEXT_NODE: break;              //text
            case XML_CDATA_SECTION_NODE: break;     //<!CDATA[...]>
            case XML_ENTITY_REF_NODE: break;        //&component;
            case XML_ENTITY_NODE: break;            //<!ENTITY...>
            case XML_PI_NODE: break;                //<\?...?\>
            case XML_COMMENT_NODE: break;           //<!--...-->
            case XML_DOCUMENT_NODE: break;          //<\?xml...?\>
            case XML_DOCUMENT_TYPE_NODE: break;     //<!DOCTYPE name [...]>
            case XML_DOCUMENT_FRAGMENT_NODE: break; //<tag>...</tag><tag>...</tag>
            default: break;
        }
    }
    
    function parseAttributes($attributes,$nodeName) {
        for($i=0;$i<$attributes->length;$i++) {
            $name = $attributes->item($i)->nodeName;
            $value = $attributes->item($i)->nodeValue;
            if($nodeName=='match'&&$name=='pattern') {
                $value = preg_replace_callback("/(\{)([A-Z_]+)(\})/is",Array(&$this,'parseAttributesReplaceVarsCallback'),$value);
            } else {
                $value = preg_replace_callback("/(\{+)([0-9]+)(\}+)/is",Array(&$this,'parseAttributesReplaceMatchesCallback'),$value);
                $value = preg_replace_callback("/(\{)([^\}]+)(\})/is",Array(&$this,'parseAttributesReplaceVarsCallback'),$value);
            }
            $attributes->item($i)->nodeValue = $value;
        }
        return $attributes;
    }
    
    function parseAttributesReplaceMatchesCallback($matches) {
        $offset = min(strlen($matches[1]),strlen($matches[3]));
        $r = $this->matches[count($this->matches)-$offset][$matches[2]];
        $r = str_repeat('{',strlen($matches[1])-$offset) . $r . str_repeat('}',strlen($matches[3])-$offset);
        return $r;
    }
    
    function parseAttributesReplaceVarsCallback($matches) {
        $r = $matches[0];
        if(defined($matches[2])) $r = constant($matches[2]);
        else if(eval('return isset($' . $matches[2] . ');')) {
            $r = eval('return $' . $matches[2] . ';');
        }
        return $r;
    }
    
    function filterContent() {
        foreach($this->filters as $filter) {
            foreach($this->template as & $tpl) {
                $tpl = call_user_func($filter, $tpl);
            }
            //$this->template = call_user_func($filter,$this->template);
        }
    }
    
    public function getContent() {
        clDebug("sitemap", "get content", array("type" => $this->tpl));
        return $this->template[$this->tpl];
    }
    
    public function sendHeaders() {
        foreach($this->headers[$this->tpl] as $k => $h) {
            header($k . ': ' . $h);
        }
    }
    
}

?>
