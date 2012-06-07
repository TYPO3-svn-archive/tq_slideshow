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
