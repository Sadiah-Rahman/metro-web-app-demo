-- sql/migrate_create_followers.sql
CREATE TABLE IF NOT EXISTS followers (
                                         id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                                         follower_id INT UNSIGNED NOT NULL,
                                         followed_id INT UNSIGNED NOT NULL,
                                         created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                                         UNIQUE KEY uniq_follow (follower_id, followed_id),
    INDEX idx_followed (followed_id),
    INDEX idx_follower (follower_id),
    FOREIGN KEY (follower_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (followed_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
