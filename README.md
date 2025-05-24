# Scoring Application - Public Scoreboard & Judge Portal

Welcome to the Scoring Application, a LAMP (Linux, Apache, MySQL, PHP) stack-based system designed to manage and display participant scores in real-time. This project features a public scoreboard (`index.php`), a judge portal (`judge.php`), and an admin dashboard (`admin.php`), all styled with an aesthetic featuring a red-to-gold gradient (#D81F26 to #FFD700), dark gray background (#121212), and white text (#FFFFFF). The application is built for ease of use, security, and visual appeal, leveraging modern web technologies like Tailwind CSS, AJAX, and Chart.js.

---

## Setup Instructions

To set up and run the Scoring Application locally, follow these steps:

### Prerequisites
- **Operating System**: Linux (e.g., Ubuntu) or Windows with WSL.
- **XAMPP**: Install XAMPP (version 8.0+ recommended) from [apachefriends.org](https://www.apachefriends.org/).
- **Text Editor/IDE**: Use an IDE like VS Code or PhpStorm for development.
- **Internet Connection**: Required for CDN-hosted dependencies (e.g., Tailwind CSS, Roboto font).

### Installation Steps
1. **Clone or Download the Repository**:
   - Clone the repository to your local machine:
     ```bash
     git clone https://github.com/Cidscapital/Scoring-App.git
     cd Scoring-App
     ```
   - Alternatively, download the ZIP file and extract it to your XAMPP `htdocs` directory (e.g., `/opt/lampp/htdocs/scoring_app` on Linux or `C:\xampp\htdocs\scoring_app` on Windows).

2. **Configure the Database**:
   - Start XAMPP: `sudo /opt/lampp/lampp start` (Linux) or use the XAMPP Control Panel (Windows).
   - Access phpMyAdmin and create a database named `scoring_app`.
   - Import the SQL schema below into the `scoring_app` database.

3. **Set Up Database Connection**:
   - Edit `includes/config.php` with your MySQL credentials:
     ```php
     <?php
     define('DB_HOST', 'localhost');
     define('DB_USER', 'your_username');
     define('DB_PASS', 'your_password');
     define('DB_NAME', 'scoring_app');
     ?>
     ```
   - Ensure `includes/db.php` is present to establish the connection.

4. **Place Files**:
   - Ensure the file structure is maintained.

5. **Run the Application**:
   - Open a browser and navigate to respective URLs for the following:
     - Scoreboard
     - Judge Portal
     - Admin Dashboard
   - The scoreboard auto-refreshes every 10 seconds; the judge portal allows score management without login (via judge ID selection).

6. **Testing**:
   - Verify data updates in real-time on the scoreboard.
   - Test score submission and history in the judge portal.
   - Ensure the admin dashboard reflects accurate metrics.

---

## Database Schema

The application uses a MySQL database named `scoring_app` with the following tables. Use these `CREATE TABLE` statements to set up the schema:

```sql
CREATE DATABASE scoring_app CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE scoring_app;

-- Create the judges table
CREATE TABLE judges (
    id VARCHAR(50) PRIMARY KEY,
    name VARCHAR(100) NOT NULL
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Create the participants table
CREATE TABLE participants (
    id VARCHAR(50) PRIMARY KEY,
    name VARCHAR(100) NOT NULL
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Create the scores table with relationships and constraints
CREATE TABLE scores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    judge_id VARCHAR(50),
    participant_id VARCHAR(50),
    score INT CHECK (score >= 1 AND score <= 100),
    UNIQUE KEY unique_score (judge_id, participant_id),
    FOREIGN KEY (judge_id) REFERENCES judges(id) ON DELETE CASCADE,
    FOREIGN KEY (participant_id) REFERENCES participants(id) ON DELETE CASCADE
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Insert sample participants
INSERT INTO participants (id, name) VALUES
('p1', 'Alex Smith'),
('p2', 'Bella Jones'),
('p3', 'Chris Lee'),
('p4', 'Dana Kim'),
('p5', 'Evan Patel');

-- Insert sample judges
INSERT INTO judges (id, name) VALUES
('j1', 'Judge Emma'),
('j2', 'Judge Noah'),
('j3', 'Judge Liam');

-- Insert sample scores
INSERT INTO scores (judge_id, participant_id, score) VALUES
('j1', 'p1', 8),
('j1', 'p2', 9),
('j1', 'p3', 7),
('j2', 'p1', 8),
('j2', 'p4', 9),
('j3', 'p2', 8),
('j3', 'p5', 9);
```

### Notes
- **Data Types**: `VARCHAR(50)` for IDs and `VARCHAR(100)` for names allow flexibility.
- **Constraints**: `CHECK` ensures scores are between 1 and 10; `FOREIGN KEY` enforces referential integrity.
- **Indexes**: Primary keys are indexed automatically for performance.

---

## Assumptions Made

- **No Authentication**: The judge portal does not require login; judges select their ID from a dropdown, stored in session for the session duration.
- **Local Development**: The application is designed for XAMPP on a local machine; deployment to a live server may require additional configuration (e.g., HTTPS, server-side optimization).
- **Static Data**: Initial participant and judge data are assumed to be pre-populated; no admin interface for adding them is provided.
- **Single Session**: Each browser session handles one judge at a time; no multi-user conflict resolution is implemented.
- **CDN Availability**: Dependencies (Tailwind CSS, Roboto font) are loaded via CDN, assuming a stable internet connection.

---

## Design Choices

### Database Structure
- **Relational Design**: The schema uses three tables (`judges`, `participants`, `scores`) to maintain a normalized structure, reducing redundancy and ensuring data integrity with foreign keys.
- **Flexible IDs**: `VARCHAR` for IDs allows custom identifiers (e.g., "J001", "P001"), accommodating various naming conventions.
- **Score Constraints**: A `CHECK` constraint on `scores` ensures valid input (1-10), enforced at the database level for consistency.

### PHP Constructs
- **API Modularity**: API scripts (e.g., `get_scoreboard.php`, `submit_score.php`) are separated into the `api` folder for maintainability and scalability, using `include` for database connections.
- **Prepared Statements**: All database queries use MySQLi prepared statements to prevent SQL injection, enhancing security.
- **Session Management**: PHP sessions store judge IDs without authentication, simplifying access while maintaining state.
- **JSON Responses**: APIs return JSON for AJAX calls, ensuring compatibility with modern JavaScript frameworks and efficient data transfer.

### Frontend Design
- **Aesthetic**: The red-to-gold gradient (#D81F26 to #FFD700) and dark gray background (#121212) create a bold, professional look.
- **Glassmorphism**: Semi-transparent containers with blur effects (e.g., `backdrop-filter: blur(10px)`) provide a futuristic, premium feel.
- **Tailwind CSS**: Chosen for rapid development and responsive design, offering utility classes for a consistent UI across `admin.php`, `judge.php`, and `index.php`.
- **AJAX**: Used for real-time updates in `index.php`, ensuring a smooth user experience without full page reloads.
- **Animations**: Fade-in and hover effects enhance engagement, implemented with CSS keyframes for performance.

### Why These Choices?
- The relational database structure supports scalability and data integrity, critical for a scoring system.
- PHP’s modularity and security features align with LAMP best practices.
- The aesthetic choices reflect a modern, user-friendly interface, appealing to both judges and public viewers.

---

## Future Enhancements

If given more time, the following enhancements would elevate the application:

- **User Authentication**: Implement secure login for judges with passwords, using PHP’s `password_hash` and `password_verify`.
- **Participant Management**: Add an admin interface to add/edit/delete participants and judges via `admin.php`.
- **Score Trends**: Integrate Chart.js for visualizations of score trends over time on both `judge.php` and `index.php`.
- **Search and Filter**: Add search bars to `index.php` and `judge.php` to filter participants by score range.
- **Mobile App**: Develop a Progressive Web App (PWA) version for offline access and push notifications.
- **Export Functionality**: Allow exporting scoreboard data as PDF or CSV from `index.php` using PHP’s `fputcsv` or a library like TCPDF.

---

## Contributing

Feel free to fork this repository, submit pull requests, or report issues on GitHub. Contributions to improve design, add features, or optimize performance are welcome!

## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details.

## Contact

For questions or support, reach out via [Mail](mailto:jesse.jason2002@gmail.com).

---
