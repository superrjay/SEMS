-- Create schedules table for multiple schedule entries per section
CREATE TABLE schedules (
    schedule_id INT AUTO_INCREMENT PRIMARY KEY,
    section_id INT NOT NULL,
    schedule_day VARCHAR(20) NOT NULL,
    time_start VARCHAR(10) NOT NULL,
    time_end VARCHAR(10) NOT NULL,
    subject_name VARCHAR(100) NOT NULL,
    room_number VARCHAR(50),
    faculty_name VARCHAR(100),
    FOREIGN KEY (section_id) REFERENCES sections(section_id)
);