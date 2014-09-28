<!DOCTYPE html>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title><?php print $_SESSION['__config__']['intestazione_scuola'] ?>:: eventi</title>
<link rel="stylesheet" href="../../css/site_themes/<?php echo getTheme() ?>/reg.css" type="text/css" media="screen,projection" />
<link rel="stylesheet" href="../../css/general.css" type="text/css" media="screen,projection" />
<link rel="stylesheet" href="../../css/site_themes/<?php echo getTheme() ?>/communication.css" type="text/css" media="screen,projection" />
<link rel="stylesheet" href="../../css/site_themes/<?php echo getTheme() ?>/jquery-ui.min.css" type="text/css" media="screen,projection" /><script type="text/javascript" src="../../js/jquery-2.0.3.min.js"></script>
<script type="text/javascript" src="../../js/jquery-ui-1.10.3.custom.min.js"></script>
<script type="text/javascript" src="../../js/jquery.show_char_limit-1.2.0.js"></script>
<script type="text/javascript">
function del_evs(id){
	if(!confirm("Sei sicuro di voler cancellare questo evento?"))
        return false;
	
	$.ajax({
		type: "POST",
		url: "events_manager.php",
		data: {action: 2, _i: id},
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
		del_evs(strs[1]);
	});
});
</script>
<style type="text/css">
.ov_red:hover{
	color: #8a1818;
	font-weight: bold
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
		Elenco eventi
	</div>
	<div id="not1" class="notification"></div>
	<div class="list_header">
		<div style="width: 65%; float: left; position: relative; top: 20%; text-align: left"><span style="padding-left: 25px">Attivit&agrave;</span></div>
		<div style="width: 15%; float: left; position: relative; top: 30%; text-align: left">Utente</div>
		<div style="width: 20%; float: left; position: relative; top: 30%">Data e ora evento</div>
	</div>
		<table style="width: 95%; margin: 0px auto 0 auto">
		<tbody id="t_body">
            <?php
            $x = 1;
            if($res_evs->num_rows > $limit)
                $max = $limit;
            else
                $max = $res_evs->num_rows;

            while($event = $res_evs->fetch_assoc()){
                if($x > $limit) break;
                list($data, $ora) = explode(" ", $event['data_evento']);
                $ora = substr($ora, 0, 5);
                $js = "evento(".$event['id_evento'].")";
                $scaduto = 0;
                if($data < date("Y-m-d")){
                	$js = "evento_scaduto()";
                	$scaduto = 1;
                }
                // verifico se l'evento e' un contenitore (ha dei figli)
                $sel_sons = "SELECT COUNT(*) AS count_sons FROM rb_com_eventi WHERE id_padre = ".$event['id_evento'];
                $res_sons = $db->execute($sel_sons);
                $count_sons = $res_sons->fetch_assoc();
            ?>
            <tr class="<?php echo $row_class ?>" id="row_<?php echo $event['id_evento'] ?>">
                <td style="width: 65%">
                	<span class="ov_red" style="font-weight: bold"><?php echo utf8_decode($event['abstract']) ?></span>
                	<div id="link_<?php echo $event['id_evento'] ?>" style="display: none">
                	<a href="event.php?id=<?php echo $event['id_evento'] ?>" class="mod_link" style="text-decoration: none; text-transform: uppercase">Modifica</a>
                	<span style="margin-left: 5px; margin-right: 5px">|</span>
                	<a href="events_manager.php?action=2&_id=<?php echo $event['id_evento'] ?>"  style="text-decoration: none; text-transform: uppercase" class="del_link">Cancella</a>
                	</div>
                </td>
                <td style="width: 15%"><?php print utf8_decode($event['nome']." ".$event['cg']) ?></td>
                <td style="width: 20%; text-align: center"><?php  if($count_sons['count_sons'] < 1) print format_date($data, 2, 1, "/")." ".$ora.""; else print ("---") ?></td>
            </tr>
            <?php
                $x++;
            }
            ?>
            </tbody>
            <tfoot>
            <?php
            $expand = false;
            include "../../shared/navigate.php";
            ?>
            <tr>
                <td colspan="3" style="text-align: right">
                    <a href="event.php?id=0" id="add" style="text-decoration: none; text-transform: uppercase">Nuovo evento</a>
                </td>
            </tr>
        </tfoot>
        </table>
</div>
<p class="spacer"></p>	
</div>
<?php include "../../intranet/{$_SESSION['__mod_area__']}/footer.php" ?>	
</body>
</html>
