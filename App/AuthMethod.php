<?php

namespace App;

use \App\Models\User;

/**
 * Authentication Methods
 * 
 * PHP version 7.0
 */ 
class AuthMethod {

    /**
     * Login the user
     * 
     * @param User $user The user model
     * 
     * @return void
     */ 
    public static function login($user)
    {
        // regenerate session_id but maintain $_SESSION-data to prevent
        // session fixation attacks due cross-site-scripting
        session_regenerate_id(true);

        $_SESSION['user_id'] = $user->id;
    }

    /**
     * Logout the user
     * 
     * @return void
     */ 
    public static function logout()
    {
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
    }

    /**
     * Remember the originally-requested page in the session
     */ 
    public static function rememberRequestedPage()
    {
        $_SESSION['return_to'] = $_SERVER['REQUEST_URI'];
    }

    /**
     * Get the originally-requested page to return to after requiring login, or * default to the homepage
     * 
     * @return string The URL from the the requested page, or back to homepage
     */ 
    public static function getRequestedPage()
    {
        return $_SESSION['return_to'] ?? '/';
    }

    /**
     * Get the current logged-in user, from the session or the remember-me
     * cookie
     * 
     * @return mixed The user model or null of not logged in
     */  
    public static function getUser()
    {
        if (isset($_SESSION['user_id'])) {
            return User::findByID($_SESSION['user_id']);
        }
    }
}