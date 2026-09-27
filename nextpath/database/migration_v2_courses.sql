-- =========================================================
-- NEXTPath — Migration v2
-- Adds College / Course Recommendations.
-- Safe to run on a database created by the original
-- database/nextpath.sql — it only adds a new table and data,
-- nothing existing is touched or removed.
--
-- Usage: import this file the same way you imported
-- nextpath.sql (phpMyAdmin → Import, or `mysql -u root nextpath < migration_v2_courses.sql`)
-- =========================================================

USE nextpath;

CREATE TABLE IF NOT EXISTS courses (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    category_id  INT NOT NULL,
    course_name  VARCHAR(150) NOT NULL,
    description  VARCHAR(300) DEFAULT NULL,
    created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
) ENGINE=InnoDB;

INSERT INTO courses (category_id, course_name, description)
SELECT * FROM (SELECT
    (SELECT id FROM categories WHERE name = 'Science') AS category_id,
    'B.Sc in Physics / Chemistry / Biology' AS course_name,
    'A foundational science degree that opens the door to research, teaching and specialised M.Sc study.' AS description
UNION ALL SELECT (SELECT id FROM categories WHERE name = 'Science'), 'M.Sc in a Core Science Discipline', 'Advanced specialisation for students aiming at research or a doctoral pathway.'

UNION ALL SELECT (SELECT id FROM categories WHERE name = 'Accounting & Finance'), 'B.Com (Accounting & Finance)', 'Covers financial accounting, auditing and taxation fundamentals.'
UNION ALL SELECT (SELECT id FROM categories WHERE name = 'Accounting & Finance'), 'Chartered Accountancy (CA) / CMA', 'Professional certification for accounting, audit and cost management careers.'

UNION ALL SELECT (SELECT id FROM categories WHERE name = 'Banking'), 'B.Com with Banking & Insurance', 'Focused commerce degree covering banking operations and financial services.'
UNION ALL SELECT (SELECT id FROM categories WHERE name = 'Banking'), 'Diploma in Banking & Finance', 'A shorter, practice-oriented path into retail and corporate banking roles.'

UNION ALL SELECT (SELECT id FROM categories WHERE name = 'Nursing & Healthcare'), 'B.Sc Nursing', 'A four-year professional nursing degree covering clinical practice and patient care.'
UNION ALL SELECT (SELECT id FROM categories WHERE name = 'Nursing & Healthcare'), 'General Nursing & Midwifery (GNM)', 'A practical diploma pathway into nursing and midwifery practice.'

UNION ALL SELECT (SELECT id FROM categories WHERE name = 'Beauty & Wellness'), 'Diploma in Cosmetology', 'Hands-on training in hair, skin and beauty treatments.'
UNION ALL SELECT (SELECT id FROM categories WHERE name = 'Beauty & Wellness'), 'Certificate in Wellness & Spa Therapy', 'Focused training for spa, wellness and holistic therapy careers.'

UNION ALL SELECT (SELECT id FROM categories WHERE name = 'Fitness & Sports'), 'Bachelor of Physical Education (B.P.Ed)', 'Prepares students for coaching, training and sports education careers.'
UNION ALL SELECT (SELECT id FROM categories WHERE name = 'Fitness & Sports'), 'Diploma in Sports Coaching', 'Practical certification focused on athlete training and team coaching.'

UNION ALL SELECT (SELECT id FROM categories WHERE name = 'Data Science'), 'B.Sc / B.Tech in Data Science', 'Covers statistics, programming and machine learning fundamentals.'
UNION ALL SELECT (SELECT id FROM categories WHERE name = 'Data Science'), 'PG Diploma in Data Analytics', 'A focused postgraduate route into analytics and applied data science roles.'

UNION ALL SELECT (SELECT id FROM categories WHERE name = 'Engineering'), 'B.E. / B.Tech (respective branch)', 'A four-year engineering degree in Mechanical, Civil, Electrical or related branches.'
UNION ALL SELECT (SELECT id FROM categories WHERE name = 'Engineering'), 'Diploma in Engineering', 'A practice-focused diploma pathway into technical and engineering roles.'

UNION ALL SELECT (SELECT id FROM categories WHERE name = 'Law'), 'BA LLB (Integrated, 5-year)', 'A combined arts and law degree that is the standard path into legal practice.'
UNION ALL SELECT (SELECT id FROM categories WHERE name = 'Law'), 'LLB (3-year, after graduation)', 'A law degree for students who complete a bachelor''s degree first.'

UNION ALL SELECT (SELECT id FROM categories WHERE name = 'Animation'), 'B.Sc in Animation & VFX', 'Covers 2D/3D animation, visual effects and post-production techniques.'
UNION ALL SELECT (SELECT id FROM categories WHERE name = 'Animation'), 'Diploma in Animation & Multimedia', 'A shorter, portfolio-focused route into animation studios.'

UNION ALL SELECT (SELECT id FROM categories WHERE name = 'Information Technology'), 'Bachelor of Computer Applications (BCA)', 'A software-focused degree covering programming, databases and web development.'
UNION ALL SELECT (SELECT id FROM categories WHERE name = 'Information Technology'), 'B.Tech in Computer Science / IT', 'An engineering degree with deeper coverage of computing theory and systems.'

UNION ALL SELECT (SELECT id FROM categories WHERE name = 'Hospitality & Tourism'), 'Bachelor of Hotel Management (BHM)', 'Prepares students for hotel operations, guest services and hospitality management.'
UNION ALL SELECT (SELECT id FROM categories WHERE name = 'Hospitality & Tourism'), 'Diploma in Travel & Tourism', 'A practical route into travel planning and tourism management roles.'

UNION ALL SELECT (SELECT id FROM categories WHERE name = 'Arts & Design'), 'Bachelor of Fine Arts (BFA)', 'A studio-based degree in illustration, painting or visual art practice.'
UNION ALL SELECT (SELECT id FROM categories WHERE name = 'Arts & Design'), 'Bachelor of Design (B.Des)', 'Covers design principles across visual, product or communication design.'

UNION ALL SELECT (SELECT id FROM categories WHERE name = 'Business & Management'), 'Bachelor of Business Administration (BBA)', 'Covers management fundamentals, marketing, finance and operations.'
UNION ALL SELECT (SELECT id FROM categories WHERE name = 'Business & Management'), 'Master of Business Administration (MBA)', 'A postgraduate management degree for leadership and strategy roles.'

UNION ALL SELECT (SELECT id FROM categories WHERE name = 'Teaching & Education'), 'Bachelor of Education (B.Ed)', 'The standard professional qualification for school teaching.'
UNION ALL SELECT (SELECT id FROM categories WHERE name = 'Teaching & Education'), 'Diploma in Elementary Education (D.El.Ed)', 'A focused route into primary and elementary school teaching.'

UNION ALL SELECT (SELECT id FROM categories WHERE name = 'Digital Marketing'), 'BBA in Marketing', 'A business degree with a specialisation in marketing strategy and branding.'
UNION ALL SELECT (SELECT id FROM categories WHERE name = 'Digital Marketing'), 'PG Diploma in Digital Marketing', 'A focused postgraduate certification in SEO, social media and online advertising.'

UNION ALL SELECT (SELECT id FROM categories WHERE name = 'Media & Journalism'), 'BA in Journalism & Mass Communication', 'Covers reporting, media production and communication theory.'
UNION ALL SELECT (SELECT id FROM categories WHERE name = 'Media & Journalism'), 'Diploma in Mass Media', 'A practical route into broadcast, print or digital media careers.'

UNION ALL SELECT (SELECT id FROM categories WHERE name = 'Architecture'), 'Bachelor of Architecture (B.Arch)', 'A five-year professional degree covering building design and construction planning.'
UNION ALL SELECT (SELECT id FROM categories WHERE name = 'Architecture'), 'B.Sc in Interior Design', 'Focuses on the design and planning of functional interior spaces.'

UNION ALL SELECT (SELECT id FROM categories WHERE name = 'Psychology & Human Services'), 'BA in Psychology', 'Covers human behaviour, cognition and foundational counselling theory.'
UNION ALL SELECT (SELECT id FROM categories WHERE name = 'Psychology & Human Services'), 'Master of Social Work (MSW)', 'A postgraduate degree preparing students for social and community service roles.'

UNION ALL SELECT (SELECT id FROM categories WHERE name = 'Agriculture & Environmental Science'), 'B.Sc in Agriculture', 'Covers crop science, soil management and sustainable farming practices.'
UNION ALL SELECT (SELECT id FROM categories WHERE name = 'Agriculture & Environmental Science'), 'B.Sc in Environmental Science', 'Focuses on sustainability, conservation and environmental policy.'

UNION ALL SELECT (SELECT id FROM categories WHERE name = 'Fashion'), 'Bachelor of Design (Fashion Design)', 'Covers garment design, textiles and the fashion business.'
UNION ALL SELECT (SELECT id FROM categories WHERE name = 'Fashion'), 'Diploma in Fashion Styling', 'A practical, portfolio-driven route into styling and fashion media.'

UNION ALL SELECT (SELECT id FROM categories WHERE name = 'Aviation'), 'B.Sc in Aviation', 'Covers aviation theory, safety regulations and airline operations.'
UNION ALL SELECT (SELECT id FROM categories WHERE name = 'Aviation'), 'Commercial Pilot License (CPL) Training', 'Flight training and certification required to fly commercially.'

UNION ALL SELECT (SELECT id FROM categories WHERE name = 'Pharmacy'), 'Bachelor of Pharmacy (B.Pharm)', 'A four-year professional degree in drug formulation and pharmaceutical care.'
UNION ALL SELECT (SELECT id FROM categories WHERE name = 'Pharmacy'), 'Diploma in Pharmacy (D.Pharm)', 'A shorter professional route to becoming a registered pharmacist.'

UNION ALL SELECT (SELECT id FROM categories WHERE name = 'Biotechnology'), 'B.Sc in Biotechnology', 'Applies biology and chemistry to medical, agricultural and industrial research.'
UNION ALL SELECT (SELECT id FROM categories WHERE name = 'Biotechnology'), 'M.Sc in Biotechnology', 'Advanced study for research or industry roles in biotech.'

UNION ALL SELECT (SELECT id FROM categories WHERE name = 'Cybersecurity'), 'B.Sc / B.Tech with Cybersecurity Specialisation', 'Covers network security, ethical hacking and digital forensics.'
UNION ALL SELECT (SELECT id FROM categories WHERE name = 'Cybersecurity'), 'PG Diploma in Cybersecurity', 'A focused postgraduate certification for security analyst roles.'

UNION ALL SELECT (SELECT id FROM categories WHERE name = 'Government & Public Administration'), 'BA in Public Administration', 'Covers governance, public policy and civil service preparation.'
UNION ALL SELECT (SELECT id FROM categories WHERE name = 'Government & Public Administration'), 'Civil Services Foundation Course', 'Preparatory coursework for national and state civil service examinations.'

UNION ALL SELECT (SELECT id FROM categories WHERE name = 'Film & Photography'), 'Diploma in Photography', 'Hands-on training in photographic technique, lighting and editing.'
UNION ALL SELECT (SELECT id FROM categories WHERE name = 'Film & Photography'), 'B.Sc in Film Making', 'Covers cinematography, direction and film production.'

UNION ALL SELECT (SELECT id FROM categories WHERE name = 'Social Work'), 'Bachelor of Social Work (BSW)', 'Prepares students for community outreach and case management roles.'
UNION ALL SELECT (SELECT id FROM categories WHERE name = 'Social Work'), 'Master of Social Work (MSW)', 'A postgraduate qualification for advanced social work practice.'
) AS course_seed
WHERE category_id IS NOT NULL;
