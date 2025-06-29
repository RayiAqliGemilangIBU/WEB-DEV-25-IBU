
CREATE DATABASE IF NOT EXISTS ChemLP1_3;
USE ChemLP1_3;


CREATE TABLE user (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(100) UNIQUE,
    password VARCHAR(255), -- Sebaiknya di-hash
    role ENUM('Admin', 'Student'),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Tabel material (Sama seperti sebelumnya, dengan penyesuaian FK)
CREATE TABLE material (
    material_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255),
    description TEXT,
    created_by_user_id INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_material_user FOREIGN KEY (created_by_user_id) REFERENCES user(user_id) ON DELETE SET NULL
);

-- Tabel quiz (Sama seperti sebelumnya, dengan penyesuaian FK)
CREATE TABLE quiz (
    quiz_id INT AUTO_INCREMENT PRIMARY KEY,
    material_id INT,
    title VARCHAR(255),
    description TEXT, -- Menambahkan kolom deskripsi seperti pada rencana sebelumnya
    CONSTRAINT fk_quiz_material FOREIGN KEY (material_id) REFERENCES material(material_id) ON DELETE CASCADE
);

-- Tabel question (Dimodifikasi untuk soal Benar/Salah)
CREATE TABLE question (
    question_id INT AUTO_INCREMENT PRIMARY KEY,
    quiz_id INT,
    header TEXT,              -- Teks pertanyaan
    explanation TEXT,         -- Penjelasan jawaban
    answer BOOLEAN,  -- Jawaban yang benar: TRUE jika "Benar", FALSE jika "Salah"
    CONSTRAINT fk_question_quiz FOREIGN KEY (quiz_id) REFERENCES quiz(quiz_id) ON DELETE CASCADE
);

-- Tabel textmaterial (Sama seperti sebelumnya, dengan penyesuaian FK)
CREATE TABLE textmaterial (
    text_id INT AUTO_INCREMENT PRIMARY KEY,
    material_id INT,
    title VARCHAR(255),
    content TEXT,
    image_path VARCHAR(255), -- Opsional, jika ada gambar pendukung
    CONSTRAINT fk_textmaterial_material FOREIGN KEY (material_id) REFERENCES material(material_id) ON DELETE CASCADE
);

-- ================================
-- INSERT SAMPLE DATA (English)
-- ================================

-- Insert Users (Modified as requested)
INSERT INTO user (name, email, password, role) VALUES
('Admin User', 'test1@test.com', '123', 'Admin'), -- Assuming '123' is hashed
('Student User', 'test2@test.com', '123', 'Student'); -- Assuming '123' is hashed

-- Insert Materials
-- Note: created_by_user_id is changed to 1 (Admin User)
INSERT INTO material (title, description, created_by_user_id) VALUES
('Fundamentals of Atomic Structure', 'Introduction to atoms, subatomic particles, and electron configurations.', 1),
('Foundational Chemical Bonding', 'Discussion on ionic, covalent, and metallic bonding.', 1),
('Basic Organic Chemistry Reactions', 'Learning about basic reaction types in organic chemistry and the reagents used.', 1);

-- Insert Quizzes
INSERT INTO quiz (material_id, title, description) VALUES
(4, 'Atomic Structure Quiz', 'Basic understanding test on atomic structure.'),
(5, 'Chemical Bonding Quiz', 'Knowledge test on various types of chemical bonds.'),
(6, 'Organic Chemistry Reagents Quiz', 'Testing understanding of reagent use in organic reactions.');

-- Insert Questions (True/False Format)

-- Questions for Quiz 1 (Atomic Structure Quiz)
INSERT INTO question (quiz_id, header, explanation, correct_is_true) VALUES
(4, 'A proton has a negative charge.', 'False. Protons are positively charged.', FALSE),
(4, 'A neutron has no electrical charge.', 'True. Neutrons are neutral.', TRUE),
(4, 'Electrons are found inside the atomic nucleus.', 'False. Electrons orbit the atomic nucleus.', FALSE);

-- Questions for Quiz 2 (Chemical Bonding Quiz)
INSERT INTO question (quiz_id, header, explanation, correct_is_true) VALUES
(5, 'Covalent bonds are formed by the transfer of electrons.', 'False. Covalent bonds are formed by the sharing of electron pairs.', FALSE),
(5, 'NaCl is formed through ionic bonding.', 'True. NaCl is an example of an ionic compound.', TRUE),
(5, 'Metallic bonding involves a sea of delocalized electrons.', 'True. This is a characteristic feature of metallic bonding.', TRUE);

-- Questions for Quiz 3 (Organic Chemistry Reagents Quiz)
INSERT INTO question (quiz_id, header, explanation, correct_is_true) VALUES
(6, 'Grignard reagents (RMgX) are used to reduce aldehydes to primary alcohols.', 'False. Grignard reagents are strong nucleophiles that react with aldehydes to form secondary alcohols (if the aldehyde is not formaldehyde) or tertiary alcohols (with ketones). For reduction to primary alcohols, NaBH4 or LiAlH4 are typically used.', FALSE),
(6, 'Ozonolysis of alkenes followed by reductive workup (e.g., with Zn/CH3COOH) will produce aldehydes or ketones.', 'True. This is a common method to cleave C=C double bonds.', TRUE),
(6, 'KMnO4 in an acidic and hot environment will oxidize toluene to benzoic acid.', 'True. KMnO4 is a strong oxidizing agent.', TRUE);

-- Insert Text Materials
INSERT INTO textmaterial (material_id, title, content) VALUES
(4, 'Introduction to Atomic Structure',
'Atoms are the basic units of matter and consist of protons, neutrons, and electrons. Protons are positively charged, neutrons have no charge, and electrons are negatively charged. The nucleus contains protons and neutrons, while electrons orbit around the nucleus.'),
(5, 'Understanding Chemical Bonding',
'Chemical bonds are forces that hold atoms together. The main types are ionic bonds (transfer of electrons), covalent bonds (sharing of electrons), and metallic bonds (sea of delocalized electrons). These bonds determine the properties of compounds.'),
(6, 'Common Reagents in Organic Synthesis',
'The selection of the appropriate reagent is crucial in organic synthesis. Some common reagents include oxidizing agents like KMnO4 and CrO3, reducing agents like LiAlH4 and NaBH4, and Grignard reagents for carbon-carbon bond formation.');
