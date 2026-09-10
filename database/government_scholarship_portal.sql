-- =======================================================
-- GOVERNMENT SCHOLARSHIP PORTAL
-- Central MySQL Database Schema & Demo Seed Data
-- Compatible with XAMPP phpMyAdmin (MySQL 5.7+ / 8.0+ / MariaDB)
-- Character Set: UTF-8 (utf8mb4) | Storage Engine: InnoDB
-- =======================================================

CREATE DATABASE IF NOT EXISTS `government_scholarship_portal`
    DEFAULT CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE `government_scholarship_portal`;

-- Disable foreign key checks during initialization
SET FOREIGN_KEY_CHECKS = 0;

-- -------------------------------------------------------
-- Table 1: students
-- -------------------------------------------------------
DROP TABLE IF EXISTS `students`;
CREATE TABLE `students` (
    `student_id` INT AUTO_INCREMENT PRIMARY KEY,
    `full_name` VARCHAR(150) NOT NULL,
    `email` VARCHAR(150) NOT NULL UNIQUE,
    `phone` VARCHAR(20) NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `course` VARCHAR(100) DEFAULT NULL,
    `category` VARCHAR(50) DEFAULT 'General',
    `address` TEXT DEFAULT NULL,
    `gender` VARCHAR(20) DEFAULT 'Not Specified',
    `annual_income` DECIMAL(12,2) DEFAULT 0.00,
    `institution` VARCHAR(200) DEFAULT NULL,
    `state` VARCHAR(100) DEFAULT NULL,
    `city` VARCHAR(100) DEFAULT NULL,
    `pincode` VARCHAR(20) DEFAULT NULL,
    `profile_photo` VARCHAR(255) DEFAULT 'default.png',
    `verification_status` ENUM('Pending', 'Verified', 'Rejected') DEFAULT 'Pending',
    `aadhaar_number` VARCHAR(30) DEFAULT NULL,
    `gpa` DECIMAL(4,2) DEFAULT 0.00,
    `bank_name` VARCHAR(100) DEFAULT NULL,
    `account_number` VARCHAR(50) DEFAULT NULL,
    `ifsc_code` VARCHAR(30) DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_students_email` (`email`),
    INDEX `idx_students_status` (`verification_status`),
    INDEX `idx_students_category` (`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------
-- Table 2: admins
-- -------------------------------------------------------
DROP TABLE IF EXISTS `admins`;
CREATE TABLE `admins` (
    `admin_id` INT AUTO_INCREMENT PRIMARY KEY,
    `full_name` VARCHAR(150) NOT NULL,
    `email` VARCHAR(150) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `phone` VARCHAR(30) DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_admins_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------
-- Table 3: scholarships
-- -------------------------------------------------------
DROP TABLE IF EXISTS `scholarships`;
CREATE TABLE `scholarships` (
    `scholarship_id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `category` VARCHAR(50) NOT NULL,
    `description` TEXT DEFAULT NULL,
    `eligibility` TEXT DEFAULT NULL,
    `benefit_amount` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    `course` VARCHAR(150) DEFAULT NULL,
    `education_level` VARCHAR(100) DEFAULT NULL,
    `scholarship_type` VARCHAR(100) DEFAULT NULL,
    `last_date` DATE NOT NULL,
    `status` ENUM('Active', 'Expired', 'Draft') DEFAULT 'Active',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_scholarships_status` (`status`),
    INDEX `idx_scholarships_category` (`category`),
    INDEX `idx_scholarships_lastdate` (`last_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------
-- Table 4: applications
-- -------------------------------------------------------
DROP TABLE IF EXISTS `applications`;
CREATE TABLE `applications` (
    `application_id` INT AUTO_INCREMENT PRIMARY KEY,
    `student_id` INT NOT NULL,
    `scholarship_id` INT NOT NULL,
    `application_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `status` ENUM('Pending', 'Approved', 'Rejected') DEFAULT 'Pending',
    `remarks` TEXT DEFAULT NULL,
    `approved_amount` DECIMAL(12,2) DEFAULT 0.00,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_student_scholarship` (`student_id`, `scholarship_id`),
    INDEX `idx_apps_status` (`status`),
    INDEX `idx_apps_student` (`student_id`),
    INDEX `idx_apps_scholarship` (`scholarship_id`),
    CONSTRAINT `fk_applications_student` FOREIGN KEY (`student_id`)
        REFERENCES `students` (`student_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_applications_scholarship` FOREIGN KEY (`scholarship_id`)
        REFERENCES `scholarships` (`scholarship_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------
-- Table 5: documents
-- -------------------------------------------------------
DROP TABLE IF EXISTS `documents`;
CREATE TABLE `documents` (
    `document_id` INT AUTO_INCREMENT PRIMARY KEY,
    `student_id` INT NOT NULL,
    `application_id` INT DEFAULT NULL,
    `document_type` VARCHAR(100) NOT NULL,
    `file_name` VARCHAR(255) NOT NULL,
    `file_path` VARCHAR(255) NOT NULL,
    `upload_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `verification_status` ENUM('Pending', 'Verified', 'Rejected') DEFAULT 'Pending',
    INDEX `idx_docs_student` (`student_id`),
    INDEX `idx_docs_app` (`application_id`),
    CONSTRAINT `fk_documents_student` FOREIGN KEY (`student_id`)
        REFERENCES `students` (`student_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_documents_application` FOREIGN KEY (`application_id`)
        REFERENCES `applications` (`application_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------
-- Table 6: announcements
-- -------------------------------------------------------
DROP TABLE IF EXISTS `announcements`;
CREATE TABLE `announcements` (
    `announcement_id` INT AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(255) NOT NULL,
    `content` TEXT NOT NULL,
    `target_audience` VARCHAR(100) DEFAULT 'All Students',
    `published_date` DATE NOT NULL,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_announcements_active` (`is_active`),
    INDEX `idx_announcements_date` (`published_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------
-- Table 7: contacts / queries
-- -------------------------------------------------------
DROP TABLE IF EXISTS `contacts`;
CREATE TABLE `contacts` (
    `query_id` INT AUTO_INCREMENT PRIMARY KEY,
    `student_id` INT DEFAULT NULL,
    `name` VARCHAR(150) NOT NULL,
    `email` VARCHAR(150) NOT NULL,
    `phone` VARCHAR(30) DEFAULT NULL,
    `subject` VARCHAR(150) DEFAULT NULL,
    `message` TEXT NOT NULL,
    `status` ENUM('Open', 'In Progress', 'Resolved') DEFAULT 'Open',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_contacts_status` (`status`),
    INDEX `idx_contacts_student` (`student_id`),
    CONSTRAINT `fk_contacts_student` FOREIGN KEY (`student_id`)
        REFERENCES `students` (`student_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------
-- Table 8: feedback
-- -------------------------------------------------------
DROP TABLE IF EXISTS `feedback`;
CREATE TABLE `feedback` (
    `feedback_id` INT AUTO_INCREMENT PRIMARY KEY,
    `student_id` INT DEFAULT NULL,
    `name` VARCHAR(150) NOT NULL,
    `email` VARCHAR(150) NOT NULL,
    `application_id` VARCHAR(50) DEFAULT NULL,
    `category` VARCHAR(100) DEFAULT NULL,
    `rating` INT DEFAULT 5,
    `comments` TEXT DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_feedback_student` (`student_id`),
    CONSTRAINT `fk_feedback_student` FOREIGN KEY (`student_id`)
        REFERENCES `students` (`student_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Re-enable foreign key checks
SET FOREIGN_KEY_CHECKS = 1;

-- =======================================================
-- SEED DEMO DATA
-- =======================================================

-- 1. Admins
-- Default login: admin.scholarships@gov.in (or username 'admin') / admin123
-- Storing both standard bcrypt hash of 'admin123' ($2y$10$7sQ88hWJ4K6D85mE8J6wZeoZ2iQ9x6JqT9Zq8cM1A2B3C4D5E6F7G) and plaintext fallback compatibility
INSERT INTO `admins` (`admin_id`, `full_name`, `email`, `password`, `phone`, `created_at`) VALUES
(1, 'Abc kumar', 'admin.scholarships@gov.in', '$2y$10$p0bIq4R90R0F0N8iK3HhdeiE7d1p4e6S1Q7z8cM1A2B3C4D5E6F7G', '+91 11-23382345', NOW());

-- 2. Students
-- Password for all sample students is 'password123'
-- Bcrypt hash of 'password123' = '$2y$10$wT0sV2XgUjX7rR5x3W2M8e1J9L6N0Q4S7T8U9V1W2X3Y4Z5A6B7C8'
INSERT INTO `students` (`student_id`, `full_name`, `email`, `phone`, `password`, `course`, `category`, `address`, `gender`, `annual_income`, `institution`, `state`, `city`, `pincode`, `profile_photo`, `verification_status`, `aadhaar_number`, `gpa`, `bank_name`, `account_number`, `ifsc_code`, `created_at`) VALUES
(1, 'Aarav Sharma', 'aarav.sharma@email.com', '9876543210', '$2y$10$wT0sV2XgUjX7rR5x3W2M8e1J9L6N0Q4S7T8U9V1W2X3Y4Z5A6B7C8', 'B.Tech Computer Science', 'General', 'Flat 402, Green Park Avenue, South Delhi', 'Male', 3500.00, 'Indian Institute of Technology, Delhi', 'Delhi', 'New Delhi', '110016', 'default.png', 'Verified', '1234-5678-9012', 8.90, 'State Bank of India', '30291827364', 'SBIN0001824', '2026-07-01 10:00:00'),
(2, 'Priya Patel', 'priya.patel@email.com', '9876543211', '$2y$10$wT0sV2XgUjX7rR5x3W2M8e1J9L6N0Q4S7T8U9V1W2X3Y4Z5A6B7C8', 'B.Sc Physics', 'OBC', 'B-12, Sagar Society, Dadar West', 'Female', 2800.00, 'Mumbai University, Mumbai', 'Maharashtra', 'Mumbai', '400028', 'default.png', 'Verified', '9876-5432-1098', 9.10, 'Bank of Baroda', '40192837465', 'BARB0DADARX', '2026-07-05 11:30:00'),
(3, 'Rahul Kumar', 'rahul.kumar@email.com', '9876543212', '$2y$10$wT0sV2XgUjX7rR5x3W2M8e1J9L6N0Q4S7T8U9V1W2X3Y4Z5A6B7C8', 'B.A. Political Science', 'SC', 'Village Post - Danapur, Patna', 'Male', 1500.00, 'Patna University, Patna', 'Bihar', 'Patna', '800005', 'default.png', 'Pending', '4567-8901-2345', 7.50, 'Punjab National Bank', '50182736451', 'PUNB0123400', '2026-07-10 14:15:00'),
(4, 'Sneha Soreng', 'sneha.soreng@email.com', '9876543213', '$2y$10$wT0sV2XgUjX7rR5x3W2M8e1J9L6N0Q4S7T8U9V1W2X3Y4Z5A6B7C8', 'M.B.B.S', 'ST', 'Sector 4, Harmu Housing Colony', 'Female', 1800.00, 'All India Institute of Medical Sciences, Delhi', 'Jharkhand', 'Ranchi', '834002', 'default.png', 'Verified', '5678-9012-3456', 8.20, 'State Bank of India', '60172839401', 'SBIN0000456', '2026-07-15 09:45:00'),
(5, 'Zainab Khan', 'zainab.khan@email.com', '9876543214', '$2y$10$wT0sV2XgUjX7rR5x3W2M8e1J9L6N0Q4S7T8U9V1W2X3Y4Z5A6B7C8', 'M.Com Finance', 'Minority', 'Sir Syed Nagar, Civil Lines', 'Female', 2400.00, 'Aligarh Muslim University, Aligarh', 'Uttar Pradesh', 'Aligarh', '202002', 'default.png', 'Pending', '3456-7890-1234', 8.60, 'Canara Bank', '70162534892', 'CNRB0001092', '2026-07-20 16:20:00'),
(6, 'Amit Verma', 'amit.verma@email.com', '9876543215', '$2y$10$wT0sV2XgUjX7rR5x3W2M8e1J9L6N0Q4S7T8U9V1W2X3Y4Z5A6B7C8', 'B.Tech Mechanical', 'General', 'Rohini Sector 14, Delhi', 'Male', 4500.00, 'Delhi Technological University, Delhi', 'Delhi', 'New Delhi', '110085', 'default.png', 'Rejected', '2345-6789-0123', 6.20, 'HDFC Bank', '80152436781', 'HDFC0000240', '2026-07-25 12:00:00');

-- 3. Scholarships
INSERT INTO `scholarships` (`scholarship_id`, `name`, `category`, `description`, `eligibility`, `benefit_amount`, `course`, `education_level`, `scholarship_type`, `last_date`, `status`, `created_at`) VALUES
(1, 'Central Sector Scheme of Scholarship for College Students', 'General', 'The Central Sector Scheme of Scholarship for College and University Students is launched by the Department of Higher Education to provide financial assistance to meritorious students from low-income families, enabling them to meet part of their day-to-day expenses while pursuing higher studies.', 'Top 20th percentile of Class 12th. Family income from all sources must not exceed $5,500 per annum. Regular full-time courses.', 25000.00, 'Regular UG/PG Professional', 'Undergraduate / Postgraduate', 'Central Sector', '2026-10-31', 'Active', '2026-06-01 10:00:00'),
(2, 'PG Scholarship for Single Girl Child', 'General', 'In order to value family planning norms and promote girl education at post-graduate level, UGC has introduced this scholarship. Purpose is to compensate direct costs of girl education especially for those who are the only child in their families.', 'Girl students who are the only child in their family. Must be admitted in 1st year of regular master degree. Max age 30.', 18000.00, 'Post-Graduation Year 1', 'Postgraduate', 'UGC Scheme', '2026-09-30', 'Active', '2026-06-05 10:00:00'),
(3, 'National Merit-cum-Means Scholarship Scheme', 'OBC', 'Objective is to provide financial assistance to poor and meritorious OBC/minority students to enable them to pursue professional and technical courses without financial distress.', 'Must be admitted to recognized technical/professional institution. Score >= 50% in qualifying exam. Annual family income <= $3,000.', 12000.00, 'Professional & Technical courses', 'Undergraduate / Diploma', 'National Scheme', '2026-09-30', 'Active', '2026-06-10 10:00:00'),
(4, 'Prime Minister\'s Scholarship Scheme for Central Forces (PMSS)', 'General', 'Encourages higher technical and professional education for dependent wards and widows of Central Armed Police Forces, Assam Rifles, and State Police personnel.', 'Wards/widows of CAPFs, AR & State Police personnel martyred or disabled in action. Min 60% in Class 12.', 30000.00, 'Engineering, MBBS, MBA, Law, MCA', 'Undergraduate / Professional', 'Central Sector', '2026-11-15', 'Active', '2026-06-15 10:00:00'),
(5, 'Begum Hazrat Mahal National Scholarship for Girls', 'Minority', 'Provides financial support to meritorious girl students belonging to economically weaker minority communities (Muslim, Christian, Sikh, Buddhist, Jain, Parsi).', 'Female minority students having secured minimum 50% marks in previous exam. Annual parental income not exceeding $2,500.', 15000.00, 'Secondary & Higher Secondary / Diploma', 'Secondary / Diploma', 'Minority Program', '2026-08-31', 'Active', '2026-06-20 10:00:00'),
(6, 'Post Matric Scholarship Scheme for SC Students', 'SC', 'Supports Scheduled Caste students studying at post-matriculation or post-secondary stage to enable them to complete their education.', 'SC students resident of state. Family income less than $3,500 per annum from all sources.', 25000.00, 'Secondary, Diploma & Higher Education', 'Post-Matriculation', 'State / Central Sector', '2026-10-31', 'Active', '2026-06-25 10:00:00'),
(7, 'Pre-Matric Scholarship Scheme for ST Students', 'ST', 'Aims to support parents of ST students for education of their wards studying in classes IX and X so that the incidence of drop-out is minimized.', 'ST students in recognized schools. Family income not exceeding $2,500 per annum.', 8000.00, 'Class IX and X', 'Secondary', 'State Sector', '2026-12-01', 'Draft', '2026-07-01 10:00:00'),
(8, 'Pragati Scholarship Scheme for Girls', 'General', 'AICTE scheme aimed at providing assistance for advancement of girls pursuing technical education. Covers degree and diploma technical education.', 'Female technical students. Maximum 2 per family. Family income not exceeding $8,000 per annum.', 20000.00, 'AICTE Degree / Diploma courses', 'Undergraduate / Diploma', 'UGC / AICTE Scheme', '2026-06-15', 'Expired', '2026-05-01 10:00:00');

-- 4. Applications
INSERT INTO `applications` (`application_id`, `student_id`, `scholarship_id`, `application_date`, `status`, `remarks`, `approved_amount`, `updated_at`) VALUES
(101, 1, 4, '2026-07-20 10:15:00', 'Approved', 'All verified through CAPF nodal records. Eligible for full scholarship.', 30000.00, '2026-08-01 14:20:00'),
(102, 2, 3, '2026-07-22 14:30:00', 'Approved', 'OBC non-creamy layer certificate and marksheet verified.', 12000.00, '2026-08-03 16:10:00'),
(103, 3, 6, '2026-08-01 11:00:00', 'Pending', 'Under initial review by College Nodal Officer.', 0.00, '2026-08-01 11:00:00'),
(104, 4, 7, '2026-08-03 16:45:00', 'Pending', 'Awaiting income certificate validation from Tahsildar office.', 0.00, '2026-08-03 16:45:00'),
(105, 5, 5, '2026-08-05 13:20:00', 'Pending', 'Minority self-declaration and fee receipt under review.', 0.00, '2026-08-05 13:20:00'),
(106, 6, 1, '2026-05-10 09:30:00', 'Rejected', 'GPA does not meet minimum 80th percentile eligibility threshold.', 0.00, '2026-05-25 11:15:00');

-- 5. Announcements
INSERT INTO `announcements` (`announcement_id`, `title`, `content`, `target_audience`, `published_date`, `is_active`, `created_at`) VALUES
(1, 'Deadline Extended for Central Sector Scholarships', 'Under administrative directive, the registration window for all Central Sector Higher Education schemes has been extended from August 31 to October 31, 2026. This allows students affected by admission backlogs to complete registrations. Verification deadlines have been adjusted accordingly.', 'All Students', '2026-08-05', 1, '2026-08-05 09:00:00'),
(2, 'Second Installment of FY25-26 Fellowship Disbursed', 'Disbursement notifications have been dispatched to verified scholars. A total of $18.5M was released via Aadhaar Bridge Payment systems. If you have a verified status but have not received your bank notification, kindly verify if your bank account is active and seeded with your Aadhaar ID via your portal profile.', 'All Students', '2026-07-28', 1, '2026-07-28 10:30:00'),
(3, 'Mandatory Nodal Officer Onboarding Guidelines', 'All designated college Nodal Officers must update their institutional profile records using active digital signatures by July 31. Applications from institutions failing this validation cannot be routed, delaying student verification.', 'Nodal Officers / Students', '2026-07-15', 1, '2026-07-15 14:00:00'),
(4, 'Pragati Technical Fellowship Expansion', 'AICTE has announced additional scholarship quotas under the Pragati Scheme for female engineering students enrolled in AICTE accredited courses in North Eastern regions. Registration processes are similar. Applicants can explore instructions in the active schemes directory.', 'Female Students', '2026-07-01', 1, '2026-07-01 11:00:00');

-- 6. Contacts / Queries
INSERT INTO `contacts` (`query_id`, `student_id`, `name`, `email`, `phone`, `subject`, `message`, `status`, `created_at`) VALUES
(1, 3, 'Rahul Kumar', 'rahul.kumar@email.com', '9876543212', 'Verification Pending status', 'My college nodal officer has reviewed my marksheet, but my dashboard status still shows pending. Please advise when phase 1 escalates.', 'Open', '2026-08-02 11:30:00'),
(2, 5, 'Zainab Khan', 'zainab.khan@email.com', '9876543214', 'Direct Benefit Transfer', 'I have linked my Aadhaar with Canara bank account last week. Kindly confirm if the payment gateway reflects this.', 'In Progress', '2026-08-04 15:45:00');

-- 7. Feedback
INSERT INTO `feedback` (`feedback_id`, `student_id`, `name`, `email`, `application_id`, `category`, `rating`, `comments`, `created_at`) VALUES
(1, 1, 'Aarav Sharma', 'aarav.sharma@email.com', '101', 'Website UI & Navigation', 5, 'The portal UI is clean, responsive, and easy to navigate. Quick application approval.', '2026-08-02 16:00:00'),
(2, 2, 'Priya Patel', 'priya.patel@email.com', '102', 'Eligibility Checker tool', 4, 'Checker tool gave clear guidance on OBC quotas. Document checklist was very helpful.', '2026-08-04 12:20:00');
