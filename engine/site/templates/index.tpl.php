<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=<?=SITE_ENCODING?>" />
    <title><?=SITE_NAME?></title>
    <link rel="shortcut icon" href="{SITEURI}/favicon.ico" type="image/x-icon" />
    <link rel="icon" href="{SITEURI}/favicon.ico" type="image/x-icon" />
    <js src="jquery"/>
    <script type="text/javascript">
        $j = jQuery.noConflict();
        var currentPage = '<?=$DATA["pagename"]?>';
        var currentPageURL = '<?=$_REQUEST["uri"]?>';
    </script>
    <js src="prototype"/>
    <js src="scriptaculous"/>
    <js src="phpjs"/>
    <js/>
    <css/>
    <?=$TEMPLATES["head"]?>
</head>
<body>
    <?=$TEMPLATES["content"]?>
</body>
</html>