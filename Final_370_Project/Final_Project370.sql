CREATE DATABASE IF NOT EXISTS Final_Project370;
USE Final_Project370;

DROP TABLE IF EXISTS merge_logs;
DROP TABLE IF EXISTS join_requests;
DROP TABLE IF EXISTS group_schedules;
DROP TABLE IF EXISTS group_members;
DROP TABLE IF EXISTS study_groups;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(120) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    reject_penalty INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE study_groups (
    group_id INT AUTO_INCREMENT PRIMARY KEY,
    group_name VARCHAR(150) NOT NULL,
    course VARCHAR(80) NOT NULL,
    interest VARCHAR(120) NOT NULL,
    description TEXT,
    admin_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (admin_id) REFERENCES users(user_id) ON DELETE CASCADE
);

CREATE TABLE group_members (
    member_id INT AUTO_INCREMENT PRIMARY KEY,
    group_id INT NOT NULL,
    user_id INT NOT NULL,
    role ENUM('admin','member') DEFAULT 'member',
    joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_group_user (group_id, user_id),
    FOREIGN KEY (group_id) REFERENCES study_groups(group_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

CREATE TABLE group_schedules (
    schedule_id INT AUTO_INCREMENT PRIMARY KEY,
    group_id INT NOT NULL,
    meeting_title VARCHAR(150) NOT NULL,
    meeting_date DATE NOT NULL,
    meeting_time TIME NOT NULL,
    location VARCHAR(150),
    note TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (group_id) REFERENCES study_groups(group_id) ON DELETE CASCADE
);

CREATE TABLE join_requests (
    request_id INT AUTO_INCREMENT PRIMARY KEY,
    group_id INT NOT NULL,
    user_id INT NOT NULL,
    status ENUM('pending','approved','rejected') DEFAULT 'pending',
    requested_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    handled_at TIMESTAMP NULL,
    UNIQUE KEY unique_request (group_id, user_id),
    FOREIGN KEY (group_id) REFERENCES study_groups(group_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

CREATE TABLE merge_logs (
    merge_id INT AUTO_INCREMENT PRIMARY KEY,
    main_group_id INT NOT NULL,
    merged_group_id INT NOT NULL,
    merged_by INT NOT NULL,
    merged_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (merged_by) REFERENCES users(user_id) ON DELETE CASCADE
);


-- email: user@mail.com
-- password: 123456

-- Leaderboard formula:
-- 10 points for each group a user owns
-- 20 points for each merge performed by that owner/admin
-- 5 points for each group the user has joined as a member
-- -5 points for each join request an admin rejects
