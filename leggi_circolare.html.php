<!DOCTYPE html>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title><?php print $_SESSION['__config__']['intestazione_scuola'] ?>:: circolare</title>
<link rel="stylesheet" href="../../intranet/teachers/reg.css" type="text/css" media="screen,projection" />
<link rel="stylesheet" href="theme/style.css" type="text/css" media="screen,projection" />
<link rel="stylesheet" href="../../js/jquery_themes/custom-theme/jquery-ui-1.10.3.custom.min.css" type="text/css" media="screen,projection" />
<script type="text/javascript" src="../../js/jquery-2.0.3.min.js"></script>
<script type="text/javascript" src="../../js/page.js"></script>
<script>
var dwl = function (id, url){
	document.location.href = url;
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
	<div style="width: 95%; height: 30px; margin: 10px auto 0 auto; text-align: center; font-size: 1.1em; text-transform: uppercase">
		Circolare n. <?php echo $circ['progressivo'] ?> del <?php echo format_date($circ['data_circolare'], SQL_DATE_STYLE, IT_DATE_STYLE, "/") ?>, prot. <?php echo $circ['protocollo'] ?>
	</div>
	<div style="width: 75%; margin: auto; padding: 15px; border: 1px solid; ">
		<div style="width: 40%; margin-left: 20px; float: left">Circolare n. <span class="bold_"><?php echo $circ['progressivo'] ?> del <?php echo format_date($circ['data_circolare'], SQL_DATE_STYLE, IT_DATE_STYLE, "/") ?></span></div>
		<div style="width: 40%; margin-right: 30px; float: right; text-align: left">Destinatari:
			<ul>
		<?php 
		$dests = preg_split("/\n/", $circ['destinatari']);
		foreach ($dests as $d){
		?>
				<li style=""><?php echo $d ?>
		<?php } ?>
			</ul>
		</div>
		<div style="width: 40%; margin-left: 20px; ">Protocollo <span class="bold_"><?php echo $circ['protocollo'] ?></span></div>
		<div style="width: 90%; margin-left: 20px; margin-top: 0; clear: right">Oggetto: <span class="bold_"><?php echo $circ['oggetto'] ?></span></div>
		<div style="width: 90%; margin-left: 20px; margin-top: 40px; line-height: 18px"><?php echo text2html($circ['testo']) ?></div>
		<div style="width: 90%; margin-left: 20px; margin-top: 40px; line-height: 18px"><?php if ($circ['allegato']): ?>Allegato: <a class="dwl" href="#" onclick="dwl(<?php echo $circ['id_allegato'] ?>, '../../modules/documents/download_manager.php?doc=allegato&id=<?php echo $circ['id_allegato'] ?>')"><?php echo $circ['allegato'] ?></a><?php endif; ?></div>
		<p class="spacer"></p>
	</div>
	
</div>
<p class="spacer"></p>
</div>
<?php include "../../intranet/{$_SESSION['__mod_area__']}/footer.php" ?>	
</body>
</html>