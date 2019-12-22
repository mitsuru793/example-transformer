SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS users;
CREATE TABLE IF NOT EXISTS users
(
    id   INTEGER AUTO_INCREMENT,
    name VARCHAR(50),
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

SET FOREIGN_KEY_CHECKS = 1;
