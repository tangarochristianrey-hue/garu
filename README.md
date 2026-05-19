# Christian Rey M. Tangaro (GARU) - Personal Portfolio

A sleek, modern, and dynamic personal portfolio website and Content Management System (CMS) built with PHP, HTML, CSS, and JavaScript. This platform serves as a digital resume and project showcase, complete with a secure admin workspace to easily manage site content without touching the code.

## 🌟 Key Features

### Front-End (Public Website)
- **Modern & Responsive UI**: Built with a sleek dark theme, glassmorphism UI elements, and responsive design for all devices.
- **Dynamic Content**: Data is pulled directly from the database, allowing for seamless updates.
- **Sections Included**: 
  - Home / Hero Section
  - About Me
  - Skills Display (with percentage progress bars)
  - Experience / Timeline
  - Projects Gallery (with detailed project views)
  - Client Reviews & Testimonials
  - Contact Form (with Brevo Transactional Email Integration)

### Back-End (Admin Workspace)
- **Secure Authentication**: Password-protected login area for the administrator (`/admin`).
- **Dashboard Analytics**: Overview of messages, active projects, and system status.
- **Content Manager**:
  - **General Settings**: Update hero text, bio, social links, and contact information.
  - **Skills Management**: Add, edit, or remove technical skills and proficiency levels.
  - **Experience Tracking**: Manage work history and educational milestones.
  - **Project Showcase**: Upload project images, descriptions, client details, and live links.
  - **Message Inbox**: Read and manage contact form submissions directly from the admin panel.

## 🛠 Tech Stack
- **Frontend**: HTML5, CSS3 (Custom properties, animations), JavaScript, Bootstrap 5, FontAwesome 6
- **Backend**: PHP 8+ (PDO for secure database interactions)
- **Database**: MySQL / MariaDB
- **Email Service**: Brevo API (Transactional SMTP for the contact form)

## 📁 Project Structure
```text
/garu
├── /admin/                # Secure Admin Workspace CMS
├── /assets/               # Static assets (CSS, JS, Images, Resume PDFs)
├── /database/             # SQL schema files (if applicable)
├── /includes/             # Reusable PHP components (header, footer, db connection)
├── index.php              # Homepage
├── about.php              # About Me page
├── projects.php           # Project showcase gallery
├── project-details.php    # Individual project deep-dive page
├── skills.php             # Technical skills overview
├── experience.php         # Professional timeline
├── reviews.php            # Client testimonials
├── contact.php            # Contact form
├── setup_db.php           # Initial database setup script
└── .htaccess              # Apache URL rewriting rules for clean URLs
```

## 🚀 Installation & Setup

1. **Clone the Repository**
   Place the project files inside your local web server environment (e.g., `C:\xampp\htdocs\garu`).

2. **Database Configuration**
   - Create a new MySQL database named `garu_portfolio` (or your preferred name).
   - Update the database connection variables in `includes/db.php`. It features smart environment detection for both local (XAMPP) and live (Production) environments.

3. **Database Migration**
   - Run the provided `setup_db.php` script by visiting `http://localhost/garu/setup_db.php` in your browser. This will automatically generate the required tables and structure.
   - For additional table updates, `includes/db.php` includes zero-effort auto-migrations for seamless upgrades.

4. **Email Configuration**
   - In `includes/db.php`, update the `sendBrevoEmail` function with your Brevo API key to enable contact form submissions.

5. **Admin Access**
   - Navigate to `http://localhost/garu/admin` to access the CMS and manage the site.

## 🎨 Theme Customization
The site uses extensive CSS Custom Properties (variables) found in `assets/css/style.css`. You can easily adjust the primary accents, background styles, and typography by modifying the `:root` variables at the top of the stylesheet.

## 📝 License
This is a proprietary personal portfolio. Please do not re-distribute or use for commercial purposes without explicit permission.
