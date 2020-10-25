<?php

// Author: Sting_Band
// URL: http://www.bluish.jp/

require_once dirname(__DIR__, 3) . '/include/cp_header.php';
require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';
define('BLUESBB_DIR', $xoopsModule->dirname());
define('BLUESBB_ROOT', XOOPS_ROOT_PATH . '/modules/' . BLUESBB_DIR);
define('BLUESBB_URL', XOOPS_URL . '/modules/' . BLUESBB_DIR);
require_once BLUESBB_ROOT . '/functions.php';
require_once BLUESBB_ROOT . '/include/btickets.php';
$topic = isset($_GET['top']) ? (int)$_GET['top'] : 0;
$mess = '';
$myts = MyTextSanitizer::getInstance();
if (!empty($topic)) {
    //トピック編集処理

    if ('topic_edit' == $_POST['mode']) {
        // Ticket Check

        if (!$xoopsBluesTicket->check()) {
            redirect_header(XOOPS_URL . '/', 3, $xoopsBluesTicket->getErrors());
        }

        $topic_name = $myts->addSlashes($_POST['topic_name']);

        $topic_access = (int)$_POST['topic_access'];

        $topic_group = (int)$_POST['topic_group'];

        $topic_info = $myts->addSlashes($_POST['topic_info']);

        $cat_id = (int)$_POST['cat_id'];

        $allow_sig = (int)$_POST['allow_sig'];

        $list_display = (int)$_POST['list_display'];

        $thread_per_page = (int)$_POST['thread_per_page'];

        $res_per_thread = (int)$_POST['res_per_thread'];

        $tree_per_page = (int)$_POST['tree_per_page'];

        $order_per_page = (int)$_POST['order_per_page'];

        $res_limit = (int)$_POST['res_limit'];

        $topic_style = (int)$_POST['topic_style'];

        $style_choice = $myts->addSlashes(implode(':', $_POST['style_choice']));

        $topic_order = (int)$_POST['topic_order'];

        if (empty($topic_name)
            || empty($topic_info) || empty($cat_id) || empty($topic_access) || empty($topic_group) || empty($thread_per_page) || empty($res_per_thread) || empty($tree_per_page) || empty($order_per_page) || empty($res_limit) || empty($topic_style) || empty($style_choice)
            || empty($topic_order)) {
            redirect_header(BLUESBB_URL . '/admin/', 2, _MD_A_YDNFOATPOTFDYAA);

            exit();
        }

        $sql = 'UPDATE '
                   . $xoopsDB->prefix('bluesbb_topic')
                   . " SET topic_name = '"
                   . $topic_name
                   . "', topic_access = '"
                   . $topic_access
                   . "', topic_group = '"
                   . $topic_group
                   . "', topic_info = '"
                   . $topic_info
                   . "', cat_id = '"
                   . $cat_id
                   . "', allow_sig = '"
                   . $allow_sig
                   . "', list_display = '"
                   . $list_display
                   . "', thread_per_page = '"
                   . $thread_per_page
                   . "', res_per_thread = '"
                   . $res_per_thread
                   . "', tree_per_page = '"
                   . $tree_per_page
                   . "', order_per_page = '"
                   . $order_per_page
                   . "', res_limit = '"
                   . $res_limit
                   . "', topic_style = '"
                   . $topic_style
                   . "', style_choice = '"
                   . $style_choice
                   . "', topic_order = '"
                   . $topic_order
                   . "' WHERE topic_id = "
                   . $topic;

        if (!$result = $xoopsDB->query($sql)) {
            redirect_header(BLUESBB_URL . '/admin/', 3, 'Error!! ' . basename(__FILE__) . ' Line ' . __LINE__);

            exit();
        }

        $mess = "<font color='#0000ff'>" . _MD_A_TOPICUPDATED . '</font>';
    }

    //トピック削除処理

    if ('topic_delete' == $_POST['mode']) {
        // Ticket Check

        if (!$xoopsBluesTicket->check()) {
            redirect_header(XOOPS_URL . '/', 3, $xoopsBluesTicket->getErrors());
        }

        $sql = 'SELECT COUNT(post_id) FROM ' . $xoopsDB->prefix('bluesbb') . ' WHERE topic_id =' . $topic;

        if (!$r = $xoopsDB->query($sql)) {
            redirect_header(BLUESBB_URL . '/admin/', 3, 'Error!! ' . basename(__FILE__) . ' Line ' . __LINE__);

            exit();
        }

        [$post_count] = $xoopsDB->fetchRow($r);

        if ('0' == $post_count) {
            $sql = sprintf('DELETE FROM %s WHERE topic_id = %u', $xoopsDB->prefix('bluesbb_topic'), $topic);

            if (!$result = $xoopsDB->query($sql)) {
                redirect_header(BLUESBB_URL . '/admin/', 3, 'Error!! ' . basename(__FILE__) . ' Line ' . __LINE__);

                exit();
            }

            redirect_header(BLUESBB_URL . '/admin/admin_topic.php', 3, "<font color='#0000ff'>" . _MD_A_TOPICREMOVED . '</font>');

            exit();
        }

        redirect_header(BLUESBB_URL . '/admin/admin_topic.php', 3, "<font color='#ff0000'>" . _MD_A_TOPDELETEWARNING . '</font>');

        exit();
    }

    xoops_cp_header();

    require __DIR__ . '/mymenu.php';

    echo $mess;

    echo '<br><br>';

    $sql = 'SELECT * FROM ' . $xoopsDB->prefix('bluesbb_topic') . ' WHERE topic_id = ' . $topic;

    if (!$r = $xoopsDB->query($sql)) {
        redirect_header(BLUESBB_URL . '/admin/', 3, 'Error!! ' . basename(__FILE__) . ' Line ' . __LINE__);

        exit();
    }

    [$topic_id, $topic_name, $topic_access, $topic_group, $topic_info, $cat_id, $allow_sig, $list_display, $thread_per_page, $res_per_thread, $tree_per_page, $order_per_page, $res_limit, $topic_style, $style_choice, $topic_order] = $xoopsDB->fetchRow($r);

    $sform = new XoopsThemeForm(_MD_A_EDITTHISTOPIC, 'topiceditform', 'admin_topic.php?top=' . $topic_id, 'post');

    $mode_check = new XoopsFormRadio(_MD_A_MODESELECT, 'mode', 'topic_edit');

    $mode_check->addOption('topic_edit', _MD_A_EDIT);

    $mode_check->addOption('topic_delete', _MD_A_REMOVE);

    $sform->addElement($mode_check);

    $sform->addElement(new XoopsFormText(_MD_A_TOPICNAME, 'topic_name', 50, 150, $topic_name), true);

    $sform->addElement(new XoopsFormDhtmlTextArea(_MD_A_TOPICINFO, 'topic_info', $topic_info, 15, 40), true);

    $catselect = new XoopsFormSelect(_MD_A_CATEGORY, 'cat_id', $cat_id);

    $sql = 'SELECT * FROM ' . $xoopsDB->prefix('bluesbb_categories') . ' ORDER BY cat_order';

    if (!$result = $xoopsDB->query($sql)) {
        redirect_header(BLUESBB_URL . '/admin/', 3, 'Error!! ' . basename(__FILE__) . ' Line ' . __LINE__);

        exit();
    }

    while (false !== ($cat_row = $xoopsDB->fetchArray($result))) {
        $catselect->addOption($cat_row['cat_id'], htmlspecialchars($cat_row['cat_title'], ENT_QUOTES | ENT_HTML5));
    }

    $sform->addElement($catselect);

    $accessselect = new XoopsFormSelect(_MD_A_ACCESSLEVEL, 'topic_access', $topic_access);

    $accessselect->addOption('1', _MD_A_ACCESSLEVEL1);

    $accessselect->addOption('2', _MD_A_ACCESSLEVEL2);

    $accessselect->addOption('3', _MD_A_ACCESSLEVEL3);

    $accessselect->addOption('4', _MD_A_ACCESSLEVEL4);

    $accessselect->addOption('5', _MD_A_ACCESSLEVEL5);

    $accessselect->addOption('6', _MD_A_ACCESSLEVEL6);

    $sform->addElement($accessselect);

    $suserselect = new XoopsFormSelect(_MD_A_SUSERSELECT, 'topic_group', $topic_group);

    $sql = 'SELECT * FROM ' . $xoopsDB->prefix('groups') . ' ORDER BY groupid';

    if (!$result = $xoopsDB->query($sql)) {
        redirect_header(BLUESBB_URL . '/admin/', 3, 'Error!! ' . basename(__FILE__) . ' Line ' . __LINE__);

        exit();
    }

    while (false !== ($group_row = $xoopsDB->fetchArray($result))) {
        $suserselect->addOption($group_row['groupid'], htmlspecialchars($group_row['name'], ENT_QUOTES | ENT_HTML5));
    }

    $sform->addElement($suserselect);

    $allow_check = new XoopsFormRadio(_MD_A_ALLOWSIGNATURES, 'allow_sig', $allow_sig);

    $allow_check->addOption('1', _YES);

    $allow_check->addOption('0', _NO);

    $sform->addElement($allow_check);

    $list_check = new XoopsFormRadio(_MD_A_LISTDISPLAY, 'list_display', $list_display);

    $list_check->addOption('1', _YES);

    $list_check->addOption('0', _NO);

    $sform->addElement($list_check);

    $sform->addElement(new XoopsFormText(_MD_A_THREADPERPAGE, 'thread_per_page', 3, 3, $thread_per_page), true);

    $sform->addElement(new XoopsFormText(_MD_A_RESPERTHREAD, 'res_per_thread', 3, 3, $res_per_thread), true);

    $sform->addElement(new XoopsFormText(_MD_A_TREEPERPAGE, 'tree_per_page', 3, 3, $tree_per_page), true);

    $sform->addElement(new XoopsFormText(_MD_A_ORDERPERPAGE, 'order_per_page', 3, 3, $order_per_page), true);

    $sform->addElement(new XoopsFormText(_MD_A_RESLIMIT, 'res_limit', 3, 3, $res_limit), true);

    $styleselect = new XoopsFormSelect(_MD_A_TOPICSTYLE, 'topic_style', $topic_style);

    $styleselect->addOption('1', _MD_A_TOPICSTYLE1);

    $styleselect->addOption('2', _MD_A_TOPICSTYLE2);

    $styleselect->addOption('3', _MD_A_TOPICSTYLE3);

    $sform->addElement($styleselect);

    $sc = [];

    $sc = explode(':', $style_choice);

    $stylechoice = new XoopsFormCheckBox(_MD_A_STYLECHOICE, 'style_choice', $sc);

    $stylechoice->addOption('1', _MD_A_TOPICSTYLE1);

    $stylechoice->addOption('2', _MD_A_TOPICSTYLE2);

    $stylechoice->addOption('3', _MD_A_TOPICSTYLE3);

    $sform->addElement($stylechoice);

    $sform->addElement(new XoopsFormText(_MD_A_CATEGORYORDER, 'topic_order', 3, 3, $topic_order), true);

    $button = new XoopsFormElementTray('', '');

    $button->addElement($GLOBALS['xoopsBluesTicket']->getTicketXoopsForm(__LINE__));

    $button->addElement(new XoopsFormButton('', 'submit', _MD_A_EDITTHISTOPIC, 'submit'));

    $button->addElement(new XoopsFormButton('', '', _MD_A_CLEAR, 'reset'));

    $sform->addElement($button);

    $sform->display();

    xoops_cp_footer();
} else {
    //新規トピック作成処理

    if ('topic_make' == $_POST['mode']) {
        // Ticket Check

        if (!$xoopsBluesTicket->check()) {
            redirect_header(XOOPS_URL . '/', 3, $xoopsBluesTicket->getErrors());
        }

        $topic_name = $myts->addSlashes($_POST['topic_name']);

        $topic_access = (int)$_POST['topic_access'];

        $topic_group = (int)$_POST['topic_group'];

        $topic_info = $myts->addSlashes($_POST['topic_info']);

        $cat_id = (int)$_POST['cat_id'];

        $allow_sig = (int)$_POST['allow_sig'];

        $list_display = (int)$_POST['list_display'];

        $thread_per_page = (int)$_POST['thread_per_page'];

        $res_per_thread = (int)$_POST['res_per_thread'];

        $tree_per_page = (int)$_POST['tree_per_page'];

        $order_per_page = (int)$_POST['order_per_page'];

        $res_limit = (int)$_POST['res_limit'];

        $topic_style = (int)$_POST['topic_style'];

        $style_choice = $myts->addSlashes(implode(':', $_POST['style_choice']));

        $topic_order = (int)$_POST['topic_order'];

        if (empty($topic_name)
            || empty($topic_info) || empty($cat_id) || empty($topic_access) || empty($topic_group) || empty($thread_per_page) || empty($res_per_thread) || empty($tree_per_page) || empty($order_per_page) || empty($res_limit) || empty($topic_style) || empty($style_choice)
            || empty($topic_order)) {
            redirect_header(BLUESBB_URL . '/admin/', 2, _MD_A_YDNFOATPOTFDYAA);

            exit();
        }

        $sql = 'INSERT INTO '
               . $xoopsDB->prefix('bluesbb_topic')
               . " (topic_name, topic_access, topic_group, topic_info, cat_id, allow_sig, list_display, thread_per_page, res_per_thread, tree_per_page, order_per_page, res_limit, topic_style, style_choice, topic_order) VALUES ('$topic_name', '$topic_access', '$topic_group', '$topic_info', '$cat_id', '$allow_sig', '$list_display', '$thread_per_page', '$res_per_thread', '$tree_per_page', '$order_per_page', '$res_limit', '$topic_style', '$style_choice', '$topic_order')";

        if (!$result = $xoopsDB->query($sql)) {
            redirect_header(BLUESBB_URL . '/admin/', 3, 'Error!! ' . basename(__FILE__) . ' Line ' . __LINE__);

            exit();
        }

        $mess = '<font color="#0000ff">' . _MD_A_TOPICCREATED . '</font>';
    }

    xoops_cp_header();

    require __DIR__ . '/mymenu.php';

    echo $mess;

    echo '<br><br>'; ?>
    <table class='outer' width='100%' cellpadding='4' cellspacing='1'>
        <tr align='center'>
            <th><?php echo _MD_A_EDITTHISTOPIC; ?></th>
        </tr>
        <tr class='bg1' align='left'>
            <td>
                <?php echo _MD_A_EDITTOPICNAMES; ?>
            </td>
        </tr>
        <tr align='center' valign='middle'>
            <td>
                <ul>
                    <?php
                    $sql = 'SELECT * FROM ' . $xoopsDB->prefix('bluesbb_topic') . ' ORDER BY topic_order';

    if (!$result = $xoopsDB->query($sql)) {
        redirect_header(BLUESBB_URL . '/admin/', 3, 'Error!! ' . basename(__FILE__) . ' Line ' . __LINE__);

        exit();
    }

    while (false !== ($topi_row = $xoopsDB->fetchArray($result))) {
        echo "<li><a href='" . BLUESBB_URL . '/admin/admin_topic.php?top=' . $topi_row['topic_id'] . "'>" . htmlspecialchars($topi_row['topic_name'], ENT_QUOTES | ENT_HTML5) . '</a>(' . $topi_row['topic_order'] . ')</li>';
    } ?>
                </ul>
            </td>
        </tr>
    </table>
    <br><br>
    <hr><br><br>
    <?php
    $sform = new XoopsThemeForm(_MD_A_CREATENEWTOPIC, 'topicmakeform', 'admin_topic.php', 'post');

    $sform->addElement(new XoopsFormText(_MD_A_TOPICNAME, 'topic_name', 50, 150, ''), true);

    $sform->addElement(new XoopsFormDhtmlTextArea(_MD_A_TOPICINFO, 'topic_info', '', 15, 40), true);

    $catselect = new XoopsFormSelect(_MD_A_CATEGORY, 'cat_id', '');

    $sql = 'SELECT * FROM ' . $xoopsDB->prefix('bluesbb_categories') . ' ORDER BY cat_order';

    if (!$result = $xoopsDB->query($sql)) {
        redirect_header(BLUESBB_URL . '/admin/', 3, 'Error!! ' . basename(__FILE__) . ' Line ' . __LINE__);

        exit();
    }

    while (false !== ($cat_row = $xoopsDB->fetchArray($result))) {
        $catselect->addOption($cat_row['cat_id'], htmlspecialchars($cat_row['cat_title'], ENT_QUOTES | ENT_HTML5));
    }

    $sform->addElement($catselect);

    $accessselect = new XoopsFormSelect(_MD_A_ACCESSLEVEL, 'topic_access', '1');

    $accessselect->addOption('1', _MD_A_ACCESSLEVEL1);

    $accessselect->addOption('2', _MD_A_ACCESSLEVEL2);

    $accessselect->addOption('3', _MD_A_ACCESSLEVEL3);

    $accessselect->addOption('4', _MD_A_ACCESSLEVEL4);

    $accessselect->addOption('5', _MD_A_ACCESSLEVEL5);

    $accessselect->addOption('6', _MD_A_ACCESSLEVEL6);

    $sform->addElement($accessselect);

    $suserselect = new XoopsFormSelect(_MD_A_SUSERSELECT, 'topic_group', '');

    $sql = 'SELECT * FROM ' . $xoopsDB->prefix('groups') . ' ORDER BY groupid';

    if (!$result = $xoopsDB->query($sql)) {
        redirect_header(BLUESBB_URL . '/admin/', 3, 'Error!! ' . basename(__FILE__) . ' Line ' . __LINE__);

        exit();
    }

    while (false !== ($group_row = $xoopsDB->fetchArray($result))) {
        $suserselect->addOption($group_row['groupid'], htmlspecialchars($group_row['name'], ENT_QUOTES | ENT_HTML5));
    }

    $sform->addElement($suserselect);

    $allow_check = new XoopsFormRadio(_MD_A_ALLOWSIGNATURES, 'allow_sig', '1');

    $allow_check->addOption('1', _YES);

    $allow_check->addOption('0', _NO);

    $sform->addElement($allow_check);

    $list_check = new XoopsFormRadio(_MD_A_LISTDISPLAY, 'list_display', '1');

    $list_check->addOption('1', _YES);

    $list_check->addOption('0', _NO);

    $sform->addElement($list_check);

    $sform->addElement(new XoopsFormText(_MD_A_THREADPERPAGE, 'thread_per_page', 3, 3, '5'), true);

    $sform->addElement(new XoopsFormText(_MD_A_RESPERTHREAD, 'res_per_thread', 3, 3, '10'), true);

    $sform->addElement(new XoopsFormText(_MD_A_TREEPERPAGE, 'tree_per_page', 3, 3, '5'), true);

    $sform->addElement(new XoopsFormText(_MD_A_ORDERPERPAGE, 'order_per_page', 3, 3, '20'), true);

    $sform->addElement(new XoopsFormText(_MD_A_RESLIMIT, 'res_limit', 3, 3, '299'), true);

    $styleselect = new XoopsFormSelect(_MD_A_TOPICSTYLE, 'topic_style', '1');

    $styleselect->addOption('1', _MD_A_TOPICSTYLE1);

    $styleselect->addOption('2', _MD_A_TOPICSTYLE2);

    $styleselect->addOption('3', _MD_A_TOPICSTYLE3);

    $sform->addElement($styleselect);

    $stylechoice = new XoopsFormCheckBox(_MD_A_STYLECHOICE, 'style_choice', ['1', '2', '3']);

    $stylechoice->addOption('1', _MD_A_TOPICSTYLE1);

    $stylechoice->addOption('2', _MD_A_TOPICSTYLE2);

    $stylechoice->addOption('3', _MD_A_TOPICSTYLE3);

    $sform->addElement($stylechoice);

    $sform->addElement(new XoopsFormText(_MD_A_CATEGORYORDER, 'topic_order', 3, 3, '1'), true);

    $button = new XoopsFormElementTray('', '');

    $button->addElement(new XoopsFormHidden('mode', 'topic_make'));

    $button->addElement($GLOBALS['xoopsBluesTicket']->getTicketXoopsForm(__LINE__));

    $button->addElement(new XoopsFormButton('', 'submit', _MD_A_CREATENEWTOPIC, 'submit'));

    $button->addElement(new XoopsFormButton('', '', _MD_A_CLEAR, 'reset'));

    $sform->addElement($button);

    $sform->display();

    xoops_cp_footer();
}
?>
