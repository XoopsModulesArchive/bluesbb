<?php

// Author: Sting_Band
// URL: http://www.bluish.jp/

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}
require_once XOOPS_ROOT_PATH . '/class/xoopslists.php';
require_once XOOPS_ROOT_PATH . '/include/xoopscodes.php';

echo "<form action='"
     . BLUESBB_URL
     . "/post.php' method='post' name='bbbform' id='bbbform' onsubmit='return xoopsValidate(\"title\", \"message\", \"contents_submit\", \""
     . htmlspecialchars(_PLZCOMPLETE, ENT_QUOTES)
     . '", "'
     . htmlspecialchars(_MESSAGETOOLONG, ENT_QUOTES)
     . '", "'
     . htmlspecialchars(_ALLOWEDCHAR, ENT_QUOTES)
     . '", "'
     . htmlspecialchars(_CURRCHAR, ENT_QUOTES)
     . "\");'><table cellspacing='1' class='outer' width='100%'>";

echo "
<tr>
<td class='head' valign='top' nowrap='nowrap'>" . _MD_POSTNAME . "</td>
<td class='odd'>";
echo "<input type='text' id='name' name='name' size='30' maxlength='50' value='$name'></td></tr>

<tr>
<td class='head' valign='top' nowrap='nowrap'>" . _MD_POSTMAIL . "</td>
<td class='odd'>";
echo "<input type='text' id='mail' name='mail' size='30' maxlength='100' value='$mail'></td></tr>

<tr>
<td class='head' valign='top' nowrap='nowrap'>" . _MD_POSTSITE . "</td>
<td class='odd'>";
echo "<input type='text' id='url' name='url' size='60' maxlength='255' value='$url'></td></tr>

<tr>
<td class='head' valign='top' nowrap='nowrap'>" . _MD_SUBJECTC . "</td>
<td class='odd'>";
echo "<input type='text' id='title' name='title' size='60' maxlength='100' value='$title'></td></tr>

<tr align='left'>
<td class='head' valign='top' nowrap='nowrap'>" . _MD_MESSAGEC . "
</td>
<td class='odd'>";
xoopsCodeTarea('message');

if (!empty($isreply) && isset($hidden) && '' != $hidden) {
    echo "<input type='hidden' name='isreply' value='1'>";

    echo "<input type='hidden' name='hidden' id='hidden' value='$hidden'>
	<input type='button' name='quote' class='formButton' value='" . _MD_QUOTE . "' onclick='xoopsGetElementById(\"message\").value=xoopsGetElementById(\"message\").value + xoopsGetElementById(\"hidden\").value; xoopsGetElementById(\"hidden\").value=\"\";'><br>";
}
xoopsSmilies('message');

echo "</td></tr>
<tr>
<td class='head' valign='top' nowrap='nowrap'>" . _MD_POSTPASS . "</td>
<td class='odd'>";
echo "<input type='password' id='pass' name='pass' size='12' maxlength='12' value='$pass'></td></tr><tr>";
echo "<td class='head' valign='top' nowrap='nowrap'>" . _MD_OPTIONS . '</td>';
echo "<td class='even'>";
echo "<input type='checkbox' name='save_cookie' value='1'>&nbsp;" . _MD_COOKIECHECK . '<br>';
if (!empty($allow_sig) && is_object($xoopsUser) && 2 != $mode) {
    echo "<input type='checkbox' name='attachsig' value='1'";

    if (isset($_POST['contents_preview'])) {
        if ($attachsig) {
            echo ' checked>&nbsp;';
        } else {
            echo '>&nbsp;';
        }
    } else {
        if ($xoopsUser->getVar('attachsig') || !empty($attachsig)) {
            echo ' checked>&nbsp;';
        } else {
            echo '>&nbsp;';
        }
    }

    echo _MD_ATTACHSIG . "<br>\n";
}
if (is_object($xoopsUser) && '1' == $mode) {
    if ($xoopsUser->isAdmin($xoopsModule->mid()) || $xoopsUser->getVar('uid') == $uid) {
        echo "<input type='checkbox' name='thread_stop' value='1'>&nbsp;" . _MD_THREADSTOP . '<br>';
    }
}
echo "</td></tr>
<tr><td class='head'></td><td class='odd'>
<input type='hidden' name='mode' value='" . $mode . "'>
<input type='hidden' name='post_id' value='" . $post_id . "'>
<input type='hidden' name='thread_id' value='" . $thread . "'>
<input type='hidden' name='topic' value='" . $topic . "'>
<input type='hidden' name='res_id' value='" . $res_id . "'>
<input type='hidden' name='style' value='" . $style . "'>
" . $GLOBALS['xoopsBluesTicket']->getTicketHtml(__LINE__) . "
<input type='submit' name='contents_preview' class='formButton' value='" . _PREVIEW . "'>&nbsp;<input type='submit' name='contents_submit' class='formButton' id='contents_submit' value='" . _MD_POST . "'>
<input type='button' onclick='location=\"" . BLUESBB_URL . '/topic.php?top=' . $topic . '&amp;sty=' . $style . "\"' class='formButton' value='" . _MD_CANCELPOST . "'></td></tr></table></form>";
