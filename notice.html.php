<!DOCTYPE html>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title><?php print $_SESSION['__config__']['intestazione_scuola'] ?>:: avviso del DS</title>
<link rel="stylesheet" href="../../css/site_themes/<?php echo getTheme() ?>/reg.css" type="text/css" media="screen,projection" />
<link rel="stylesheet" href="../../css/general.css" type="text/css" media="screen,projection" />
<link rel="stylesheet" href="../../css/site_themes/<?php echo getTheme() ?>/communication.css" type="text/css" media="screen,projection" />
<link rel="stylesheet" href="../../css/site_themes/<?php echo getTheme() ?>/jquery-ui.min.css" type="text/css" media="screen,projection" />
<script type="text/javascript" src="../../js/jquery-2.0.3.min.js"></script>
<script type="text/javascript" src="../../js/jquery-ui-1.10.3.custom.min.js"></script>
<script type="text/javascript" src="../../js/jquery-ui-timepicker-addon.js"></script>
<script type="text/javascript" src="../../js/page.js"></script>
<script type="text/javascript">
$(document).ready(function(){
	$('#data').datepicker({
		dateFormat: "dd/mm/yy",
		currentText: "Oggi",
		closeText: "Chiudi"
	});
});

function registra(){
	if(trim(document.forms[0].data.value) == ""){
		alert("La data di scadeza e` obbligatoria.");
		return false;
	}
	else if(trim(document.forms[0].testo.value) == ""){
		alert("Il testo e` obbligatorio");
		return false;
	}

	$.ajax({
		type: "POST",
		url: "notice_manager.php",
		data: $('#my_form').serialize(true),
		dataType: 'json',
		error: function(data, status, errore) {
			alert("Si e' verificato un errore");
			return false;
		},
		succes: function(result) {
			alert("ok");
		},
		complete: function(data, status){
			r = data.responseText;
			var json = $.parseJSON(r);
			if(json.status == "kosql"){
				alert("Errore SQL. \nQuery: "+json.query+"\nErrore: "+json.message);
				return;
      		}
			else {
				$('#not1').text(json.message);
				$('#not1').show(1000);
			}
		}
	});
}
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
	<div class="group_head">
		Gestione avviso
	</div>
	<div id="not1" class="notification"></div>
 	<form id="my_form" method="post" action="../../admin/adm_news/news_manager.php" style="border: 1px solid #666666; border-radius: 10px; margin-top: 20px; text-align: left; width: 560px; margin-left: auto; margin-right: auto">
	<table style="width: 500px; margin-left: auto; margin-right: auto; margin-top: 30px; margin-bottom: 20px">
		<tr>
			<td style="width: 30%">Data scadenza</td>
			<td style="width: 70%">
				<input type="text" name="data" id="data" style="width: 350px; font-size: 11px; border: 1px solid #AAAAAA" value="<?php if(isset($notice)) echo format_date($notice['data_scadenza'], SQL_DATE_STYLE, IT_DATE_STYLE, "/") ?>" />
			</td> 
		</tr>
		<tr>
			<td style="width: 30%">Testo</td>
			<td style="width: 70%">
				<textarea name="testo" id="testo" style="width: 350px; height: 100px; font-size: 11px; border: 1px solid #AAAAAA"><?php if(isset($notice)) echo $notice['testo'] ?></textarea>
			</td> 
		</tr>
		<tr>
			<td colspan="2">&nbsp;
				<input type="hidden" name="action" id="action" value="<?php echo $action ?>" />
    			<input type="hidden" name="_i" id="_i" value="<?php echo $idnotice ?>" />
			</td> 
		</tr>
		<tr>
			<td colspan="2" style="text-align: right; margin-right: 50px">
				<a href="#" onclick="registra()" style="text-decoration: none; text-transform: uppercase">Registra</a>
			</td> 
		</tr>
	</table>
	</form>
</div>
<p class="spacer"></p>	
</div>
<?php include "../../intranet/{$_SESSION['__mod_area__']}/footer.php" ?>	
</body>
</html>
