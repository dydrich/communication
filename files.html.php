<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<title><?php print $_SESSION['__config__']['intestazione_scuola'] ?>:: file</title>
	<link rel="stylesheet" href="../../css/site_themes/<?php echo getTheme() ?>/reg.css" type="text/css" media="screen,projection" />
	<link rel="stylesheet" href="../../css/general.css" type="text/css" media="screen,projection" />
	<link rel="stylesheet" href="../../css/site_themes/<?php echo getTheme() ?>/communication.css" type="text/css" media="screen,projection" />
	<link rel="stylesheet" href="../../css/site_themes/<?php echo getTheme() ?>/jquery-ui.min.css" type="text/css" media="screen,projection" /><script type="text/javascript" src="../../js/jquery-2.0.3.min.js"></script>
	<script type="text/javascript" src="../../js/jquery-ui-1.10.3.custom.min.js"></script>
	<script type="text/javascript" src="../../js/page.js"></script>
	<script>
	$(function(){
		load_jalert();
		setOverlayEvent();
		//autocomplete
		$("#target").autocomplete({
			source: "get_users.php",
			minLength: 2,
			select: function(event, ui){
				uid = ui.item.uid;
				tp = ui.item.type;
				$('#targetID').val(uid);
				$('#target_type').val(tp);
			}
		});
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
			send_file();
		});
		$('#get_target').click(function(event){
			event.preventDefault();
			$('#targets').show(1500);
		});

		//interval = window.setInterval(check_for_updates, 5000);
	});

	var send_file = function(){
		if($('#server_file').val() == ""){
			show_error("Non hai ancora fatto l'upload di nessun file");
			return false;
		}
		else if($('#targetID').val() == ""){
			show_error("Inserisci un destinatario per il file");
			return false;
		}
		//var url = "../../lib/document_manager.php";
		var url = "../../modules/documents/document_manager.php";
		$.ajax({
			type: "POST",
			url: url,
			data: {server_file: $('#server_file').val(), action: "1", doc_type: "file", targetID: $('#targetID').val(), id: 0},
			dataType: 'text',
			error: function() {
				show_error("Errore di trasmissione dei dati");
			},
			succes: function() {

			},
			complete: function(data){
				r = data.responseText;
				if(r == "null"){
					return false;
				}
				dati = r.split("|");
				if (dati[0] == "kosql"){
					show_error("Errore nella registrazione dei dati");
					console.log(json.query+"\n"+json.message);
				}
				else {
					$('#not1').text("File inviato");
					$('#not1').removeClass("error");
					$('#not1').show(2000);
					$('#not1').hide(2000);
					$('#aframe').attr('src', '../../modules/documents/upload_manager.php?upl_type=document&area=teachers&tipo=files');
					$('#server_file').val("");
				}
			}
		});
	};

	var del_file = function(){
		if($('#server_file').val() == ""){
			alert("Non hai ancora fatto l'upload di nessun file");
			return false;
		}
		//var url = "../../admin/adm_docs/document_manager.php";
		var url = "../../modules/documents/document_manager.php";

		$.ajax({
			type: "POST",
			url: url,
			data: {server_file: $('#server_file').val(), action: "4", tipo: "files", doc_type: "document"},
			dataType: 'json',
			error: function() {
				show_error("Errore di trasmissione dei dati");
			},
			succes: function() {

			},
			complete: function(data){
				r = data.responseText;
				if(r == "null"){
					return false;
				}
				var json = $.parseJSON(r);
				if (json.status == "kosql"){
					show_error(json.message);
					console.log(json.dbg_message);
				}
				else {
					$('#not1').text("File cancellato");
					$('#not1').show(1000);
					$('#not1').hide(1000);
					$('#aframe').attr('src', '../../modules/documents/upload_manager.php?upl_type=document&area=teachers&tipo=files');
					$('#server_file').val("");
				}
			}
	    });
	};

	var dwl = function (id, url){

		$('#file_'+id).hide(500);
		document.location.href = url;
	};

	var loading = function(vara){
		$('#not1').text("Attendere il caricamento del file");
		$('#not1').show(500);
	};

	var loaded = function(r){
		//var json = $.parseJSON(r);
		$('#not1').text("Caricamento completato");
		$('#del_upl').show();
		$('#not1').hide(1500);
		$('#server_file').val(r);
	};

	var show_error = function(text){
		//$('#iframe').show();
		$('#not1').text(text);
		$('#not1').addClass("error");
		$('#not1').show(1000);
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
		<div id="username">File di <?php echo $_SESSION['__user__']->getFullName() ?></div>
		<div id="newmsg">
			<?php if ($_SESSION['__user__']->check_perms(DIR_PERM|DSG_PERM|SEG_PERM|APS_PERM|AIS_PERM|AMS_PERM|DOC_PERM)): ?><a href="#" id="newmsg_lnk"><img src="theme/new_mail.png" style="" /></a><?php endif; ?>
		</div>
		<div id="viewlist">
			<a href="#" id="viewlist_lnk"><img src="theme/view-list-icon.png" style="width: 32px; height: 32px; margin-top: 4px" /></a>
		</div>
	</div>
	<div id="threads">
	<?php
	while ($row = $res_received->fetch_assoc()){
	?>
		<div id="file_<?php echo $row['id'] ?>" class="thread">
			<div class="thread_header ">
				<div class="thread_user">Da: <?php echo $row['nome'] ?></div>
				<div class="thread_msg_count"></div>
				<div class="thread_lm"><?php echo "Inviato il ".$row['data_invio'] ?></div>
			</div>
			<div class="thread_text"><a class="dwl" href="#" onclick="dwl(<?php echo $row['id'] ?>, '../../modules/documents/download_manager.php?doc=file&id=<?php echo $row['id'] ?>')">File: <?php echo $row['file'] ?></a></div>
		</div>
	<?php
	}
	?>
	</div>
	<div id="message">
		<form class="no_border">
		<div class="notification" id="not1"></div>
		<div id="to"><input type="text" name="target" id="target" /></div>
		<div id="get_to"><a href="#" id="get_target"><img src="theme/36.png" style="margin-top: 4px" /></a></div>
		<div id="iframe"><iframe src="../../modules/documents/upload_manager.php?upl_type=document&area=teachers&tipo=files" style="border: none; width: 95%;  margin: 0px; height: 80px" id="aframe"></iframe></div>
		<a href="#" onclick="del_file()" id="del_upl" style="float: right; padding-top: 45px; padding-right: 20px; display: none; text-decoration: none">Annulla upload</a>
		<input type="hidden" name="targetID" id="targetID" />
		<input type="hidden" name="server_file" id="server_file" />
		<input type="hidden" name="id" id="id" value="0" />
		</form>
		<a href="#" id="send_lnk"><img src="theme/mail-send-icon.png" style="width: 24px; height: 24px" /></a>
	</div>
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
