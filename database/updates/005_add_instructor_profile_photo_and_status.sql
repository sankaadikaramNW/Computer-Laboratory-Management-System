-- Update database to support instructor profile photos and archived status
ALTER TABLE instructors 
ADD COLUMN profile_photo VARCHAR(255) NULL AFTER email,
ADD COLUMN photo_uploaded_at TIMESTAMP NULL AFTER profile_photo;

ALTER TABLE instructors
MODIFY COLUMN status ENUM('active', 'inactive', 'archived') DEFAULT 'active';
