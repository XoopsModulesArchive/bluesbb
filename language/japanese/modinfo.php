<?php

// Module Info

// The name of this module
define('_MI_BLUESBB_NAME', 'BluesBB');

// A brief description of this module
define('_MI_BLUESBB_DESC', 'XOOPS掲示板モジュール');

// Names of blocks for this module (Not all module has blocks)
define('_MI_BLUESBB_BNAME1', 'BluesBBでの最近の投稿(投稿単位)');
define('_MI_BLUESBB_BNAME2', 'BluesBBでの最近の投稿(投稿単位＆シンプル)');
define('_MI_BLUESBB_BNAME3', 'カテゴリ別トピックメニュー');
define('_MI_BLUESBB_BNAME4', 'BluesBBでの最近の投稿(スレッド単位)');
define('_MI_BLUESBB_BNAME5', 'BluesBBでの最近の投稿(スレッド単位＆シンプル)');

define('_MI_BLUESBB_ADMENU1', 'カテゴリ設定');
define('_MI_BLUESBB_ADMENU2', 'トピック設定');
define('_MI_BLUESBB_ADMENU3', 'ブロック・グループ設定');

// Title of config items
// Description of each config items
define('_MI_BLUESBB_INDEXREDIRECT', 'インデックスリダイレクト');
define('_MI_BLUESBB_INDEXREDIRECTDSC', 'トピックが１つだけだった場合、index.phpを表示しても意味が無いので、topic.phpへリダイレクトさせます。');
define('_MI_BLUESBB_PERTH', 'インデックススレッド');
define('_MI_BLUESBB_PERTHDSC', 'index.phpのリストディスプレイに表示させるスレッド数を決めます。');
define('_MI_BLUESBB_TOPICPERTH', 'トピックスレッド');
define('_MI_BLUESBB_TOPICPERTHDSC', 'topic.phpのリストディスプレイに表示させるスレッド数を決めます。');
define('_MI_BLUESBB_SENDMAIL', 'お知らせメール');
define('_MI_BLUESBB_SENDMAILDSC', '投稿があった時に管理人に自動でメールを送るかどうか設定します。');
define('_MI_BLUESBB_MESSAGEMAX', '投稿文字数制限');
define('_MI_BLUESBB_MESSAGEMAXDSC', '投稿メッセージ文字数の最大値を設定します。デフォルトは半角英数換算で10000文字です。');
define('_MI_BLUESBB_PWORD', '禁止ワード');
define('_MI_BLUESBB_PWORDDSC', '禁止ワードを設定します。ここで設定された文字が本文に含まれている場合は投稿できません。ワードはカンマ「,」で区切っていくつでも設定できます。');
define('_MI_BLUESBB_PWORDDEF', 'hogehogeshop.com,オ-プン特価セ－ル,今なら無料サービス');
define('_MI_BLUESBB_NEWTIME', 'NEWマーク表示時間');
define('_MI_BLUESBB_NEWTIMEDSC', '新規投稿にNEWマークを表示させる「時間」を設定します。');

// RMV-NOTIFY
// Notification event descriptions and mail templates
define('_MI_BLUESBB_THREAD_NOTIFY', '表示中のスレッド');
define('_MI_BLUESBB_THREAD_NOTIFYDSC', '表示中のスレッドに対する通知オプション');

define('_MI_BLUESBB_TOPIC_NOTIFY', '表示中のトピック');
define('_MI_BLUESBB_TOPIC_NOTIFYDSC', '表示中のトピックに対する通知オプション');

define('_MI_BLUESBB_GLOBAL_NOTIFY', 'モジュール全体');
define('_MI_BLUESBB_GLOBAL_NOTIFYDSC', 'モジュール全体における通知オプション');

define('_MI_BLUESBB_THREAD_NEWPOST_NOTIFY', '返信の投稿');
define('_MI_BLUESBB_THREAD_NEWPOST_NOTIFYCAP', 'このスレッドにおいて返信が投稿された場合に通知する');
define('_MI_BLUESBB_THREAD_NEWPOST_NOTIFYDSC', 'このスレッドにおいて返信が投稿された場合に通知する');
define('_MI_BLUESBB_THREAD_NEWPOST_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE}: スレッド内に返信が投稿されました');

define('_MI_BLUESBB_TOPIC_NEWTHREAD_NOTIFY', '新規スレッド');
define('_MI_BLUESBB_TOPIC_NEWTHREAD_NOTIFYCAP', 'このトピックにおいて新規スレッドの投稿があった場合に通知する');
define('_MI_BLUESBB_TOPIC_NEWTHREAD_NOTIFYDSC', 'このトピックにおいて新規スレッドの投稿があった場合に通知する');
define('_MI_BLUESBB_TOPIC_NEWTHREAD_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE}: 新規スレッドが投稿されました');

define('_MI_BLUESBB_GLOBAL_NEWPOST_NOTIFY', '新規投稿');
define('_MI_BLUESBB_GLOBAL_NEWPOST_NOTIFYCAP', '新規スレッドまたは返信の投稿があった場合に通知する');
define('_MI_BLUESBB_GLOBAL_NEWPOST_NOTIFYDSC', '新規スレッドまたは返信の投稿があった場合に通知する');
define('_MI_BLUESBB_GLOBAL_NEWPOST_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE}: 新規投稿がありました');

define('_MI_BLUESBB_TOPIC_NEWPOST_NOTIFY', '新規投稿');
define('_MI_BLUESBB_TOPIC_NEWPOST_NOTIFYCAP', 'このトピックにおいて新規投稿があった場合に通知する');
define('_MI_BLUESBB_TOPIC_NEWPOST_NOTIFYDSC', 'このトピックにおいて新規投稿があった場合に通知する');
define('_MI_BLUESBB_TOPIC_NEWPOST_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE}: トピックにて新規投稿がありました');

define('_MI_BLUESBB_GLOBAL_NEWFULLPOST_NOTIFY', '新規投稿（投稿文含む）');
define('_MI_BLUESBB_GLOBAL_NEWFULLPOST_NOTIFYCAP', '新規スレッドまたは返信の投稿があった場合に通知する（投稿文付き）');
define('_MI_BLUESBB_GLOBAL_NEWFULLPOST_NOTIFYDSC', '新規スレッドまたは返信の投稿があった場合に通知する（投稿文付き）');
define('_MI_BLUESBB_GLOBAL_NEWFULLPOST_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE}: 新規投稿（投稿文付き）');
