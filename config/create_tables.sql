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
    id        INTEGER AUTO_INCREMENT,
    author_id INTEGER,
    title     VARCHAR(100),
    content     VARCHAR(400),
    viewable_user_ids TEXT,
    year      INTEGER,
    PRIMARY KEY (id),
    FOREIGN KEY (author_id) REFERENCES users (id)
);

SET FOREIGN_KEY_CHECKS = 1;
