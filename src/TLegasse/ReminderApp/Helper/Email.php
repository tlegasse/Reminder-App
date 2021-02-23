<?php

namespace TLegasse\ReminderApp\Helper;

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

/**
 * Class Email
 * This class handles emailing.
 * @package TLegasse\ReminderApp\Helper
 */
class Email
{
    private $recipient;
    private $sender;
    private $subject;
    private $body;
    private $sender_name;

    public function __construct(
        string $recipient,
        string $subject,
        string $body
    ) {
        global $config;
        $this->subject     = $subject;
        $this->body        = $body;
        $this->recipient   = $recipient;
        $this->sender      = $config['email']['sender'];
        $this->sender_name = $config['general']['app_name'];
    }

    public function send() {
        // Initializing Mailer
        $mail = new PHPMailer(true);
        try {
            // Setting up Mailer
            $mail->setFrom($this->sender, $this->sender_name);
            $mail->addAddress($this->recipient, $this->recipient);
            $mail->Subject = 'Here is the subject';
            $mail->Body    = 'This is the HTML message body <b>in bold!</b>';
            // Sending mail
            $mail->send();
        } catch (Exception $e) {
            // Or report trying
            Flash::set('There was a problem sending the email.');
        }
    }
}