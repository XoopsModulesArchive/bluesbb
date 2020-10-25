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
$mess = '';
$myts = MyTextSanitizer::getInstance();
$cat_get = isset($_GET['cat_id']) ? (int)$_GET['cat_id'] : 0;
//新規カテゴリ作成処理
if ('category_make' == $_POST['mode']) {
    // Ticket Check

    if (!$xoopsBluesTicket->check()) {
        redirect_header(XOOPS_URL . '/', 3, $xoopsBluesTicket->getErrors());
    }

    $title = trim($_POST['title']);

    if (empty($title)) {
        redirect_header(BLUESBB_URL . '/admin/', 2, _MD_A_CATTITLEEMPTY);

        exit();
    }

    $sql = 'SELECT max(cat_order) AS highest FROM ' . $xoopsDB->prefix('bluesbb_categories') . '';

    if (!$r = $xoopsDB->query($sql)) {
        redirect_header(BLUESBB_URL . '/admin/', 3, 'Error!! ' . basename(__FILE__) . ' Line ' . __LINE__);

        exit();
    }

    [$highest] = $xoopsDB->fetchRow($r);

    $highest++;

    $title = $myts->addSlashes($title);

    $sql = 'INSERT INTO ' . $xoopsDB->prefix('bluesbb_categories') . " (cat_title, cat_order) VALUES ('$title', '$highest')";

    if (!$result = $xoopsDB->query($sql)) {
        redirect_header(BLUESBB_URL . '/admin/', 3, 'Error!! ' . basename(__FILE__) . ' Line ' . __LINE__);

        exit();
    }

    $mess = '<font color="#0000ff">' . _MD_A_CATEGORYCREATED . '</font>';
}
//カテゴリ削除処理
if ('category_delete' == $_POST['mode']) {
    // Ticket Check

    if (!$xoopsBluesTicket->check()) {
        redirect_header(XOOPS_URL . '/', 3, $xoopsBluesTicket->getErrors());
    }

    $cat_id = isset($_POST['cat_id']) ? (int)$_POST['cat_id'] : 0;

    if (empty($cat_id)) {
        redirect_header(BLUESBB_URL . '/admin/', 2, _MD_A_CATIDEMPTY);

        exit();
    }

    $sql = 'SELECT COUNT(topic_id) FROM ' . $xoopsDB->prefix('bluesbb_topic') . ' WHERE cat_id =' . $cat_id;

    if (!$r = $xoopsDB->query($sql)) {
        redirect_header(BLUESBB_URL . '/admin/', 3, 'Error!! ' . basename(__FILE__) . ' Line ' . __LINE__);

        exit();
    }

    [$topic_count] = $xoopsDB->fetchRow($r);

    if ('0' == $topic_count) {
        $sql = sprintf('DELETE FROM %s WHERE cat_id = %u', $xoopsDB->prefix('bluesbb_categories'), $cat_id);

        if (!$result = $xoopsDB->query($sql)) {
            redirect_header(BLUESBB_URL . '/admin/', 3, 'Error!! ' . basename(__FILE__) . ' Line ' . __LINE__);

            exit();
        }

        $mess = '<font color="#0000ff">' . _MD_A_CATEGORYDELETED . '</font>';
    } else {
        $mess = '<font color="#ff0000">' . _MD_A_CATDELETEWARNING . '</font>';
    }
}
//カテゴリ編集処理
if ('category_edit' == $_POST['mode']) {
    // Ticket Check

    if (!$xoopsBluesTicket->check()) {
        redirect_header(XOOPS_URL . '/', 3, $xoopsBluesTicket->getErrors());
    }

    $cat_id = isset($_POST['cat_id']) ? (int)$_POST['cat_id'] : 0;

    $cat_title = trim($_POST['cat_title']);

    $cat_order = isset($_POST['cat_order']) ? (int)$_POST['cat_order'] : 0;

    if (empty($cat_id)) {
        redirect_header(BLUESBB_URL . '/admin/', 2, _MD_A_CATIDEMPTY);

        exit();
    } elseif (empty($cat_title)) {
        redirect_header(BLUESBB_URL . '/admin/', 2, _MD_A_CATTITLEEMPTY);

        exit();
    } elseif (empty($cat_order)) {
        redirect_header(BLUESBB_URL . '/admin/', 2, _MD_A_CATORDEREMPTY);

        exit();
    }

    $sql = 'UPDATE ' . $xoopsDB->prefix('bluesbb_categories') . " SET cat_title = '" . $cat_title . "', cat_order = '" . $cat_order . "' WHERE cat_id = " . $cat_id;

    if (!$result = $xoopsDB->query($sql)) {
        redirect_header(BLUESBB_URL . '/admin/', 3, 'Error!! ' . basename(__FILE__) . ' Line ' . __LINE__);

        exit();
    }

    $mess = '<font color="#0000ff">' . _MD_A_CATEGORYUPDATED . '</font>';
}
if (!empty($cat_get)) {
    $sql = 'SELECT * FROM ' . $xoopsDB->prefix('bluesbb_categories') . ' WHERE cat_id = ' . $cat_get;

    if (!$result = $xoopsDB->query($sql)) {
        redirect_header(BLUESBB_URL . '/admin/', 3, 'Error!! ' . basename(__FILE__) . ' Line ' . __LINE__);

        exit();
    }

    $catdata = $xoopsDB->fetchArray($result);

    xoops_cp_header();

    require __DIR__ . '/mymenu.php';

    echo $mess;

    echo '<br><br>';

    $sform = new XoopsThemeForm(_MD_A_EDITCATEGORY, 'cateditform', 'admin_categories.php', 'post');

    $sform->addElement(new XoopsFormText(_MD_A_CATEGORYTITLE, 'cat_title', 50, 100, htmlspecialchars($catdata['cat_title'], ENT_QUOTES | ENT_HTML5)), true);

    $sform->addElement(new XoopsFormText(_MD_A_CATEGORYORDER, 'cat_order', 2, 2, $catdata['cat_order']), true);

    $mode_check = new XoopsFormRadio(_MD_A_EDIT . '/' . _MD_A_REMOVE, 'mode', 'category_edit');

    $mode_check->addOption('category_edit', _MD_A_EDIT);

    $mode_check->addOption('category_delete', _MD_A_REMOVE);

    $sform->addElement($mode_check);

    $button = new XoopsFormElementTray('', '');

    $button->addElement(new XoopsFormHidden('cat_id', $catdata['cat_id']));

    $button->addElement($GLOBALS['xoopsBluesTicket']->getTicketXoopsForm(__LINE__));

    $button->addElement(new XoopsFormButton('', 'submit', _MD_A_GO, 'submit'));

    $sform->addElement($button);

    $sform->display();

    xoops_cp_footer();
} else {
    xoops_cp_header();

    require __DIR__ . '/mymenu.php';

    echo $mess;

    echo '<br><br>'; ?>
    <table class='outer' width='100%' cellpadding='4' cellspacing='1'>
    <tr class='bg3' align='left'>
        <td align='center' colspan="2"><span class='fg2'><b><?php echo _MD_A_EDITCATEGORY . '/' . _MD_A_REMOVE; ?></b></span></td>
    </tr>
    <tr class='bg1' align='left'>
        <td>
            <?php echo _MD_A_CATEGORYTITLE . '(' . _MD_A_CATEGORYORDER . ')'; ?>
        </td>
    </tr>
    <tr>
        <td>
            <ul>
    <?php
    $sql = 'SELECT * FROM ' . $xoopsDB->prefix('bluesbb_categories') . ' ORDER BY cat_order';

    if (!$result = $xoopsDB->query($sql)) {
        redirect_header(BLUESBB_URL . '/admin/', 3, 'Error!! ' . basename(__FILE__) . ' Line ' . __LINE__);

        exit();
    }

    while (false !== ($cat_row = $xoopsDB->fetchArray($result))) {
        echo '<li><a href="' . BLUESBB_URL . '/admin/admin_categories.php?cat_id=' . $cat_row['cat_id'] . '">' . htmlspecialchars($cat_row['cat_title'], ENT_QUOTES | ENT_HTML5) . '</a>(' . $cat_row['cat_order'] . ')</li>';
    }

    echo '</ul></td></tr></table><br><br><hr><br><br>';

    $sform = new XoopsThemeForm(_MD_A_CREATENEWCATEGORY, 'catmakeform', 'admin_categories.php', 'post');

    $sform->addElement(new XoopsFormText(_MD_A_CATEGORYTITLE, 'title', 50, 100, ''), true);

    $button = new XoopsFormElementTray('', '');

    $button->addElement(new XoopsFormHidden('mode', 'category_make'));

    $button->addElement($GLOBALS['xoopsBluesTicket']->getTicketXoopsForm(__LINE__));

    $button->addElement(new XoopsFormButton('', 'submit', _MD_A_CREATENEWCATEGORY, 'submit'));

    $sform->addElement($button);

    $sform->display();

    xoops_cp_footer();
}
?>
