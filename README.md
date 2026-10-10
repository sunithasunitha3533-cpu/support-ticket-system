
# Support Ticket System

## 1. Project Overview

The Support Ticket System is a web-based application developed to manage user complaints and support requests. Users can create tickets, track ticket status, and view replies from the administrator. The administrator can manage tickets, update their status, and reply to users.

## 2. Objectives

- To provide an easy way to submit support requests.
- To allow users to track their tickets.
- To help administrators manage support tickets.
- To maintain reply history and notifications.
- To display ticket status clearly.

## 3. Technologies Used

- Frontend: HTML, CSS
- Backend: PHP
- Database: MySQL
- Server: XAMPP
- Code Editor: Visual Studio Code

## 4. Features

### User Features

- User registration and login
- User dashboard
- Create support tickets
- View ticket details
- Track ticket status
- View administrator replies
- Receive in-app notifications
- Logout

### Admin Features

- Admin login
- Admin dashboard
- View all tickets
- View ticket statistics
- Reply to tickets
- Update ticket status
- View ticket details

## 5. Database

Database name: `support_ticket_system`

Main tables:

- `users`
- `tickets`
- `replies`
- `notifications`

## 6. Installation and Setup

1. Install XAMPP.
2. Start Apache and MySQL.
3. Place the project folder inside `C:\xampp\htdocs\`.
4. Open phpMyAdmin.
5. Create the database named `support_ticket_system`.
6. Import or create the required database tables.
7. Check the database connection settings in `db.php`.
8. Open the application in a browser.

Application URL:

`http://localhost/support_ticket_system/templates/login.php`

## 7. How to Use

1. Register a user account.
2. Log in to the application.
3. Create a support ticket.
4. View the ticket in the user dashboard.
5. Log in as an administrator to view all tickets.
6. Reply to tickets and update their status.
7. Check notifications from the user account.

## 8. Testing

The following features were tested during development:

- Login and logout
- User dashboard
- Admin dashboard
- Ticket creation and display
- Ticket status updates
- Administrator replies
- In-app notifications
- Access to protected pages

## 9. Security

- Session-based login checks are used for protected pages.
- Admin pages check the user's role.
- Prepared statements are used for relevant database operations.
- Output is escaped when displaying user-provided text.

Further security testing is recommended before production use.

## 10. AI Tool Usage Disclosure

AI assistance from ChatGPT was used during development to understand PHP and MySQL concepts, troubleshoot errors, improve code, and prepare project documentation. The application was tested locally during development.

## 11. Conclusion

The Support Ticket System provides a simple way to manage support requests. It helps users submit tickets and track their status while allowing administrators to review tickets, reply to users, and manage ticket progress.