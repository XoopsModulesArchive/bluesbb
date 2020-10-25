<?php

// Author: Sting_Band
// URL: http://www.bluish.jp/

ini_set('mbstring.language', 'Japanese');
ini_set('mbstring.http_output', 'pass');
require dirname(__DIR__, 3) . '/mainfile.php';
require __DIR__ . '/i_header.php';
require_once BLUESBB_ROOT . '/include/btickets.php';
if (mb_strstr($_SERVER['HTTP_USER_AGENT'], 'DoCoMo') || mb_strstr($_SERVER['HTTP_USER_AGENT'], 'J-PHONE') || mb_strstr($_SERVER['HTTP_USER_AGENT'], 'UP.Browser')) {
    // Ticket Check

    if (!$xoopsBluesTicket->i_check()) {
        redirect_header(BLUESBB_URL . '/i/', 3, $xoopsBluesTicket->getErrors());
    }
} else {
    // Ticket Check

    if (!$xoopsBluesTicket->check()) {
        redirect_header(BLUESBB_URL . '/i/', 3, $xoopsBluesTicket->getErrors());
    }
}
$_POST = array_map('eucconvert', $_POST);
$mess = '';
$topic = isset($_POST['topic']) ? (int)$_POST['topic'] : 0;
if (empty($topic)) {
    redirect_header(BLUESBB_URL . '/i/', 3, 'トピックが選択されていません。');

    exit();
}
$sql = 'SELECT topic_name, topic_access, res_limit FROM ' . $xoopsDB->prefix('bluesbb_topic') . ' WHERE topic_id = ' . $topic;
if (!$result = $xoopsDB->query($sql)) {
    redirect_header(BLUESBB_URL . '/i/', 3, 'Error!! ' . basename(__FILE__) . ' Line ' . __LINE__);

    exit();
}
if (!$topicdata = $xoopsDB->fetchArray($result)) {
    redirect_header(BLUESBB_URL . '/i/', 2, 'トピックデータの収得に失敗しました。');

    exit();
}
$accesserror = 1;
if ('1' == $topicdata['topic_access']) {
    $accesserror = 0;
}
if (1 == $accesserror) {
    redirect_header(BLUESBB_URL . '/i/', 3, 'このトピックへのアクセスは許可されていません。');

    exit();
}
$myts = MyTextSanitizer::getInstance();
if (!$_POST['name']) {
    $input_name = $myts->addSlashes(eucconvert('名無しさん'));
} else {
    $input_name = $myts->addSlashes($_POST['name']);
}
if (!$_POST['title']) {
    $mess .= "<font color=\"#FF0000\">ﾀｲﾄﾙが入力されていません。</font>\r\n";
} else {
    $input_title = $myts->addSlashes($_POST['title']);
}
if (!$_POST['message']) {
    $mess .= "<font color=\"#FF0000\">ﾒｯｾｰｼﾞが入力されていません。</font>\r\n";
} else {
    $input_message = $myts->addSlashes(bbb_p_word($_POST['message'], BLUESBB_I_PWORD));
}
if (isset($_POST['mail'])) {
    $input_mail = $myts->addSlashes($_POST['mail']);
}
if (isset($_POST['url'])) {
    $input_url = $myts->addSlashes($_POST['url']);
}
if (!isset($_POST['mode']) || ('1' != $_POST['mode'] && '2' != $_POST['mode'])) {
    $mess .= "<font color=\"#FF0000\">ﾓｰﾄﾞが指定されていません。</font>\r\n";
}
if ('' == $mess) {
    $now_time = time();

    $input_sre_id = '';

    $input_res_id = '';

    $input_hig_id = '';

    if ('1' == $_POST['mode']) {
        $sql = 'SELECT MAX(thread_id) FROM ' . $xoopsDB->prefix('bluesbb') . ' WHERE res_id = 0';

        if (!$result = $xoopsDB->query($sql)) {
            redirect_header(BLUESBB_URL . '/i/', 3, 'Error!! ' . basename(__FILE__) . ' Line ' . __LINE__);

            exit();
        }

        [$input_sre_id] = $xoopsDB->fetchRow($result);

        ++$input_sre_id;

        $input_res_id = '0';

        $input_hig_id = '0';

        $GLOBALS['xoopsDB']->freeRecordSet($result);
    }

    if ('2' == $_POST['mode']) {
        $input_sre_id = (int)$_POST['thread_id'];

        $input_hig_id = '1';

        $sql = 'SELECT topic_id FROM ' . $xoopsDB->prefix('bluesbb') . ' WHERE thread_id = ' . $input_sre_id . ' AND res_id = 0';

        if (!$result = $xoopsDB->query($sql)) {
            redirect_header(BLUESBB_URL . '/', 3, 'Error!! ' . basename(__FILE__) . ' Line ' . __LINE__);

            exit();
        }

        if (!$topic_id = $xoopsDB->fetchArray($result)) {
            redirect_header(BLUESBB_URL . '/', 3, 'Error!! ' . basename(__FILE__) . ' Line ' . __LINE__);

            exit();
        }

        $topic = '';

        $topic = $topic_id['topic_id'];

        if (isset($_POST['thread_stop']) && '1' == $_POST['thread_stop']) {
            $input_res_id = $topicdata['res_limit'];
        } else {
            $sql = 'SELECT MAX(res_id) FROM ' . $xoopsDB->prefix('bluesbb') . ' WHERE thread_id = ' . $input_sre_id;

            if (!$result = $xoopsDB->query($sql)) {
                redirect_header(BLUESBB_URL . '/i/', 3, 'Error!! ' . basename(__FILE__) . ' Line ' . __LINE__);

                exit();
            }

            [$input_res_id] = $xoopsDB->fetchRow($result);

            ++$input_res_id;

            $GLOBALS['xoopsDB']->freeRecordSet($result);
        }
    }

    $input_uid = '0';

    $input_sig = '0';

    $input_cols = "'" . $topic . "', ";

    $input_cols .= "'" . $input_sre_id . "', ";

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

    $dbinput = '0';

    if (mb_strstr($_SERVER['HTTP_USER_AGENT'], 'DoCoMo') || mb_strstr($_SERVER['HTTP_USER_AGENT'], 'J-PHONE') || mb_strstr($_SERVER['HTTP_USER_AGENT'], 'UP.Browser')) {
        if (!$result = $xoopsDB->queryF($sql)) {
            $mess .= "<font color=\"#FF0000\">DBへの入力エラー！</font>投稿処理は失敗しました。\r\n";

            $dbinput = '1';
        }
    } else {
        if (!$result = $xoopsDB->query($sql)) {
            $mess .= "<font color=\"#FF0000\">DBへの入力エラー！</font>投稿処理は失敗しました。\r\n";

            $dbinput = '1';
        }
    }

    if ('0' == $dbinput) {
        //スレッドの上げ下げ

        if ('2' == $_POST['mode'] && 'sage' !== $_POST['mail']) {
            $sql = 'update ' . $xoopsDB->prefix('bluesbb') . ' set res_time = ' . $now_time . ' where thread_id = ' . $input_sre_id . ' and res_id = 0';

            if (mb_strstr($_SERVER['HTTP_USER_AGENT'], 'DoCoMo') || mb_strstr($_SERVER['HTTP_USER_AGENT'], 'J-PHONE') || mb_strstr($_SERVER['HTTP_USER_AGENT'], 'UP.Browser')) {
                if (!$result = $xoopsDB->queryF($sql)) {
                    $mess .= "<font color=\"#FF0000\">エラー！</font>スレッドの最新返信日時の更新に失敗しました。\r\n";

                    $dbinput = '1';
                }
            } else {
                if (!$result = $xoopsDB->query($sql)) {
                    $mess .= "<font color=\"#FF0000\">エラー！</font>スレッドの最新返信日時の更新に失敗しました。\r\n";

                    $dbinput = '1';
                }
            }
        }
    }

    if ('0' == $dbinput) {
        //メール送信処理

        if (BLUESBB_I_SENDMAIL == 'on') {
            if ('1' == $_POST['mode']) {
                $m_message = htmlspecialchars($topicdata['topic_name'], ENT_QUOTES | ENT_HTML5) . '' . eucconvert('に新規スレッドがたてられました。');
            } elseif ('2' == $_POST['mode']) {
                $m_message = htmlspecialchars($topicdata['topic_name'], ENT_QUOTES | ENT_HTML5) . '' . eucconvert('に返信が投稿されました。');
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

        $mess .= "<font color=\"#0000ff\">投稿ﾃﾞｰﾀは正常に処理されました。</font>\r\n";
    }
}
header('Content-type: text/html; charset=Shift-jis');
?>
<html>
<head>
    <title>投稿処理結果</title>
    <META HTTP-EQUIV="Content-Type" CONTENT="text/html;CHARSET=Shift-jis">
</head>
<body bgcolor="#F0F0F0" text="#000000">
<?php
if (eregi('mozilla', $_SERVER['HTTP_USER_AGENT'])) {
    echo "<div align=\"center\">\r\n";

    echo "<table width=\"200\" border=\"0\" cellspacing=\"1\" cellpadding=\"5\" bgcolor=\"#696969\">\r\n";

    echo "<tr>\r\n";

    echo "<td style=\"font-family: monospace\" bgcolor=\"#f0f0f0\">\r\n";
}
?>
<center>投稿処理結果</font></center>
<hr>
<?php echo $mess; ?>
<hr>
<center>
    <?php
    if ('1' == $_POST['mode']) {
        echo '<a href="' . BLUESBB_URL . '/i/index.php">Top</a>&nbsp;';

        echo '<a href="' . BLUESBB_URL . '/i/i_threadlist.php?top=' . $topic . '">Back</a>';
    } elseif ('2' == $_POST['mode']) {
        echo '<a href="' . BLUESBB_URL . '/i/index.php">Top</a>&nbsp;';

        echo '<a href="' . BLUESBB_URL . '/i/i_threadlist.php?top=' . $topic . '">板</a>&nbsp;';

        echo '<a href="' . BLUESBB_URL . '/i/i_message.php?top=' . $topic . '&amp;thr=' . (int)$_POST['thread_id'] . '&amp;num=l10">Back</a>';
    }
    ?>
</center>
<hr>
<center><?php echo sjisconvert(BLUESBB_COPYRIGHT); ?></center>
<?php
if (eregi('mozilla', $_SERVER['HTTP_USER_AGENT'])) {
        echo "</td>\r\n";

        echo "</tr>\r\n";

        echo "</table>\r\n";

        echo "</div>\r\n";
    }
?>
</body>
</html>
