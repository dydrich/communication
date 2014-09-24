<!DOCTYPE html>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title><?php print $_SESSION['__config__']['intestazione_scuola'] ?>:: avvisi del DS</title>
<link rel="stylesheet" href="../../css/site_themes/<?php echo getTheme() ?>/reg.css" type="text/css" media="screen,projection" />
<link rel="stylesheet" href="../../css/general.css" type="text/css" media="screen,projection" />
<link rel="stylesheet" href="../../css/site_themes/<?php echo getTheme() ?>/communication.css" type="text/css" media="screen,projection" />
<link rel="stylesheet" href="../../css/site_themes/<?php echo getTheme() ?>/jquery-ui.min.css" type="text/css" media="screen,projection" /><script type="text/javascript" src="../../js/jquery-2.0.3.min.js"></script>
<script type="text/javascript" src="../../js/jquery-ui-1.10.3.custom.min.js"></script>
<script type="text/javascript" src="../../js/jquery.show_char_limit-1.2.0.js"></script>
<script type="text/javascript">
function del_news(id){
	if(!confirm("Sei sicuro di voler cancellare questo avviso"))
        return false;
	
	$.ajax({
		type: "POST",
		url: "notice_manager.php",
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
		del_news(strs[1]);
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
		Elenco avvisi
	</div>
	<div id="not1" class="notification"></div>
	<div class="list_header">
		<div style="width: 70%; float: left; position: relative; top: 30%; left: 5px">Testo</div>
		<div style="width: 30%; float: left; position: relative; top: 30%; text-align: left">Data scadenza</div>
	</div>
		<table style="width: 95%; margin: 20px auto 0 auto">
		<tbody id="t_body">
            <?php
            $x = 1;
            if($res_notices->num_rows > $limit)
                $max = $limit;
            else
                $max = $res_notices->num_rows;
            while($notice = $res_notices->fetch_assoc()){
            	if($x > $limit) break;
	            if (isset($notice['ora'])) {
                    list($data, $ora) = explode(" ", $notice['ora']);
	            }
            ?>
            <tr class="<?php echo $row_class ?>" id="row_<?php echo $notice['id'] ?>">
                <td style="padding-left: 10px; ">
                	<span class="ov_red" style="font-weight: normal"><?php print truncateString($notice['testo'], 72) ?></span>
                	<div id="link_<?php echo $notice['id'] ?>" style="display: none; margin-top: 2px">
                	<a href="notice.php?idn=<?php print $notice['id'] ?>" style="text-decoration: none; text-transform: uppercase">Modifica</a>
                	<span style="margin-left: 5px; margin-right: 5px">|</span>
                	<a href="notices_manager.php?action=2&_id=<?php if(isset($news)) echo $news['id_news'] ?>" class="del_link" style="text-decoration: none; text-transform: uppercase">Cancella</a>
                	</div>
                </td>
                <td ><?php echo $notice['data_scadenza'] ?></td>
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
                    <a href="notice.php?idn=0" id="add_news" style="text-decoration: none; text-transform: uppercase">Nuovo avviso</a>
                </td>
            </tr>
            <tr>
                <td colspan="3"></td>
            </tr>
        </tfoot>
        </table>
</div>
<p class="spacer"></p>	
</div>
<?php include "../../intranet/{$_SESSION['__mod_area__']}/footer.php" ?>
</body>
</html>
