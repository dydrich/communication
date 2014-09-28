<!DOCTYPE html>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title><?php print $_SESSION['__config__']['intestazione_scuola'] ?>:: circolari</title>
<link rel="stylesheet" href="../../css/site_themes/<?php echo getTheme() ?>/reg.css" type="text/css" media="screen,projection" />
<link rel="stylesheet" href="../../css/general.css" type="text/css" media="screen,projection" />
<link rel="stylesheet" href="../../css/site_themes/<?php echo getTheme() ?>/communication.css" type="text/css" media="screen,projection" />
<link rel="stylesheet" href="../../css/site_themes/<?php echo getTheme() ?>/jquery-ui.min.css" type="text/css" media="screen,projection" /><script type="text/javascript" src="../../js/jquery-2.0.3.min.js"></script>
<script type="text/javascript" src="../../js/jquery-ui-1.10.3.custom.min.js"></script>
<script type="text/javascript" src="../../js/jquery.show_char_limit-1.2.0.js"></script>
<script type="text/javascript" src="../../js/page.js"></script>
<script type="text/javascript">
function check_form(frm){
	var ind = 0;
	var msg = "Il modulo non e' stato compilato correttamente. Sono stati riscontrati i seguenti errori:\n";
	var bool = true;
	//alert(isNaN(parseFloat(frm.num_c.value)));
	if((frm.num_c.value == "0") || (isNaN(parseFloat(frm.num_c.value)))){
		ind++;
		msg += "\n"+ind+". Numero di circolare assente o errato";
		$('lab1').style.color = "#ff0000";
		bool = false;
	}
	else
		$('lab1').style.color = "inherit";
	
	if(trim(frm.data.value) == ""){
		ind++;
		msg += "\n"+ind+". Data non inserita";
		$('lab3').style.color = "#ff0000";
		bool = false;
	}
	else if(!valida_data(frm.data.value)){
		ind++;
		msg += "\n"+ind+". Data non corretta";
		$('lab3').style.color = "#ff0000";
		frm.data.value = "";
		bool = false;
	}
	else
		$('lab3').style.color = "inherit";
	
	if(trim(frm.obj.value) == ""){
		ind++;
		msg += "\n"+ind+". Oggetto non inserito";
		$('lab4').style.color = "#ff0000";
		bool = false;
	}
	else
		$('lab4').style.color = "inherit";
	
	if(trim(frm.dest.value) == ""){
		ind++;
		msg += "\n"+ind+". Destinatari assenti";
		$('lab5').style.color = "#ff0000";
		bool = false;
	}
	else
		$('lab5').style.color = "inherit";
	
	if(trim(frm.txt.value) == ""){
		ind++;
		msg += "\n"+ind+". Testo della circolare assente";
		$('lab6').style.color = "#ff0000";
		bool = false;
	}
	else
		$('lab6').style.color = "inherit";
	
	if(!bool)
		alert(msg);
	return bool;
}

function del_circ(id){
	if(!confirm("Sei sicuro di voler cancellare questa circolare?"))
        return false;

	$.ajax({
		type: "POST",
		url: "circ_manager.php",
		data: {action: 'delete', idc: id},
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
				$('#row_'+id).hide();
			}
		}
	});
}

$(document).ready(function(){
	$('table tbody > tr').mouseover(function(event){
		//alert(this.id);
		var strs = this.id.split("_");
		$('#link_'+strs[1]).show();
	});
	$('table tbody > tr').mouseout(function(event){
			//alert(this.id);
			var strs = this.id.split("_");
			$('#link_'+strs[1]).hide();
	});
	$('table tbody a.del_link').click(function(event){
		event.preventDefault();
		var strs = this.parentNode.id.split("_");
		del_circ(strs[1]);
	});
});
</script>
<style type="text/css">
.ov_red {
	font-weight: bold
}
.ov_red:hover{
	color: #8a1818;
}
</style>
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
		Elenco circolari
	</div>
	<div id="not1" class="notification"></div>
	<div class="list_header">
		<div style="width: 35%; float: left; position: relative; top: 30%; left: 5px"><span style="padding-left: 15px">Oggetto</span></div>
		<div style="width: 25%; float: left; position: relative; top: 30%; text-align: center">Data</div>
		<div style="width: 10%; float: left; position: relative; top: 30%">Numero</div>
		<div style="width: 10%; float: left; position: relative; top: 30%">Prot.</div>
		<div style="width: 20%; float: left; position: relative; top: 30%; text-align: left">Inserita da</div>
	</div>
	<table style="width: 95%; margin: 0 auto 0 auto">
		<tbody>
 	    <?php 
 	    if($result->num_rows < 1){
 	    ?>
 	    <tr>
			<td colspan="5" style="height: 50px; text-align: center; font-weight: bold; text-transform: uppercase">Nessuna circolare presente</td>
		</tr>
 	    <?php 
 	    }
 	    else{
 	    	$x = 1;
 	    	if($result->num_rows > $limit)
 	    		$max = $limit;
 	    	else
 	    		$max = $result->num_rows;
 	    	$row = 0;
			while ($circolare = $result->fetch_assoc()){
				if($x > $limit) break;
 	    ?>
 	    	<tr class="<?php echo $row_class ?>" id="row_<?php echo $circolare['id_circolare'] ?>">
				<td style="width: 35%">
					<span class="ov_red"><?php print $circolare['oggetto'] ?></span>
                	<div id="link_<?php echo $circolare['id_circolare'] ?>" style="display: none; margin-top: 2px">
                	<a href="circolare.php?idc=<?php print $circolare['id_circolare'] ?>" style="text-decoration: none; text-transform: uppercase">Modifica</a>
                	<span style="margin-left: 5px; margin-right: 5px">|</span>
                	<a href="circ_manager.php?action=2&_id=<?php print $circolare['id_circolare'] ?>" class="del_link" style="text-decoration: none; text-transform: uppercase">Cancella</a>
                	<span style="margin-left: 5px; margin-right: 5px">|</span>
                	<a href="lettura_circolari.php?idc=<?php print $circolare['id_circolare'] ?>" class="ver_link" style="text-decoration: none; text-transform: uppercase">Lettura</a>
                	</div>
				</td>
				<td style="width: 25%; text-align: center"><?php print format_date($circolare['data_circolare'], SQL_DATE_STYLE, IT_DATE_STYLE, "/") ?></td>
				<td style="width: 10%; text-align: center"><?php print $circolare['progressivo'] ?></td>
				<td style="width: 10%; text-align: center"><?php print $circolare['protocollo'] ?></td>
				<td style="width: 20%; text-align: center"><?php print $circolare['nome']." ".$circolare['cognome'] ?></td>
			</tr>
 	    <?php 
 	    		$row++;
 	    		$x++;
 	    	}
 	    ?>
 	    </tbody>
        <tfoot>
        <?php
            $expand = false;
            include "../../shared/navigate.php";
 	    }
 	    ?>
 	    <tr style="text-align: right; font-weight: normal; ">
				<td colspan="5" style="padding-top: 20px"><a href="circolare.php?idc=0" style="text-decoration: none; text-transform: uppercase">Nuova circolare</a></td>
		</tr>
		</tfoot>	
 	</table>	
	</div>
<p class="spacer"></p>
</div>
<?php include "../../intranet/{$_SESSION['__mod_area__']}/footer.php" ?>	
</body>
</html>
