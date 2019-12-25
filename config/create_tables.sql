SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS users;
CREATE TABLE IF NOT EXISTS users
(
    id   INTEGER AUTO_INCREMENT,
    name VARCHAR(50),
    PRIMARY KEY (id)
);

DROP TABLE IF EXISTS ui_faces_users;
CREATE TABLE IF NOT EXISTS ui_faces_users
(
    id         INTEGER AUTO_INCREMENT,
    name       VARCHAR(50),
    email      VARCHAR(50),
    position   VARCHAR(50),
    photo_url  VARCHAR(500),
    photo_file VARCHAR(500),
    PRIMARY KEY (id)
);

DROP TABLE IF EXISTS posts;
CREATE TABLE IF NOT EXISTS posts
(
    id                INTEGER AUTO_INCREMENT,
    author_id         INTEGER,
    title             VARCHAR(100),
    content           VARCHAR(400),
    viewable_user_ids TEXT,
    year              INTEGER,
    PRIMARY KEY (id),
    FOREIGN KEY (author_id) REFERENCES users (id)
);

DROP TABLE IF EXISTS tags;
CREATE TABLE IF NOT EXISTS tags
(
    id   INTEGER AUTO_INCREMENT,
    name VARCHAR(50),
    PRIMARY KEY (id)
);

DROP TABLE IF EXISTS posts_tags;
CREATE TABLE IF NOT EXISTS posts_tags
(
    post_id INTEGER,
    tag_id  INTEGER,
    PRIMARY KEY (post_id, tag_id)
);

DROP TABLE IF EXISTS http_request_caches;
CREATE TABLE IF NOT EXISTS http_request_caches
(
    id                     INTEGER AUTO_INCREMENT,
    http_response_cache_id INTEGER,
    method                 VARCHAR(10),
    path                   TEXT,
    options                JSON,
    PRIMARY KEY (id),
    FOREIGN KEY (http_response_cache_id) REFERENCES http_response_caches (id)
);

DROP TABLE IF EXISTS http_response_caches;
CREATE TABLE IF NOT EXISTS http_response_caches
(
    id                    INTEGER AUTO_INCREMENT,
    http_request_cache_id INTEGER,
    response_phrase       TEXT,
    status_code           INTEGER,
    headers               JSON,
    protocol_version      VARCHAR(10),
    body                  TEXT,
    PRIMARY KEY (id),
    FOREIGN KEY (http_request_cache_id) REFERENCES http_request_caches (id)
);

DROP TABLE IF EXISTS http_request_histories;
CREATE TABLE IF NOT EXISTS http_request_histories
(
    id                    INTEGER AUTO_INCREMENT,
    http_request_cache_id INTEGER,
    PRIMARY KEY (id),
    FOREIGN KEY (http_request_cache_id) REFERENCES http_request_caches (id)
);

SET FOREIGN_KEY_CHECKS = 1;
