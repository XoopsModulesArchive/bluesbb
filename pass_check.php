<?php

// Author: Sting_Band
// URL: http://www.bluish.jp/

require dirname(__DIR__, 2) . '/mainfile.php';
require __DIR__ . '/header.php';
require_once BLUESBB_ROOT . '/include/btickets.php';
$post_id = isset($_GET['pos']) ? (int)$_GET['pos'] : 0;
$style = isset($_GET['sty']) ? (int)$_GET['sty'] : 0;
if (empty($post_id) || empty($style)) {
    redirect_header(BLUESBB_URL . '/', 2, _MD_ERRORPOST);

    exit();
}
$sql = 'SELECT thread_id, res_id, pass, uid FROM ' . $xoopsDB->prefix('bluesbb') . ' WHERE post_id = ' . $post_id;
if (!$result = $xoopsDB->query($sql)) {
    redirect_header(BLUESBB_URL . '/', 3, 'Error!! ' . basename(__FILE__) . ' Line ' . __LINE__);

    exit();
}
if (!$passdata = $xoopsDB->fetchArray($result)) {
    redirect_header(BLUESBB_URL . '/', 3, 'Error!! ' . basename(__FILE__) . ' Line ' . __LINE__);

    exit();
}
$sql = 'SELECT COUNT(post_id) FROM ' . $xoopsDB->prefix('bluesbb') . ' WHERE thread_id = ' . $passdata['thread_id'];
if (!$count_result = $xoopsDB->query($sql)) {
    redirect_header(BLUESBB_URL . '/', 3, 'Error!! ' . basename(__FILE__) . ' Line ' . __LINE__);

    exit();
}
[$count] = $xoopsDB->fetchRow($count_result);
require XOOPS_ROOT_PATH . '/header.php';
echo "<div align='center'>" . $post_id . '' . _MD_POSTEDIT . '</div><br>';
echo "<table class='outer' cellspacing='0'><form action='" . BLUESBB_URL . "/edit.php' method='post'><tr><td class='odd'>";
echo "<input type='radio' name='post_edit' value='edit' checked>" . _MD_POSTEDIT2 . '<br>';
if ('0' == $passdata['res_id'] && $count > 1) {
    echo _MD_POSTEDIT3 . '<br>';
} else {
    echo "<input type='radio' name='post_edit' value='delete'>" . _MD_POSTEDIT4 . '<br>';
}
if (is_object($xoopsUser)) {
    if ($xoopsUser->isAdmin($xoopsModule->mid())) {
        if ('0' == $passdata['res_id']) {
            echo "<br><input type='radio' name='post_edit' value='alldelete'>" . _MD_POSTEDIT7 . '<br><br>';
        }

        echo _MD_POSTEDIT6 . "<input type='hidden' name='post_pass' value=''><br>";
    } elseif ($passdata['uid'] == $xoopsUser->getVar('uid')) {
        echo _MD_POSTEDIT5 . "<input type='hidden' name='post_pass' value=''><br>";
    } else {
        echo _MD_PASS . "<input type='password' name='post_pass' value='' size='12' maxlength='12'>";
    }
} else {
    echo _MD_PASS . "<input type='password' name='post_pass' value='' size='12' maxlength='12'>";
}
echo "<input type='hidden' name='post_id' value='" . $post_id . "'><input type='hidden' name='style' value='" . $style . "'>" . $GLOBALS['xoopsBluesTicket']->getTicketHtml(__LINE__) . "<input type='submit' name='from_passcheck' value='" . _MD_GO . "'>";
echo '</td></tr></form></table>';
require XOOPS_ROOT_PATH . '/footer.php';
