jQuery(document).ready(function () {
    jQuery('.chat-message').niceScroll(
        {
            cursorcolor:"#c1c1c1",
            cursorborderradius: "3px",
            cursorborder: "1px solid #c1c1c1"
        }
        );
    jQuery('.list-online').niceScroll(
        {
        cursorcolor:"#c1c1c1",
        cursorborderradius: "3px",
        cursorborder: "1px solid #c1c1c1"
    }
    );

    if(document.cookie.indexOf('chat_been_state=open') != -1){
        if(document.cookie.indexOf('chat_been_status=on') != -1) {
            chat_bee_set_status(1);
        }
        chat_bee_get_list_online();
        var message_interval = setInterval(chat_bee_get_lists,2000);
        var list_online_interval = setInterval(chat_bee_get_list_online,120000);
    }
    /* get List Online */
    function chat_bee_get_list_online() {
        jQuery.ajax({
            url: chat_bee.ajax,
            type:'post',
            dataType:'json',
            data:{
                action:'chat_bee_get_user_lists',
                user_id : chat_bee.current_id
            },
            success:function (response) {
            }
        })
    }


    /* Get List Chat */
    function chat_bee_get_lists() {
        var count = Number(jQuery('.count-message-old').html());
        if(jQuery('.check_ajax').html() == 1 ){
            jQuery('.check_ajax').html(0);
            jQuery.ajax({
                url: chat_bee.ajax,
                type:'post',
                dataType:'json',
                data:{
                    action:'chat_bee_get_lists',
                    user_id : chat_bee.current_id,
                    count:count
                },
                success:function (response) {
                    if(response.count){
                        jQuery('#chat-box-live .chat-body ul.chat-message').html(response.html);
                        jQuery('.count-message-old').html(response.count);
                        var chat_message_box = document.getElementsByClassName('chat-message')[0];
                        if(document.cookie.indexOf('chat_been_scroll=on') != -1) {
                            chat_message_box.scrollTo(0, chat_message_box.scrollHeight);
                        }
                        if(document.cookie.indexOf('chat_been_speaker=open') != -1){
                            new Audio(chat_bee.chat_bee_url+'assets/mp3/ping.mp3').play();
                        }
                        jQuery('.check_ajax').html(1);
                    }else {
                        jQuery('.check_ajax').html(1);
                    }
                }
            })
        }
    }
    /* Show List Emoji */
    jQuery('#chat-live .chat-body .send-box #sample-emoji').click(function () {
        if(jQuery('#list-emoji').css('display') == 'none'){
            jQuery('#list-emoji').css('display','block');
        }else {
            jQuery('#list-emoji').css('display','none');
        }
    });

    jQuery('.chat-body .send-box .user-list-online').click(function () {
        if(jQuery('.chat-body .send-box .list-online').css('display') == 'none'){
            jQuery('.chat-body .send-box .list-online').css('display','block');
        }else {
            jQuery('.chat-body .send-box .list-online').css('display','none');
        }
    });
    /* Speaker Change Image */
    jQuery('#chat-speaker').click(function()
    {
        var $this = jQuery(this);
        var img = jQuery('#chat-speaker img');
        if($this.attr('class') == 'speaker'){
            img.attr('src',chat_bee.chat_bee_url+'assets/img/mute.png');
            $this.addClass('mute');
            $this.removeClass('speaker');
            jQuery.cookie('chat_been_speaker', 'mute', {expires: 40, path: '/'});
        }else{
            img.attr('src',chat_bee.chat_bee_url+'assets/img/speaker.png');
            $this.addClass('speaker');
            $this.removeClass('mute');
            jQuery.cookie('chat_been_speaker', 'open', {expires: 40, path: '/'});
        }

    });
    /* Send Emoji To Input */
    jQuery('#list-emoji li').click(function () {
        var emoji = jQuery(this).attr('data-idEmoji');
        var input = jQuery('#text-send').val();
        jQuery('#text-send').val(input+emoji);
        jQuery('#text-send').focus();

    });
    /* Add Class Closed To Box */
    jQuery('.chat-title').click(function () {
        var $this = jQuery(this);
        if(jQuery('.chat-live').attr('class').indexOf('closed') != -1){
            jQuery('.chat-live').removeClass('closed');
            jQuery.cookie('chat_been_state', 'open', {expires: 40, path: '/'});
            chat_bee_get_list_online();
            message_interval = setInterval(chat_bee_get_lists,2000);
            list_online_interval = setInterval(chat_bee_get_list_online,120000);
        }else{
            jQuery('.chat-live').addClass('closed');
            jQuery.cookie('chat_been_state', 'closed', {expires: 40, path: '/'});
            clearInterval(message_interval);
            clearInterval(list_online_interval);
        }
    });
    /* Get List If ChatBox is Open */
    if(document.cookie.indexOf('chat_been_state') == -1){
        chat_bee_get_list_online();
        jQuery.cookie('chat_been_state', 'open', {expires: 40, path: '/'});
        jQuery.cookie('chat_been_speaker', 'open', {expires: 40, path: '/'});
        jQuery.cookie('chat_been_scroll', 'on', {expires: 40, path: '/'});
        jQuery.cookie('chat_been_status', 'off', {expires: 40, path: '/'});
        message_interval = setInterval(chat_bee_get_lists,2000);
        list_online_interval = setInterval(chat_bee_get_list_online,120000);
    }
    /* Send Message */
    jQuery('#text-send').keyup(function (e) {
        var $this = jQuery(this);
        var text = $this.val();
        if(text.length > 0){
            if(e.keyCode == 13){
                var nonce = jQuery('#chatBee_nonce').val();
                $this.val('');
                chat_bee_get_list_online();
                jQuery.ajax({
                    url: chat_bee.ajax,
                    type:'post',
                    data:{
                        action:'chat_bee_send',
                        user_id : chat_bee.current_id,
                        nonce:nonce,
                        text:text
                    },
                    success:function (response) {
                        chat_bee_get_lists();
                    }
                })
            }
        }
    });

    jQuery('.chat-bee-scroll').click(function () {
        var $this = jQuery(this);
        if($this.text() == 'Auto Scroll : On'){
            $this.html('Auto Scroll : Off');
            jQuery.cookie('chat_been_scroll', 'off', {expires: 40, path: '/'});
        }else {
            $this.html('Auto Scroll : On');
            jQuery.cookie('chat_been_scroll', 'on', {expires: 40, path: '/'});
        }

    });

    jQuery('.chat-bee-status').click(function () {
        var $this = jQuery(this);
        if($this.text() == 'Hide Status : Off'){
            $this.html('Hide Status : On');
            jQuery.cookie('chat_been_status', 'on', {expires: 40, path: '/'});
            chat_bee_set_status(1);
        }else {
            $this.html('Hide Status : Off');
            jQuery.cookie('chat_been_status', 'off', {expires: 40, path: '/'});
            chat_bee_set_status(0);
        }
    });

    function chat_bee_set_status(status) {
        jQuery.ajax({
            url: chat_bee.ajax,
            type:'post',
            data:{
                action:'chat_bee_status',
                user_id : chat_bee.current_id,
                status:status
            },
            success:function (response) {
            }
        })
    }
});