<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class Chat_Bee_Admin {
    public function __construct()
    {
        add_action('admin_menu',array($this,'init_admin'));
    }
    public function init_admin(){
        add_menu_page('Chat Bee Options','Chat Bee','manage_options','chat_bee',array($this,'add_page_options'));
    }
    public function add_page_options(){
        include CHATBEE_DIR_PATH.'backend/admin.php';
    }
}
new Chat_Bee_Admin();