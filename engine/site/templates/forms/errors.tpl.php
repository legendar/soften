<?
    function errorProcess(&$allErrors,$errors,$conf,$count) {
        if($conf["mode"] == 'pref') {
            $errorLine = '';
            if($count == 1) {
                //$errorLine = "{$conf["pref_one_previous"]} {$errors[0]} {$conf["pref_one_following"]}.";
                $errorLine = preg_replace('/\\\\1/is', $errors[0], $conf["message"]);
            } else {
                $lastError = $errors[$count-1];
                unset($errors[$count-1]);
                //$errorLine = "{$conf["pref_many_previous"]} ".implode(", ",$errors)." and {$lastError} {$conf["pref_many_following"]}.";
                $errorLine = preg_replace("/\\\\1/is", implode(", ",$errors) . " " . lng('and') . " " . $lastError, $conf["message_many"]);
            }
            $allErrors[] = htmlspecialchars(addslashes($errorLine));
        } else $allErrors = array_merge($allErrors,$errors);
    }
?>

<? if(intval($GLOBALS["_ERRORS"]["COUNT"]["_ERRORS"]) > 0) { ?>
<!-- ERRORS}}} -->
<script type="text/javascript">
    <?
        $allErrors = Array();
        foreach($GLOBALS["_ERRORS"]["MESSAGES"] as $type => $errors) {
            if(in_array($type,$GLOBALS["_ERRORS"]["CONFIG"]["MESSAGE_TYPES"])) continue;
            errorProcess($allErrors,$errors,$GLOBALS["_ERRORS"]["CONFIG"]["TYPES"][$type],$GLOBALS["_ERRORS"]["COUNT"][$type]);
        }
    ?>
    sftError('<?=implode('<br />',$allErrors)?>');
</script>
<!-- {{{ERRORS -->
<? } else if(intval($GLOBALS["_ERRORS"]["COUNT"]["_MESSAGES"]) > 0) { ?>
<!-- MESSAGES}}} -->
<script type="text/javascript">
    <?
        $allMessages = Array();
        foreach($GLOBALS["_ERRORS"]["MESSAGES"] as $type => $messages) {
            if(!in_array($type,$GLOBALS["_ERRORS"]["CONFIG"]["MESSAGE_TYPES"])) continue;
            errorProcess($allMessages,$messages,$GLOBALS["_ERRORS"]["CONFIG"]["TYPES"][$type],$GLOBALS["_ERRORS"]["COUNT"][$type]);
        }
    ?>
    sftMessage('<?=implode('<br />',$allMessages)?>');
</script>
<!-- {{{MESSAGES -->
<? } ?>