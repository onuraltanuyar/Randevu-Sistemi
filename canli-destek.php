<?php
$page_title = 'Canlı Destek';
require_once 'panel-header.php'; 
?>
<style>
    #chat-container {
        max-width: 800px;
        margin: 0 auto;
        background-color: var(--light-bg);
        border: 1px solid var(--border-color);
        border-radius: 8px;
        display: flex;
        flex-direction: column;
        height: 70vh; 
    }
    #chat-box {
        flex-grow: 1;
        padding: 20px;
        overflow-y: auto; 
        display: flex;
        flex-direction: column;
        gap: 15px;
    }
    .message-bubble {
        padding: 10px 15px;
        border-radius: 15px;
        max-width: 70%;
        line-height: 1.4;
    }
    .message-user {
        background-color: #007bff;
        color: white;
        align-self: flex-end; 
        border-bottom-right-radius: 3px;
    }
    .message-admin {
        background-color: #4A5568;
        color: white;
        align-self: flex-start; 
        border-bottom-left-radius: 3px;
    }
    #send-form {
        display: flex;
        padding: 15px;
        border-top: 1px solid var(--border-color);
    }
    #message-input {
        flex-grow: 1;
        margin: 0;
        border-right: none;
        border-top-right-radius: 0;
        border-bottom-right-radius: 0;
    }
    #send-button {
        border-top-left-radius: 0;
        border-bottom-left-radius: 0;
    }
</style>

<div class="container">
    <h1 class="page-title">Canlı Destek</h1>
    <p style="text-align:center; color: var(--text-secondary); margin-bottom:20px;">Yardım ekibimize anında ulaşın. Lütfen sorunuzu aşağıya yazın.</p>
    
    <div id="chat-container">
        <div id="chat-box">
            </div>
        <form id="send-form">
            <input type="text" id="message-input" placeholder="Mesajınızı yazın..." autocomplete="off" required>
            <button type="submit" id="send-button">Gönder</button>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function(){

    function loadMessages() {
        $.ajax({
            url: 'mesajlari-getir.php',
            method: 'GET',
            success: function(data) {
                $('#chat-box').html(data);
                $('#chat-box').scrollTop($('#chat-box')[0].scrollHeight);
            }
        });
    }

    loadMessages();
    setInterval(loadMessages, 3000);

    $('#send-form').submit(function(e){
        e.preventDefault(); 

        var message = $('#message-input').val();
        if(message.trim() == '') {
            return;
        }

        $.ajax({
            url: 'mesaj-gonder.php',
            method: 'POST',
            data: { message: message },
            success: function(response) {
                $('#message-input').val(''); 
                loadMessages(); 
            }
        });
    });
});
</script>

</body>
</html>