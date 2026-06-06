-- Update database to support session completion tracking
ALTER TABLE allocations
ADD COLUMN session_status VARCHAR(50) NOT NULL DEFAULT 'Scheduled',
ADD COLUMN instructor_remarks TEXT NULL,
ADD COLUMN completed_at DATETIME NULL,
ADD COLUMN completed_by INT NULL,
ADD CONSTRAINT fk_completed_by FOREIGN KEY (completed_by) REFERENCES users(id) ON DELETE SET NULL;
