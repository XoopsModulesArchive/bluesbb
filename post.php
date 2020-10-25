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
$topic = isset($_POST['topic']) ? (int)$_POST['topic'] : 0;
$style = isset($_POST['style']) ? (int)$_POST['style'] : 0;
if (empty($topic)) {
    redirect_header(BLUESBB_URL . '/', 3, _MD_ERRORTOPIC);

    exit();
}
$sql = 'SELECT topic_name, topic_access, topic_group, res_limit FROM ' . $xoopsDB->prefix('bluesbb_topic') . ' WHERE topic_id = ' . $topic;
if (!$result = $xoopsDB->query($sql)) {
    redirect_header(BLUESBB_URL . '/', 3, 'Error!! ' . basename(__FILE__) . ' Line ' . __LINE__);

    exit();
}
if (!$topicdata = $xoopsDB->fetchArray($result)) {
    redirect_header(BLUESBB_URL . '/', 2, _MD_ERROREXIST);

    exit();
}
//アクセスレベルチェック
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
//プレビュー処理
if (!empty($_POST['contents_preview'])) {
    require XOOPS_ROOT_PATH . '/header.php';

    echo "<table width='100%' border='0' cellspacing='1' class='outer'><tr><td>";

    $myts = MyTextSanitizer::getInstance();

    $p_title = $myts->stripSlashesGPC(htmlspecialchars($_POST['title'], ENT_QUOTES | ENT_HTML5));

    $p_message = $myts->previewTarea($_POST['message'], 0, 1, 1, 1, 1);

    themecenterposts($p_title, $p_message);

    echo '<br>';

    $name = $myts->stripSlashesGPC(htmlspecialchars($_POST['name'], ENT_QUOTES | ENT_HTML5));

    $mail = $myts->stripSlashesGPC(htmlspecialchars($_POST['mail'], ENT_QUOTES | ENT_HTML5));

    $url = $myts->stripSlashesGPC(htmlspecialchars($_POST['url'], ENT_QUOTES | ENT_HTML5));

    $title = $myts->stripSlashesGPC(htmlspecialchars($_POST['title'], ENT_QUOTES | ENT_HTML5));

    $message = $myts->stripSlashesGPC(htmlspecialchars($_POST['message'], ENT_QUOTES | ENT_HTML5));

    $pass = $myts->stripSlashesGPC(htmlspecialchars($_POST['pass'], ENT_QUOTES | ENT_HTML5));

    $mode = (int)$_POST['mode'];

    $post_id = (int)$_POST['post_id'];

    $thread = (int)$_POST['thread_id'];

    $topic = (int)$_POST['topic'];

    $res_id = (int)$_POST['res_id'];

    $attachsig = !empty($_POST['attachsig']) ? 1 : 0;

    $allow_sig = '1' == $attachsig ? 1 : 0;

    include BLUESBB_ROOT . '/include/bbbform.inc.php';

    echo '</td></tr></table>';
} else {
    //データベースへの入力処理スタート

    $myts = MyTextSanitizer::getInstance();

    $input_name = !empty($_POST['name']) ? $myts->addSlashes($_POST['name']) : $myts->addSlashes(_MD_NOBODY);

    if (!$_POST['title']) {
        redirect_header(BLUESBB_URL . '/topic.php?top=' . $topic, 3, _MD_NOTITLE);

        exit();
    }

    $input_title = $myts->addSlashes($_POST['title']);

    if (!$_POST['message']) {
        redirect_header(BLUESBB_URL . '/topic.php?top=' . $topic, 3, _MD_NOMESSAGE);

        exit();
    } elseif (mb_strlen($_POST['message']) > $xoopsModuleConfig['messmax']) {
        redirect_header(BLUESBB_URL . '/topic.php?top=' . $topic, 3, _MD_MESSAGEMAX);

        exit();
    }

    $input_message = $myts->addSlashes(bbb_p_word($_POST['message'], $xoopsModuleConfig['pword']));

    $input_mail = !empty($_POST['mail']) ? $myts->addSlashes($_POST['mail']) : '';

    $input_url = !empty($_POST['url']) ? $myts->addSlashes($_POST['url']) : '';

    if (!isset($_POST['mode']) || ('0' != $_POST['mode'] && '1' != $_POST['mode'] && '2' != $_POST['mode'])) {
        redirect_header(BLUESBB_URL . '/topic.php?top=' . $topic, 3, _MD_NOMODE);

        exit();
    }

    $now_time = time();

    $input_thread_id = '';

    $input_res_id = '';

    $input_hig_id = '';

    if ('0' == $_POST['mode']) {
        $sql = 'SELECT MAX(thread_id) FROM ' . $xoopsDB->prefix('bluesbb') . ' WHERE res_id = 0';

        if (!$result = $xoopsDB->query($sql)) {
            redirect_header(BLUESBB_URL . '/', 3, 'Error!! ' . basename(__FILE__) . ' Line ' . __LINE__);

            exit();
        }

        [$input_thread_id] = $xoopsDB->fetchRow($result);

        ++$input_thread_id;

        $input_res_id = '0';

        $input_hig_id = '0';

        $GLOBALS['xoopsDB']->freeRecordSet($result);
    }

    if ('1' == $_POST['mode']) {
        $input_thread_id = (int)$_POST['thread_id'];

        $input_hig_id = (int)$_POST['res_id'];

        if (isset($_POST['thread_stop']) && '1' == $_POST['thread_stop']) {
            $input_res_id = $topicdata['res_limit'];
        } else {
            $sql = 'SELECT MAX(res_id) FROM ' . $xoopsDB->prefix('bluesbb') . ' WHERE thread_id = ' . $input_thread_id;

            if (!$result = $xoopsDB->query($sql)) {
                redirect_header(BLUESBB_URL . '/', 3, 'Error!! ' . basename(__FILE__) . ' Line ' . __LINE__);

                exit();
            }

            [$input_res_id] = $xoopsDB->fetchRow($result);

            ++$input_res_id;

            $GLOBALS['xoopsDB']->freeRecordSet($result);
        }
    }

    if (is_object($xoopsUser)) {
        $input_uid = $xoopsUser->getVar('uid');

        if (isset($_POST['attachsig']) && 1 == $_POST['attachsig']) {
            $input_sig = (int)$_POST['attachsig'];
        } else {
            $input_sig = '0';
        }
    } else {
        $input_uid = '0';

        $input_sig = '0';
    }

    if ('0' == $_POST['mode'] || '1' == $_POST['mode']) {
        $input_cols = "'" . $topic . "', ";

        $input_cols .= "'" . $input_thread_id . "', ";

        $input_cols .= "'" . $input_res_id . "', ";

        $input_cols .= "'" . $input_hig_id . "', ";

        $input_cols .= "'" . $input_name . "', ";

        $input_cols .= "'" . $input_mail . "', ";

        $input_cols .= "'" . $input_url . "', ";

        $input_cols .= "'" . $input_title . "', ";

        $input_cols .= "'" . $input_message . "', ";

        $input_cols .= "'" . md5(htmlspecialchars($_POST['pass'], ENT_QUOTES | ENT_HTML5)) . "', ";

        $input_cols .= "'" . $now_time . "', ";

        $input_cols .= "'" . $now_time . "', ";

        $input_cols .= "'" . $input_uid . "', ";

        $input_cols .= "'" . $input_sig . "', ";

        $input_cols .= "'" . $myts->addSlashes($_SERVER['REMOTE_ADDR']) . "', ";

        $input_cols .= "'" . $myts->addSlashes(gethostbyaddr($_SERVER['REMOTE_ADDR'])) . "', ";

        $input_cols .= "'" . $myts->addSlashes($_SERVER['HTTP_USER_AGENT']) . "'";

        $db_cols = 'topic_id, thread_id, res_id, hig_id, name, mail, url, title, message, pass, post_time, res_time, uid, attachsig, poster_ip, poster_host, poster_agent';

        $sql = 'insert into ' . $xoopsDB->prefix('bluesbb') . " ( $db_cols ) values ( $input_cols )";
    } elseif ('2' == $_POST['mode']) {
        $update_cols = "name='" . $input_name . "', ";

        $update_cols .= "mail='" . $input_mail . "', ";

        $update_cols .= "url='" . $input_url . "', ";

        $update_cols .= "title='" . $input_title . "', ";

        if (!empty($_POST['pass'])) {
            $update_cols .= "message='" . $input_message . "', ";

            $update_cols .= "pass='" . md5(htmlspecialchars($_POST['pass'], ENT_QUOTES | ENT_HTML5)) . "'";
        } else {
            $update_cols .= "message='" . $input_message . "'";
        }

        $post_id = (int)$_POST['post_id'];

        $sql = 'update ' . $xoopsDB->prefix('bluesbb') . ' set ' . $update_cols . ' where post_id = ' . $post_id;
    }

    if (!$result = $xoopsDB->query($sql)) {
        redirect_header(BLUESBB_URL . '/topic.php?top=' . $topic, 3, _MD_DBINSERTERROR);

        exit();
    }

    //クッキー処理

    if (isset($_POST['save_cookie']) && '1' == $_POST['save_cookie']) {
        setcookie('xoops_bluesbb', implode(',', [$myts->stripSlashesGPC($_POST['name']), $myts->stripSlashesGPC($_POST['mail']), $myts->stripSlashesGPC($_POST['url']), $myts->stripSlashesGPC($_POST['pass'])]), time() + 60 * 60 * 24 * 30);
    }

    //スレッドの上げ下げ

    if ('1' == $_POST['mode'] && 'sage' !== $_POST['mail']) {
        $sql = 'update ' . $xoopsDB->prefix('bluesbb') . ' set res_time = ' . $now_time . ' where thread_id = ' . $input_thread_id . ' and res_id = 0';

        if (!$result = $xoopsDB->query($sql)) {
            redirect_header(BLUESBB_URL . '/topic.php?top=' . $topic, 3, _MD_TIMEUPDATEERROR);

            exit();
        }
    }

    //投稿ランキングへの加算処理

    if (('0' == $_POST['mode'] || '1' == $_POST['mode']) && $input_uid > 0) {
        $sql = 'SELECT posts FROM ' . $xoopsDB->prefix('users') . ' WHERE uid = ' . $input_uid;

        if (!$result = $xoopsDB->query($sql)) {
            redirect_header(BLUESBB_URL . '/topic.php?top=' . $topic, 3, _MD_COULDNOTQUERY);

            exit();
        }

        [$update_posts] = $xoopsDB->fetchRow($result);

        ++$update_posts;

        $sql = 'update ' . $xoopsDB->prefix('users') . ' set posts = ' . $update_posts . ' where uid = ' . $input_uid;

        if (!$result = $xoopsDB->query($sql)) {
            redirect_header(BLUESBB_URL . '/topic.php?top=' . $topic, 3, _MD_POSTSUPDATEERROR);

            exit();
        }
    }

    //メール送信処理

    if ('1' == $xoopsModuleConfig['sendmail']) {
        if ('0' == $_POST['mode']) {
            $m_message = htmlspecialchars($topicdata['topic_name'], ENT_QUOTES | ENT_HTML5) . '' . _MD_NEWPOSTED;
        } elseif ('1' == $_POST['mode']) {
            $m_message = htmlspecialchars($topicdata['topic_name'], ENT_QUOTES | ENT_HTML5) . '' . _MD_REPLYPOSTED;
        } elseif ('2' == $_POST['mode']) {
            $m_message = htmlspecialchars($topicdata['topic_name'], ENT_QUOTES | ENT_HTML5) . '' . _MD_EDITPOSTED;
        }

        $m_message .= "\n\n[" . $myts->stripSlashesGPC(htmlspecialchars($input_name, ENT_QUOTES | ENT_HTML5)) . "]\n";

        $m_message .= '[' . $myts->stripSlashesGPC(htmlspecialchars($input_title, ENT_QUOTES | ENT_HTML5)) . "]\n\n";

        $m_message .= $myts->stripSlashesGPC(htmlspecialchars($input_message, ENT_QUOTES | ENT_HTML5)) . "\n\n";

        $m_message .= BLUESBB_URL . '/topic.php?top=' . $topic;

        $xoopsMailer = getMailer();

        $xoopsMailer->useMail();

        $xoopsMailer->setToEmails($xoopsConfig['adminmail']);

        $xoopsMailer->setFromEmail($xoopsConfig['adminmail']);

        $xoopsMailer->setFromName($xoopsConfig['sitename']);

        $xoopsMailer->setSubject($topicdata['topic_name']);

        $xoopsMailer->setBody($m_message);

        $xoopsMailer->send();
    }

    // RMV-NOTIFY

    // Define tags for notification message

    if (('0' == $_POST['mode'] || '1' == $_POST['mode']) && '6' != $topicdata['topic_access']) {
        $tags = [];

        $tags['THREAD_NAME'] = $myts->stripSlashesGPC(htmlspecialchars($input_title, ENT_QUOTES | ENT_HTML5));

        $sql = 'SELECT post_id, res_id FROM ' . $xoopsDB->prefix('bluesbb') . ' ORDER BY post_time DESC LIMIT 0, 1';

        if (!$result = $xoopsDB->query($sql)) {
            redirect_header(BLUESBB_URL . '/', 3, 'Error!! ' . basename(__FILE__) . ' Line ' . __LINE__);

            exit();
        }

        if (!$notdata = $xoopsDB->fetchArray($result)) {
            redirect_header(BLUESBB_URL . '/', 3, 'Error!! ' . basename(__FILE__) . ' Line ' . __LINE__);

            exit();
        }

        switch ($style) {
                case '1':
                    $lt = 'l50#p' . $notdata['post_id'];
                    break;
                case '2':
                    $lt = ++$notdata['res_id'];
                    break;
                case '3':
                    $lt = $notdata['post_id'];
                    break;
            }

        $tags['THREAD_URL'] = BLUESBB_URL . '/thread.php?top=' . $topic . '&amp;thr=' . $input_thread_id . '&amp;sty=' . $style . '&amp;num=' . $lt;

        $tags['POST_URL'] = $tags['THREAD_URL'];

        require_once __DIR__ . '/include/notification.inc.php';

        $topic_info = bluesbb_notify_iteminfo('topic', $topic);

        $tags['TOPIC_NAME'] = $topic_info['name'];

        $tags['TOPIC_URL'] = $topic_info['url'];

        $notificationHandler = xoops_getHandler('notification');

        if ('0' == $_POST['mode']) {
            // Notify of new thread

            $notificationHandler->triggerEvent('topic', $topic, 'new_thread', $tags);
        } else {
            // Notify of new post

            $notificationHandler->triggerEvent('thread', $input_thread_id, 'new_post', $tags);
        }

        $notificationHandler->triggerEvent('global', 0, 'new_post', $tags);

        $notificationHandler->triggerEvent('topic', $topic, 'new_post', $tags);

        $tags['POST_CONTENT'] = $input_message;

        $tags['POST_TITLE'] = $input_title;

        $tags['POST_NAME'] = $input_name;

        $notificationHandler->triggerEvent('global', 0, 'new_fullpost', $tags);
    }

    redirect_header(BLUESBB_URL . '/topic.php?top=' . $topic . '&amp;sty=' . $style, 3, _MD_THANKSSUBMIT);

    exit();
}
require XOOPS_ROOT_PATH . '/footer.php';
