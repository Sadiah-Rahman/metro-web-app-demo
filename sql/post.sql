CREATE TABLE posts (
                       id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                       user_id INT UNSIGNED NOT NULL,
                       content TEXT,
                       image VARCHAR(255) DEFAULT NULL,
                       created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                       FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
