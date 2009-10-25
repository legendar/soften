<?php

def('CONFIG_DIR', 'config');
def('SITEMAP_DIR', CONFIG_DIR . '/sitemap');

def('CACHE_DIR', 'cache');
def('CACHE_DIR_FULL', correctPath(SITEPATH . '/' . CACHE_DIR));
def('SESSION_DIR', correctPath('cache/sessions'));
def('COOKIE_DIR', correctPath('cache/cookie'));

def('LOG_DIR', 'log');

def('CSS_DIR', 'res/css');
def('IMG_DIR', 'res/img');
def('JS_DIR', 'res/js');

def('TEMPLATE_DIR', 'site/templates');
def('DATA_DIR', 'site/data');
def('FORMS_DIR', 'site/forms');

def('SQL_DIR', 'sql');

def('ENGINEPATH', correctPath(BASEPATH . '/engine'));

def('SITEMAP_FILE', correctPath(ENGINEPATH . '/' . SITEMAP_DIR . '/sitemap.xml'));

def('AJAX_KEY', 'ajax');

def('FORMS_DEFAULT_SECURE', 'user|up|false');

?>
