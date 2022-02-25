CREATE TABLE tt_content (
    tx_charts_chartdata int(10) unsigned DEFAULT 0 NOT NULL,
);
CREATE TABLE tx_charts_domain_model_chartdata (
    title varchar(255) DEFAULT '' NOT NULL,
    type tinyint(1) unsigned DEFAULT '0' NOT NULL,
    labels text,
    datasets text,
    datasets_labels text,
    spreadsheet_labels text,
    spreadsheet_datasets text,
    spreadsheet_datasets_labels text,
    spreadsheet_assets int(11) unsigned DEFAULT '0' NOT NULL,
    background_colors text,
    border_colors text,
);
