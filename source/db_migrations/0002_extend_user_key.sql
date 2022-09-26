INSERT
INTO wr_db_changelog
(timestamp, version, status)
VALUES
    (NOW(), '0002', 'started')
;
COMMIT;

ALTER TABLE wr_user_keys CHANGE COLUMN user_key user_key VARCHAR(50) NOT NULL ;

INSERT
INTO wr_db_changelog
(timestamp, version, status)
VALUES
    (NOW(), '0002', 'complete')
;
COMMIT;
