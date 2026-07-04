-- Update database to support camps management and camp isolation
CREATE TABLE IF NOT EXISTS camps (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    code VARCHAR(50) NOT NULL UNIQUE,
    address TEXT NULL,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Insert default camp SLAF Ekala
INSERT INTO camps (id, name, code, address, status) VALUES 
(1, 'SLAF Trade Training School Ekala', 'TTS-EKA', 'Ekala, Ja-Ela, Sri Lanka', 'active')
ON DUPLICATE KEY UPDATE name=name;

-- Add camp_id columns to related tables
ALTER TABLE users 
ADD COLUMN camp_id INT NULL,
ADD CONSTRAINT fk_users_camp FOREIGN KEY (camp_id) REFERENCES camps(id) ON DELETE SET NULL;

ALTER TABLE laboratories 
ADD COLUMN camp_id INT NULL,
ADD CONSTRAINT fk_laboratories_camp FOREIGN KEY (camp_id) REFERENCES camps(id) ON DELETE SET NULL;

ALTER TABLE allocations 
ADD COLUMN camp_id INT NULL,
ADD CONSTRAINT fk_allocations_camp FOREIGN KEY (camp_id) REFERENCES camps(id) ON DELETE SET NULL;

-- Associate existing entities with the default camp
UPDATE users SET camp_id = 1 WHERE camp_id IS NULL;
UPDATE laboratories SET camp_id = 1 WHERE camp_id IS NULL;
UPDATE allocations SET camp_id = 1 WHERE camp_id IS NULL;
