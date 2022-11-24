# Secure Authentication System
## Description
The idea was to create a secure authentication and registration system using XAMPP, PHP and MySQL. The application creates the underlying database on requesting the login page. 

## Vulnerabilities
### Session Management
1) Session variables were used to prevent users accessing pages they were not suppose to have access to.
2) A user's session will expire after 10 minutes of inactivity. 

### Brute Force
1) A user is locked out for 3 minutes after 5 failed login attempts.

### Cross-site Scripting (XSS)
1) All of the input fields are sanitised upon submission to prevent XSS.

### Cross Site Request Forgery (CSRF)
1) A token is used to prevent CRSF. The system checks if a token is present if a user is trying to reset a password. If not, the user is redirected to the login page.

## Clone the project
1) Above the list of files, click **code**.
2) To clone the repository using HTTPS, under "Clone with HTTPS," click the copy icon.
3) Open Git Bash.
4) Change the current working directory to the location where you want the cloned directory.
5) Type git clone, and then paste the URL you copied earlier. For example: git clone https://github.com/Adamcoakley/secure-authentication.git
6) Press enter and the clone will be created.

## Usage
Ensure XAMPP is installed on your computer. Load XAMPP and start both the Apache and MySQL server.

The cloned project needs to be stored in the htdocs folder on C directory. For example: C:\XAMPP\htdocs\

Enter the path of the login file into your browser followed by "localhost." For example: http://localhost/SecureAppsProject/login.php 

Enjoy!
