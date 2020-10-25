#
# Table structure for table `bluesbb`
#

CREATE TABLE bluesbb (
    post_id      INT(8) UNSIGNED       NOT NULL AUTO_INCREMENT,
    topic_id     SMALLINT(3) UNSIGNED  NOT NULL DEFAULT '1',
    thread_id    INT(6) UNSIGNED       NOT NULL DEFAULT '1',
    res_id       SMALLINT(3) UNSIGNED  NOT NULL DEFAULT '0',
    hig_id       SMALLINT(3) UNSIGNED  NOT NULL DEFAULT '0',
    name         VARCHAR(50)           NOT NULL DEFAULT '',
    mail         VARCHAR(100)          NOT NULL DEFAULT '',
    url          VARCHAR(255)                   DEFAULT NULL,
    title        VARCHAR(100)                   DEFAULT NULL,
    message      TEXT                  NOT NULL,
    pass         VARCHAR(32)                    DEFAULT NULL,
    post_time    INT(10) UNSIGNED      NOT NULL DEFAULT '0',
    res_time     INT(10) UNSIGNED      NOT NULL DEFAULT '0',
    uid          MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
    attachsig    TINYINT(1)            NOT NULL DEFAULT '0',
    poster_ip    VARCHAR(20)                    DEFAULT NULL,
    poster_host  VARCHAR(80)                    DEFAULT NULL,
    poster_agent VARCHAR(255)                   DEFAULT NULL,
    PRIMARY KEY (post_id),
    KEY topic_id (topic_id),
    KEY thread_id (thread_id),
    KEY uid (uid)
)
    ENGINE = ISAM;
# --------------------------------------------------------

#
# Table structure for table `bluesbb_topic`
#

CREATE TABLE bluesbb_topic (
    topic_id        SMALLINT(3) UNSIGNED NOT NULL AUTO_INCREMENT,
    topic_name      VARCHAR(150)         NOT NULL DEFAULT '',
    topic_access    TINYINT(2)           NOT NULL DEFAULT '1',
    topic_group     TINYINT(2)           NOT NULL DEFAULT '1',
    topic_info      TEXT,
    cat_id          INT(2)               NOT NULL DEFAULT '1',
    allow_sig       ENUM ('0','1')       NOT NULL DEFAULT '1',
    list_display    ENUM ('0','1')       NOT NULL DEFAULT '1',
    thread_per_page TINYINT(3) UNSIGNED  NOT NULL DEFAULT '5',
    res_per_thread  TINYINT(3) UNSIGNED  NOT NULL DEFAULT '10',
    tree_per_page   TINYINT(3) UNSIGNED  NOT NULL DEFAULT '5',
    order_per_page  TINYINT(3) UNSIGNED  NOT NULL DEFAULT '20',
    res_limit       SMALLINT(3) UNSIGNED NOT NULL DEFAULT '299',
    topic_style     ENUM ('1','2','3')   NOT NULL DEFAULT '1',
    style_choice    VARCHAR(5)           NOT NULL DEFAULT '1:2:3',
    topic_order     SMALLINT(3)          NOT NULL DEFAULT '1',
    PRIMARY KEY (topic_id)
)
    ENGINE = ISAM;
# --------------------------------------------------------

#
# Table structure for table `bluesbb_categories`
#

CREATE TABLE bluesbb_categories (
    cat_id    SMALLINT(2) UNSIGNED NOT NULL AUTO_INCREMENT,
    cat_title VARCHAR(100)         NOT NULL DEFAULT '',
    cat_order SMALLINT(2)          NOT NULL DEFAULT '1',
    PRIMARY KEY (cat_id)
)
    ENGINE = ISAM;
