<?php

namespace TLegasse\ReminderApp\Helper;

class Flash
{
    private static $instance = null;

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
     * @param string $message The message to be sent.
     * @param string $type    The type of message.
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
     * Takes nothing, echoes HTML
     */
    public static function get()
    {
        if(isset($_SESSION['type']) && isset($_SESSION['message'])) {
            echo "<p class='alert alert-" . $_SESSION['type'] . "'>" . $_SESSION['message'] . "</p>";
            unset($_SESSION['type'],$_SESSION['message']);
        }
    }
}