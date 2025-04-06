-- Buat Database
CREATE DATABASE ChemLP;
USE ChemLP;

-- Tabel User (Parent)
CREATE TABLE User (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(100) UNIQUE,
    password VARCHAR(255),
    role ENUM('admin', 'student') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel Admin (Child dari User)
CREATE TABLE Admin (
    admin_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    FOREIGN KEY (user_id) REFERENCES User(user_id)
        ON DELETE CASCADE
);

-- Tabel Student (Child dari User)
CREATE TABLE Student (
    student_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    FOREIGN KEY (user_id) REFERENCES User(user_id)
        ON DELETE CASCADE
);

-- Tabel Material
CREATE TABLE Material (
    material_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255),
    description TEXT,
    created_by_admin_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by_admin_id) REFERENCES Admin(admin_id)
        ON DELETE SET NULL
);

-- Tabel Text (Child dari Material)
CREATE TABLE TextMaterial (
    text_id INT AUTO_INCREMENT PRIMARY KEY,
    material_id INT,
    title VARCHAR(255),
    content TEXT,
    image_path VARCHAR(255),
    FOREIGN KEY (material_id) REFERENCES Material(material_id)
        ON DELETE CASCADE
);

-- Tabel Quiz (Child dari Material)
CREATE TABLE Quiz (
    quiz_id INT AUTO_INCREMENT PRIMARY KEY,
    material_id INT,
    title VARCHAR(255),
    FOREIGN KEY (material_id) REFERENCES Material(material_id)
        ON DELETE CASCADE
);

-- Tabel Question (Child dari Quiz)
CREATE TABLE Question (
    question_id INT AUTO_INCREMENT PRIMARY KEY,
    quiz_id INT,
    FOREIGN KEY (quiz_id) REFERENCES Quiz(quiz_id)
        ON DELETE CASCADE
);

-- Tabel Header (Child dari Question)
CREATE TABLE Header (
    header_id INT AUTO_INCREMENT PRIMARY KEY,
    question_id INT,
    text TEXT,
    image_path VARCHAR(255),
    FOREIGN KEY (question_id) REFERENCES Question(question_id)
        ON DELETE CASCADE
);

-- Tabel Option (Child dari Question)
CREATE TABLE OptionItem (
    option_id INT AUTO_INCREMENT PRIMARY KEY,
    question_id INT,
    option_text TEXT,
    is_correct BOOLEAN,
    image_path VARCHAR(255),
    FOREIGN KEY (question_id) REFERENCES Question(question_id)
        ON DELETE CASCADE
);

-- Tabel Explanation (Child dari Question)
CREATE TABLE Explanation (
    explanation_id INT AUTO_INCREMENT PRIMARY KEY,
    question_id INT,
    explanation_text TEXT,
    FOREIGN KEY (question_id) REFERENCES Question(question_id)
        ON DELETE CASCADE
);

-- Tabel StudentQuizResult (Child dari Student)
CREATE TABLE StudentQuizResult (
    result_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT,
    quiz_id INT,
    total_questions INT,
    correct_answers INT,
    attempt_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES Student(student_id)
        ON DELETE CASCADE,
    FOREIGN KEY (quiz_id) REFERENCES Quiz(quiz_id)
        ON DELETE CASCADE
);
