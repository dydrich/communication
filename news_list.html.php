<!DOCTYPE html>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title><?php print $_SESSION['__config__']['intestazione_scuola'] ?>:: eventi</title>
<link rel="stylesheet" href="../../intranet/teachers/reg.css" type="text/css" media="screen,projection" />
<link rel="stylesheet" href="theme/style.css" type="text/css" media="screen,projection" />
<link rel="stylesheet" href="../../js/jquery_themes/custom-theme/jquery-ui-1.10.3.custom.min.css" type="text/css" media="screen,projection" />
<script type="text/javascript" src="../../js/jquery-2.0.3.min.js"></script>
<script type="text/javascript" src="../../js/jquery-ui-1.10.3.custom.min.js"></script>
<script type="text/javascript" src="../../js/jquery.show_char_limit-1.2.0.js"></script>
<script type="text/javascript">
function del_news(id){
	if(!confirm("Sei sicuro di voler cancellare questa news?"))
        return false;
	
	$.ajax({
		type: "POST",
		url: "news_manager.php",
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
	<div style="width: 95%; height: 30px; margin: 10px auto 0 auto; text-align: center; font-size: 1.1em; text-transform: uppercase">
		Elenco news
	</div>
	<div id="not1" class="notification"></div>
	<div style="width: 95%; margin: auto; height: 28px; text-align: center; text-transform: uppercase; font-weight: bold; border: 1px solid rgb(211, 222, 199); outline-style: double; outline-color: rgb(211, 222, 199); background-color: rgba(211, 222, 199, 0.7)">
		<div style="width: 65%; float: left; position: relative; top: 30%">Titolo</div>
		<div style="width: 15%; float: left; position: relative; top: 30%; text-align: left">Utente</div>
		<div style="width: 20%; float: left; position: relative">Data e ora Inserimento</div>
	</div>
		<table style="width: 95%; margin: 20px auto 0 auto">
		<tbody id="t_body">
            <?php
            $x = 1;
            if($res_news->num_rows > $limit)
                $max = $limit;
            else
                $max = $res_news->num_rows;
            while($news = $res_news->fetch_assoc()){
            	if($x > $limit) break;
                list($data, $ora) = split(" ", $news['ora']);
            ?>
            <tr class="<?php echo $row_class ?>" id="row_<?php echo $news['id_news'] ?>">
                <td style="padding-left: 10px; ">
                	<span class="ov_red" style="font-weight: normal"><?php print utf8_decode(truncateString($news['abstract'], 72)) ?></span>
                	<div id="link_<?php echo $news['id_news'] ?>" style="display: none; margin-top: 2px">
                	<a href="news.php?idn=<?php print $news['id_news'] ?>" style="text-decoration: none; text-transform: uppercase">Modifica</a>
                	<span style="margin-left: 5px; margin-right: 5px">|</span>
                	<a href="../../admin/adm_news/news_manager.php?action=2&_id=<?php print $news['id_news'] ?>" class="del_link" style="text-decoration: none; text-transform: uppercase">Cancella</a>
                	</div>
                </td>
                <td ><?php print utf8_decode($news['nome']." ".$news['cg']) ?></td>
                <td style="text-align: center"><?php print format_date($data, SQL_DATE_STYLE, IT_DATE_STYLE, "/")." ".$ora ?></td>
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
                    <a href="news.php?idn=0" id="add_news" style="text-decoration: none; text-transform: uppercase">Nuova news</a>
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