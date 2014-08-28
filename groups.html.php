<!DOCTYPE html>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title><?php print $_SESSION['__config__']['intestazione_scuola'] ?>:: messaggi</title>
<link rel="stylesheet" href="../../css/reg.css" type="text/css" media="screen,projection" />
<link rel="stylesheet" href="../../css/general.css" type="text/css" media="screen,projection" />
<link rel="stylesheet" href="theme/style.css" type="text/css" media="screen,projection" />
<link rel="stylesheet" href="../../js/jquery_themes/custom-theme/jquery-ui-1.10.3.custom.min.css" type="text/css" media="screen,projection" />
<script type="text/javascript" src="../../js/jquery-2.0.3.min.js"></script>
<script type="text/javascript" src="../../js/jquery-ui-1.10.3.custom.min.js"></script>
<script>
	$(document).ready(function(){
		$('#newmsg_lnk').click(function(event){
			event.preventDefault();
			$('#txt').val("");
			$('#message').show(1500);
			$('#newmsg').hide(1500);
			$('#viewlist').show(1500);
			$('#sel_thread').hide(1500);
			window.setTimeout(function(){
				$('#txt').focus();
			}, 1500);
		});
		$('#viewlist_lnk').click(function(event){
			event.preventDefault();
			$('#sel_thread').show(1500);
			$('#message').hide(1500);
			$('#newmsg').show(1500);
			$('#viewlist').hide(1500);
		});
		$('#send_lnk').click(function(event){
			event.preventDefault();
			send_message();
		});
		$('#get_target').click(function(event){
			event.preventDefault();
			$('#targets').show();
		});

	});
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
		<div id="navbar">
			<div id="username">I tuoi gruppi</div>
			<div id="newmsg">
				<a href="#" id="newmsg_lnk"><img src="theme/group.png" style="margin-top: 4px" /></a>
			</div>
			<div id="viewlist">
				<a href="#" id="viewlist_lnk"><img src="theme/view-list-icon.png" style="width: 32px; height: 32px; margin-top: 4px" /></a>
			</div>
		</div>
		<div id="groups">
			<div class="outline_line_wrapper" style="margin-top: 15px">
				<div class="outline_cell wd_40" style="text-align: left">Gruppo</div>
				<div class="outline_cell wd_40" style="text-align: left">Amministratore</div>
				<div class="outline_cell wd_20" style="text-align: left">Iscritti</div>
			</div>
			<table>
				<thead>
				<tbody>
				<?php
				foreach ($threads as $thread) {
					if ($thread->getType() == 'G') {
						$owner = $thread->getOwner();
						if ($owner != null) {
							$owner_name = $owner->getFullName();
						}
						else {
							$owner_name = "Admin";
						}
				?>
				<tr>
					<td style="width: 40%" class="bold_"><a href="group.php?tid=<?php echo $thread->getTid() ?>"><?php echo $thread->getName() ?></a></td>
					<td style="width: 40%"><?php echo $owner_name ?></td>
					<td style="width: 40%"><?php echo count($thread->getUsers()) ?> iscritti</td>
				</tr>
				<?php
					}
				}
				?>
				</tbody>
			</table>
		</div>
	</div>
	<p class="spacer"></p>
	<p class="spacer"></p>
</div>
<?php include "../../intranet/{$_SESSION['__mod_area__']}/footer.php" ?>
</body>
</html>
