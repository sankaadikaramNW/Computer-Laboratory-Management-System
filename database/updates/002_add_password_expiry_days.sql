-- Update database to support password expiry, policy configurations, and history
ALTER TABLE users 
ADD COLUMN last_password_change DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
ADD COLUMN failed_attempts INT DEFAULT 0,
ADD COLUMN force_password_change TINYINT(1) DEFAULT 0,
ADD COLUMN password_expiry_days INT DEFAULT 90;

CREATE TABLE IF NOT EXISTS password_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    changed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;
