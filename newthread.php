<?php

// Author: Sting_Band
// URL: http://www.bluish.jp/

require dirname(__DIR__, 2) . '/mainfile.php';
require __DIR__ . '/header.php';
require_once BLUESBB_ROOT . '/include/btickets.php';
$topic = isset($_GET['top']) ? (int)$_GET['top'] : 0;
$style = isset($_GET['sty']) ? (int)$_GET['sty'] : 0;
if (empty($topic) || empty($style)) {
    redirect_header(BLUESBB_URL . '/', 2, _MD_ERRORTOPIC);

    exit();
}
$sql = 'SELECT topic_access, topic_group, allow_sig FROM ' . $xoopsDB->prefix('bluesbb_topic') . ' WHERE topic_id = ' . $topic;
if (!$result = $xoopsDB->query($sql)) {
    redirect_header(BLUESBB_URL . '/', 3, 'Error!! ' . basename(__FILE__) . ' Line ' . __LINE__);

    exit();
}
if (!$topicdata = $xoopsDB->fetchArray($result)) {
    redirect_header(BLUESBB_URL . '/', 2, _MD_ERROREXIST);

    exit();
}
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
$myts = MyTextSanitizer::getInstance();
require XOOPS_ROOT_PATH . '/header.php';
$mode = '0';
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
$title = '';
$message = '';
$hidden = '';
$post_id = '';
$thread = '';
$res_id = '';
$allow_sig = $topicdata['allow_sig'];
include BLUESBB_ROOT . '/include/bbbform.inc.php';
require XOOPS_ROOT_PATH . '/footer.php';
