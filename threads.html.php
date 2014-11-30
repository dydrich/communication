<!DOCTYPE html>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title><?php print $_SESSION['__config__']['intestazione_scuola'] ?>:: messaggi</title>
<link href='http://fonts.googleapis.com/css?family=Source+Sans+Pro:400,300,400italic,600,600italic,700,700italic,900,200' rel='stylesheet' type='text/css'>
<link rel="stylesheet" href="../../css/site_themes/<?php echo getTheme() ?>/reg.css" type="text/css" media="screen,projection" />
<link rel="stylesheet" href="../../css/general.css" type="text/css" media="screen,projection" />
<link rel="stylesheet" href="../../css/site_themes/<?php echo getTheme() ?>/communication.css" type="text/css" media="screen,projection" />
<link rel="stylesheet" href="../../css/site_themes/<?php echo getTheme() ?>/jquery-ui.min.css" type="text/css" media="screen,projection" /><script type="text/javascript" src="../../js/jquery-2.0.3.min.js"></script>
<script type="text/javascript" src="../../js/jquery-ui-1.10.3.custom.min.js"></script>
<script type="text/javascript" src="../../js/page.js"></script>
<script>
var last_tid = <?php echo $last_tid ?>;
var last_msg = <?php echo $last_msg ?>;
$(function(){
	load_jalert();
	setOverlayEvent();
	$('#newmsg_lnk').click(function(event){
		event.preventDefault();
		$('#threads').slideUp(1500);
		$('#message').slideDown(1500);
		$('#newmsg').slideUp(1500);
		$('#viewlist').slideDown(1500);
		$('#target').focus();
		
	});
	$('#viewlist_lnk').click(function(event){
		event.preventDefault();
		$('#txt').val("");
		$('#target').val("");
		$('#threads').show(1500);
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
		$('#targets').show(1500);
	});

	interval = window.setInterval(check_for_updates, 5000);
});

$(function() {   
    //autocomplete
    $("#target").autocomplete({
        source: "get_users.php",
        minLength: 2,
        select: function(event, ui){
			uid = ui.item.uniqID;
			tp = ui.item.type;
			$('#targetID').val(uid);
        }
    });                
 
});

var send_message = function(){
	//alert($('#targetID').val());
	$.ajax({
		type: "POST",
		url: "controller.php?do=send&tid=0",
		data: $('form').serialize(),
		error: function() {

		},
		succes: function(data) {
			alert(data);
			
		},
		complete: function(data){
			$('#txt').val("");
			$('#target').val("");
			//dati = data.responseText.split("|");
			r = data.responseText;
			if(r == ""){
				return false;
			}
			var json = $.parseJSON(r);

			lnk = document.createElement("A");
			lnk.setAttribute("href", "controller.php?do=show_thread&tid="+json.thread);
			lnk.setAttribute("class", "th_link");
			
			div_th = document.createElement("DIV");
			div_th.setAttribute("id", "thread_"+json.thread);
			div_th.setAttribute("class", "card");
			
			div_h = document.createElement("div");
			div_h.setAttribute("class", "card_title");
			
			div_user = document.createElement("div");
			div_user.setAttribute("class", "thread_user");
			div_user.appendChild(document.createTextNode(json.target));

			div_count = document.createElement("div");
			div_count.setAttribute("class", "thread_msg_count");
			div_count.appendChild(document.createTextNode(json.date));

			//div_lm = document.createElement("div");
			//div_lm.setAttribute("class", "thread_lm");
			//div_lm.appendChild(document.createTextNode(json.date));

			div_txt = document.createElement("div");
			div_txt.setAttribute("class", "card_content");
			div_txt.appendChild(document.createTextNode(json.text));

			div_h.appendChild(div_user);
			div_h.appendChild(div_count);
			//div_h.appendChild(div_lm);

			div_th.appendChild(div_h);
			div_th.appendChild(div_txt);

			lnk.appendChild(div_th);

			$('#threads').prepend(lnk);

			$('#threads').show(1500);
			$('#message').hide(1500);
			$('#newmsg').show();
			$('#viewlist').hide();
			last_tid = json.thread;
			last_msg = json.mid;
		}
	});
};

var check_for_updates = function(){
	
	tid = last_tid;
	lmsg = last_msg;
	upd = "th";
	var p = document.getElementsByTagName("audio")[0];
	$.ajax({
		type: "POST",
		url: "check_for_updates.php",
		data: {tid: tid, upd: upd, lmsg: lmsg},
		dataType: 'json',
		error: function() {

		},
		succes: function() {
			
		},
		complete: function(data){
			r = data.responseText;
			if(r == ""){
				return false;
			}
			var json = $.parseJSON(r);
			if(json.status == "no_upd"){
				return false;
			}
			$.each(json, function(){
				var t = this;
				if (this.type == "del_new"){
					// delete element
					//alert($("ln_"+t.tid));
					$("#ln_"+t.tid).hide();
					$('#thread_'+t.tid).hide();
				}
				else if (t.type == "upd") {
					//alert(t.count);
					//$('#count_thr_'+t.tid).text(t.count);
					$('#date_thr_'+t.tid).text(t.datetime);
					if (t.thread_type == 'G') {
						$('#txt_thr_'+t.tid).text(t.sender+": "+t.text);
					}
					else {
						$('#txt_thr_'+t.tid).text(">> "+t.text);
					}
					$('#head_thr_'+ t.tid).addClass("bold_");
					txt = $('#thread_user_'+ t.tid).text();
					$('#thread_user_'+ t.tid).html(t.user+"<span class='new_msg_sign'>(nuovi messaggi)</span>")
				}
				else {
					a_ln = document.createElement("a");
					a_ln.setAttribute("href", "controller.php?do=show_thread&tid="+t.tid);
					a_ln.setAttribute("id", "ln_"+t.tid);
					a_ln.setAttribute("class", "th_link");

					div_th = document.createElement("div");
					div_th.setAttribute("id", "thread_"+t.tid);
					div_th.setAttribute("display", "none");
					div_th.setAttribute("class", "card");

					div_h = document.createElement("div");
					div_h.setAttribute("class", "card_title bold_");

					div_user = document.createElement("div");
					div_user.setAttribute("class", "thread_user");
					div_user.appendChild(document.createTextNode(t.user));

					span = document.createElement("span");
					span.setAttribute("class", "new_msg_sign");
					span.appendChild(document.createTextNode("(nuovi messaggi)"));

					div_count = document.createElement("div");
					div_count.setAttribute("class", "thread_msg_count");
					div_count.appendChild(document.createTextNode(t.datetime));

					//div_lm = document.createElement("div");
					//div_lm.setAttribute("class", "thread_lm");
					//div_lm.appendChild(document.createTextNode(t.datetime));

					div_txt = document.createElement("div");
					div_txt.setAttribute("class", "card_content");
					div_txt.appendChild(document.createTextNode(t.text));

					div_user.appendChild(span);
					div_h.appendChild(div_user);
					div_h.appendChild(div_count);
					//div_h.appendChild(div_lm);
					div_th.appendChild(div_h);
					div_th.appendChild(div_txt);
					a_ln.appendChild(div_th);

					$('#threads').prepend(a_ln);
					$('#thread_'+t.tid).hide();
					$('#thread_'+t.tid).toggle({effect: 'scale', percent: 150});
				}
				p.play();
				if (this.type == "new"){
					last_tid = t.tid;
				}
				last_msg = t.mid;
			});
		}
	});
};

</script>
</head>
<body>
<?php include "../../intranet/{$_SESSION['__mod_area__']}/header.php" ?>
<?php include "navigation_th.php" ?>
<div id="main">
<div id="right_col">
<?php include "menu.php" ?>
</div>
<div id="left_col">
	<div id="threads" class="card_container" style="margin-top: 25px">
	<?php
	if (isset($threads) && count($threads) > 0){
		foreach ($ordered_threads as $k => $thread){
			list($date, $time) = explode(" ", $k);
			if (date("Y-m-d") == $date){
				$date = "Oggi alle";
			}
			else if ($date == date('Y-m-d',time() - (24 * 60 * 60))){
				$date = " Ieri alle";
			}
			else {
				$date = format_date($date, SQL_DATE_STYLE, IT_DATE_STYLE, "/");
			}
			$text = "Nessun messaggio";
			if (count($thread->getMessages()) > 0) {
				$text = "";
				if ($thread->getType() == 'G') {
					if ($thread->getLastMessage()->getFrom()->getUniqID() != $uniqID) {
						$text = $thread->getLastMessage()->getFrom()->getFullName().": ";
					}
					else {
						$text = "Tu: ";
					}
				}
				else {
					if ($thread->getLastMessage()->getFrom()->getUniqID() != $uniqID) {
						$text = "&gt;&gt;  ";
					}
				}
				$text .= truncateString(utf8_decode($thread->getLastMessage()->getText()), 200);
			}
	?>
		<a href="controller.php?do=show_thread&tid=<?php echo $thread->getTid() ?>" id="ln_<?php echo $thread->getTid() ?>" class="th_link">
		<div id="thread_<?php echo $thread->getTid(); ?>" class="card">
			<div id="head_thr_<?php echo $thread->getTid() ?>" class="card_title <?php if (!$thread->isRead($_SESSION['__user__'])) echo "bold_" ?>">
				<div id="thread_user_<?php echo $thread->getTid() ?>" class="thread_user"><?php echo $thread->getTargetName($_SESSION['__user__']->getUniqID()); if ($thread->isRead($_SESSION['__user__']) === false): ?><span class="new_msg_sign">(nuovi messaggi)</span><?php endif; ?></div>
				<div id="date_thr_<?php echo $thread->getTid() ?>" class="thread_msg_count"><?php echo $date." ".substr($time, 0, 5) ?></div>
				<!--<div id="count_thr_<?php echo $thread->getTid() ?>" class="thread_lm"><?php echo $thread->getMessagesCount() ?> messaggi</div> -->
			</div>
			<div id="txt_thr_<?php echo $thread->getTid() ?>" class="card_content" style="color: #1E4389"><?php echo $text ?></div>
		</div>
		</a>
	<?php
		}
	}
	?>
	</div>
	<div id="message">
		<form class="no_border">
		<div id="to"><input type="text" name="target" id="target" /></div>
		<div id="get_to"><a href="#" id="get_target"><img src="theme/36.png" style="margin-top: 4px" /></a></div>
		<div id="msgtxt">
			<textarea id="txt" name="txt" placeholder="Componi il messaggio (max 400 caratteri)" maxlength="400"></textarea>
		</div>
		<input type="hidden" name="targetID" id="targetID" />
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
<div id="drawer" class="drawer" style="display: none; position: absolute">
	<div style="width: 100%; height: 430px">
		<div class="drawer_link"><a href="<?php echo $_SESSION['__modules__']['com']['path_to_root'] ?>intranet/<?php echo $_SESSION['__mod_area__'] ?>/index.php"><img src="../../images/6.png" style="margin-right: 10px; position: relative; top: 5%" />Home</a></div>
		<div class="drawer_link"><a href="<?php echo $_SESSION['__modules__']['com']['path_to_root'] ?>intranet/<?php echo $_SESSION['__mod_area__'] ?>/profile.php"><img src="../../images/33.png" style="margin-right: 10px; position: relative; top: 5%" />Profilo</a></div>
		<?php if (!$_SESSION['__user__'] instanceof ParentBean) : ?>
			<div class="drawer_link"><a href="<?php echo $_SESSION['__modules__']['com']['path_to_root'] ?>modules/documents/load_module.php?module=docs&area=<?php echo $_SESSION['__mod_area__'] ?>"><img src="<?php echo $_SESSION['__modules__']['com']['path_to_root'] ?>images/11.png" style="margin-right: 10px; position: relative; top: 5%" />Documenti</a></div>
		<?php endif; ?>
		<?php if(is_installed("com")){ ?>
			<div class="drawer_link"><a href="<?php echo $_SESSION['__modules__']['com']['path_to_root'] ?>modules/communication/load_module.php?module=com&area=<?php echo $_SESSION['__mod_area__'] ?>"><img src="<?php echo $_SESSION['__modules__']['com']['path_to_root'] ?>images/57.png" style="margin-right: 10px; position: relative; top: 5%" />Comunicazioni</a></div>
		<?php } ?>
	</div>
	<?php if (isset($_SESSION['__sudoer__'])): ?>
		<div class="drawer_lastlink"><a href="<?php echo $_SESSION['__modules__']['com']['path_to_root'] ?>admin/sudo_manager.php?action=back"><img src="<?php echo $_SESSION['__modules__']['com']['path_to_root'] ?>images/14.png" style="margin-right: 10px; position: relative; top: 5%" />DeSuDo</a></div>
	<?php endif; ?>
	<div class="drawer_lastlink"><a href="<?php echo $_SESSION['__modules__']['com']['path_to_root'] ?>shared/do_logout.php"><img src="<?php echo $_SESSION['__modules__']['com']['path_to_root'] ?>images/51.png" style="margin-right: 10px; position: relative; top: 5%" />Logout</a></div>
</div>
</body>
</html>
