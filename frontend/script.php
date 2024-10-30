<script type="text/javascript">

    function chat_bee_remove_chat(e, ele) {
        e.preventDefault();
        var $this = ele;
        var id = $this.getAttribute('data-id');
        $this.firstElementChild.setAttribute('src',chat_bee.chat_bee_url+'assets/img/loader.gif');
        jQuery.ajax({
            url:chat_bee.ajax,
            type:'post',
            data:{
                action:'chat_bee_remove',
                id : id,
                user_id : chat_bee.current_id
            },
            success:function (response) {

            }
        })
    }

    function chat_bee_reply_user(element) {
        var $this = element;
        var user_name = element.innerHTML;
        var input = document.getElementById('text-send');
        input.value = '@'+ user_name + ' ';
        input.focus();
    }


    function chat_bee_ban(id) {
        jQuery.ajax({
            url:chat_bee.ajax,
            type:'post',
            data:{
                action:'chat_bee_ban',
                id : id,
                user_id : chat_bee.current_id
            },
            success:function (response) {
                jQuery('.count-message-old').html(0)
            }
        })
    }

</script>