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
	<script type="text/javascript" src="../../js/page.js"></script>
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
				j_alert("error", "Si e' verificato un errore");
				return false;
			},
			succes: function(result) {
				j_alert("error", "ok");
			},
			complete: function(data, status){
				r = data.responseText;
				var json = $.parseJSON(r);
				if(json.status == "kosql"){
					j_alert("error", "Errore SQL. \nQuery: "+json.query+"\nErrore: "+json.message);
					return;
	            }
				else {
					$('#not1').text(json.message);
					$('#not1').show(1000);
					$('#row_'+id).hide();
					setTimeout(function(){
						$('#not1').hide(1000);
					}, 2000);
				}
			}
		});
	}

	$(function(){
		load_jalert();
		setOverlayEvent();
		$('a.del_link').click(function(event){
			event.preventDefault();
			var strs = $(this).parent().attr("id").split("_");
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
	<div id="not1" class="notification"></div>
	<div style="position: absolute; top: 75px; margin-left: 625px; margin-bottom: -5px" class="rb_button">
		<a href="news.php?id=0">
			<img src="<?php echo $_SESSION['__modules__']['com']['path_to_root'] ?>images/39.png" style="padding: 12px 0 0 12px" />
		</a>
	</div>
	<div class="card_container" style="margin-top: 20px">
    <?php
    $x = 1;
    if($res_news->num_rows > $limit)
        $max = $limit;
    else
        $max = $res_news->num_rows;
    while($news = $res_news->fetch_assoc()){
        if($x > $limit) break;
        list($data, $ora) = explode(" ", $news['ora']);
	    setlocale(LC_TIME, "it_IT.utf8");
	    $date_string = strftime("%A %d %B", strtotime($data));
    ?>
	    <div class="card" id="row_<?php echo $news['id_news'] ?>">
		    <div class="card_title accent_color">
			    <a href="news.php?idn=<?php echo $news['id_news'] ?>" class="mod_link">
				    <?php print utf8_decode(truncateString($news['abstract'], 72)) ?>
			    </a>
			    <div style="float: right; margin-right: 20px" id="del_<?php echo $news['id_news'] ?>">
				    <a href="<?php echo $_SESSION['__modules__']['com']['path_to_root'] ?>admin/adm_news/news_manager.php?action=2&_id=<?php print $news['id_news'] ?>" class="del_link">
					    <img src="../../images/51.png" style="position: relative; bottom: 2px" />
				    </a>
			    </div>
		    </div>
		    <div class="card_minicontent">
			    <div class="minicard">
				    by <?php print utf8_decode($news['nome']." ".$news['cg']) ?>
			    </div>
		    </div>
	    </div>
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
		</div>
	</div>
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
