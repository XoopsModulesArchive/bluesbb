<?php

require __DIR__ . '/header.php';
$topic = (int)$_GET['topic'];
if (empty($topic)) {
    redirect_header('index.php', 2, _MD_ERRORTOPIC);

    exit();
}
    $sql = 'SELECT topic_access, topic_group, allow_sig FROM ' . $xoopsDB->prefix('bluesbb_topic') . ' WHERE topic_id = ' . $topic;
    if (!$result = $xoopsDB->query($sql)) {
        redirect_header('index.php', 2, _MD_ERRORCONNECT);

        exit();
    }
    if (!$topicdata = $xoopsDB->fetchArray($result)) {
        redirect_header('index.php', 2, _MD_ERROREXIST);

        exit();
    }
    $accesserror = 1;
    if ('1' == $topicdata['topic_access']) {
        $accesserror = 0;
    } elseif ('2' == $topicdata['topic_access'] || '3' == $topicdata['topic_access']) {
        if ($xoopsUser) {
            $accesserror = 0;
        }
    } elseif ('4' == $topicdata['topic_access'] || '5' == $topicdata['topic_access']) {
        if ($xoopsUser) {
            if ($xoopsUser->isAdmin($xoopsModule->mid())) {
                $accesserror = 0;
            }
        }
    } elseif ('6' == $topicdata['topic_access']) {
        if ($xoopsUser) {
            $groups = $memberHandler->getGroupsByUser($xoopsUser->getVar('uid'), true);

            foreach ($groups as $group) {
                if (preg_match('/' . $topicdata['topic_group'] . '/', $group->getVar('groupid'))) {
                    $accesserror = 0;
                }
            }

            if ($xoopsUser->isAdmin($xoopsModule->mid())) {
                $accesserror = 0;
            }
        }
    }
    if (1 == $accesserror) {
        redirect_header('index.php', 2, _MD_TOPICACCESSERROR2);

        exit();
    }
    require XOOPS_ROOT_PATH . '/header.php';
    $mode = 0;
    if (isset($_COOKIE['xoops_bluesbb'])) {
        $coo = explode(',', $_COOKIE['xoops_bluesbb']);

        $name = $coo[0];

        $mail = $coo[1];

        $url = $coo[2];

        $pass = $coo[3];
    } else {
        $name = '';

        $mail = '';

        $url = 'http://';

        $pass = '';
    }
    $title = '';
    $message = '';
    $myts = MyTextSanitizer::getInstance();
    $hidden = '';
    $post_id = '';
    $sread_id = '';
    $allow_sig = $topicdata['allow_sig'];
    require __DIR__ . '/include/bbbform.inc.php';
    require XOOPS_ROOT_PATH . '/footer.php';
