<?php

// Author: Sting_Band
// URL: http://www.bluish.jp/

require dirname(__DIR__, 2) . '/mainfile.php';
require __DIR__ . '/header.php';
require_once BLUESBB_ROOT . '/include/btickets.php';
// Ticket Check
if (!$xoopsBluesTicket->check()) {
    redirect_header(XOOPS_URL . '/', 3, $xoopsBluesTicket->getErrors());
}
$post_id = isset($_POST['post_id']) ? (int)$_POST['post_id'] : 0;
$style = isset($_POST['style']) ? (int)$_POST['style'] : 0;
$myts = MyTextSanitizer::getInstance();
$post_pass = htmlspecialchars($_POST['post_pass'], ENT_QUOTES | ENT_HTML5);
if (empty($post_id)) {
    redirect_header(BLUESBB_URL . '/', 3, _MD_ERRORTOPIC);

    exit();
}
$sql = 'SELECT b.*, t.allow_sig FROM ' . $xoopsDB->prefix('bluesbb') . ' b LEFT JOIN ' . $xoopsDB->prefix('bluesbb_topic') . ' t ON t.topic_id = b.topic_id WHERE b.post_id = ' . $post_id;
if (!$result = $xoopsDB->query($sql)) {
    redirect_header(BLUESBB_URL . '/topic.php?top=' . $topic, 3, 'Error!! ' . basename(__FILE__) . ' Line ' . __LINE__);

    exit();
}
if (!$editdata = $xoopsDB->fetchArray($result)) {
    redirect_header(BLUESBB_URL . '/', 2, _MD_ERROREXIST2);

    exit();
}
$topic = '';
$topic = $editdata['topic_id'];
if (is_object($xoopsUser)) {
    if ('alldelete' == $_POST['post_edit'] && $xoopsUser->isAdmin($xoopsModule->mid())) {
        $sql = 'delete from ' . $xoopsDB->prefix('bluesbb') . ' where thread_id = ' . $editdata['thread_id'];

        if (!$result = $xoopsDB->query($sql)) {
            redirect_header(BLUESBB_URL . '/topic.php?top=' . $topic, 3, $editdata['thread_id'] . '' . _MD_COULDNOTREMOVETXT2);

            exit();
        }

        redirect_header(BLUESBB_URL . '/topic.php?top=' . $topic, 3, $editdata['thread_id'] . '' . _MD_POSTSDELETED2);

        exit();
    }

    if ((!empty($post_pass) && md5($post_pass) == $editdata['pass']) || $xoopsUser->isAdmin($xoopsModule->mid()) || $editdata['uid'] == $xoopsUser->getVar('uid')) {
        if ('delete' == $_POST['post_edit']) {
            $sql = 'delete from ' . $xoopsDB->prefix('bluesbb') . ' where post_id = ' . $post_id;

            if (!$result = $xoopsDB->query($sql)) {
                redirect_header(BLUESBB_URL . '/topic.php?top=' . $topic, 3, $post_id . '' . _MD_COULDNOTREMOVETXT);

                exit();
            }

            if ($editdata['uid'] > 0) {
                $sql = 'SELECT posts FROM ' . $xoopsDB->prefix('users') . ' WHERE uid = ' . $editdata['uid'];

                if (!$result = $xoopsDB->query($sql)) {
                    redirect_header(BLUESBB_URL . '/topic.php?top=' . $topic, 3, _MD_COULDNOTQUERY);

                    exit();
                }

                [$update_posts] = $xoopsDB->fetchRow($result);

                --$update_posts;

                $sql = 'update ' . $xoopsDB->prefix('users') . ' set posts = ' . $update_posts . ' where uid = ' . $editdata['uid'];

                if (!$result = $xoopsDB->query($sql)) {
                    redirect_header(BLUESBB_URL . '/topic.php?top=' . $topic, 3, _MD_POSTSUPDATEERROR);

                    exit();
                }
            }

            redirect_header(BLUESBB_URL . '/topic.php?top=' . $topic, 3, $post_id . '' . _MD_POSTSDELETED);

            exit();
        } elseif ('edit' == $_POST['post_edit']) {
            require XOOPS_ROOT_PATH . '/header.php';

            $mode = '2';

            $thread = '';

            $name = htmlspecialchars($editdata['name'], ENT_QUOTES | ENT_HTML5);

            $mail = htmlspecialchars($editdata['mail'], ENT_QUOTES | ENT_HTML5);

            $url = htmlspecialchars($editdata['url'], ENT_QUOTES | ENT_HTML5);

            $title = htmlspecialchars($editdata['title'], ENT_QUOTES | ENT_HTML5);

            $message = htmlspecialchars($editdata['message'], ENT_QUOTES | ENT_HTML5);

            $pass = $post_pass;

            $allow_sig = $editdata['allow_sig'];

            $hidden = '';

            include BLUESBB_ROOT . '/include/bbbform.inc.php';

            require XOOPS_ROOT_PATH . '/footer.php';
        }
    } else {
        redirect_header(BLUESBB_URL . '/topic.php?top=' . $topic, 3, _MD_PASSERROR);

        exit();
    }
} else {
    if (!empty($post_pass) && md5($post_pass) == $editdata['pass']) {
        if ('delete' == $_POST['post_edit']) {
            $sql = 'delete from ' . $xoopsDB->prefix('bluesbb') . ' where post_id = ' . $post_id;

            if (!$result = $xoopsDB->query($sql)) {
                redirect_header(BLUESBB_URL . '/topic.php?top=' . $topic, 3, $post_id . '' . _MD_COULDNOTREMOVETXT);

                exit();
            }

            redirect_header(BLUESBB_URL . '/topic.php?top=' . $topic, 3, $post_id . '' . _MD_POSTSDELETED);

            exit();
        } elseif ('edit' == $_POST['post_edit']) {
            require XOOPS_ROOT_PATH . '/header.php';

            $mode = '2';

            $thread = '';

            $res_id = '';

            $name = htmlspecialchars($editdata['name'], ENT_QUOTES | ENT_HTML5);

            $mail = htmlspecialchars($editdata['mail'], ENT_QUOTES | ENT_HTML5);

            $url = htmlspecialchars($editdata['url'], ENT_QUOTES | ENT_HTML5);

            $title = htmlspecialchars($editdata['title'], ENT_QUOTES | ENT_HTML5);

            $message = htmlspecialchars($editdata['message'], ENT_QUOTES | ENT_HTML5);

            $pass = $post_pass;

            $allow_sig = $editdata['allow_sig'];

            $hidden = '';

            include BLUESBB_ROOT . '/include/bbbform.inc.php';

            require XOOPS_ROOT_PATH . '/footer.php';
        }
    } else {
        redirect_header(BLUESBB_URL . '/topic.php?top=' . $topic, 3, _MD_PASSERROR);

        exit();
    }
}
