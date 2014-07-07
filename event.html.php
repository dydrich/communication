<!DOCTYPE html>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title><?php print $_SESSION['__config__']['intestazione_scuola'] ?>:: evento</title>
<link rel="stylesheet" href="../../intranet/teachers/reg.css" type="text/css" media="screen,projection" />
<link rel="stylesheet" href="theme/style.css" type="text/css" media="screen,projection" />
<link rel="stylesheet" href="../../js/jquery_themes/custom-theme/jquery-ui-1.10.3.custom.min.css" type="text/css" media="screen,projection" />
<script type="text/javascript" src="../../js/jquery-2.0.3.min.js"></script>
<script type="text/javascript" src="../../js/jquery-ui-1.10.3.custom.min.js"></script>
<script type="text/javascript" src="../../js/jquery-ui-timepicker-addon.js"></script>
<script type="text/javascript" src="../../js/page.js"></script>
<script type="text/javascript">

$(document).ready(function(){
	$('#sel3').datetimepicker({
		dateFormat: "dd/mm/yy",
		altField: "#time",
		altFieldTimeOnly: true,
		altTimeFormat: "HH:mm",
		currentText: "Ora",
		closeText: "Chiudi"
	});

	$('#time').timepicker({
		currentText: "Ora",
		closeText: "Chiudi"
	});
});


var registra = function(){
	if(trim(document.forms[0].titolo.value) == ""){
		alert("Il titolo e` obbligatorio.");
		return false;
	}
	
	$.ajax({
		type: "POST",
		url: "events_manager.php",
		data: $('#my_form').serialize(),
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
				window.setTimeout("$('#not1').hide(1000)", 2000);
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
	<div style="width: 95%; height: 30px; margin: 10px auto 0 auto; text-align: center; font-size: 1.1em; text-transform: uppercase">
		Gestione evento
	</div>
	<div id="not1" class="notification"></div>
 	<form id="my_form" method="post" action="events_manager.php" style="border: 1px solid #666666; border-radius: 10px; margin-top: 20px; text-align: left; width: 80%; margin-left: auto; margin-right: auto">
	<table style="width: 90%; margin-left: auto; margin-right: auto; margin-top: 30px; margin-bottom: 20px">
		<tr>
            <td style="width: 30%"><label for="titolo">Titolo</label></td>
            <td style="width: 70%">
                <input type="text" id="titolo" autofocus style="width: 350px" value="<?php if(isset($evs)) print utf8_decode($evs['abstract']) ?>" name="titolo" />
            </td>
        </tr>
        <tr>
            <td style="width: 30%"><label for="evento_padre">Evento padre</label></td>
            <td style="width: 70%">
                <select style="width: 350px" class="form_input" name="evento_padre" id="evento_padre">
                	<option value="" selected="selected">Nessuno</option>
                <?php
				while($ev_p = $res_eventi_p->fetch_assoc()){
                ?>
                	<option <?php if($ev_p['id_evento'] == $evs['id_padre']) print("selected='selected'") ?> value="<?php print $ev_p['id_evento'] ?>"><?php print $ev_p['abstract'] ?></option>
                <?php 
				} 
				?>
                </select>
            </td>
        </tr>
        <tr>
            <td style="width: 30%"><label for="ordine_di_scuola">Ordine di scuola</label></td>
            <td style="width: 70%">
                <select style="width: 350px" class="form_input" name="ordine_di_scuola" id="ordine_di_scuola">
                	<option value="0" selected="selected">Tutti</option>
                <?php
				foreach ($_SESSION['__school_level__'] as $k => $level){
                ?>
                	<option <?php if($k == $evs['ordine_di_scuola']) print("selected='selected'") ?> value="<?php print $k ?>"><?php print $level ?></option>
                <?php 
				} 
				?>
                </select>
            </td>
        </tr>
        <tr>
            <td style="width: 30%"><label for="classe">Classe</label></td>
            <td style="width: 70%">
                <select style="width: 350px" name="classe" id="classe">
                	<option value="" selected="selected">Nessuna</option>
                <?php
				while($cls = $res_classi->fetch_assoc()){
                ?>
                	<option <?php if($cls['id_classe'] == $evs['classe']) print("selected='selected'") ?> value="<?php print $cls['id_classe'] ?>"><?php print $cls['cls']." (".$cls['nome'].")" ?></option>
                <?php } ?>
                </select>
            </td>
        </tr>
        <tr>
            <td style="width: 30%"><label for="data_evento">Data e ora evento</label></td>
            <td>
                <input type="text" name="data_evento" style="width: 70px" value="<?php print $my_date ?>" id="sel3" readonly="readonly" />
                <script type="text/javascript">
                
	        	</script>
                <input class="form_input" type="text" name="ora_evento" id="time" style="width: 35px; margin-left: 10px" value="<?php print substr($my_ora, 0, 5) ?>" />
                <label for="pub" style="margin-left: 5px; margin-right: 8px; " class="popup_title">Pubblico</label>
                <input type="checkbox" name="pub" <?php if($pubblico == 1) print "checked='true'" ?> />
                <label for="upr" style="margin-left: 7px; margin-right: 8px; " class="popup_title">Modificabile</label>
                <input type="checkbox" name="upr" <?php if($modificabile == 1) print "checked='true'" ?> />
            </td>
        </tr>
        <tr>
            <td style="width: 30%; vertical-align: middle"><label for="testo">Testo</label></td>
            <td style="width: 70%">
                <textarea style="width: 350px; height: 100px" name="testo" id="testo"><?php if(isset($evs)) print utf8_decode($evs['testo']) ?></textarea>
            </td>
        </tr>
		<tr>
			<td colspan="2">&nbsp;
				<input type="hidden" name="action" id="action" value="<?php echo $action ?>" />
    			<input type="hidden" name="_i" id="_i" value="<?php echo $_i ?>" />
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
