/* To log database migrations & status. */
CREATE TABLE `wr_db_changelog` (
    `timestamp` datetime NOT NULL,
    `version` varchar(64) NOT NULL,
    `status` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT
    INTO `wr_db_changelog`
        (timestamp, version, status)
    VALUES
        (NOW(), '0000', 'complete');
