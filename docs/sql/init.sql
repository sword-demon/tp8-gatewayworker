create database tp8_chat;

create table if not exists adsense
(
    id          int auto_increment
    primary key,
    src         varchar(255)             not null,
    url         varchar(255)             null,
    type        varchar(10) default 'my' not null comment 'my个人中心',
    create_time int                      null,
    update_time int(10)                  null
    )
    comment '广告位表' engine = InnoDB;

create index type
    on adsense (type);

create table if not exists article
(
    id            int auto_increment
    primary key,
    user_id       int(20) unsigned default 0    not null comment '发布人',
    title         varchar(255)     default '”“' not null comment '标题',
    content       text                          null comment '内容',
    images        text                          null comment '图片',
    url           varchar(50)      default ''   not null comment '分享链接',
    category_id   int                           null comment '所属分类ID',
    topic_id      int(11) unsigned default 0    null comment '所属话题ID',
    share_count   int unsigned     default 0    not null comment '分享数',
    ding_count    int unsigned     default 0    not null comment '点赞数',
    cai_count     int unsigned     default 0    not null comment '踩数',
    comment_count int unsigned     default 0    not null comment '评论数',
    read_count    int unsigned     default 0    not null comment '浏览数',
    collect_count int unsigned     default 0    not null comment '收藏数',
    create_time   int                           null comment '发布时间',
    update_time   int(10)                       null
    )
    comment '帖子表' engine = InnoDB;

create index category_id
    on article (category_id);

create fulltext index content
    on article (content);

create index topic_id
    on article (topic_id);

create index user_id
    on article (user_id);

create table if not exists article_read_log
(
    id          int auto_increment
    primary key,
    ip          varchar(100) default '”“' not null comment '访问IP',
    article_id  int                       null comment '所属帖子ID',
    user_id     int                       null comment '访问用户',
    create_time int                       null comment '访问时间',
    update_time int(10)                   null comment '更新时间'
    )
    comment '帖子观看记录表' engine = InnoDB
    row_format = DYNAMIC;

create index article_id
    on article_read_log (article_id);

create index ip
    on article_read_log (ip);

create index user_id
    on article_read_log (user_id);

create table if not exists blacklist
(
    id          int auto_increment
    primary key,
    black_id    int unsigned default 0 not null comment '拉黑id',
    user_id     int unsigned default 0 not null comment '用户id',
    create_time int                    null,
    update_time int(10)                null
    )
    comment '黑名单表' engine = InnoDB;

create index black_id
    on blacklist (black_id);

create index user_id
    on blacklist (user_id);

create table if not exists category
(
    id          int auto_increment
    primary key,
    title       varchar(5)                                  not null,
    type        enum ('article', 'topic') default 'article' not null comment '类型：article|topic',
    status      tinyint(1) unsigned       default 1         not null comment '0 禁用 1启用',
    create_time int                                         null,
    update_time int(10)                                     null
    )
    comment '分类表' engine = InnoDB;

create index status
    on category (status);

create index type
    on category (type);

create table if not exists collection
(
    id          int auto_increment
    primary key,
    article_id  int unsigned default 0 not null comment '帖子id',
    user_id     int unsigned default 0 not null comment '用户id',
    create_time int                    null,
    update_time int(10)                null
    )
    comment '收藏表' engine = InnoDB
    row_format = DYNAMIC;

create index article_id
    on collection (article_id);

create index user_id
    on collection (user_id);

create table if not exists comment
(
    id          int auto_increment
    primary key,
    article_id  int                        null comment '帖子ID',
    user_id     int(20) unsigned default 0 not null comment '评论人',
    reply_count int unsigned     default 0 not null comment '回复数',
    content     text                       null comment '评论内容',
    comment_id  int                        null comment '回复评论ID',
    quote       mediumtext                 null comment '引用回复',
    create_time int                        null,
    update_time int(10)                    null
    )
    comment '评论表' engine = InnoDB;

create index article_id
    on comment (article_id);

create index comment_id
    on comment (comment_id);

create index user_id
    on comment (user_id);

create table if not exists conversation_message
(
    id              int auto_increment
    primary key,
    user_id         int(20) unsigned default 0 not null comment '发送者id',
    conversation_id int unsigned     default 0 not null comment '会话id',
    message_id      int unsigned     default 0 not null comment '消息id',
    create_time     int                        null,
    update_time     int(10)                    null
    )
    comment '会话与聊天信息关联表' engine = InnoDB
    row_format = DYNAMIC;

create index conversation_id
    on conversation_message (conversation_id);

create index message_id
    on conversation_message (message_id);

create index user_id
    on conversation_message (user_id);

create table if not exists feedback
(
    id          int auto_increment
    primary key,
    user_id     int unsigned default 0      not null comment '会话ID',
    content     text                        null comment '反馈内容',
    images      mediumtext                  null comment '图片',
    type        varchar(10)  default 'user' null comment '类型：user用户，worker工作人员',
    create_time int                         null,
    update_time int(10)                     null
    )
    comment '反馈表' engine = InnoDB;

create index type
    on feedback (type);

create index user_id
    on feedback (user_id);

create table if not exists follow
(
    id          int auto_increment
    primary key,
    follow_id   int unsigned default 0 not null comment '关注id',
    user_id     int unsigned default 0 not null comment '用户id',
    create_time int                    null,
    update_time int(10)                null
    )
    comment '关注表' engine = InnoDB;

create index follow_id
    on follow (follow_id);

create index user_id
    on follow (user_id);

create table if not exists im_conversation
(
    id            int auto_increment
    primary key,
    user_id       int(20) unsigned default 0    not null comment '发送者id',
    target_id     int(20) unsigned default 0    not null comment '接收者id',
    unread_count  int unsigned     default 0    not null comment '未读消息数量',
    last_msg_note varchar(255)     default '”“' not null comment '最后一条消息',
    create_time   int                           null comment '发布时间',
    update_time   int(10)                       null comment '最后一条消息时间'
    )
    comment '聊天会话表' engine = InnoDB
    row_format = DYNAMIC;

create index target_id
    on im_conversation (target_id);

create index update_time
    on im_conversation (update_time);

create index user_id
    on im_conversation (user_id);

create table if not exists im_message
(
    id                 int auto_increment
    primary key,
    user_id            int(20) unsigned    default 0      not null comment '发送者id',
    target_id          int(20) unsigned    default 0      not null comment '接收者id',
    is_revoke          tinyint(1) unsigned default 0      not null comment '是否撤回 0未撤回 1已撤回',
    is_push            tinyint(1) unsigned default 0      not null comment '是否推送消息 0否 1是',
    type               varchar(10)         default 'text' not null comment '消息类型 text文本',
    state              int(4) unsigned     default 0      not null comment '消息状态 int 0发送中，100发送成功，-100 发送失败，-200 禁止发送（内容不合法）',
    body               text                               null comment '消息内容',
    create_time        int(10)                            null comment '发布时间',
    update_time        int(10)                            null comment '更新时间',
    client_create_time int(12)                            null comment '客户端创建消息的时间戳'
    )
    comment '聊天消息表' engine = InnoDB
    row_format = DYNAMIC;

create index is_push
    on im_message (is_push);

create index state
    on im_message (state);

create index user_id_target_id
    on im_message (user_id, target_id);

create table if not exists report
(
    id          int auto_increment
    primary key,
    user_id     int unsigned default 0         not null comment '用户ID',
    content     text                           null comment '举报内容',
    report_uid  int                            null comment '被举报用户ID',
    state       varchar(10)  default 'pending' null comment '处理状态：pending处理中，success成功，fail失败',
    create_time int                            null,
    update_time int(10)                        null
    )
    comment '举报表' engine = InnoDB
    row_format = DYNAMIC;

create index report_uid
    on report (report_uid);

create index state
    on report (state);

create index user_id
    on report (user_id);

create table if not exists role
(
    id          int auto_increment
    primary key,
    name        varchar(80)  not null comment '角色名称',
    `desc`      varchar(255) null comment '角色描述',
    create_time int          null comment '创建时间',
    update_time int(10)      null
    )
    comment '角色表' engine = InnoDB
    row_format = DYNAMIC;

create table if not exists support
(
    id          int auto_increment
    primary key,
    user_id     int unsigned        default 0 not null comment '发布人',
    article_id  int unsigned        default 0 not null comment '帖子ID',
    type        tinyint(1) unsigned default 1 not null comment '0踩 1顶',
    create_time int                           null,
    update_time int(10)                       null
    )
    comment '顶/踩表' engine = InnoDB;

create index article_id
    on support (article_id);

create index user_id
    on support (user_id);

create table if not exists topic
(
    id            int auto_increment
    primary key,
    title         varchar(80)   not null comment '话题名称',
    cover         varchar(255)  not null comment '封面',
    `desc`        varchar(255)  not null comment '话题描述',
    create_time   int           null comment '创建时间',
    update_time   int(10)       null,
    category_id   int           null comment '所属分类ID',
    article_count int default 0 null comment '动态数'
    )
    comment '话题表' engine = InnoDB;

create index category_id
    on topic (category_id);

create fulltext index title
    on topic (title);

create table if not exists upgradation
(
    id              int auto_increment
    primary key,
    appid           varchar(100)                         null comment '应用的AppID',
    name            varchar(100)                         null comment '应用名称',
    title           varchar(255)                         null comment '更新标题',
    contents        text                                 null comment '更新内容',
    platform        varchar(255)                         null comment '更新平台，["Android"] || ["iOS"] || ["Android","iOS"]',
    type            varchar(30)                          null comment '安装包类型，native_app,原生App安装包 || wgt,Wgt资源包',
    version         varchar(10)                          null comment '当前包版本号，必须大于当前线上发行版本号',
    min_uni_version varchar(10)                          null comment '原生App最低版本',
    url             varchar(255)                         null comment '下载链接',
    stable_publish  tinyint(1)  default 0                null comment '是否上线发行',
    is_silently     tinyint(1)  default 0                null comment '是否静默更新 默认0',
    is_mandatory    tinyint(1)  default 0                null comment '是否强制更新 默认0',
    uni_platform    varchar(10) default 'android'        null comment 'uni平台信息，如：mp-weixin/web/ios/android',
    create_time     int                                  null comment '上传时间',
    update_time     int(10)                              null comment '更新时间',
    create_env      varchar(15) default 'upgrade-center' null comment '创建来源，upgrade-center：升级中心管理员创建',
    store_list      text                                 null comment '发布的应用市场	[{	id:"应用ID",	name:"应用名称",scheme:"应用scheme",enable:"是否启用，bool",priority:"优先级，按照从大到小排序，int" }]'
    )
    comment '升级表' engine = InnoDB;

create index appid
    on upgradation (appid);

create index platform
    on upgradation (platform);

create index stable_publish
    on upgradation (stable_publish);

create index type
    on upgradation (type);

create index uni_platform
    on upgradation (uni_platform);

create table if not exists user
(
    id             int auto_increment
    primary key,
    username       varchar(80)                   null,
    avatar         varchar(255)                  null,
    password       varchar(255)                  null,
    phone          varchar(11)                   null,
    email          varchar(255)                  null,
    status         tinyint(1) unsigned default 1 not null comment '0 禁用 1启用',
    age            int(3) unsigned     default 0 not null comment '年龄',
    sex            tinyint(1) unsigned default 0 not null comment '0不限 1男 2女',
    qg             tinyint(1) unsigned default 0 not null comment '0不限',
    job            varchar(10)                   null comment '职业',
    path           varchar(255)                  null comment '所在地',
    birthday       varchar(20)                   null comment '生日',
    `desc`         tinytext                      null comment '个性签名',
    wx_openid      varchar(255)                  null comment '微信openid',
    wx_unionid     varchar(255)                  null comment '微信unioinid',
    create_time    int(10)                       null comment '注册时间',
    update_time    int(10)                       null comment '更新时间',
    fans_count     int(10)             default 0 null comment '粉丝数',
    follows_count  int(10)             default 0 null comment '关注数',
    articles_count int(10)             default 0 null comment '帖子数',
    comments_count int(10)             default 0 null comment '评论数',
    constraint email
    unique (email),
    constraint phone
    unique (phone),
    constraint wx_openid_wx_unionid
    unique (wx_openid, wx_unionid)
    )
    comment '用户表' engine = InnoDB;

create index status
    on user (status);

create index username
    on user (username);

create table if not exists user_role
(
    id          int auto_increment
    primary key,
    role_id     int unsigned default 0 not null comment '角色id',
    user_id     int unsigned default 0 not null comment '用户id',
    create_time int                    null,
    update_time int(10)                null
    )
    comment '用户角色关系表' engine = InnoDB
    row_format = DYNAMIC;

create index black_id
    on user_role (role_id);

create index user_id
    on user_role (user_id);

