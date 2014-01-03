#
# Table structure for table 'pages'
#
CREATE TABLE pages (
	tx_tqslideshow_images text,
	tx_tqslideshow_image_title text,
	tx_tqslideshow_image_link text,
	tx_tqslideshow_image_autochange int(11) DEFAULT '0' NOT NULL
);

CREATE TABLE tt_content (
	tx_tqslideshow_image text NOT NULL,
	tx_tqslideshow_content text NOT NULL,
);


#
# Table structure for table 'tq_slideshow'
#

#
# Table structure for table 'tq_slideshow'
#
CREATE TABLE tx_tq_slideshow (
  uid int(11) NOT NULL auto_increment,
    pid int(11) DEFAULT '0' NOT NULL,
    title varchar(255) DEFAULT '',

    tstamp int(11) unsigned DEFAULT '0' NOT NULL,
    crdate int(11) unsigned DEFAULT '0' NOT NULL,
    cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
    deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
    hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
    starttime int(11) unsigned DEFAULT '0' NOT NULL,
    endtime int(11) unsigned DEFAULT '0' NOT NULL,

    mode varchar(255) DEFAULT '',
    changeTime int(11) unsigned DEFAULT '0' NOT NULL,
    imageWidth  int(11) unsigned DEFAULT '0' NOT NULL,
    imageHeight  int(11) unsigned DEFAULT '0' NOT NULL,
    pageSelector varchar(255) DEFAULT '',

    slideshow_media varchar(255) DEFAULT '',
    collection_id int(11) unsigned DEFAULT '0' NOT NULL,
    media_mode varchar(255) DEFAULT '',

    showToolbar int(11) unsigned DEFAULT '0' NOT NULL,
    showThumbnails int(11) unsigned DEFAULT '0' NOT NULL,
    showPaging int(11) unsigned DEFAULT '0' NOT NULL,

    templateFile varchar(255) DEFAULT '',
    keyEvents  int(11) unsigned DEFAULT '0' NOT NULL,

    indexed int(11) unsigned DEFAULT '0' NOT NULL,
    published int(11) unsigned DEFAULT '0' NOT NULL,

    PRIMARY KEY (uid),
    KEY parent (pid),
    KEY deleted (deleted),
    KEY hidden (hidden),
    KEY starttime (starttime),
    KEY endtime (endtime),
    KEY indexed (indexed)
) ENGINE=InnoDB;


#
# Table structure for table 'tx_tq_slideshow_images'
#
CREATE TABLE tx_tq_slideshow_media (
    uid int(11) NOT NULL auto_increment,
    pid int(11) DEFAULT '0' NOT NULL,
    slideshow_id int(11) NOT NULL ,
    collection_id int(11) NOT NULL ,
    title varchar(255) DEFAULT '',
    media_type varchar(255) DEFAULT '',


    video_type varchar(255) DEFAULT '',
    media_video_youtube varchar(255) DEFAULT '' NOT NULL,
    media_image_preview int(11) unsigned DEFAULT '0' NOT NULL,
    media_video_flash int(11) unsigned DEFAULT '0' NOT NULL,
    media_video_theora int(11) unsigned DEFAULT '0' NOT NULL,
    media_video_h264 int(11) unsigned DEFAULT '0' NOT NULL,

    tstamp int(11) unsigned DEFAULT '0' NOT NULL,
    crdate int(11) unsigned DEFAULT '0' NOT NULL,
    cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
    deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
    hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
    starttime int(11) unsigned DEFAULT '0' NOT NULL,
    endtime int(11) unsigned DEFAULT '0' NOT NULL,
    sorting int(11) unsigned DEFAULT '0' NOT NULL,

    image varchar(255) DEFAULT '',

    thumbnail_alt varchar(255) DEFAULT '',


    link_type varchar(255) DEFAULT '',
    link_page varchar(255) DEFAULT '',
    link_video varchar(255) DEFAULT '',

    is_lightbox int(11) unsigned DEFAULT '0' NOT NULL,

    effect_forward varchar(255) DEFAULT '',
    effect_backward varchar(255) DEFAULT '',
    preview text,
    description text,

    indexed int(11) unsigned DEFAULT '0' NOT NULL,
    published int(11) unsigned DEFAULT '0' NOT NULL,

    PRIMARY KEY (uid),
    KEY parent (pid),
    KEY deleted (deleted),
    KEY hidden (hidden),
    KEY starttime (starttime),
    KEY endtime (endtime),
    KEY indexed (indexed)
) ENGINE=InnoDB;


#
# Table structure for table 'tx_tq_slideshow_collection'
#
CREATE TABLE tx_tq_slideshow_collection (
    uid int(11) NOT NULL auto_increment,
    pid int(11) DEFAULT '0' NOT NULL,
    title varchar(255) DEFAULT '',
    mode varchar(255) DEFAULT '',

    slideshow_media varchar(255) DEFAULT '',
    directory_uploader varchar(255) DEFAULT '',

    tstamp int(11) unsigned DEFAULT '0' NOT NULL,
    crdate int(11) unsigned DEFAULT '0' NOT NULL,
    cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
    deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
    hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
    starttime int(11) unsigned DEFAULT '0' NOT NULL,
    endtime int(11) unsigned DEFAULT '0' NOT NULL,
    sorting int(11) unsigned DEFAULT '0' NOT NULL,


    indexed int(11) unsigned DEFAULT '0' NOT NULL,
    published int(11) unsigned DEFAULT '0' NOT NULL,

    PRIMARY KEY (uid),
    KEY parent (pid),
    KEY deleted (deleted),
    KEY hidden (hidden),
    KEY starttime (starttime),
    KEY endtime (endtime),
    KEY indexed (indexed)
) ENGINE=InnoDB;


