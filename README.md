# Secure Application Project
## Description
The idea was to create a secure authentication and registration system using XAMPP, PHP and MySQL. The application creates the underlying database on requesting the login page. 

### Vulnerabilities
## Session Management
1) Session variables were used to prevent users accessing pages they were not suppose to have access to.
2) A user's session will expire after 10 minutes of activity. 

## Brute Force
1) A user is locked out for 3 minutes after 5 failed login attempts.

## Cross-site Scripting (XSS)
1) All of the input fields are sanitised upon submission to prevent XSS.

## Cross Site Request Forgery (CRSF)
1) A token is used to prevent CRSF. The system checks if a token is present if a user is trying to reset a password. If not, the user is redirected to the login page.
