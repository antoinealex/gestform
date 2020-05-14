<?php


namespace App\Service;

use App\Template\ResetPasswordMailTemplate;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class SendMail
 * @package App\Service
 */
class SendMail
{
    /**
     * @var UserInterface Recipient of the email
     */
    private $recipient;

    /**
     * @var String Subject of the email
     */
    private $subject;

    /**
     * @var String Email address to show in "From :" field
     */
    private $sender;

    /**
     * @var String Content of the mail
     */
    private $message;

    /**
     * @var Array Header of the message to define specific Content-type or MIME version
     */
    private $headers;

    /**
     * SendMail constructor.
     * @param UserInterface $user User will be the recipient of the email.
     * @param string $format Optional : Format of the email : HTML or TEXT, HTML Default
     */
    public function __construct(UserInterface $user = null, string $format = "HTML")
    {
        $this->recipient    =   $user;
        $this->sender       =   $_ENV["APP_EMAIL_ADDRESS"];
        if ($format == "HTML") {
            $this->headers = [
                "MIME-Version: 1.0",
                "Content-Type: text/html; charset=utf-8"
            ];
        }
        $this->headers[] = "From: ".$this->sender;
    }

    public function sendResetPasswordMail() : Void
    {
        //TODO Retrieve User Token and email template. Send mail to the user with the information using php built in mail sender functions.
        $template = new ResetPasswordMailTemplate();
        $token = $this->recipient->getResetToken();
        $url = $_ENV["FRONTEND_URL"]."resetpassword?token=".$token;
        $this->subject = $this->recipient->getFirstname()." votre demande de réinitialisation de mot de passe GestForm";
        $this->message = $template->generateMessageBody($url);
    }

    /**
     * @param String $subject
     */
    public function setSubject(string $subject) : Void
    {
        $this->subject = $subject;
    }

    /**
     * @param UserInterface $recipient
     */
    public function setRecipient(UserInterface $recipient): void
    {
        $this->recipient = $recipient;
    }

    /**
     * Send the message to the recipient.
     */
    public function send() : Void
    {
        mail(
            $this->recipient->getEmail(),
            $this->subject,
            $this->message,
            implode("\r\n", $this->headers)
        );
    }
}