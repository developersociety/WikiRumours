SET autocommit=1;
INSERT
    INTO `wr_db_changelog`
        (timestamp, version, status)
    VALUES
        (NOW(), '0001', 'started')
;
COMMIT;

ALTER
    TABLE wr_rumours
    ADD response_who int(9) UNSIGNED,
    ADD response_start_date date,
    ADD response_duration_weeks int,
    ADD reponse_completion_date date,
    ADD response_completed bool NOT NULL default false,
    ADD response_outcomes text COLLATE utf8_unicode_ci NOT NULL,
    ADD FOREIGN KEY (response_who) REFERENCES wr_users(user_id)
;

INSERT
    INTO wr_db_changelog
        (timestamp, version, status)
    VALUES
        (NOW(), '0001', 'complete')
;
COMMIT;
