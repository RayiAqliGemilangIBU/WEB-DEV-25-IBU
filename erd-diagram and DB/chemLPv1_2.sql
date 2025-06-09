-- Create Database
CREATE DATABASE IF NOT EXISTS ChemLP1_2;
USE ChemLP1_2;


CREATE TABLE user (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(100) UNIQUE,
    password VARCHAR(255),
    role ENUM('Admin', 'Student'),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE material (
    material_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255),
    description TEXT,
    created_by_user_id INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by_user_id) REFERENCES user(user_id)
);

CREATE TABLE quiz (
    quiz_id INT AUTO_INCREMENT PRIMARY KEY,
    material_id INT,
    title VARCHAR(255),
    FOREIGN KEY (material_id) REFERENCES material(material_id)
);

CREATE TABLE question (
    question_id INT AUTO_INCREMENT PRIMARY KEY,
    quiz_id INT,
    header TEXT,
    explanation TEXT,
    FOREIGN KEY (quiz_id) REFERENCES quiz(quiz_id)
);

CREATE TABLE optionitem (
    option_id INT AUTO_INCREMENT PRIMARY KEY,
    question_id INT,
    option_text VARCHAR(255),
    is_correct BOOLEAN,
    image_path VARCHAR(255),
    FOREIGN KEY (question_id) REFERENCES question(question_id)
);

CREATE TABLE textmaterial (
    text_id INT AUTO_INCREMENT PRIMARY KEY,
    material_id INT,
    title VARCHAR(255),
    content TEXT,
    image_path VARCHAR(255),
    FOREIGN KEY (material_id) REFERENCES material(material_id)
);

CREATE TABLE student_answer (
    answer_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    question_id INT,
    selected_option_id INT,
    FOREIGN KEY (user_id) REFERENCES user(user_id),
    FOREIGN KEY (question_id) REFERENCES question(question_id),
    FOREIGN KEY (selected_option_id) REFERENCES optionitem(option_id)
);

-- Insert Users
INSERT INTO user (name, email, password, role) VALUES
('Alice', 'alice@student.com', 'password123', 'Student'),
('Bob', 'bob@student.com', 'password456', 'Student'),
('Dr. Smith', 'smith@admin.com', 'adminpass', 'Admin');

-- Insert Materials
INSERT INTO material (title, description, created_by_user_id) VALUES
('Atomic Structure', 'Learn about atoms, subatomic particles, and electron configurations.', 3),
('Chemical Bonding', 'Introduction to ionic, covalent, and metallic bonding.', 3);

-- Insert Quizzes
INSERT INTO quiz (material_id, title) VALUES
(1, 'Quiz on Atomic Structure'),
(2, 'Quiz on Chemical Bonding');

-- Insert Questions for Quiz 1 (Atomic Structure)
INSERT INTO question (quiz_id, header, explanation) VALUES
(1, 'What is the charge of a proton?', 'Protons are positively charged.'),
(1, 'What particle has no charge?', 'Neutrons have no electric charge.'),
(1, 'What is the center of an atom called?', 'The nucleus is the central core.'),
(1, 'Which particle is found outside the nucleus?', 'Electrons orbit the nucleus.'),
(1, 'How many protons does Carbon have?', 'Carbon has 6 protons.');

-- Options for Quiz 1
-- Q1
INSERT INTO optionitem (question_id, option_text, is_correct) VALUES
(1, 'Positive', TRUE),
(1, 'Negative', FALSE),
(1, 'Neutral', FALSE),
(1, 'Zero', FALSE);

-- Q2
INSERT INTO optionitem (question_id, option_text, is_correct) VALUES
(2, 'Proton', FALSE),
(2, 'Neutron', TRUE),
(2, 'Electron', FALSE),
(2, 'Photon', FALSE);

-- Q3
INSERT INTO optionitem (question_id, option_text, is_correct) VALUES
(3, 'Shell', FALSE),
(3, 'Nucleus', TRUE),
(3, 'Electron Cloud', FALSE),
(3, 'Molecule', FALSE);

-- Q4
INSERT INTO optionitem (question_id, option_text, is_correct) VALUES
(4, 'Proton', FALSE),
(4, 'Neutron', FALSE),
(4, 'Electron', TRUE),
(4, 'Ion', FALSE);

-- Q5
INSERT INTO optionitem (question_id, option_text, is_correct) VALUES
(5, '12', FALSE),
(5, '8', FALSE),
(5, '6', TRUE),
(5, '4', FALSE);

-- Insert Questions for Quiz 2 (Chemical Bonding)
INSERT INTO question (quiz_id, header, explanation) VALUES
(2, 'What type of bond shares electrons?', 'Covalent bonds share electrons.'),
(2, 'Which bond involves metal and non-metal?', 'Ionic bonds usually form between metals and non-metals.'),
(2, 'Which bond is found in metals?', 'Metallic bonding occurs in metals.'),
(2, 'Which bond forms NaCl?', 'NaCl forms via ionic bonding.'),
(2, 'Which bond involves a sea of electrons?', 'Metallic bonds have delocalized electrons.');

-- Options for Quiz 2
-- Q6
INSERT INTO optionitem (question_id, option_text, is_correct) VALUES
(6, 'Ionic', FALSE),
(6, 'Covalent', TRUE),
(6, 'Metallic', FALSE),
(6, 'Hydrogen', FALSE);

-- Q7
INSERT INTO optionitem (question_id, option_text, is_correct) VALUES
(7, 'Covalent', FALSE),
(7, 'Hydrogen', FALSE),
(7, 'Ionic', TRUE),
(7, 'Metallic', FALSE);

-- Q8
INSERT INTO optionitem (question_id, option_text, is_correct) VALUES
(8, 'Covalent', FALSE),
(8, 'Ionic', FALSE),
(8, 'Hydrogen', FALSE),
(8, 'Metallic', TRUE);

-- Q9
INSERT INTO optionitem (question_id, option_text, is_correct) VALUES
(9, 'Covalent', FALSE),
(9, 'Hydrogen', FALSE),
(9, 'Ionic', TRUE),
(9, 'Van der Waals', FALSE);

-- Q10
INSERT INTO optionitem (question_id, option_text, is_correct) VALUES
(10, 'Covalent', FALSE),
(10, 'Hydrogen', FALSE),
(10, 'Metallic', TRUE),
(10, 'Ionic', FALSE);

-- Text material for 'Atomic Structure'
INSERT INTO textmaterial (material_id, title, content) VALUES
(1, 'Introduction to Atomic Structure', 
'Atoms are the basic units of matter and consist of protons, neutrons, and electrons. Protons are positively charged, neutrons have no charge, and electrons are negatively charged. The nucleus contains protons and neutrons, while electrons orbit around the nucleus.');

-- Text material for 'Chemical Bonding'
INSERT INTO textmaterial (material_id, title, content) VALUES
(2, 'Understanding Chemical Bonding', 
'Chemical bonds are forces that hold atoms together. The main types are ionic bonds (transfer of electrons), covalent bonds (sharing of electrons), and metallic bonds (sea of delocalized electrons). These bonds determine the properties of compounds.');


-- ================================
-- ALTER CONSTRAINT FOR ChemLP1_2
-- ================================

USE ChemLP1_2;

-- 1. material.created_by_user_id → SET NULL
ALTER TABLE material DROP FOREIGN KEY material_ibfk_1;
ALTER TABLE material
ADD CONSTRAINT fk_material_user
FOREIGN KEY (created_by_user_id) REFERENCES user(user_id) ON DELETE SET NULL;

-- 2. quiz.material_id → CASCADE
ALTER TABLE quiz DROP FOREIGN KEY quiz_ibfk_1;
ALTER TABLE quiz
ADD CONSTRAINT fk_quiz_material
FOREIGN KEY (material_id) REFERENCES material(material_id) ON DELETE CASCADE;

-- 3. question.quiz_id → CASCADE
ALTER TABLE question DROP FOREIGN KEY question_ibfk_1;
ALTER TABLE question
ADD CONSTRAINT fk_question_quiz
FOREIGN KEY (quiz_id) REFERENCES quiz(quiz_id) ON DELETE CASCADE;

-- 4. optionitem.question_id → CASCADE
ALTER TABLE optionitem DROP FOREIGN KEY optionitem_ibfk_1;
ALTER TABLE optionitem
ADD CONSTRAINT fk_optionitem_question
FOREIGN KEY (question_id) REFERENCES question(question_id) ON DELETE CASCADE;

-- 5. textmaterial.material_id → CASCADE
ALTER TABLE textmaterial DROP FOREIGN KEY textmaterial_ibfk_1;
ALTER TABLE textmaterial
ADD CONSTRAINT fk_textmaterial_material
FOREIGN KEY (material_id) REFERENCES material(material_id) ON DELETE CASCADE;

-- 6. student_answer.user_id → CASCADE
ALTER TABLE student_answer DROP FOREIGN KEY student_answer_ibfk_1;
ALTER TABLE student_answer
ADD CONSTRAINT fk_answer_user
FOREIGN KEY (user_id) REFERENCES user(user_id) ON DELETE CASCADE;

-- 7. student_answer.question_id → CASCADE
ALTER TABLE student_answer DROP FOREIGN KEY student_answer_ibfk_2;
ALTER TABLE student_answer
ADD CONSTRAINT fk_answer_question
FOREIGN KEY (question_id) REFERENCES question(question_id) ON DELETE CASCADE;

-- 8. student_answer.selected_option_id → SET NULL
ALTER TABLE student_answer DROP FOREIGN KEY student_answer_ibfk_3;
ALTER TABLE student_answer
ADD CONSTRAINT fk_answer_option
FOREIGN KEY (selected_option_id) REFERENCES optionitem(option_id) ON DELETE SET NULL;
