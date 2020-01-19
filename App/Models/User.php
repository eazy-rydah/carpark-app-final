<?php

namespace App\Models;

use PDO;
use \App\Token;
use \Core\View;
use \App\Mail;

/**
 * User model
 *
 * PHP version 7.0
 */
class User extends \Core\Model
{

    /**
     * Error messages
     * 
     * @var array
     */  
    public $errors = [];

    /**
     * Class constructor
     * 
     * @param array $data Initial property values (optional)
     * 
     * @return void
     */  
    public function __construct($data = [])
    {
      
        foreach ($data as $key => $value) {
            $this->$key = $value;
        };

    }

    /**
     * Save the user model with the current property values
     * 
     * @return boolean True if the user was saved, false otherwise
     */ 
    public function save()
    {
        $this->validate();

        if (empty($this->errors)) {

            $password_hash = password_hash($this->password, PASSWORD_DEFAULT);

            $token = new Token();
            $hashed_token = $token->getHash();
            $this->activation_token = $token->getValue();

           /*  $sql = 'INSERT INTO user (first_name, last_name, email ,password_hash,          type, activation_hash)
                    VALUES (:first_name, :last_name, :email, :password_hash, :type :activation_hash)'; */
            $sql = 'INSERT INTO user (first_name, last_name, email ,password_hash,          user_role_id, activation_hash)
            VALUES (:first_name, :last_name, :email, :password_hash, :user_role_id, :activation_hash)';
    
            $db = static::getDB();
            $stmt = $db->prepare($sql);
    
            $stmt->bindValue(':first_name', $this->first_name, PDO::PARAM_STR);
            $stmt->bindValue(':last_name', $this->last_name, PDO::PARAM_STR);$stmt->bindValue(':email', $this->email, PDO::PARAM_STR);
            $stmt->bindValue(':password_hash', $password_hash, PDO::PARAM_STR);
            $stmt->bindValue(':user_role_id', $this->user_role_id, PDO::PARAM_INT);
            $stmt->bindValue(':activation_hash', $hashed_token, PDO::PARAM_STR);
    
            return $stmt->execute();

        }

        return false;
    }

    /**
     * Validate the current property values, adding validation error messages
     * to the errors array property
     *  
     * @return void
     */  
    public function validate()
    {

        if ($this->first_name == '') {
            $this->errors[] = 'Bitte Vornamen angeben';
        }

        if ($this->last_name == '') {
            $this->errors[] = 'Bitte Nachnamen angeben';
        }

        if (filter_var($this->email, FILTER_VALIDATE_EMAIL) === false) {
            $this->errors[] = 'E-Mail-Adresse ist ungültig';
        }
        if (static::emailExists($this->email, $this->user_id ?? null)) {
            $this->errors[] = 'E-Mail-Adresse ist bereits vergeben';
        }

        if (isset($this->password) || isset($this->password_confirmation)) {

            if ($this->password != $this->password_confirmation) {
                $this->errors[] = 'Passwörter müssen übereinstimmen';
            }
    
            if (strlen($this->password) < 6) {
                $this->errors[] = 'Passwort muss mindestens 6 Zeichen lang sein';
            }
    
            if (preg_match('/.*[a-z]+.*/i', $this->password) == 0) {
                $this->errors[] = 'Passwort muss mindestens einen Buchstaben enthalten';
            }
    
            if (preg_match('/.*\d+.*/i', $this->password) == 0) {
                $this->errors[] = 'Passwort muss mindestens eine Zahl enthalten';
            }
        }
    }

    /**
     * See if a user record already exists with the specified email
     * 
     * @param string $email email address to search for
     * @param string $ignore_id Return false anyway if the record found has 
     * this ID
     * 
     * @return boolean True if a record already exists with the specified email,
     * false otherwise
    */
    public static function emailExists($email, $ignore_id = null)
    {
       $user = static::findByEmail($email);

       if ($user) {
           if ($user->user_id != $ignore_id) {
               return true;
           }
       }

       return false;
    }

    /**
     * Find a user model by email address
     * 
     * @param string $email address to search for
     * 
     * @return mixed User object if found, false otherwise
     */  
    public static function findByEmail($email)
    {
        $sql = 'SELECT * FROM user WHERE email = :email';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);

        // fetch object with dynamic namespace, instead of array
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        
        $stmt->execute();

        return $stmt->fetch(); 
    }

    /**
     * Authenticate a user by email and password
     * 
     * @param string $email email address
     * @param string $password password
     * 
     * @return mixed The user Object or false if authentication fails
     */  
    public static function authenticate($email, $password)
    {
        $user = static::findByEmail($email);

        if (($user && $user->is_active)) {
            if (password_verify($password, $user->password_hash)) {
                return $user;
            }
        }

        return false;
    }

    /**
     * Find a user model by ID
     * 
     * @param integer $id The user ID
     * 
     * @return mixed User object if found, false otherwise
     */  
    public static function findByID($id)
    {
        $sql = 'SELECT * FROM user WHERE user_id = :id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        // fetch object with dynamic namespace, instead of array
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        
        $stmt->execute();

        return $stmt->fetch(); 
    }

    /**
     * Send password reset instructions to the user specified
     * 
     * @param string $email The email address
     * 
     * @return void
     */  
    public static function sendPasswordReset($email)
    {
        $user = static::findByEmail($email);

        if ($user) {

            if ($user->startPasswordReset()) {
            
                $user->sendPasswordResetEmail();

            }
        }
    }

    /**
     * Start the password reset process by generating a new token and expiry
     * 
     * @return void
     */ 
    protected function startPasswordReset()
    {
        $token = new Token();
        $hashed_token = $token->getHash();
        $this->password_reset_token = $token->getValue();

        $expiry_timestamp = time() + 60 * 60 * 2; // 2 hours from now

        $sql = 'UPDATE user
                SET password_reset_hash = :token_hash,
                    password_reset_expires_at = :expires_at
                WHERE user_id= :id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':token_hash', $hashed_token, PDO::PARAM_STR);
        $stmt->bindValue('expires_at', date('Y-m-d H:i:s', $expiry_timestamp), PDO::PARAM_STR);
        $stmt->bindValue(':id', $this->user_id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * Send password reset instructions in an email to the user
     * 
     * @return void
     */
    protected function sendPasswordResetEmail()
    {
        $url = 'http://' . $_SERVER['HTTP_HOST'] . '/password/show-reset/' . $this->password_reset_token;

        $text = View::getTemplate('Password/reset_email.txt', ['url' => $url]);
        $html = View::getTemplate('Password/reset_email.html', ['url' => $url]);

        Mail::send($this->email, 'Passwort Vergessen', $text, $html);
    }

    /**
     * Find a user model by password reset token and expiry
     * 
     * @param string $token Password reset token sent to user
     * 
     * @return mixed User object if found and the token hasn't expired, null otherwise
    */
    public static function findByPasswordReset($token)
    {
        $token = new Token($token);
        $hashed_token = $token->getHash();

        $sql = 'SELECT * FROM user
                WHERE password_reset_hash = :token_hash';
        
        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':token_hash', $hashed_token, PDO::PARAM_STR);
        
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        $user = $stmt->fetch();

        if ($user) {
            
            // Check password reset token hasn't expired
            if (strtotime($user->password_reset_expires_at) > time()) {
                
                return $user;

            }
        } 
    }

    /**
     * Reset the password
     * 
     * @param string $password The new password
     * 
     * @return boolean True if the password was updated successfully, false 
     * otherwise
    */
    public function resetPassword($password, $password_confirmation)
    {
        $this->password = $password;
        $this->password_confirmation = $password_confirmation;

        $this->validate();

        if(empty($this->errors)) {

            $password_hash = password_hash($this->password, PASSWORD_DEFAULT);

            $sql = 'UPDATE user
                    SET password_hash = :password_hash,
                        password_reset_hash = NULL,
                        password_reset_expires_at = NULL
                    WHERE user_id = :id';
            
            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':id', $this->user_id, PDO::PARAM_INT);
            $stmt->bindValue(':password_hash', $password_hash, PDO::PARAM_STR);

            return $stmt->execute();

        }

        return false;
    }

    /**
     * Send an email to the user containing the activation link
     * 
     * @return void
     */
    public function sendActivationEmail()
    {
        $url = 'http://' . $_SERVER['HTTP_HOST'] . '/signup/activate/' . $this->activation_token;

        $text = View::getTemplate('Signup/activation_email.txt', ['url' => $url]);
        $html = View::getTemplate('Signup/activation_email.html', ['url' => $url]);

        Mail::send($this->email, 'Nutzerkonto aktivieren', $text, $html);
    }

    /**
     * Activate the user account with the specified activation token
     * 
     * @param string $token Activation token from the URL
     * 
     * @return void
     * */
    public static function activate($token)
    {
        $token = new Token($token);
        $hashed_token = $token->getHash();

        $sql = 'UPDATE user
                SET is_active = 1,
                    activation_hash = null
                WHERE activation_hash = :hashed_token';
        

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':hashed_token', $hashed_token, PDO::PARAM_STR);

        $stmt->execute();
    }

    /**
     * Update the user's profile
     * 
     * @param array $data Data from the edit profile form
     * 
     * @return boolean True if the data was updated, false otherwise
    */  
    public function updateProfile($data)
    {
        $this->first_name = $data['first_name'];
        $this->last_name = $data['last_name'];
        $this->email = $data['email'];

        if ($data['password'] != '' || $data['password_confirmation'] != '') {

            $this->password = $data['password'];
            $this->password_confirmation = $data['password_confirmation'];

        }

        $this->validate();

        if (empty($this->errors)) {
            
            $sql = 'UPDATE user
                    SET first_name = :first_name,
                        last_name = :last_name,
                        email = :email';

            // Add password if it's set
            if (isset($this->password)) {
                $sql .= ', password_hash = :password_hash';
            }
                        
            $sql .= "\nWHERE user_id = :id";

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':first_name', $this->first_name, PDO::PARAM_STR);
            $stmt->bindValue(':last_name', $this->last_name, PDO::PARAM_STR);
            $stmt->bindValue(':email', $this->email, PDO::PARAM_STR);
            $stmt->bindValue(':id', $this->user_id, PDO::PARAM_INT);

            // Add password if it's set
            if (isset($this->password)) {
                
                $password_hash = password_hash($this->password, PASSWORD_DEFAULT);
                $stmt->bindValue(':password_hash', $password_hash, PDO::PARAM_STR);
            }

            return $stmt->execute();
        }

        return false;
    }

    /**
     * Delete a user model by ID
     * 
     * @param string $id The user ID
     * 
     * @return void 
    */ 
    public function delete()
    {
        $sql = 'DELETE FROM user WHERE user_id = :id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id', $this->user_id, PDO::PARAM_INT);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();
    }  
}
