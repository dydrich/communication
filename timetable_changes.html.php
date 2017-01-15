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

            $('.enter').timepicker({
                onClose: function(){
                    var cls = $(this).data('cls');
                    var h = $(this).val();
                    updateStudentTime(cls, <?php echo $aid ?>, h, "ingresso");
                }
			});
            $('.exit').timepicker({
                onClose: function(){
                    var cls = $(this).data('cls');
                    var h = $(this).val();
                    updateStudentTime(cls, <?php echo $aid ?>, h, "uscita");
                }
            });

            $('.delete_enter').on('click', function (event) {
                event.preventDefault();
                var cls = $(this).data('cls');
                delete_record(cls, 'ingresso');
            });
            $('.delete_exit').on('click', function (event) {
                event.preventDefault();
                var cls = $(this).data('cls');
                delete_record(cls, 'uscita');
            });
		});

        var delete_record = function(cls, field) {
            var url = "assemblies_manager.php";
            $.ajax({
                type: "POST",
                url: url,
                data: {action: "delete_time", cls: cls, field: field},
                dataType: 'json',
                error: function() {
                    j_alert("error", "Errore di trasmissione dei dati");
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
                        j_alert("error", json.message);
                        console.log(json.dbg_message);
                    }
                    else {
                        if (json.response.campo == 'ingresso') {
                            $('input.enter[data-cls="'+cls+'"]').val(json.response.valore);
                            if (json.response.valore2 != "") {
                                $('input.exit[data-cls="'+cls+'"]').val(json.response.valore2);
                            }
                        }
                        else {
                            $('input.exit[data-cls="'+cls+'"]').val(json.response.valore);
                            if (json.response.valore2 != "") {
                                $('input.enter[data-cls="'+cls+'"]').val(json.response.valore2);
                            }
                        }
                        if (json.response.delete == 1) {
                            $('tr[data-cls="'+cls+'"]').removeClass('_bold');
                            $('tr[data-cls="'+cls+'"] td:nth-child(6)').empty();
                        }
                        else {
                            $('tr[data-cls="'+cls+'"] td:nth-child(6)').empty().append("<a href='notice.php?idn="+json.response.notice+"'>Avviso</a>");
                        }
                    }
                }
            });
        };

        var updateStudentTime = function(cls, assembly, value, field) {
            var url = "assemblies_manager.php";
            $.ajax({
                type: "POST",
                url: url,
                data: {action: "change_time", cls: cls, field: field, value: value, assembly: assembly},
                dataType: 'json',
                error: function() {
                    j_alert("error", "Errore di trasmissione dei dati");
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
                        j_alert("error", json.message);
                        console.log(json.dbg_message);
                    }
                    else {
                        if (json.id != 0) {
                            $('tr[data-cls="'+cls+'"] td:nth-child(6)').empty().append("<a href='notice.php?idn="+json.notice+"'>Avviso</a>");
                            $('tr[data-cls="'+cls+'"]').addClass('_bold');
                        }
                        else {
                            $('tr[data-cls="'+cls+']"').removeClass('_bold');
                        }
                    }
                }
            });
        };
	</script>
	<style>
		tr {
			height: 25px;
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
		<form method="post" action="assemblies_manager.php" style="margin-top: 20px; text-align: left; width: 80%; margin-left: auto; margin-right: auto">
			<table style="width: 90%; margin-left: auto; margin-right: auto; margin-top: 10px; margin-bottom: 10px">
				<?php
				foreach ($variazioni as $k => $item) {
					$changed = 'none';
					if($item['ingresso'] != '' && $item['ingresso'] != $item['ingresso_previsto']) {
						$changed = 'enter';
					}
					if($item['uscita'] != '' && $item['uscita'] != $item['uscita_prevista']) {
						$changed = 'exit';
					}
					?>
					<tr class="bottom_decoration <?php if($changed != 'none') echo '_bold' ?>" data-cls="<?php echo $k ?>">
						<td style="width: 40%"><?php echo $item['classe']." (".$item['ordine'].")" ?></td>
						<td style="width: 15%">
							<input type="text" data-cls="<?php echo $k ?>" data-id="<?php echo $item['id_record'] ?>" class="enter" value="<?php echo $item['ingresso'] ?>" style="width: 50%" />
						</td>
						<td style="width: 10%">
                            <a href="#" class="delete_enter" data-cls="<?php echo $k ?>">
                                <i class="fa fa-trash accent_color"></i>
                            </a>
                        </td>
						<td style="width: 15%">
							<input type="text" data-cls="<?php echo $k ?>" data-id="<?php echo $item['id_record'] ?>" class="exit" value="<?php echo $item['uscita'] ?>" style="width: 50%" />
						</td>
						<td style="width: 10%">
                            <a href="#" class="delete_exit" data-cls="<?php echo $k ?>">
                                <i class="fa fa-trash accent_color"></i>
                            </a>
                        </td>
						<td style="width: 10%">
							<?php if($item['avviso'] != null && $item['avviso'] != ''): ?>
								<a href="notice.php?idn=<?php echo $item['avviso'] ?>">Avviso</a>
							<?php endif; ?>
						</td>
					</tr>
					<?php
				}
				?>
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
