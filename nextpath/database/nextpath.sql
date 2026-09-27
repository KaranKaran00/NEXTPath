-- =========================================================
-- NEXTPath - Adaptive Skill Assessment & Career Discovery
-- Database Schema + Seed Data
-- =========================================================

CREATE DATABASE IF NOT EXISTS nextpath
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE nextpath;

-- ---------------------------------------------------------
-- USERS
-- ---------------------------------------------------------
CREATE TABLE users (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    full_name       VARCHAR(120)  NOT NULL,
    email           VARCHAR(150)  NOT NULL UNIQUE,
    mobile          VARCHAR(20)   NOT NULL,
    password        VARCHAR(255)  NOT NULL,
    gender          ENUM('Male','Female','Other') NOT NULL,
    education_level VARCHAR(80)   NOT NULL,
    college_name    VARCHAR(150)  NOT NULL,
    interests       VARCHAR(255)  DEFAULT NULL,
    skills          VARCHAR(255)  DEFAULT NULL,
    hobbies         VARCHAR(255)  DEFAULT NULL,
    role            ENUM('student','admin') NOT NULL DEFAULT 'student',
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- CATEGORIES
-- ---------------------------------------------------------
CREATE TABLE categories (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(120) NOT NULL UNIQUE,
    description VARCHAR(400) NOT NULL,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- CATEGORY -> SKILL MAPPING (drives the Skill Profile)
-- ---------------------------------------------------------
CREATE TABLE category_skills (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    skill_name  VARCHAR(80) NOT NULL,
    weight      DECIMAL(3,2) NOT NULL DEFAULT 1.00,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- QUESTIONS  (no difficulty column, by design)
-- ---------------------------------------------------------
CREATE TABLE questions (
    id             INT AUTO_INCREMENT PRIMARY KEY,
    category_id    INT NOT NULL,
    question_text  VARCHAR(500) NOT NULL,
    option_a       VARCHAR(255) NOT NULL,
    option_b       VARCHAR(255) NOT NULL,
    option_c       VARCHAR(255) NOT NULL,
    option_d       VARCHAR(255) NOT NULL,
    correct_answer ENUM('A','B','C','D') NOT NULL,
    created_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- CAREERS
-- ---------------------------------------------------------
CREATE TABLE careers (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    career_name VARCHAR(150) NOT NULL,
    description VARCHAR(400) DEFAULT NULL,
    relevance   DECIMAL(3,2) NOT NULL DEFAULT 1.00,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- ASSESSMENTS
-- ---------------------------------------------------------
CREATE TABLE assessments (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    user_id         INT NOT NULL,
    total_questions INT NOT NULL,
    correct_answers INT NOT NULL DEFAULT 0,
    overall_score   DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    completed_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- ASSESSMENT ANSWERS
-- ---------------------------------------------------------
CREATE TABLE assessment_answers (
    id               INT AUTO_INCREMENT PRIMARY KEY,
    assessment_id    INT NOT NULL,
    question_id      INT NOT NULL,
    selected_answer  ENUM('A','B','C','D') NOT NULL,
    is_correct       TINYINT(1) NOT NULL DEFAULT 0,
    score            DECIMAL(4,2) NOT NULL DEFAULT 0.00,
    FOREIGN KEY (assessment_id) REFERENCES assessments(id) ON DELETE CASCADE,
    FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- SETTINGS (lets admin change assessment length without code edits)
-- ---------------------------------------------------------
CREATE TABLE settings (
    setting_key   VARCHAR(80) PRIMARY KEY,
    setting_value VARCHAR(255) NOT NULL
) ENGINE=InnoDB;

INSERT INTO settings (setting_key, setting_value) VALUES
('assessment_length', '40');

-- =========================================================
-- SEED: ADMIN ACCOUNT
-- Email: admin@nextpath.com  Password: Admin@123
-- =========================================================
INSERT INTO users (full_name, email, mobile, password, gender, education_level, college_name, interests, skills, hobbies, role)
VALUES (
  'NEXTPath Admin', 'admin@nextpath.com', '9999999999',
  '$2y$10$dx0O3Jd3dNYlkzoYDgcFWetqujOR2JhHgubEgNYy8nkqTHCWeoUhS',
  'Other', 'Graduate', 'NEXTPath HQ', NULL, NULL, NULL, 'admin'
);
-- The hash above is a genuine bcrypt hash of "Admin@123" (verified with
-- PHP 8.3's password_hash/password_verify). If you ever need a fresh one,
-- run: php -r "echo password_hash('yourpassword', PASSWORD_DEFAULT);"

-- =========================================================
-- SEED: CATEGORIES (20 active + 8 prepared-for)
-- =========================================================
INSERT INTO categories (name, description) VALUES
('Science', 'Explore careers built on scientific inquiry, experimentation and discovery.'),
('Accounting & Finance', 'Careers involving financial records, analysis, auditing and planning.'),
('Banking', 'Careers in retail and corporate banking, credit and financial services.'),
('Nursing & Healthcare', 'Careers focused on patient care, treatment and health services.'),
('Beauty & Wellness', 'Careers in personal care, grooming, cosmetology and wellness services.'),
('Fitness & Sports', 'Careers in athletic training, coaching and sports management.'),
('Data Science', 'Careers involving data analysis, statistics and machine learning.'),
('Engineering', 'Careers designing, building and maintaining machines, systems and structures.'),
('Law', 'Careers involving legal advice, advocacy, contracts and justice.'),
('Animation', 'Careers producing animated visuals, motion graphics and visual effects.'),
('Information Technology', 'Explore careers involving software, technology, systems, computing and digital problem solving.'),
('Hospitality & Tourism', 'Careers in hotels, travel, tourism and guest services.'),
('Arts & Design', 'Careers involving visual arts, illustration and creative design.'),
('Business & Management', 'Careers in organizational leadership, strategy and operations.'),
('Teaching & Education', 'Careers involving instruction, curriculum and student development.'),
('Digital Marketing', 'Careers in online branding, advertising and audience growth.'),
('Media & Journalism', 'Careers reporting, producing and distributing news and media content.'),
('Architecture', 'Careers designing buildings, spaces and the built environment.'),
('Psychology & Human Services', 'Careers supporting mental health, counselling and social wellbeing.'),
('Agriculture & Environmental Science', 'Careers in farming, sustainability and environmental management.'),
('Fashion', 'Careers in clothing design, styling and the fashion industry.'),
('Aviation', 'Careers in flight operations, aircraft systems and airport management.'),
('Pharmacy', 'Careers dispensing medication and advising on pharmaceutical care.'),
('Biotechnology', 'Careers applying biology to research, medicine and industry.'),
('Cybersecurity', 'Careers protecting systems, networks and data from digital threats.'),
('Government & Public Administration', 'Careers in public policy, civil service and administration.'),
('Film & Photography', 'Careers capturing and producing visual stories through film and photography.'),
('Social Work', 'Careers supporting individuals, families and communities in need.');

-- =========================================================
-- SEED: CATEGORY -> SKILL MAPPING
-- =========================================================
INSERT INTO category_skills (category_id, skill_name, weight) VALUES
(1,  'Analytical Thinking', 1.00),
(2,  'Analytical Thinking', 1.00),
(3,  'Analytical Thinking', 1.00),
(4,  'Communication',       1.00),
(5,  'Creativity',          1.00),
(6,  'Leadership',          1.00),
(7,  'Analytical Thinking', 1.00),
(8,  'Problem Solving',     1.00),
(9,  'Communication',       1.00),
(10, 'Creativity',          1.00),
(11, 'Technical Aptitude',  1.00),
(12, 'Communication',       1.00),
(13, 'Creativity',          1.00),
(14, 'Leadership',          1.00),
(15, 'Communication',       1.00),
(16, 'Creativity',          1.00),
(17, 'Communication',       1.00),
(18, 'Problem Solving',     1.00),
(19, 'Communication',       1.00),
(20, 'Problem Solving',     1.00),
(21, 'Creativity',          1.00),
(22, 'Technical Aptitude',  1.00),
(23, 'Analytical Thinking', 1.00),
(24, 'Analytical Thinking', 1.00),
(25, 'Technical Aptitude',  1.00),
(26, 'Leadership',          1.00),
(27, 'Creativity',          1.00),
(28, 'Communication',       1.00);

-- =========================================================
-- SEED: QUESTIONS (2 per active category = 40 total)
-- =========================================================
INSERT INTO questions (category_id, question_text, option_a, option_b, option_c, option_d, correct_answer) VALUES
(1,'Which activity interests you most?','Running a controlled experiment','Preparing financial reports','Designing a marketing campaign','Managing a hotel front desk','A'),
(1,'What excites you most about a school project?','Testing a hypothesis with real data','Sketching a poster for the event','Interviewing people for a story','Organising a sports tournament','A'),

(2,'Which activity would you prefer?','Preparing financial reports','Designing websites','Treating patients','Creating animations','A'),
(2,'What sounds most satisfying to you?','Reconciling a company''s accounts','Coaching a football team','Writing a news article','Designing a building facade','A'),

(3,'Which task feels most natural to you?','Evaluating a loan application','Choreographing a dance routine','Diagnosing a patient','Filming a short documentary','A'),
(3,'What would you enjoy doing?','Advising a customer on savings plans','Sketching a fashion collection','Repairing farm irrigation systems','Writing legal briefs','A'),

(4,'Which activity interests you most?','Helping patients','Designing machines','Managing financial records','Creating artwork','A'),
(4,'What would you find most rewarding?','Comforting a worried patient''s family','Auditing a company''s expenses','Building a mobile app','Photographing a wedding','A'),

(5,'Which task appeals to you the most?','Designing a skincare routine for a client','Debugging a software program','Preparing a legal contract','Piloting a small aircraft','A'),
(5,'What would you enjoy spending a day doing?','Styling hair for a photoshoot','Auditing tax records','Teaching a classroom of students','Analysing crime scene evidence','A'),

(6,'Which activity excites you the most?','Coaching a youth sports team','Writing software code','Preparing a balance sheet','Designing a museum exhibit','A'),
(6,'What would you prefer to do?','Leading a fitness bootcamp','Diagnosing a computer network fault','Editing a documentary film','Counselling a struggling student','A'),

(7,'Which activity interests you most?','Finding patterns in a large dataset','Sketching a magazine cover','Interviewing witnesses in court','Managing a hotel''s guest services','A'),
(7,'What would you enjoy working on?','Building a predictive model from data','Designing a fashion lookbook','Leading a public policy meeting','Caring for hospital patients','A'),

(8,'Which activity interests you most?','Designing machines','Managing financial records','Helping patients','Creating artwork','A'),
(8,'What would you enjoy building?','A bridge that can bear heavy loads','A marketing campaign for a new brand','A community counselling program','A short animated film','A'),

(9,'Which activity interests you most?','Drafting a legal contract','Repairing an aircraft engine','Running a chemistry experiment','Editing photographs','A'),
(9,'What would you find satisfying?','Arguing a case in a courtroom','Coaching an athletics team','Styling a fashion shoot','Analysing soil samples','A'),

(10,'Which activity appeals to you most?','Creating an animated character','Preparing a tax return','Diagnosing a patient''s illness','Managing a bank''s loan portfolio','A'),
(10,'What would you enjoy the most?','Storyboarding a short animated film','Auditing a company''s finances','Teaching a mathematics class','Piloting a commercial flight','A'),

(11,'Which activity interests you most?','Building software','Managing financial records','Caring for patients','Creating fashion designs','A'),
(11,'What sounds most exciting?','Writing code to automate a task','Coaching a sports team','Designing a hotel lobby','Counselling a client on wellbeing','A'),

(12,'Which activity interests you most?','Planning a guest''s travel itinerary','Debugging a mobile application','Preparing a court filing','Running a biology experiment','A'),
(12,'What would you enjoy doing?','Managing a hotel''s front desk operations','Designing a database schema','Photographing a news event','Diagnosing patients in a clinic','A'),

(13,'Which activity interests you most?','Creating artwork','Designing machines','Managing financial records','Helping patients','A'),
(13,'What would you enjoy the most?','Illustrating a children''s book','Reconciling company ledgers','Repairing a car engine','Leading a government committee','A'),

(14,'Which activity interests you most?','Leading a project team','Diagnosing a patient','Writing software code','Photographing wildlife','A'),
(14,'What would you enjoy doing?','Setting strategy for a growing company','Treating patients in a clinic','Styling a fashion shoot','Investigating a crime scene','A'),

(15,'Which activity interests you most?','Teaching a classroom of students','Auditing financial records','Piloting an aircraft','Designing a skincare product','A'),
(15,'What would you find rewarding?','Mentoring students through a difficult topic','Building a mobile application','Managing a hotel''s operations','Editing a short film','A'),

(16,'Which activity interests you most?','Growing a brand''s social media audience','Repairing farm equipment','Diagnosing patients','Drafting legal contracts','A'),
(16,'What would you enjoy the most?','Planning an online advertising campaign','Conducting a chemistry experiment','Coaching a sports team','Managing a bank branch','A'),

(17,'Which activity interests you most?','Writing a news article','Designing a building','Treating patients','Managing financial records','A'),
(17,'What would you enjoy doing?','Interviewing people for a documentary','Debugging computer software','Styling a fashion collection','Auditing company accounts','A'),

(18,'Which activity interests you most?','Designing a building''s structure','Preparing financial reports','Treating patients','Creating digital artwork','A'),
(18,'What would you enjoy the most?','Planning the layout of a new building','Teaching a classroom','Coaching an athletics team','Managing a hotel''s guest services','A'),

(19,'Which activity interests you most?','Counselling someone through a difficult time','Repairing aircraft systems','Auditing financial records','Designing fashion garments','A'),
(19,'What would you find rewarding?','Helping a family resolve a conflict','Building a predictive data model','Styling a photoshoot','Managing a bank''s loan portfolio','A'),

(20,'Which activity interests you most?','Studying soil and crop health','Designing a marketing campaign','Auditing financial records','Treating patients','A'),
(20,'What would you enjoy the most?','Working on sustainable farming methods','Writing software code','Styling a fashion shoot','Managing a hotel''s front desk','A');

-- =========================================================
-- SEED: CAREERS (per category)
-- =========================================================
INSERT INTO careers (category_id, career_name, description, relevance) VALUES
(1,'Research Scientist','Designs and runs experiments to advance scientific knowledge.',1.00),
(1,'Lab Technician','Supports experiments and maintains laboratory equipment.',0.90),
(1,'Science Journalist','Communicates scientific findings to the public.',0.75),

(2,'Accountant','Prepares and audits financial records for organisations.',1.00),
(2,'Financial Analyst','Analyses financial data to guide investment decisions.',0.95),
(2,'Tax Consultant','Advises individuals and businesses on tax planning.',0.85),

(3,'Bank Relationship Manager','Manages client accounts and banking relationships.',1.00),
(3,'Credit Analyst','Assesses the creditworthiness of loan applicants.',0.90),
(3,'Investment Banker','Advises companies on raising capital and mergers.',0.85),

(4,'Registered Nurse','Provides direct patient care in clinical settings.',1.00),
(4,'Healthcare Administrator','Manages the operations of a healthcare facility.',0.80),
(4,'Physiotherapist','Helps patients recover movement and manage pain.',0.85),

(5,'Cosmetologist','Provides hair, skin and beauty treatments to clients.',1.00),
(5,'Wellness Coach','Guides clients toward healthier lifestyle habits.',0.85),
(5,'Spa Manager','Runs the operations of a wellness or spa centre.',0.75),

(6,'Sports Coach','Trains athletes and teams to improve performance.',1.00),
(6,'Fitness Trainer','Designs and leads personal or group fitness programs.',0.90),
(6,'Sports Manager','Manages the business side of sports teams and events.',0.75),

(7,'Data Analyst','Turns raw data into actionable business insights.',1.00),
(7,'Data Scientist','Builds models to predict trends and patterns.',0.95),
(7,'Business Intelligence Analyst','Builds dashboards and reports for decision-making.',0.85),

(8,'Software Engineer','Designs and builds software systems and applications.',0.95),
(8,'Mechanical Engineer','Designs and analyses mechanical systems and machines.',1.00),
(8,'Civil Engineer','Plans and oversees construction of infrastructure.',0.90),

(9,'Corporate Lawyer','Advises businesses on legal and regulatory matters.',1.00),
(9,'Legal Advisor','Provides legal guidance to individuals or organisations.',0.90),
(9,'Judge','Presides over court proceedings and delivers rulings.',0.80),

(10,'2D/3D Animator','Creates animated visuals for film, games and media.',1.00),
(10,'Motion Graphics Designer','Produces animated graphics for video and digital media.',0.90),
(10,'VFX Artist','Creates visual effects for film and television.',0.85),

(11,'Software Developer','Builds and maintains software applications.',1.00),
(11,'Web Developer','Builds and maintains websites and web applications.',0.95),
(11,'Database Administrator','Manages and secures organisational databases.',0.85),

(12,'Hotel Manager','Oversees the daily operations of a hotel.',1.00),
(12,'Travel Consultant','Plans and books travel experiences for clients.',0.85),
(12,'Event Manager','Plans and coordinates events and conferences.',0.80),

(13,'Graphic Designer','Creates visual content for print and digital media.',1.00),
(13,'Illustrator','Creates original artwork for books, media and products.',0.90),
(13,'UI/UX Designer','Designs user interfaces and digital experiences.',0.85),

(14,'Business Analyst','Analyses processes to improve business performance.',0.90),
(14,'Operations Manager','Oversees day-to-day operations of an organisation.',1.00),
(14,'Management Consultant','Advises organisations on strategy and performance.',0.85),

(15,'School Teacher','Educates and mentors students in a classroom setting.',1.00),
(15,'Curriculum Designer','Designs educational content and learning programs.',0.80),
(15,'Academic Counsellor','Guides students on academic and career choices.',0.80),

(16,'Digital Marketing Specialist','Plans and runs online marketing campaigns.',1.00),
(16,'SEO Analyst','Improves a website''s visibility in search results.',0.85),
(16,'Social Media Manager','Manages a brand''s presence across social platforms.',0.90),

(17,'Journalist','Researches and reports news stories.',1.00),
(17,'Content Writer','Creates written content for digital and print media.',0.85),
(17,'News Anchor','Presents news broadcasts to an audience.',0.80),

(18,'Architect','Designs buildings and oversees their construction.',1.00),
(18,'Urban Planner','Plans the development and layout of towns and cities.',0.85),
(18,'Interior Designer','Designs functional and aesthetic interior spaces.',0.80),

(19,'Counsellor','Supports individuals through personal and emotional challenges.',1.00),
(19,'Psychologist','Studies behaviour and mental processes to help clients.',0.95),
(19,'Social Services Coordinator','Connects individuals with community support services.',0.80),

(20,'Agricultural Scientist','Researches methods to improve crop and farm output.',1.00),
(20,'Environmental Consultant','Advises on sustainable and environmental practices.',0.90),
(20,'Farm Manager','Manages the daily operations of a farm.',0.80),

(21,'Fashion Designer','Designs clothing and fashion collections.',1.00),
(21,'Fashion Stylist','Curates outfits and looks for clients or shoots.',0.85),
(21,'Textile Designer','Designs fabric patterns and textures.',0.75),

(22,'Commercial Pilot','Operates aircraft to transport passengers or cargo.',1.00),
(22,'Air Traffic Controller','Coordinates aircraft movement for safe air travel.',0.85),
(22,'Airport Operations Manager','Manages the daily operations of an airport.',0.75),

(23,'Pharmacist','Dispenses medication and advises on drug therapy.',1.00),
(23,'Pharmaceutical Researcher','Researches and develops new medications.',0.85),
(23,'Clinical Pharmacologist','Studies how drugs affect the human body.',0.80),

(24,'Biotechnologist','Applies biology to develop products and processes.',1.00),
(24,'Genetic Researcher','Studies genes and heredity for medical advances.',0.90),
(24,'Bioprocess Engineer','Designs processes for biological manufacturing.',0.80),

(25,'Cybersecurity Analyst','Protects systems and networks from digital threats.',1.00),
(25,'Penetration Tester','Tests systems for security vulnerabilities.',0.90),
(25,'Security Consultant','Advises organisations on cybersecurity strategy.',0.85),

(26,'Civil Servant','Administers public programs and government services.',1.00),
(26,'Policy Analyst','Researches and evaluates public policy options.',0.85),
(26,'Public Administrator','Manages public sector organisations and services.',0.80),

(27,'Photographer','Captures visual stories through photography.',1.00),
(27,'Film Editor','Edits raw footage into a finished film or video.',0.90),
(27,'Cinematographer','Designs the visual look and camera work of a film.',0.85),

(28,'Social Worker','Supports individuals and families facing hardship.',1.00),
(28,'Community Outreach Coordinator','Connects communities with support programs.',0.85),
(28,'Case Manager','Coordinates services for clients in need of support.',0.80);

-- =========================================================
-- COURSES  (College / Course Recommendations)
-- Two suggested courses per category, matched by category_id
-- to the order categories were inserted above.
-- =========================================================
CREATE TABLE courses (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    category_id  INT NOT NULL,
    course_name  VARCHAR(150) NOT NULL,
    description  VARCHAR(300) DEFAULT NULL,
    created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
) ENGINE=InnoDB;

INSERT INTO courses (category_id, course_name, description) VALUES
(1,'B.Sc in Physics / Chemistry / Biology','A foundational science degree that opens the door to research, teaching and specialised M.Sc study.'),
(1,'M.Sc in a Core Science Discipline','Advanced specialisation for students aiming at research or a doctoral pathway.'),
(2,'B.Com (Accounting & Finance)','Covers financial accounting, auditing and taxation fundamentals.'),
(2,'Chartered Accountancy (CA) / CMA','Professional certification for accounting, audit and cost management careers.'),
(3,'B.Com with Banking & Insurance','Focused commerce degree covering banking operations and financial services.'),
(3,'Diploma in Banking & Finance','A shorter, practice-oriented path into retail and corporate banking roles.'),
(4,'B.Sc Nursing','A four-year professional nursing degree covering clinical practice and patient care.'),
(4,'General Nursing & Midwifery (GNM)','A practical diploma pathway into nursing and midwifery practice.'),
(5,'Diploma in Cosmetology','Hands-on training in hair, skin and beauty treatments.'),
(5,'Certificate in Wellness & Spa Therapy','Focused training for spa, wellness and holistic therapy careers.'),
(6,'Bachelor of Physical Education (B.P.Ed)','Prepares students for coaching, training and sports education careers.'),
(6,'Diploma in Sports Coaching','Practical certification focused on athlete training and team coaching.'),
(7,'B.Sc / B.Tech in Data Science','Covers statistics, programming and machine learning fundamentals.'),
(7,'PG Diploma in Data Analytics','A focused postgraduate route into analytics and applied data science roles.'),
(8,'B.E. / B.Tech (respective branch)','A four-year engineering degree in Mechanical, Civil, Electrical or related branches.'),
(8,'Diploma in Engineering','A practice-focused diploma pathway into technical and engineering roles.'),
(9,'BA LLB (Integrated, 5-year)','A combined arts and law degree that is the standard path into legal practice.'),
(9,'LLB (3-year, after graduation)','A law degree for students who complete a bachelor''s degree first.'),
(10,'B.Sc in Animation & VFX','Covers 2D/3D animation, visual effects and post-production techniques.'),
(10,'Diploma in Animation & Multimedia','A shorter, portfolio-focused route into animation studios.'),
(11,'Bachelor of Computer Applications (BCA)','A software-focused degree covering programming, databases and web development.'),
(11,'B.Tech in Computer Science / IT','An engineering degree with deeper coverage of computing theory and systems.'),
(12,'Bachelor of Hotel Management (BHM)','Prepares students for hotel operations, guest services and hospitality management.'),
(12,'Diploma in Travel & Tourism','A practical route into travel planning and tourism management roles.'),
(13,'Bachelor of Fine Arts (BFA)','A studio-based degree in illustration, painting or visual art practice.'),
(13,'Bachelor of Design (B.Des)','Covers design principles across visual, product or communication design.'),
(14,'Bachelor of Business Administration (BBA)','Covers management fundamentals, marketing, finance and operations.'),
(14,'Master of Business Administration (MBA)','A postgraduate management degree for leadership and strategy roles.'),
(15,'Bachelor of Education (B.Ed)','The standard professional qualification for school teaching.'),
(15,'Diploma in Elementary Education (D.El.Ed)','A focused route into primary and elementary school teaching.'),
(16,'BBA in Marketing','A business degree with a specialisation in marketing strategy and branding.'),
(16,'PG Diploma in Digital Marketing','A focused postgraduate certification in SEO, social media and online advertising.'),
(17,'BA in Journalism & Mass Communication','Covers reporting, media production and communication theory.'),
(17,'Diploma in Mass Media','A practical route into broadcast, print or digital media careers.'),
(18,'Bachelor of Architecture (B.Arch)','A five-year professional degree covering building design and construction planning.'),
(18,'B.Sc in Interior Design','Focuses on the design and planning of functional interior spaces.'),
(19,'BA in Psychology','Covers human behaviour, cognition and foundational counselling theory.'),
(19,'Master of Social Work (MSW)','A postgraduate degree preparing students for social and community service roles.'),
(20,'B.Sc in Agriculture','Covers crop science, soil management and sustainable farming practices.'),
(20,'B.Sc in Environmental Science','Focuses on sustainability, conservation and environmental policy.'),
(21,'Bachelor of Design (Fashion Design)','Covers garment design, textiles and the fashion business.'),
(21,'Diploma in Fashion Styling','A practical, portfolio-driven route into styling and fashion media.'),
(22,'B.Sc in Aviation','Covers aviation theory, safety regulations and airline operations.'),
(22,'Commercial Pilot License (CPL) Training','Flight training and certification required to fly commercially.'),
(23,'Bachelor of Pharmacy (B.Pharm)','A four-year professional degree in drug formulation and pharmaceutical care.'),
(23,'Diploma in Pharmacy (D.Pharm)','A shorter professional route to becoming a registered pharmacist.'),
(24,'B.Sc in Biotechnology','Applies biology and chemistry to medical, agricultural and industrial research.'),
(24,'M.Sc in Biotechnology','Advanced study for research or industry roles in biotech.'),
(25,'B.Sc / B.Tech with Cybersecurity Specialisation','Covers network security, ethical hacking and digital forensics.'),
(25,'PG Diploma in Cybersecurity','A focused postgraduate certification for security analyst roles.'),
(26,'BA in Public Administration','Covers governance, public policy and civil service preparation.'),
(26,'Civil Services Foundation Course','Preparatory coursework for national and state civil service examinations.'),
(27,'Diploma in Photography','Hands-on training in photographic technique, lighting and editing.'),
(27,'B.Sc in Film Making','Covers cinematography, direction and film production.'),
(28,'Bachelor of Social Work (BSW)','Prepares students for community outreach and case management roles.'),
(28,'Master of Social Work (MSW)','A postgraduate qualification for advanced social work practice.');

