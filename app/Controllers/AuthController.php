<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;

class AuthController extends Controller {

    private $userModel;

    public function __construct($config) {
        parent::__construct($config);
        $this->userModel = $this->loadModel('User');
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';

            if (empty($username) || empty($password)) {
                $this->renderView('auth.login', ['error' => 'Username and password are required.']);
                return;
            }

            $user = $this->userModel->findByUsername($username);

            if ($user && $this->userModel->verifyPassword($user, $password)) {
                if ($user['status'] == 0) {
                    $this->renderView('auth.login', ['error' => 'Your account is disabled. Please contact an administrator.']);
                    return;
                }
                
                // Regenerate session ID to prevent session fixation
                session_regenerate_id(true); 
                
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                // Store other necessary user info in session, e.g., role
                // $_SESSION['role_id'] = ... (fetch role if needed)

                $this->userModel->updateLastLogin($user['id']);
                    $this->logger?->log('Authentication', 'Login Success');

                // Redirect to a dashboard or home page
                $this->redirect('index.php?controller=home&action=index');
            } else {
                    // Inside login(), else branch of password verification
                    // Before rendering view with error:
                    $attemptedUsername = $username; // Already have this
                    // Try to find user_id based on username to log against that user.
                    // This means an extra DB call on failed login.
                    $failedLoginUserId = null;
                    if ($user) { // $user is fetched based on username
                        $failedLoginUserId = $user['id'];
                    }
                    // If we want to log even if username doesn't exist, we'd need a placeholder or allow NULL user_id in logs.
                    // For now, let's log if user was found but password was wrong.
                    if ($failedLoginUserId) {
                         $_SESSION['user_id'] = $failedLoginUserId; // Temporarily set for logger
                         $this->logger?->log('Authentication', 'Login Failed', ['username' => $attemptedUsername, 'reason' => 'Invalid password']);
                         unset($_SESSION['user_id']); // Unset temporary session
                    } else {
                         // If username itself was not found, we cannot log against a specific user_id.
                         // We could log this with a system user_id if desired, or skip.
                         // For now, skipping if username not found to avoid user_id issues.
                         // Alternative: ActivityLogger could have a method for anonymous/system logs.
                         error_log("Login attempt for non-existent username: " . $attemptedUsername); // Log to server error log instead
                    }
                $this->renderView('auth.login', ['error' => 'Invalid username or password.']);
            }
        } else {
            // Display the login form
            $this->renderView('auth.login');
        }
    }

    public function logout() {
            $this->logger?->log('Authentication', 'Logout Success');
        // Unset all of the session variables.
        $_SESSION = array();

        // If it's desired to kill the session, also delete the session cookie.
        // Note: This will destroy the session, and not just the session data!
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        // Finally, destroy the session.
        session_destroy();

        // Redirect to login page
        $this->redirect('index.php?controller=auth&action=login&message=logged_out');
    }
}
