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
$sql = 'SELECT b.*, t.topic_access, t.topic_group, t.allow_sig, t.res_limit FROM ' . $xoopsDB->prefix('bluesbb') . ' b LEFT JOIN ' . $xoopsDB->prefix('bluesbb_topic') . ' t ON t.topic_id = b.topic_id WHERE b.post_id = ' . $post_id;
if (!$result = $xoopsDB->query($sql)) {
    redirect_header(BLUESBB_URL . '/', 3, 'Error!! ' . basename(__FILE__) . ' Line ' . __LINE__);

    exit();
}
if (!$topicdata = $xoopsDB->fetchArray($result)) {
    redirect_header(BLUESBB_URL . '/', 2, _MD_ERROREXIST);

    exit();
}
$topic = '';
$topic = $topicdata['topic_id'];
$thread = '';
$thread = $topicdata['thread_id'];
$res_id = '';
$res_id = $topicdata['res_id'] + 1;
$accesserror = 1;
if ('1' == $topicdata['topic_access']) {
    $accesserror = 0;
} elseif ('2' == $topicdata['topic_access'] || '3' == $topicdata['topic_access']) {
    if (is_object($xoopsUser)) {
        $accesserror = 0;
    }
} elseif ('4' == $topicdata['topic_access'] || '5' == $topicdata['topic_access']) {
    if (is_object($xoopsUser) && $xoopsUser->isAdmin($xoopsModule->mid())) {
        $accesserror = 0;
    }
} elseif ('6' == $topicdata['topic_access']) {
    if (is_object($xoopsUser)) {
        $groups = $memberHandler->getGroupsByUser($xoopsUser->getVar('uid'), true);

        foreach ($groups as $group) {
            if (preg_match("/\b" . $topicdata['topic_group'] . "\b/", $group->getVar('groupid'))) {
                $accesserror = 0;
            }
        }

        if ($xoopsUser->isAdmin($xoopsModule->mid())) {
            $accesserror = 0;
        }
    }
}
if (1 == $accesserror) {
    redirect_header(BLUESBB_URL . '/', 2, _MD_TOPICACCESSERROR2);

    exit();
}
$sql = 'SELECT MAX(res_id) FROM ' . $xoopsDB->prefix('bluesbb') . ' WHERE thread_id =' . $thread;
if (!$r = $xoopsDB->query($sql)) {
    redirect_header(BLUESBB_URL . '/', 1);

    exit();
}
[$max_res] = $xoopsDB->fetchRow($r);
if ($max_res >= $topicdata['res_limit']) {
    redirect_header(BLUESBB_URL . '/topic.php?top=' . $topic, 3, _MD_RESERROR);

    exit();
}
$sql = 'SELECT uid FROM ' . $xoopsDB->prefix('bluesbb') . ' WHERE thread_id = ' . $thread . ' AND res_id = 0';
if (!$result = $xoopsDB->query($sql)) {
    redirect_header(BLUESBB_URL . '/', 3, 'Error!! ' . basename(__FILE__) . ' Line ' . __LINE__);

    exit();
}
if (!$uiddata = $xoopsDB->fetchArray($result)) {
    redirect_header(BLUESBB_URL . '/', 2, _MD_ERROREXIST);

    exit();
}
require XOOPS_ROOT_PATH . '/header.php';
$myts = MyTextSanitizer::getInstance();
$r_message = gt_link($myts->displayTarea($topicdata['message'], 0, 1, 1, 1, 1), $topic, $thread, BLUESBB_URL, $style);
$r_date = formatTimestamp($topicdata['post_time']);
$r_name = htmlspecialchars($topicdata['name'], ENT_QUOTES | ENT_HTML5);
$r_content = _MD_BY . ' ' . $r_name . ' ' . _MD_ON . ' ' . $r_date . '<br><br>';
$r_content .= $r_message;
$r_subject = htmlspecialchars($topicdata['title'], ENT_QUOTES | ENT_HTML5);
if (!preg_match('/^Re:/i', $r_subject)) {
    $title = 'Re: ' . htmlspecialchars($r_subject, ENT_QUOTES | ENT_HTML5);
} else {
    $title = htmlspecialchars($r_subject, ENT_QUOTES | ENT_HTML5);
}
$q_message = htmlspecialchars($topicdata['message'], ENT_QUOTES | ENT_HTML5);
$hidden = "[quote]\n";
$hidden .= sprintf(_MD_USERWROTE, $r_name);
$hidden .= "\n" . $q_message . '[/quote]';
themecenterposts($r_subject, $r_content);
echo '<br>';
$mode = '1';
$message = '';
if (isset($_COOKIE['xoops_bluesbb'])) {
    $coo = explode(',', $_COOKIE['xoops_bluesbb']);

    $name = htmlspecialchars($coo[0], ENT_QUOTES | ENT_HTML5);

    $mail = htmlspecialchars($coo[1], ENT_QUOTES | ENT_HTML5);

    $url = htmlspecialchars($coo[2], ENT_QUOTES | ENT_HTML5);

    $pass = htmlspecialchars($coo[3], ENT_QUOTES | ENT_HTML5);
} else {
    $name = '';

    $mail = '';

    $url = 'http://';

    $pass = '';
}
$uid = $uiddata['uid'];
$allow_sig = $topicdata['allow_sig'];
$isreply = 1;
include BLUESBB_ROOT . '/include/bbbform.inc.php';
require XOOPS_ROOT_PATH . '/footer.php';
