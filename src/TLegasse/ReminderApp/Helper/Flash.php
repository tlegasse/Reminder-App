<?php

namespace TLegasse\ReminderApp\Helper;

/**
 * Class Flash
 * Our singleton glass for sending messages to the user.
 * @package TLegasse\ReminderApp\Helper
 */
class Flash
{
    private static $instance = null;

    /**
     * Flash constructor.
     * @param $message The message to send to the user
     * @param $type    The type of flash to send, based on bootstrap classes.
     */
    private function __construct($message, $type)
    {
        // Our flash message.
        $_SESSION['message'] = $message;

        // For the alert class echoed in self::get().
        // Can be (primary|secondary|success|danger|warning|info|light|dark)
        $_SESSION['type'] = $type;
    }


    /**
     * Return our singleton object.
     * @param string $message The message to send to the user
     * @param string $type    The type of flash to send, based on bootstrap classes.
     * @return Flash
     */
    public static function set(string $message, $type = 'danger')
    {
        if(self::$instance == null)
        {
            self::$instance = new Flash($message,$type);
        }
        return self::$instance;
    }

    /**
     * Getting a flash string that goes in the header (or wherever)
     * Takes nothing, returns HTML
     */
    public static function get()
    {
        if(isset($_SESSION['type']) && isset($_SESSION['message'])) {
            $flash_html = "<p class='alert alert-" . $_SESSION['type'] . "'>" . $_SESSION['message'] . "</p>";
            unset($_SESSION['type'],$_SESSION['message']);
            return $flash_html;
        }
    }
}