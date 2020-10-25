<?php

// Module Info

// The name of this module
define('_MI_BLUESBB_NAME', 'BluesBB');

// A brief description of this module
define('_MI_BLUESBB_DESC', 'XOOPS bulletin board module');

// Names of blocks for this module (Not all module has blocks)
define('_MI_BLUESBB_BNAME1', 'Recent Posts');
define('_MI_BLUESBB_BNAME2', 'Recent Posts(Simple)');
define('_MI_BLUESBB_BNAME3', 'The topic menu classified by category');
define('_MI_BLUESBB_BNAME4', 'Recent Posts(Every thread)');
define('_MI_BLUESBB_BNAME5', 'Recent Posts(Every thread&Simple)');

define('_MI_BLUESBB_ADMENU1', 'Category setup');
define('_MI_BLUESBB_ADMENU2', 'Topic setup');
define('_MI_BLUESBB_ADMENU3', 'Block group setup');

// Title of config items
// Description of each config items
define('_MI_BLUESBB_INDEXREDIRECT', 'Index redirection');
define('_MI_BLUESBB_INDEXREDIRECTDSC', 'Since it is meaningless even if it displays index.php when the number of topics is one, it is made to redirect to topic.php.');
define('_MI_BLUESBB_PERTH', 'Index thread');
define('_MI_BLUESBB_PERTHDSC', 'The number of threads displayed on the list display of index.php is decided.');
define('_MI_BLUESBB_TOPICPERTH', 'Topic thread');
define('_MI_BLUESBB_TOPICPERTHDSC', 'The number of threads displayed on the list display of topic.php is decided.');
define('_MI_BLUESBB_SENDMAIL', 'Information mai');
define('_MI_BLUESBB_SENDMAILDSC', 'When there is contribution, it sets up whether e-mail is automatically sent to a janitor.');
define('_MI_BLUESBB_MESSAGEMAX', 'The number restrictions of message characters');
define('_MI_BLUESBB_MESSAGEMAXDSC', 'The maximum of the number of message characters is set up. A default is 10000 characters.');
define('_MI_BLUESBB_PWORD', 'Prohibition WORD');
define('_MI_BLUESBB_PWORDDSC', 'Prohibition WORD is set up. It cannot contribute, when the character set up here is contained in the text. WORD is divided with the comma and can also set up shoes.');
define('_MI_BLUESBB_PWORDDEF', 'hogehogeshop.com,Adult_site_Information,Illegalfreeservice');
define('_MI_BLUESBB_NEWTIME', 'NEW mark display time');
define('_MI_BLUESBB_NEWTIMEDSC', 'The "time" which displays a NEW mark on new contribution is set up.');

// RMV-NOTIFY
// Notification event descriptions and mail templates
define('_MI_BLUESBB_THREAD_NOTIFY', 'A thread on display');
define('_MI_BLUESBB_THREAD_NOTIFYDSC', 'The notice option to a thread on display');

define('_MI_BLUESBB_TOPIC_NOTIFY', 'A topic on display');
define('_MI_BLUESBB_TOPIC_NOTIFYDSC', 'The notice option to a topic on display');

define('_MI_BLUESBB_GLOBAL_NOTIFY', 'Module whole');
define('_MI_BLUESBB_GLOBAL_NOTIFYDSC', 'The notice option in the whole module');

define('_MI_BLUESBB_THREAD_NEWPOST_NOTIFY', 'Post of a reply');
define('_MI_BLUESBB_THREAD_NEWPOST_NOTIFYCAP', 'It notifies, when a reply is contributed in this thread.');
define('_MI_BLUESBB_THREAD_NEWPOST_NOTIFYDSC', 'It notifies, when a reply is contributed in this thread.');
define('_MI_BLUESBB_THREAD_NEWPOST_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE}: The reply was contributed in the thread.');

define('_MI_BLUESBB_TOPIC_NEWTHREAD_NOTIFY', 'New thread');
define('_MI_BLUESBB_TOPIC_NEWTHREAD_NOTIFYCAP', 'It notifies, when there is contribution of a new thread in this topic.');
define('_MI_BLUESBB_TOPIC_NEWTHREAD_NOTIFYDSC', 'It notifies, when there is contribution of a new thread in this topic.');
define('_MI_BLUESBB_TOPIC_NEWTHREAD_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE}: The new thread was contributed.');

define('_MI_BLUESBB_GLOBAL_NEWPOST_NOTIFY', 'New contribution');
define('_MI_BLUESBB_GLOBAL_NEWPOST_NOTIFYCAP', 'It notifies, when there is contribution of a new thread or a reply.');
define('_MI_BLUESBB_GLOBAL_NEWPOST_NOTIFYDSC', 'It notifies, when there is contribution of a new thread or a reply.');
define('_MI_BLUESBB_GLOBAL_NEWPOST_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE}: There was new contribution.');

define('_MI_BLUESBB_TOPIC_NEWPOST_NOTIFY', 'New contribution');
define('_MI_BLUESBB_TOPIC_NEWPOST_NOTIFYCAP', 'It notifies, when there is new contribution in this topic.');
define('_MI_BLUESBB_TOPIC_NEWPOST_NOTIFYDSC', 'It notifies, when there is new contribution in this topic.');
define('_MI_BLUESBB_TOPIC_NEWPOST_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE}: There was new contribution in a topic.');

define('_MI_BLUESBB_GLOBAL_NEWFULLPOST_NOTIFY', 'New contribution(A message is also included)');
define('_MI_BLUESBB_GLOBAL_NEWFULLPOST_NOTIFYCAP', 'It notifies, when there is contribution of a new thread or a reply.(A message is also included)');
define('_MI_BLUESBB_GLOBAL_NEWFULLPOST_NOTIFYDSC', 'It notifies, when there is contribution of a new thread or a reply.(A message is also included)');
define('_MI_BLUESBB_GLOBAL_NEWFULLPOST_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE}: New contribution(A message is also included)');
