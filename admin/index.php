<?php

// Author: Sting_Band
// URL: http://www.bluish.jp/

require_once dirname(__DIR__, 3) . '/include/cp_header.php';
define('BLUESBB_DIR', $xoopsModule->dirname());
define('BLUESBB_ROOT', XOOPS_ROOT_PATH . '/modules/' . BLUESBB_DIR);
define('BLUESBB_URL', XOOPS_URL . '/modules/' . BLUESBB_DIR);
require_once BLUESBB_ROOT . '/functions.php';
xoops_cp_header();
?>
    <table width='100%' border='0' cellspacing='1' class='outer'>
        <tr>
            <td class="odd">
                <table border='0' cellpadding='4' cellspacing='1' width='100%'>
                    <tr class='bg1' align="left">
                        <td><span class='fg2'><a href='<?php echo BLUESBB_URL . '/admin/admin.php?fct=preferences&amp;op=showmod&amp;mod=' . $xoopsModule->getVar('mid'); ?>'><?php echo _PREFERENCES; ?></a></span></td>
                        <td><span class='fg2'><?php echo _MD_A_PREFERENCES; ?></span></td>
                    </tr>
                    <tr class='bg1' align="left">
                        <td><span class='fg2'><a href='<?php echo BLUESBB_URL; ?>/admin/admin_categories.php'><?php echo _MD_A_CATEGORIES; ?></a></span></td>
                        <td><span class='fg2'><?php echo _MD_A_CATEGORIES2; ?></span></td>
                    </tr>
                    <tr class='bg1' align='left'>
                        <td><span class='fg2'><a href='<?php echo BLUESBB_URL; ?>/admin/admin_topic.php'><?php echo _MD_A_TOPIC; ?></a></span></td>
                        <td><span class='fg2'><?php echo _MD_A_TOPIC2; ?></span></td>
                    </tr>
                    <tr class='bg1' align='left'>
                        <td><span class='fg2'><a href='<?php echo BLUESBB_URL; ?>/admin/myblocksadmin.php'><?php echo _MD_A_MYBLOCKSADMIN; ?></a></span></td>
                        <td><span class='fg2'><?php echo _MD_A_MYBLOCKSADMIN2; ?></span></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
<?php
xoops_cp_footer();
?>
