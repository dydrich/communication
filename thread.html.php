<!DOCTYPE html>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title><?php print $_SESSION['__config__']['intestazione_scuola'] ?>:: messaggi</title>
<link rel="stylesheet" href="../../css/site_themes/<?php echo getTheme() ?>/reg.css" type="text/css" media="screen,projection" />
<link rel="stylesheet" href="../../css/general.css" type="text/css" media="screen,projection" />
<link rel="stylesheet" href="../../css/site_themes/<?php echo getTheme() ?>/communication.css" type="text/css" media="screen,projection" />
<link rel="stylesheet" href="../../css/site_themes/<?php echo getTheme() ?>/jquery-ui.min.css" type="text/css" media="screen,projection" /><script type="text/javascript" src="../../js/jquery-2.0.3.min.js"></script>
<script type="text/javascript" src="../../js/jquery-ui-1.10.3.custom.min.js"></script>
<script type="text/javascript" src="../../js/jquery.show_char_limit-1.2.0.js"></script>
<script>
$(document).ready(function(){
	$('#newmsg_lnk').click(function(event){
		event.preventDefault();
		$('#txt').val("");
		$('#message').show(1500);
		$('#newmsg').hide(1500);
		$('#viewlist').show(1500);
		$('#sel_thread').hide(1500);
		window.setTimeout(function(){
			$('#txt').focus();
		}, 1500);
	});
	$('#viewlist_lnk').click(function(event){
		event.preventDefault();
		$('#sel_thread').show(1500);
		$('#message').hide(1500);
		$('#newmsg').show(1500);
		$('#viewlist').hide(1500);
	});
	$('#send_lnk').click(function(event){
		event.preventDefault();
		send_message();
	});
	$('#get_target').click(function(event){
		event.preventDefault();
		$('#targets').show();
	});

	$('#txt').show_char_limit({ 
		status_element: '#char_left', 
		status_style: 'chars_left', 
		maxlength: 400 
	});

	interval = window.setInterval(check_for_updates, 5000);
	
});

var check_for_updates = function(){
	last_msg = <?php if (count($thread->getMessages()) > 0) echo $thread->getLastMessage()->getID(); else echo 0 ?>;
	tid = <?php echo $thread->getTid() ?>;
	upd = "msg";
	var p = document.getElementsByTagName("audio")[0];
	$.ajax({
		type: "POST",
		url: "check_for_updates.php",
		data: {msg: last_msg, tid: tid, upd: upd},
		dataType: 'json',
		error: function() {

		},
		succes: function() {
			
		},
		complete: function(data){
			r = data.responseText;
			if(r == "null"){
				return false;
			}
			var json = $.parseJSON(r);
			$.each(json, function(){
				var t = this;
				if (this.type == "new"){
					div_msg = document.createElement("div");
					div_msg.setAttribute("id", "msg_"+t.mid);
					div_msg.setAttribute("display", "none");
					div_msg.setAttribute("class", "message_detail target_msg");
		
					div_h = document.createElement("div");
					div_h.setAttribute("class", "msg_header");
		
					div_send = document.createElement("div");
					div_send.setAttribute("class", "msg_send");
					if (t.t_t != 'G') {
						div_send.appendChild(document.createTextNode(t.send));
					}
					else {

					}
		
					div_read = document.createElement("div");
					div_read.setAttribute("class", "msg_read");
					if (t.t_t != 'G') {
						div_read.appendChild(document.createTextNode("Letto "+t.read));
					}
					else {
						div_read.appendChild(document.createTextNode(t.send));
					}
		
					div_txt = document.createElement("div");
					div_txt.setAttribute("class", "msg_text");
					div_txt.appendChild(document.createTextNode(t.text));
		
					div_h.appendChild(div_send);
					div_h.appendChild(div_read);
		
					div_msg.appendChild(div_h);
					div_msg.appendChild(div_txt);

					$('#oth_user').after(div_msg);
					$('#msg_'+t.mid).hide();
					$('#msg_'+t.mid).toggle({effect: 'scale', percent: 150});
					p.play();
				}
				else {
					mid = t.mid;
					$('#read_'+mid).text("Letto "+t.read);
				}
			});
		}
	});
};

var send_message = function(){
	$.ajax({
		type: "POST",
		url: "controller.php?do=send&tid=<?php echo $thread->getTid() ?>",
		data: $('form').serialize(),
		error: function() {

		},
		succes: function() {
			alert("Message sent");
			
		},
		complete: function(data){
			//$('#target').val("");
			r = data.responseText;
			if(r == "null"){
				return false;
			}
			var json = $.parseJSON(r);

			div_msg = document.createElement("div");
			div_msg.setAttribute("id", "msg_"+json.mid);
			div_msg.setAttribute("display", "none");
			div_msg.setAttribute("class", "message_detail my_msg");

			div_h = document.createElement("div");
			div_h.setAttribute("class", "msg_header");

			div_send = document.createElement("div");
			div_send.setAttribute("class", "msg_send");
			div_send.appendChild(document.createTextNode(json.date));

			div_read = document.createElement("div");
			div_read.setAttribute("class", "msg_read");
			div_read.setAttribute("id", "read_"+json.mid);
			if (json.t_t != 'G') {
				div_read.appendChild(document.createTextNode("Letto: no"));
			}

			div_txt = document.createElement("div");
			div_txt.setAttribute("class", "msg_text");
			div_txt.appendChild(document.createTextNode(json.text));

			div_h.appendChild(div_send);
			div_h.appendChild(div_read);

			div_msg.appendChild(div_h);
			div_msg.appendChild(div_txt);

			$('#oth_user').after(div_msg);

			$('#sel_thread').show();
			$('#message').hide();
			$('#newmsg').show();
			$('#viewlist').hide();

			$('#msg_'+json.mid).show(1500);
		}
	});
};

</script>
</head>
<body>
<?php include "../../intranet/{$_SESSION['__mod_area__']}/header.php" ?>
<?php include "navigation.php" ?>
<div id="main">
<div id="right_col">
<?php include "menu.php" ?>
</div>
<div id="left_col">
	<div id="navbar">
		<div id="username">Messaggi di <?php echo $_SESSION['__user__']->getFullName() ?></div>
		<div id="newmsg">
			<a href="#" id="newmsg_lnk"><img src="theme/new_mail.png" style="" /></a>
		</div>
		<div id="viewlist">
			<a href="#" id="viewlist_lnk"><img src="theme/view-list-icon.png" style="width: 32px; height: 32px; margin-top: 4px" /></a>
		</div>
	</div>
	<div id="sel_thread">
		<div id="oth_user"><?php echo $thread->getTargetName($uniqID) ?></div>
	<?php
	if (count($thread->getMessages()) > 0) {
		foreach ($thread->getMessages() as $k => $msg){
			list($date, $time) = explode(" ", $msg->getSendTimestamp());
			if (date("Y-m-d") == $date){
				$date = " oggi alle";
			}
			else {
				$date = "il ". format_date($date, SQL_DATE_STYLE, IT_DATE_STYLE, "/");
			}
			if ($msg->getReadTimestamp() == null){
				$rdate = ": no";
				$rtime = "";
			}
			else {
				list($rdate, $rtime) = explode(" ", $msg->getReadTimestamp());
				if (date("Y-m-d") == $rdate){
					$rdate = " oggi alle";
					$rtime = substr($rtime, 0, 5);
				}
				else {
					$rdate = " il ". format_date($rdate, SQL_DATE_STYLE, IT_DATE_STYLE, "/");
					$rtime = substr($rtime, 0, 5);
				}
			}
			$msg_send = $msg_read = "";
			if ($thread->getType() == 'G') {
				if ($msg->getFrom()->getUniqID() == $uniqID) {
					$msg_send = "Tu";
				}
				else {
					$msg_send = $msg->getFrom()->getFullName();
				}
				$msg_read = "Inviato ". $date." ".substr($time, 0, 5);
			}
			else {
				$msg_send = "Inviato ". $date." ".substr($time, 0, 5);
				$msg_read = "Letto ".$rdate." ".$rtime;
			}
	?>
		<div id="msg_<?php echo $k; ?>" class="message_detail <?php if ($msg->getFrom()->getUid() == $uid) echo "my_msg"; else echo "target_msg" ?>">
			<div class="msg_header">
				<div class="msg_send"><?php echo $msg_send ?></div>
				<div class="msg_read" id="read_<?php echo $k ?>"><?php echo $msg_read ?></div>
			</div>
			<div class="msg_text"><?php echo utf8_decode($msg->getText()) ?></div>
		</div>
	<?php
		}
	}
	?>
	</div>
	<div id="message">
		<form class="no_border">
		<div id="to"><input type="text" name="target" id="target" readonly value="<?php echo $thread->getTargetName($uniqID) ?>" /></div>
		<div id="get_to"><a href="#" id="get_target"><img src="theme/36.png" style="margin-top: 4px" /></a></div>
		<div id="msgtxt">
			<textarea id="txt" name="txt" placeholder="Componi il messaggio (max 400 caratteri)" ></textarea>
		</div>
		<input type="hidden" name="targetID" id="targetID" value="<?php echo $thread->getTid() ?>" />
		</form>
		<span>Rimangono <span id="char_left">400</span> caratteri</span>
		<a href="#" id="send_lnk"><img src="theme/mail-send-icon.png" style="width: 24px; height: 24px" /></a>
	</div>
	<div id="targets">Elenco utenti</div>
</div>
<audio src="theme/new_msg.ogg" preload="auto" id="mp3"></audio>
<p class="spacer"></p>
<p class="spacer"></p>
</div>
<?php include "../../intranet/{$_SESSION['__mod_area__']}/footer.php" ?>
</body>
</html>
