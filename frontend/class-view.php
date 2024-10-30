<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class chat_bee_view{

    public function __construct()
    {
        add_action('wp_footer',array($this,'footer'));
        add_action('wp_footer',array($this,'script'));

    }

    public function footer(){
        include CHATBEE_DIR_PATH."frontend/view.php";
    }

    public function script(){
        include CHATBEE_DIR_PATH."frontend/script.php";
    }
}

new chat_bee_view();