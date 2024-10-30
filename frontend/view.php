<?php
$options = get_option('chat_bee_op');
$side = $options['dir'];
$active = $options['active'];
$allow = in_array(get_current_user_id(),$options['users_ban']);
$colors = get_option('chat_bee_color');
$texts = get_option('chat_bee_text');
?>
<div id="chat-box-live" class="<?php echo ($side == 'left')? 'chat-box-live-left':'chat-box-live-right' ?>">
    <div id="chat-live" class="chat-live <?php echo ($_COOKIE['chat_been_state'] == 'closed')? 'closed':'' ?>">
        <div style="background: <?php echo $colors['back_chat_title'] ?>" class="chat-title">
            <h5><?php echo $texts['title_text'] ?></h5>
        </div>
        <div class="chat-body">
            <ul style="background: <?php echo $colors['back_chat_message'] ?>" class="chat-message">
            </ul>
            <div style="background: <?php echo $colors['back_send_box'] ?>" class="send-box">
                <?php if( is_user_logged_in() && $active=='true'  && !$allow): ?>
                    <div class="chat-bee-options"><span class="chat-bee-scroll"><?php echo ($_COOKIE['chat_been_scroll'] == 'on')? 'Auto Scroll : On':'Auto Scroll : Off' ?></span><span class="chat-bee-status"><?php echo ($_COOKIE['chat_been_status'] == 'off')? 'Hide Status : Off':'Hide Status : On' ?></span></div>
                <a id="chat-speaker" class="<?php echo ($_COOKIE['chat_been_speaker'] == 'open')? 'speaker':'mute'  ?>"><img src="<?php echo ($_COOKIE['chat_been_speaker'] == 'open')? CHATBEE_IMAGE_URL.'speaker.png':CHATBEE_IMAGE_URL.'mute.png'  ?>" alt=""></a>
                <img id="sample-emoji" class="<?php echo ($side == 'left')? 'sample-emoji':'sample-emoji-right' ?>" src="<?php echo CHATBEE_IMAGE_URL.'happiness.png' ?>" alt="">
                <ul id="list-emoji">
                    <li data-idEmoji=":r/">👅</li>
                    <li data-idEmoji=":d/">😀</li>
                    <li data-idEmoji=":q/">😂</li>
                    <li data-idEmoji=":w/">😆</li>
                    <li data-idEmoji=":e/">😇</li>
                    <li data-idEmoji=":r/">😈</li>
                    <li data-idEmoji=":t/">😋</li>
                    <li data-idEmoji=":y/">😎</li>
                    <li data-idEmoji=":u/">😏</li>
                    <li data-idEmoji=":i/">😐</li>
                    <li data-idEmoji=":o/">😓</li>
                    <li data-idEmoji=":p/">😔</li>
                    <li data-idEmoji=":a/">😒</li>
                    <li data-idEmoji=":s/">😠</li>
                    <li data-idEmoji=":f/">😤</li>
                    <li data-idEmoji=":g/">😱</li>
                    <li data-idEmoji=":h/">😜</li>
                    <li data-idEmoji=":j/">😕</li>
                    <li data-idEmoji=":k/">😷</li>
                    <li data-idEmoji=":l/">😳</li>
                    <li data-idEmoji=":z/">😫</li>
                </ul>
                <form class="<?php echo ($side == 'left')? 'left-form':'right-form' ?>" action="" method="post" onsubmit="return false">
                    <?php wp_nonce_field( 'chatBee_nonce_action','chatBee_nonce' ); ?>
                    <input style="background: <?php echo $colors['back_input'] ?>" type="text" id="text-send" autocomplete="off">
                </form>
                    <div class="clear-fix"></div>
                <?php elseif(!is_user_logged_in() && $active=='true'): ?>
                    <p style="color: <?php echo $colors['color_warning'] ?>" class="chat-bee-warning">
                        <?php echo $texts['login_warning'] ?>
                    </p>
                <?php elseif($active=='false'): ?>
                    <p style="color: <?php echo $colors['color_warning'] ?>" class="chat-bee-warning">
                        <?php echo $texts['disable_send_message'] ?>
                    </p>
                <?php elseif($allow): ?>
                    <p style="color: <?php echo $colors['color_warning'] ?>" class="chat-bee-warning">
                        <?php echo $texts['ban_message'] ?>
                    </p>
                <?php endif; ?>
                <div class="count-message-old">0</div>
                <div class="check_ajax">1</div>
                <div class="clear-fix"></div>
            </div>
        </div>
    </div>
</div>