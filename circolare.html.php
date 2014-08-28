<!DOCTYPE html>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title><?php print $_SESSION['__config__']['intestazione_scuola'] ?>:: circolari</title>
<link rel="stylesheet" href="../../css/reg.css" type="text/css" media="screen,projection" />
<link rel="stylesheet" href="../../css/general.css" type="text/css" media="screen,projection" />
<link rel="stylesheet" href="theme/style.css" type="text/css" media="screen,projection" />
<link rel="stylesheet" href="../../js/jquery_themes/custom-theme/jquery-ui-1.10.3.custom.min.css" type="text/css" media="screen,projection" />
<script type="text/javascript" src="../../js/jquery-2.0.3.min.js"></script>
<script type="text/javascript" src="../../js/jquery-ui-1.10.3.custom.min.js"></script>
<script type="text/javascript" src="../../js/jquery.show_char_limit-1.2.0.js"></script>
<script type="text/javascript" src="../../js/page.js"></script>
<script type="text/javascript">
$(document).ready(function(){
	$('#data').datepicker({
		dateFormat: "dd/mm/yy"
	});
});


var registra = function(){
	$.ajax({
		type: "POST",
		url: "circ_manager.php",
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
	<div class="group_head">
		Gestione circolare
	</div>
	<div id="not1" class="notification"></div>
 	<form id="my_form" method="post" action="../../admin/adm_docs/circ_manager.php" style="border: 1px solid #666666; border-radius: 10px; margin-top: 20px; text-align: left; width: 80%; margin-left: auto; margin-right: auto">
	<table style="width: 90%; margin-left: auto; margin-right: auto; margin-top: 30px; margin-bottom: 20px">
		<tr>			
			<td style='width: 20%' id='lab1'>Circolare n. *</td>
			<td style="width: 80%">
				<input style='width: 95%; text-align: right' name='num_c' id='num_c' value="<?php if(isset($circ)) echo $circ['progressivo'] ?>" />
			</td>
		</tr>
		<tr>
			<td style='width: 20%' id='lab2' class='label'>Protocollo </td>
			<td style="width: 80%">
				<input style='width: 95%; text-align: right' name='prot' id='prot' value="<?php if(isset($circ)) echo $circ['protocollo'] ?>" />
			</td>
		</tr>
		<tr>
			<td style='width: 20%' id='lab3' class='label'>Data *</td>
			<td style="width: 80%">
				<input style='width: 95%; text-align: right' name='data' id='data' value="<?php if(isset($circ)) echo format_date($circ['data_circolare'], SQL_DATE_STYLE, IT_DATE_STYLE, "/"); ?>" />
			</td>	
		</tr>
		<tr>
			<td style='width: 20%' id='lab4' class='label'>Oggetto *</td>
			<td style="width: 80%">
				<input style='width: 95%' name='obj' id='obj' value="<?php if(isset($circ)) echo $circ['oggetto'] ?>" />
			</td>
		</tr>
		<tr>
			<td style='width: 20%' id='lab5' class='label'>Destinatari *</td>
			<td style="width: 80%">
				<textarea style='width: 95%; height: 50px' name='dest' id='dest'><?php if(isset($circ)) echo $circ['destinatari'] ?></textarea>
			</td>
		</tr>
		<tr>
			<td style='width: 20%' id='lab6' class='label'>Testo *</td>
			<td style="width: 80%">
				<textarea style='width: 95%; height: 100px' name='txt' id='txt' ><?php if(isset($circ)) echo $circ['testo'] ?></textarea>
			</td>
		</tr>
		<tr>
			<td style='width: 20%' class='label'>Allegato</td>
			<td style="width: 80%">
			<?php if (isset($circ) && $allegato != ""){ ?>
				<input class="form_input" type="text" name="fname" id="fname" style="width: 95%" readonly value="<?php print $allegato ?>"/>
			<?php } else if (!isset($circ)){ ?>
				<div id="iframe"><iframe src="../../modules/documents/upload_manager.php?upl_type=document&area=teachers&tipo=allegati" style="border: none; width: 95%;  margin: 0px; height: 80px" id="aframe"></iframe></div>
				<a href="#" onclick="del_file()" id="del_upl" style="float: right; padding-top: 45px; padding-right: 20px; display: none; text-decoration: none">Annulla upload</a>
			<?php } else { ?>
				Nessun file allegato
			<?php } ?>
			</td>
		</tr>
		<tr>
			<td colspan="2">&nbsp;
				<input type="hidden" name="action" id="action" value="<?php echo $action ?>" />
    			<input type="hidden" name="idc" id="idc" value="<?php echo $idc ?>" />
    			<input type="hidden" name="server_file" id="server_file" />
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
