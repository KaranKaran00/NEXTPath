# NEXTPath
### Adaptive Skill Assessment &amp; Career Discovery Platform

*Discover Your Skills. Find Your Direction.*

NEXTPath is a full-stack web platform that helps students discover their
strengths and explore matching career paths through an adaptive,
multi-category skill assessment.

---

## Overview

Students create a profile, complete a mixed-category multiple-choice
assessment, and receive a personalised skill profile, strength analysis,
and ranked career recommendations — all calculated from their actual
answers. Administrators manage the question bank, career categories,
career mappings and students through a dedicated Admin Dashboard.

## Features

**Student**
- Registration and login with hashed passwords and server-side validation
- 40-question adaptive assessment (configurable), mixed across career
  categories rather than grouped, with a distraction-free one-question
  interface, progress bar and Previous/Next navigation
- Real, calculated category-wise scoring (no randomised results)
- Skill Profile derived from category performance (Analytical Thinking,
  Problem Solving, Technical Aptitude, Communication, Creativity, Leadership)
- Strength Analysis ranking the student's strongest areas
- Multiple ranked Career Recommendations with match percentages
- Career Dashboard with category performance bars (pure CSS, no chart libs)
- Assessment History across every attempt
- Editable profile (interests, skills, hobbies, education, etc.)

**Admin**
- Dashboard with platform-wide totals, recent activity and question distribution
- Full Student directory (no passwords ever shown)
- Full CRUD for Questions, Categories, Careers and Courses
- Category filter on the question bank
- Assessment log across all students
- Statistics: totals, average score, most popular category, most recommended career

**Career Explorer** — a browsable, searchable directory of all 28 career
categories, their mapped careers and suggested courses, independent of
the student's own assessment results.

**College / Course Recommendations** — every career recommendation on the
Career Dashboard and in the downloadable report is paired with real
suggested courses/degrees for that field (e.g. Software Developer →
BCA, B.Tech CS/IT), pulled from the `courses` table so admins can expand
or correct them at any time.

**Downloadable Career Report** — a clean, print-formatted version of a
student's results (`career-report.php`) with a "Print / Save as PDF"
button. It uses the browser's native print-to-PDF rather than a PHP PDF
library, so there's nothing extra to install — it works on any XAMPP
setup out of the box.

## Technology Stack

Strictly:
- **Frontend:** HTML5, CSS3, Vanilla JavaScript (no frameworks, no jQuery, no Bootstrap/Tailwind)
- **Backend:** PHP (PDO, prepared statements throughout)
- **Database:** MySQL / MariaDB

No React, Vue, Angular, Node.js, Python, Laravel, CodeIgniter, Chart.js or
any other framework is used anywhere in this project.

## System Requirements

- PHP 8.0+ with the `pdo_mysql` extension
- MySQL 5.7+ or MariaDB 10.3+
- Apache (via XAMPP/WAMP/LAMP) or the PHP built-in server

## Installation

1. Copy the `nextpath` folder to your web root:
   ```
   C:\xampp\htdocs\nextpath      (Windows/XAMPP)
   /Applications/XAMPP/htdocs/nextpath   (macOS)
   /var/www/html/nextpath        (Linux)
   ```
2. Start **Apache** and **MySQL** from the XAMPP control panel.
3. Open `http://localhost/phpmyadmin`, create a database (or let the SQL
   script do it), and import `database/nextpath.sql`.
4. If your MySQL root user has a password, update `config/database.php`
   accordingly (defaults to `root` / empty password, XAMPP's default).
5. Visit `http://localhost/nextpath/`.

### Database Setup

`database/nextpath.sql` will, on import:
1. Create the `nextpath` database and all tables
2. Seed 28 career categories (20 active with questions, 8 ready for expansion)
3. Seed 40 realistic multiple-choice questions (mixed across the 20 active categories)
4. Seed 84 careers mapped to categories with a relevance weight
5. Seed a category → skill mapping used to build the Skill Profile
6. Seed 56 suggested courses (2 per category) used for College / Course Recommendations
7. Create a sample admin account

**Admin login:** `admin@nextpath.com` / `Admin@123`
(the password hash in the SQL file is a genuine bcrypt hash of this
password, verified against PHP's `password_hash`/`password_verify`).

**Already installed NEXTPath before this update?** You don't need to
re-import everything — just import `database/migration_v2_courses.sql`
the same way (phpMyAdmin → Import). It only adds the new `courses`
table and its seed data; nothing existing is touched.

## Student Workflow

```
Register → Login → Dashboard → Start Assessment
  → 40 mixed-category questions → Submit
  → Overall Score, Category Scores, Skill Profile, Strengths
  → Career Recommendations (+ Suggested Courses) → Career Dashboard
  → Download Career Report (PDF via browser print) → Assessment History
  → Career Explorer (browse all 28 fields any time)
```

## Admin Workflow

```
Admin Login → Admin Dashboard
  → Students (read-only directory)
  → Questions (create / read / update / delete, filter by category)
  → Categories (create / read / update / delete)
  → Careers (create / read / update / delete)
  → Courses (create / read / update / delete)
  → Assessments (full log)
  → Statistics
```

## Assessment Algorithm

```
Student answers question
        ↓
Compare selected answer to questions.correct_answer
        ↓
Store the answer + correctness in assessment_answers
        ↓
Category Score = correct in category / answered in category × 100
        ↓
Overall Score = total correct / total questions × 100
        ↓
Skill Profile = weighted average of category scores per mapped skill
        ↓
Career Match = Category Score × Career Relevance
        ↓
Recommendations sorted by match, highest first
```

The whole pipeline lives in `includes/functions.php` so the scoring or
recommendation formula can be changed in one place.

## Career Recommendation System

Every career belongs to one category and carries a `relevance` weight
(0–1) set by the admin. A student's match percentage for that career is
their score in the career's category multiplied by that relevance,
capped at 100%. Recommendations across all matched careers are then
sorted highest-to-lowest and the top results are shown.

## Security

- **Passwords:** `password_hash()` / `password_verify()` — never stored in plain text
- **SQL:** PDO prepared statements everywhere; no raw string concatenation into queries
- **XSS:** every piece of user-controlled output is passed through `htmlspecialchars()`
  via the `clean()` helper
- **Authentication:** PHP sessions (`includes/functions.php`)
- **Authorization:** `includes/auth.php` (student-only pages) and
  `includes/admin_auth.php` (admin-only pages) guard every protected page;
  a student can never reach `/admin/*` and is redirected back to their
  own dashboard if they try
- **Error handling:** database exceptions are logged server-side and never
  shown to the user; the user only ever sees "Something went wrong. Please try again."

## Project Structure

```
nextpath/
├── index.php, login.php, register.php, logout.php
├── contact.php, privacy.php, terms.php
├── config/database.php
├── includes/
│   ├── header.php, footer.php          (public site chrome)
│   ├── sidebar.php, admin-sidebar.php   (dashboard navigation)
│   ├── auth.php, admin_auth.php         (route guards)
│   └── functions.php                    (all shared logic)
├── student/
│   ├── dashboard.php, assessment.php, submit_assessment.php
│   ├── result.php, career-dashboard.php, career-report.php, history.php
│   ├── explorer.php
│   └── profile.php, update-profile.php
├── admin/
│   ├── dashboard.php, students.php, assessments.php, statistics.php
│   ├── questions.php, add-question.php, edit-question.php, delete-question.php
│   ├── categories.php, add-category.php, edit-category.php, delete-category.php
│   ├── careers.php, add-career.php, edit-career.php, delete-career.php
│   └── courses.php, add-course.php, edit-course.php, delete-course.php
├── assets/
│   ├── css/ (main, auth, dashboard, assessment, admin, explorer, report)
│   └── js/  (main, assessment, validation, explorer)
└── database/ (nextpath.sql, migration_v2_courses.sql)
```

## Design System

- **Typography:** Manrope (headings) / Inter (body), a modern grotesk pairing,
  loaded from Google Fonts with system-font fallbacks so the layout stays
  fully functional if the webfont fails to load
- **Color palette:** off-white background, dark text, restrained navy/green
  accents (`#1F3A5F` primary, `#4C6548` secondary) — no gradients, glassmorphism
  or decorative illustration
- **Components:** consistent buttons, cards, tables, badges and form states
  with moderate border radii (7–12px) and subtle shadows only
- **Responsive:** a single fluid layout adapts from mobile through desktop
  via CSS media queries — no separate mobile site

## Future Improvements

- Password reset via email
- Pagination on large admin tables
- Configurable skill-to-category weighting from the Admin Dashboard
- Export of assessment history to PDF
- Multi-language support

---

Built with HTML5, CSS3, Vanilla JavaScript, PHP and MySQL — no frameworks.
