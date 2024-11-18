CREATE TABLE enrollment_schema (
    id        int unsigned NOT NULL AUTO_INCREMENT,
    version   varchar(64) NOT NULL,
    timestamp bigint unsigned NOT NULL,
    success   enum('n', 'y') DEFAULT NULL,
    reason    text DEFAULT NULL,

    PRIMARY KEY (id),
    CONSTRAINT idx_enrollment_schema_version UNIQUE (version)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin ROW_FORMAT=DYNAMIC;

CREATE TABLE enrollment_userenrollment (
    id int(10) unsigned NOT NULL AUTO_INCREMENT,
    name    TEXT,
    email    TEXT,
    groups    TEXT,
    status INTEGER,
    user_backend TEXT,
    group_backend TEXT,
    enabled enum ('y', 'n')DEFAULT 'n' NOT NULL,
    secret TEXT,
    allow_password_reset enum ('y', 'n')DEFAULT 'n' NOT NULL,
    etime   BIGINT,
    mtime   BIGINT,
    ctime   BIGINT,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE enrollment_activitylog (
    id int(10) unsigned NOT NULL AUTO_INCREMENT,
    username    TEXT,
    task    TEXT,
    ctime   BIGINT,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO enrollment_schema (version, timestamp, success)
VALUES ('0.1.1', UNIX_TIMESTAMP() * 1000, 'y');
