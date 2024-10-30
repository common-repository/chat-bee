<?php
/*
Plugin Name: Chat Bee
Plugin URI: http://wordpress.org/plugins/chat-bee/
Description: Chat Bee is a plug-in that helps you to easily create a chat environment with great features for your WordPress site so users can easily interact with each other.
Author: kamyabsoft
Version: 1.1.0
Author URI: wp-team.info
*/

if ( ! defined( 'ABSPATH' ) ) exit;

class Chat_bee_Main {

    public $version = '1.1.0'; //version Chat bee

    protected static $_instance;

    private $options = array(
        'number_last_chat' => 30,
        'role' => array('administrator'),
        'dir' => 'left',
        'active' => 'true',
        'unLogged-in' => 'show',
        'users_ban' => array()
    );

    private $color_options = array(
        'back_chat_message' => '#EEEEEE',
        'back_input' => '#CFD8DC',
        'back_chat_title' => '#448AFF',
        'color_warning' => '#C2185B',
        'back_send_box' => '#FAFAFA'
    );

    private $Text_options = array(
        'title_text' => 'Chat For Users',
        'ban_message' => 'Your user has been baned in by admin',
        'login_warning' => 'You must be logged in to submit the text',
        'disable_send_message' => 'Send Message is Disable',
    );

    public function __construct()
    {
        $this->define_constants();
        $this->init();
        $this->assets();
        $this->do_include();
    }

    public static function getInstance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self;
        }
    }

    public function is_enable(){
        $option = get_option('chat_bee_op')['unLogged-in'];
        require (ABSPATH . WPINC . '/pluggable.php');
        if($option == 'hide'){
            if(get_current_user_id()){
                return true;
            }
            return false;
        }
        return true;
    }

    public static function get_emoji($emoji){
        return str_replace(array(":r/",":d/",":q/",":w/",":e/",":r/",":t/",":y/",":u/",":i/",":o/",":p/",":a/",":s/",":f/",":g/",":h/",":j/",":k/",":l/",":z/"),
            array('👅','😀','😂','😆','😇','😈','😋','😎','😏','😐','😓','😔','😒','😠','😤','😱','😜','😕','😷','😳','😫'),$emoji);
    }

    private function init(){

        if(!isset($_COOKIE['chat_been_state'])){
            setcookie('chat_been_state','open',strtotime( '+30 days' ),'/');
        }
        if(!isset($_COOKIE['chat_been_speaker'])){
            setcookie('chat_been_speaker','open',strtotime( '+30 days' ),'/');
        }
        if(!isset($_COOKIE['chat_been_status'])){
            setcookie('chat_been_status','off',strtotime( '+30 days' ),'/');
        }
        if(!isset($_COOKIE['chat_been_scroll'])){
            setcookie('chat_been_scroll','on',strtotime( '+30 days' ),'/');
        }
        $get_ver = get_option('chat_bee_ver');
        if(!$get_ver or $get_ver != $this->version ){
            update_option('chat_bee_ver',$this->version);
            update_option('chat_bee_op', $this->options);
            update_option('chat_bee_color', $this->color_options);
            update_option('chat_bee_text', $this->Text_options);
        }
       register_activation_hook( __FILE__, array( $this, 'install' ) );
    }

    public function install(){

        global $wpdb,$table_prefix;

        $pages_to_display_table_name = $table_prefix . "chat_bee";

        $online_user_table = $table_prefix . "chat_bee_user_online";

        if($wpdb->get_var("SHOW TABLES LIKE '$pages_to_display_table_name'") != $pages_to_display_table_name) {

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        $create_pages_to_display_table_clause = "CREATE TABLE $pages_to_display_table_name (id int(11) NOT NULL AUTO_INCREMENT, user_id int(11) NOT NULL, chat_date TIMESTAMP NOT NULL, content LONGTEXT NOT NULL, PRIMARY KEY  (id));";

        dbDelta($create_pages_to_display_table_clause);

        }

        if($wpdb->get_var("SHOW TABLES LIKE '$online_user_table'") != $online_user_table) {

            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

            $create_table_user_online = "CREATE TABLE $online_user_table (id int(11) NOT NULL AUTO_INCREMENT, user_id int(11) NOT NULL, online_time TIMESTAMP NOT NULL, hidden_status int(10) NOT NULL DEFAULT 0, PRIMARY KEY  (id));";

            dbDelta($create_table_user_online);

        }
    }
    private function define_constants(){

        define('CHATBEE_DIR_PATH',plugin_dir_path(__FILE__));
        define('CHATBEE_DIR_URL',plugin_dir_url(__FILE__));
        define('CHATBEE_IMAGE_URL',CHATBEE_DIR_URL.'assets/img/');
        define('CHATBEE_CSS_URL',CHATBEE_DIR_URL.'assets/css/');
        define('CHATBEE_JS_URL',CHATBEE_DIR_URL.'assets/js/');
    }

    private function assets(){
        if($this->is_enable()) {
            add_action('wp_enqueue_scripts', array($this, 'inc_assets'));
        }
        add_action('admin_enqueue_scripts',array($this,'admin_assets'));
    }

    public function inc_assets(){
        wp_register_style('style-css',CHATBEE_CSS_URL.'style.css',null,$this->version);
        wp_enqueue_style('style-css');
        wp_register_script('cookie-js',CHATBEE_JS_URL.'jquery-cookie.js',array('jquery'),$this->version,true);
        wp_enqueue_script('cookie-js');
        wp_register_script('scroll-js',CHATBEE_JS_URL.'jquery.nicescroll.min.js',array('cookie-js'),$this->version,true);
        wp_enqueue_script('scroll-js');
        wp_register_script('base-js',CHATBEE_JS_URL.'base.js',array('scroll-js'),$this->version,true);
        wp_enqueue_script('base-js');
        wp_localize_script('base-js', 'chat_bee', array(
            'ajax' => admin_url('admin-ajax.php'),
            'current_id' => get_current_user_id(),
            'chat_bee_url' => CHATBEE_DIR_URL
        ));
    }

    public function admin_assets(){
        wp_register_style('chat-admin-css',CHATBEE_CSS_URL.'admin.css',null,$this->version);
        wp_enqueue_style('chat-admin-css');
    }

    private function do_include(){
        include CHATBEE_DIR_PATH."backend/class-admin.php";
        if($this->is_enable()) {
            include CHATBEE_DIR_PATH . "ajax/ajax.php";
            include CHATBEE_DIR_PATH . "frontend/class-view.php";
        }
    }
}

Chat_bee_Main::getInstance();