<?php


namespace App\Template;

/**
 * Class ResetPasswordMailTemplate
 * @package App\Template
 */
class ResetPasswordMailTemplate
{

    /**
     * Template to generate a forgot password email
     * @param String $resetURL The full link of password reset to be sent to the user
     * @return String The body of the email to be sent
     */
    public function generateMessageBody(String $resetURL) : String {
        //TODO Write template content in HTML Format in return function
        return "
        // Some HTML <strong>code</strong> including <a href='$resetURL'>the link</a>
        ";
    }
}