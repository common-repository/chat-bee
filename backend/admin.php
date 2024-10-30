<h2 class="nav-tab-wrapper">
    <a href="?page=chat_bee&tab=display_options" class="nav-tab">Display Options</a>
    <a href="?page=chat_bee&tab=color_options" class="nav-tab">Color Options</a>
    <a href="?page=chat_bee&tab=text_options" class="nav-tab">Text Options</a>
    <a href="?page=chat_bee&tab=message_options" class="nav-tab">Message Options</a>
</h2>
<?php if(@$_GET['tab'] == 'display_options' or !isset($_GET['tab'])): ?>
<div class="wrap chat-bee-options">
    <?php
    if(isset($_POST['chat_settings_update'])){
        $option_array = array(
                'number_last_chat' => $_POST['number_last_chat'],
                'role' => $_POST['role'],
                'dir' => $_POST['dir'],
                'active' => $_POST['active'],
                'unLogged-in' => $_POST['show'],
                'users_ban' => explode(',',$_POST['users_ban'])
        );
        update_option('chat_bee_op',$option_array);
    }
    global $wp_roles;
    $roles = $wp_roles->role_objects;
    $options = get_option('chat_bee_op');
    ?>
    <form action="" method="post">
        <h4>Show the latest chats</h4>
        <label>
            <input style="width: 80px;" type="number" name="number_last_chat" value="<?php echo $options['number_last_chat'] ?>">
        </label>
        <h4>Send Message</h4>
        <label>Enable<input type="radio" name="active" <?php checked($options['active'],"true") ?> value="true"></label>
        <label>Disable<input type="radio" name="active" <?php checked($options['active'],"false") ?> value="false"></label>
        <h4>Moderator Roles</h4>
        <?php foreach ($roles as $role): ?>
            <label><?php echo $role->name ?><input type="checkbox" name="role[]" <?php echo in_array($role->name,$options['role'])? 'checked':'' ?> value="<?php echo $role->name ?>"></label>
        <?php endforeach; ?>
        <h4>Show ChatBox For Not Logged-in Users</h4>
        <label>Yes<input type="radio" name="show" <?php checked($options['unLogged-in'],"show") ?> value="show"></label>
        <label>No<input type="radio" name="show" <?php checked($options['unLogged-in'],"hide") ?> value="hide"></label>
        <h4>Direction</h4>
        <label>Left<input type="radio" name="dir" <?php checked($options['dir'],"left") ?> value="left"></label>
        <label>Right<input type="radio" name="dir" <?php checked($options['dir'],"right") ?> value="right"></label>
        <h4>List of ban users</h4>
        <div>example : id1,id2,id3  </div>
        <br>
        <textarea name="users_ban" rows="3" cols="100"><?php echo implode(',',$options['users_ban']) ?></textarea>
        <br>
        <br>
        <button type="submit" class="button-primary" name="chat_settings_update">Save Settings</button>
    </form>
</div>
<?php elseif ($_GET['tab'] == 'color_options'): ?>
    <div class="wrap chat-bee-options color-options">
        <?php
        if(isset($_POST['chat_color_update'])){
            $option_array = array(
                'back_chat_message' => $_POST['back_chat_message'],
                'back_input' => $_POST['back_input'],
                'back_chat_title' => $_POST['back_chat_title'],
                'color_warning' => $_POST['color_warning'],
                'back_send_box' => $_POST['back_send_box']
            );
            update_option('chat_bee_color',$option_array);
        }
        $options = get_option('chat_bee_color');
        ?>
        <form action="" method="post">
            <label>Background Chat Message<input type="text" name="back_chat_message" value="<?php echo $options['back_chat_message'] ?>"></label>
            <label>Background Box Send Message<input type="text" name="back_input" value="<?php echo $options['back_input'] ?>"></label>
            <label>Background Chat Title<input type="text" name="back_chat_title" value="<?php echo $options['back_chat_title'] ?>"></label>
            <label>Color Text Warning<input type="text" name="color_warning" value="<?php echo $options['color_warning'] ?>"></label>
            <label>Background Bottom Box<input type="text" name="back_send_box" value="<?php echo $options['back_send_box'] ?>"></label>
            <br>
            <button type="submit" class="button-primary" name="chat_color_update">Save Colors</button>
        </form>
    </div>
<?php elseif ($_GET['tab'] == 'text_options'): ?>
    <div class="wrap chat-bee-options text-options">
        <?php
        if(isset($_POST['chat_text_update'])){
            $option_array = array(
                'title_text' => $_POST['title_text'],
                'ban_message' => $_POST['ban_message'],
                'login_warning' => $_POST['login_warning'],
                'disable_send_message' => $_POST['disable_send_message'],
            );
            update_option('chat_bee_text',$option_array);
        }
        $options = get_option('chat_bee_text');
        ?>
        <form action="" method="post">
            <label>Title Text<input type="text" name="title_text" value="<?php echo $options['title_text'] ?>"></label>
            <label>Ban Text<input type="text" name="ban_message" value="<?php echo $options['ban_message'] ?>"></label>
            <label>Login Warning<input type="text" name="login_warning" value="<?php echo $options['login_warning'] ?>"></label>
            <label>Disable Send Message<input type="text" name="disable_send_message" value="<?php echo $options['disable_send_message'] ?>"></label>
            <br>
            <button type="submit" class="button-primary" name="chat_text_update">Save Texts</button>
        </form>
    </div>
<?php elseif ($_GET['tab'] == 'message_options'): ?>
    <?php
    if(isset($_POST['clear_all_message'])){
        global $wpdb,$table_prefix;
        $wpdb->query("delete from {$table_prefix}chat_bee");
    }
    ?>
<div class="wrap chat-bee-options">
    <form class="remove-all-message" action="" method="post" onsubmit="return confirm('You are sure of removing all messages ?')">
        <button type="submit" class="button-primary" name="clear_all_message">Clear all message in chat bee</button>
    </form>
</div>


<?php endif; ?>
