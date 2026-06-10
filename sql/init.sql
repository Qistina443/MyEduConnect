-- ============================================
-- MyEduConnect Database Initialization Script
-- ============================================

-- Create database (if not exists)
CREATE DATABASE IF NOT EXISTS myeduconnect_db;
USE myeduconnect_db;

-- ============================================
-- 1. USERS TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS users (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) UNIQUE NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('student', 'admin', 'instructor') NOT NULL,
    specialization VARCHAR(100) DEFAULT NULL
   
);

-- ============================================
-- 2. COURSES TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS courses (
    id INT (11) AUTO_INCREMENT PRIMARY KEY,
    course_name VARCHAR(200) NOT NULL,
    description TEXT DEFAULT NULL,
    day VARCHAR(20),
    time VARCHAR(50),
    amount DECIMAL(10,2) NOT NULL,
    instructor_id int(11) DEFAULT NULL,
    admin_id int(11) DEFAULT NULL,
    FOREIGN KEY (admin_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ============================================
-- 3. ENROLLMENTS TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS enrollments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT (11) NOT NULL,
    course_id INT (11) NOT NULL,
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
);

-- ============================================
-- 4. PAYMENTS TABLE (VULN: Plaintext credit cards)
-- ============================================
CREATE TABLE IF NOT EXISTS payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    course_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    card_holdername VARCHAR(100),
    card_number VARCHAR(20),
    card_expiry VARCHAR(7),
    card_cvv INT(4),
    transaction_id VARCHAR(100),
    payment_date DATE,
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
);

-- ============================================
-- 5. DELETED COURSES TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS deleted_courses (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
  course_id INT(11) DEFAULT NULL,
  course_name VARCHAR(150) DEFAULT NULL,
  day VARCHAR(50) DEFAULT NULL,
  time VARCHAR (50) DEFAULT NULL,
  deleted_by INT(11) DEFAULT NULL,
  deleted_at timestamp NOT NULL DEFAULT current_timestamp(),
  FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
  FOREIGN KEY (deleted_by) REFERENCES users(id) ON DELETE CASCADE
);



-- ============================================
-- INSERT SAMPLE DATA
-- ============================================

-- Insert users (VULN: MD5 password hashing)
INSERT INTO users (id, name, email, password, role, specialization) VALUES
(1, 'admin', 'admin@myeduconnect.com', 'admin123', 'admin', NULL),
(2, 'Ali Saheed', 'ali@myeduconnect.com', 'password123', 'student', NULL),
(3, 'Jane White', 'jane@myeduconnect.com', 'password123', 'student', NULL),
(4, 'Emily Red', 'emily@myeduconnect.com', 'teacher123', 'instructor', 'English'),
(5, 'Harry Blue', 'harry@myeduconnect.com', 'teacher123', 'instructor', 'Science'),
(6, 'Bob Brown', 'bob@myeduconnect.com', 'teacher123', 'instructor', 'Mathematics');




-- Insert courses
INSERT INTO courses (id, course_name, description, day, time, amount, instructor_id, admin_id) VALUES
(1, 'Mathematics', 'Basic algebra and geometry fundamentals.', 'Monday', '10:00-12:00 PM', 49.99, 6, 1),
(2, 'Mathematics', 'Basic algebra and geometry fundamentals.', 'Wednesday', '2:00-4:00 PM', 99.99, 6, 1),
(3, 'Science', 'Learn about biology, physics and chemistry.', 'Friday', '11:00-1:00 PM', 79.99, 5, 1),
(4, 'Science', 'Learn about biology, physics and chemistry.', 'Tuesday', '3:00-5:00 PM', 89.99, 5, 1),
(5, 'English', 'Practice grammar, sentence formation of the English language.', 'Thursday', '9:00-11:00 AM', 39.99, 4, 1);

-- Insert enrollments
INSERT INTO enrollments (id, student_id, course_id) VALUES
(1, 2, 1),
(2, 2, 4),
(3, 3, 1),
(4, 3, 3);

-- Insert sample payments (VULN: Plaintext credit card data)
INSERT INTO payments (id, student_id, course_id, amount, card_holdername, card_number, card_expiry, card_cvv, payment_date) VALUES
(1, 2, 1, 49.99, 'Ali Saheed', '4111111111111111', '12/25', '123', '2024-09-05'),
(2, 2, 4, 89.99, 'Ali Saheed', '4111111111111111', '12/25', '123', '2024-09-05'),
(3, 3, 1, 49.99, 'Jane White', '5555555555554444', '06/26', '456','2024-10-05' );

-- ============================================
-- VERIFY DATA
-- ============================================
SELECT 'Users:' AS '', COUNT(*) FROM users;
SELECT 'Courses:' AS '', COUNT(*) FROM courses;
SELECT 'Enrollments:' AS '', COUNT(*) FROM enrollments;
SELECT 'Payments:' AS '', COUNT(*) FROM payments;

-- ============================================
--INDEXING FOR TABLES
-- ============================================

ALTER TABLE `courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `enrollments`
--
ALTER TABLE `enrollments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;


-- ============================================
-- END OF SCRIPT
-- ============================================