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
<script type="text/javascript" src="../../js/page.js"></script>
<script type="text/javascript">
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
		Elenco circolari
	</div>
	<div id="not1" class="notification"></div>
	<div class="list_header">
		<div style="width: 35%; float: left; position: relative; top: 30%; left: 5px">Oggetto</div>
		<div style="width: 25%; float: left; position: relative; top: 30%; text-align: center">Data</div>
		<div style="width: 10%; float: left; position: relative; top: 30%">Numero</div>
		<div style="width: 10%; float: left; position: relative; top: 30%">Prot.</div>
		<div style="width: 20%; float: left; position: relative; top: 30%; text-align: center">Inserita da</div>
	</div>
	<table style="width: 95%; margin: 20px auto 0 auto">
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
				$sel_read = "SELECT data_lettura FROM rb_com_lettura_circolari WHERE id_circolare = {$circolare['id_circolare']} AND docente = {$_SESSION['__user__']->getUId()}";
				$read = $db->executeCount($sel_read);
 	    ?>
 	    	<tr class="<?php echo $row_class; if ($read == null) echo " bold_" ?>" id="row_<?php echo $circolare['id_circolare'] ?>">
				<td style="width: 35%; <?php print $background ?>">
					<a href="leggi_circolare.php?idc=<?php echo $circolare['id_circolare']; if ($read == null) echo "&read=1"  ?>"><?php print $circolare['oggetto'] ?></a>
				</td>
				<td style="width: 25%; text-align: center"><?php print format_date($circolare['data_circolare'], SQL_DATE_STYLE, IT_DATE_STYLE, "/") ?></td>
				<td style="width: 10%; text-align: center"><?php print $circolare['progressivo'] ?></td>
				<td style="width: 10%; text-align: center"><?php print $circolare['protocollo'] ?></td>
				<td style="width: 20%; text-align: center"><?php print $circolare['nome']." ".$circolare['cognome'] ?></td>
			</tr>
 	    <?php 
 	    		$row++;
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
