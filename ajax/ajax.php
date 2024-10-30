<?php

add_action("wp_ajax_chat_bee_send","chat_bee_send_message");

function chat_bee_send_message(){
    if(!isset($_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'chatBee_nonce_action' ) ){
        exit();
    }
    global $wpdb,$table_prefix;
    $options = get_option('chat_bee_op');
    $active = $options['active'];
    $id = get_current_user_id();
    $allow_send_message = $options['users_ban'];
    if($active == 'true' && !in_array($id,$allow_send_message)){
        $text = sanitize_text_field($_POST['text']);
        $wpdb->insert($table_prefix."chat_bee",array(
            'user_id' => $id,
            'content' => $text
        ));
    }
    wp_die();
}

add_action("wp_ajax_chat_bee_get_lists","chat_bee_get_lists");
add_action("wp_ajax_nopriv_chat_bee_get_lists","chat_bee_get_lists");

function chat_bee_get_lists(){
    global $wpdb,$table_prefix;
    $result_array = array();
    $count = sanitize_text_field($_POST['count']);
    $count_sql = $wpdb->get_var("select count(*) from {$table_prefix}chat_bee");
    if($count == 0 or $count_sql > $count or $count_sql < $count ){
        $count = $count_sql;
        $user_request = get_current_user_id();
        $options = get_option('chat_bee_op');
        $limit = trim($options['number_last_chat']);
        $offset = $count_sql - $limit ;
        if($offset<0){
            $offset = 0;
        }
        $user_role = get_userdata($user_request)->roles[0];
        $allow_remove_role = $options['role'];
        $results = $wpdb->get_results("select * from {$table_prefix}chat_bee ORDER BY chat_date ASC limit $offset,$limit");
        $html = "";
        if(in_array($user_role,$allow_remove_role)){
            foreach ($results as $result){
                $ban_users = (in_array($result->user_id,$options['users_ban'])) ? 'lock':'unlock';
                $user_name = get_userdata($result->user_id);
                $time_old = date('Y-m-d H:i:s', strtotime("-240 seconds"));
                $users_online = $wpdb->get_row("select * from {$table_prefix}chat_bee_user_online WHERE user_id = {$result->user_id}");
                $status_user = ($users_online->online_time > $time_old)? 'online':"offline";
                $user_check_status = ($users_online->hidden_status == 1) ? 'invisible':$status_user;
                $html .= '<li>';
                $html .= '<div class="status-user '.'chat-bee-'.$user_check_status.'">'.$user_check_status.'</div>';
                $html .= get_avatar($user_name->ID,50);
                $html .= '<div class="content">';
                $html .= '<img class="ban-user" onclick="chat_bee_ban('.$result->user_id.')" src="'.CHATBEE_IMAGE_URL.$ban_users.'.png">';
                $html .= '<span class="user-name"  onclick="chat_bee_reply_user(this)">'.$user_name->user_nicename.'</span>';
                $html .= '<div class="clear-fix"></div>';
                $html .= '<span class="chat-text">'.Chat_bee_Main::get_emoji($result->content).'</span>';
                $html .= '<span class="chat-date">'.date('m-d H:i',strtotime($result->chat_date)).'</span>';
                $html .= '<a data-id="'.$result->id.'" class="remove-chat" onclick="chat_bee_remove_chat(event,this)"><img src="'.CHATBEE_IMAGE_URL.'close.png'.'" ></a>';
                $html .= '</div><div class="clear-fix"></div>';
                $html .= '</li>';
            }
        }else{
            foreach ($results as $result){
                $user_name = get_userdata($result->user_id);
                $time_old = date('Y-m-d H:i:s', strtotime("-240 seconds"));
                $users_online = $wpdb->get_row("select * from {$table_prefix}chat_bee_user_online WHERE user_id = {$result->user_id}");
                $status_user = ($users_online->online_time > $time_old)? 'online':"offline";
                $user_check_status = ($users_online->hidden_status == 1) ? 'invisible':$status_user;
                $html .= '<li>';
                $html .= '<div class="status-user '.'chat-bee-'.$user_check_status.'">'.$user_check_status.'</div>';
                $html .= get_avatar($user_name->ID,50);
                $html .= '<div class="content">';
                $html .= '<span class="user-name" onclick="chat_bee_reply_user(this)">'.$user_name->user_nicename.'</span>';
                $html .= '<div class="clear-fix"></div>';
                $html .= '<span class="chat-text">'.Chat_bee_Main::get_emoji($result->content).'</span>';
                $html .= '<span class="chat-date">'.date('m-d H:i',strtotime($result->chat_date)).'</span>';
                $html .= '</div><div class="clear-fix"></div>';
                $html .= '</li>';
            }
        }
        $html .= '<li class="last-message"><div>----</div></li>';
        $result_array = array(
            'html' => $html,
            'count' => (int)$count_sql
        );
        wp_die(json_encode($result_array));
    }
    wp_die(json_encode(array(
        'count'=>0
    )));
}

add_action('wp_ajax_chat_bee_remove','chat_bee_remove');

function chat_bee_remove(){
    global $wpdb,$table_prefix;
    $id = sanitize_text_field($_POST['id']);
    $user_id = get_current_user_id();
    $user_role = get_userdata($user_id)->roles[0];
    $allow_remove_role = get_option('chat_bee_op')['role'];
    if(in_array($user_role,$allow_remove_role)){
        $wpdb->delete($table_prefix.'chat_bee',array(
            'id' => $id
        ));
    }
    wp_die();
}

add_action('wp_ajax_chat_bee_get_user_lists','chat_bee_get_user_online_list');
function chat_bee_get_user_online_list(){
    global $wpdb,$table_prefix;
    $id = get_current_user_id();
    $time = date('Y-m-d H:i:s');
    $is_exist = $wpdb->get_row("select * from {$table_prefix}chat_bee_user_online WHERE user_id = '$id'");
    if(!$is_exist){
        $wpdb->insert($table_prefix.'chat_bee_user_online',array(
            'user_id'=> $id,
            'online_time' => $time
        ));
    }else{
        $wpdb->update($table_prefix.'chat_bee_user_online',array(
            'online_time' => $time
        ),array(
            'user_id' => $id
        ));
    }
    wp_die();
}

add_action('wp_ajax_chat_bee_status','chat_bee_status');
function chat_bee_status(){
    global $wpdb,$table_prefix;
    $id = get_current_user_id();
    $status = $_POST['status'];
    $time = date('Y-m-d H:i:s');
        $wpdb->update($table_prefix.'chat_bee_user_online',array(
            'online_time' => $time,
            'hidden_status' => (int)$status
        ),array(
            'user_id' => $id
        ));

    wp_die();
}

add_action('wp_ajax_chat_bee_ban','chat_bee_ban');
function chat_bee_ban(){
    global $wpdb,$table_prefix;
    $id = $_POST['id'];
    $user_request = get_current_user_id();
    $options = get_option('chat_bee_op');
    $user_list_ban = $options['users_ban'];
    $user_role = get_userdata($user_request)->roles[0];
    $allow_remove_role = $options['role'];
    if(in_array($user_role,$allow_remove_role)){
        if(in_array($id,$user_list_ban)){
            unset($options['users_ban'][array_search($id,$user_list_ban)]);
            update_option('chat_bee_op',$options);
        }else{
            $options['users_ban'][] = $id;
            update_option('chat_bee_op',$options);
        }
    }
    wp_die();
}