<?php

// Author: Sting_Band
// URL: http://www.bluish.jp/

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

define('BLUESBB_DIR', $xoopsModule->dirname());
define('BLUESBB_ROOT', XOOPS_ROOT_PATH . '/modules/' . BLUESBB_DIR);
define('BLUESBB_URL', XOOPS_URL . '/modules/' . BLUESBB_DIR);

require_once BLUESBB_ROOT . '/functions.php';
require_once BLUESBB_ROOT . '/config.php';
