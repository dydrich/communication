<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<title><?php print $_SESSION['__config__']['intestazione_scuola'] ?>:: assemblea sindacale</title>
	<link rel="stylesheet" href="../../font-awesome/css/font-awesome.min.css">
	<link href='http://fonts.googleapis.com/css?family=Source+Sans+Pro:400,300,400italic,600,600italic,700,700italic,900,200' rel='stylesheet' type='text/css'>
	<link rel="stylesheet" href="../../css/site_themes/<?php echo getTheme() ?>/reg.css" type="text/css" media="screen,projection" />
	<link rel="stylesheet" href="../../css/general.css" type="text/css" media="screen,projection" />
	<link rel="stylesheet" href="../../css/site_themes/<?php echo getTheme() ?>/communication.css" type="text/css" media="screen,projection" />
	<link rel="stylesheet" href="../../css/site_themes/<?php echo getTheme() ?>/jquery-ui.min.css" type="text/css" media="screen,projection" /><script type="text/javascript" src="../../js/jquery-2.0.3.min.js"></script>
	<script type="text/javascript" src="../../js/jquery-ui-1.10.3.custom.min.js"></script>
	<script type="text/javascript" src="../../js/jquery-ui-timepicker-addon.js"></script>
	<script type="text/javascript" src="../../js/page.js"></script>
	<script type="text/javascript">

        $(function(){
            load_jalert();
            setOverlayEvent();
            $('#data_evento').datetimepicker({
                dateFormat: "dd/mm/yy",
                altField: "#starttime",
                altFieldTimeOnly: true,
                altTimeFormat: "HH:mm",
                currentText: "Ora",
                closeText: "Chiudi"
            });

            $('#starttime').timepicker({
                currentText: "Ora",
                closeText: "Chiudi"
            });

            $('#saver').on('click', function () {
               registra();
            });
        });


        var registra = function(){
            if($('#place').val().trim() == ""){
                j_alert("error", "Il luogo è obbligatorio.");
                return false;
            }

            $.ajax({
                type: "POST",
                url: "assemblies_manager.php",
                data: $('#my_form').serialize(),
                dataType: 'json',
                error: function(data, status, errore) {
                    j_alert("error", "Si è verificato un errore di rete");
                    return false;
                },
                succes: function(result) {

                },
                complete: function(data, status){
                    r = data.responseText;
                    var json = $.parseJSON(r);
                    if(json.status == "kosql"){
                        j_alert("error", "Errore SQL. \nQuery: "+json.query+"\nErrore: "+json.message);
                    }
                    else {
                        j_alert("alert", json.message);
                    }
                }
            });
        }

	</script>
    <style>
        tr {
            height: 26px;
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
		<div style="top: -10px; margin-left: 35px; margin-bottom: -10px" class="rb_button">
			<a href="assemblies.php">
				<img src="../../images/47bis.png" style="padding: 12px 0 0 12px" />
			</a>
		</div>
		<form method="post" action="assemblies_manager.php" style="margin-top: 20px; text-align: left; width: 60%; margin-left: auto; margin-right: auto" id="my_form">
			<table style="width: 90%; margin-left: auto; margin-right: auto; margin-top: 10px; margin-bottom: 10px">
				<tr>
					<td style="width: 20%"><label for="data_evento">Data *</label></td>
					<td style="width: 30%">
						<input type="text" name="data_evento" style="width: 110px" value="<?php if(isset($assembly)) echo format_date($assembly->getDate(), SQL_DATE_STYLE, IT_DATE_STYLE, "/"); ?>" id="data_evento" readonly="readonly" />
					</td>
					<td style="width: 20%" class="_center"><label for="starttime">Ora *</label></td>
					<td style="width: 30%">
						<input type="text" name="starttime" style="width: 110px" value="<?php if(isset($assembly)) {$st = $assembly->getStartTime(); echo $st->toString(RBTime::$RBTIME_SHORT); } ?>" id="starttime" readonly="readonly" />
					</td>
				</tr>
                <tr>
                    <td style="width: 20%"><label for="place">Luogo *</label></td>
                    <td colspan="3">
                        <input type="text" name="place" id="place" value="<?php if(isset($assembly)) echo $assembly->getWhere() ?>" style="width: 100%" />
                    </td>
                </tr>
                <tr>
                    <td style="width: 20%"><label for="associations">Sigle sindacali</label></td>
                    <td colspan="3">
                        <input type="text" name="associations" id="associations" value="<?php if(isset($assembly)) echo $assembly->getUnions() ?>" style="width: 100%" />
                    </td>
                </tr>
                <tr>
                    <td colspan="4" class="_right" style="margin-right: 10%; padding-top: 20px">
                        <a href="#" id="saver">Registra</a>
                        <input type="hidden" name="action" id="action" value="<?php if($_REQUEST['id'] == 0) echo "insert"; else echo "update" ?>">
                    </td>
                </tr>
			</table>
		</form>
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
