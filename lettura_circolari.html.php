<!DOCTYPE html>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title><?php print $_SESSION['__config__']['intestazione_scuola'] ?>:: circolari</title>
<link rel="stylesheet" href="../../intranet/teachers/reg.css" type="text/css" media="screen,projection" />
<link rel="stylesheet" href="theme/style.css" type="text/css" media="screen,projection" />
<link rel="stylesheet" href="../../js/jquery_themes/custom-theme/jquery-ui-1.10.3.custom.min.css" type="text/css" media="screen,projection" />
<script type="text/javascript" src="../../js/jquery-2.0.3.min.js"></script>
<script type="text/javascript" src="../../js/page.js"></script>
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
	<div style="width: 95%; height: 30px; margin: 10px auto 0 auto; text-align: center; font-size: 1.1em; text-transform: uppercase">
		Letture circolare n. <?php echo $circ['progressivo'] ?> del <?php echo format_date($circ['data_circolare'], SQL_DATE_STYLE, IT_DATE_STYLE, "/") ?>, prot. <?php echo $circ['protocollo'] ?>
	</div>
	<div style="width: 95%; margin: auto; height: 35px; text-align: center; text-transform: uppercase; font-weight: bold; border: 1px solid rgb(211, 222, 199); outline-style: double; outline-color: rgb(211, 222, 199); background-color: rgba(211, 222, 199, 0.7)">
		<div style="width: 50%; float: left; position: relative; top: 30%">Docente</div>
		<div style="width: 15%; float: left; position: relative; top: 30%">Letta</div>
		<div style="width: 35%; float: left; position: relative; top: 30%">In data</div>
	</div>
	<table style="width: 95%; margin: 20px auto 0 auto">
	<tbody>
	<?php
	$x = 1;
	if($res_read->num_rows > $limit)
		$max = $limit;
	else
		$max = $res_read->num_rows;
	$row = 0;
	while ($read = $res_read->fetch_assoc()){
		if($x > $limit) break;
	?>
		<tr style="border-bottom: 1px solid rgb(211, 222, 199)">
			<td style="width: 50%"><?php echo $read['cognome']." ".$read['nome'] ?></td>
			<td style="width: 15%; text-align: center"><?php if ($read['letta'] == 1) echo "SI" ; else echo "<span class='attention' style='font-weight: bold'>NO</span>" ?></td>
			<td style="width: 35%; text-align: center"><?php if ($read['letta'] == 1) echo $read['data_lettura'] ?></td>
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
 	</tfoot>
	</table>	
	</div>
<p class="spacer"></p>
</div>
<?php include "../../intranet/{$_SESSION['__mod_area__']}/footer.php" ?>	
</body>
</html>