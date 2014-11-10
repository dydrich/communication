<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<title><?php print $_SESSION['__config__']['intestazione_scuola'] ?>:: messaggi</title>
	<link rel="stylesheet" href="../../css/site_themes/<?php echo getTheme() ?>/reg.css" type="text/css" media="screen,projection" />
	<link rel="stylesheet" href="../../css/general.css" type="text/css" media="screen,projection" />
	<link rel="stylesheet" href="../../css/site_themes/<?php echo getTheme() ?>/communication.css" type="text/css" media="screen,projection" />
	<link rel="stylesheet" href="../../css/site_themes/<?php echo getTheme() ?>/jquery-ui.min.css" type="text/css" media="screen,projection" />	<script type="text/javascript" src="../../js/jquery-2.0.3.min.js"></script>
	<script type="text/javascript" src="../../js/jquery-ui-1.10.3.custom.min.js"></script>
	<script type="text/javascript" src="../../js/jquery.show_char_limit-1.2.0.js"></script>
	<script>
		$(function(){
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
			$('.exit_link').click(function(event){
				data = this.id.split("_");
				leave_group(data[1]);
			});
		});

		var leave_group = function(group){
			if (!confirm("Sei sicuro di voler abbandonare questo gruppo?")) {
				return false;
			}
			action = "leave";
			$.ajax({
				type: "POST",
				url: "controller.php",
				data: {do: action, tid: group},
				dataType: 'json',
				error: function() {

				},
				succes: function() {

				},
				complete: function(data){
					r = data.responseText;
					if(r == ""){
						return false;
					}
					var json = $.parseJSON(r);
					if (json.status == "ok") {
						document.location.href = "groups.php";
					}
				}
			});
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
		<div id="navbar">
			<div id="username"><?php echo $thread->getTargetName($uniqID) ?></div>
		</div>
		<div id="threads">
			<div class="thread">
				<div class="thread_header">
					<div class="thread_user"><?php echo $thread->getTargetName($_SESSION['__user__']->getUniqID()) ?></div>
					<div class="thread_msg_count"><a id="del_<?php echo $thread->getTid() ?>" class="exit_link" href="#">Cancellati</a></div>
				</div>
				<div class="thread_text">
					<?php
					$rb = RBUtilities::getInstance($db);
					$us_array = array();
					foreach ($thread->getUsers() as $user) {
						$ud = $rb->loadUserFromUniqID($user);
						$us_array[$user] = $ud->getFullName();
					}
					echo implode(", ", $us_array);
					?>
				</div>
			</div>
		</div>
	</div>
	<p class="spacer"></p>
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
			<div class="drawer_link"><a href="<?php echo $_SESSION['__modules__']['com']['path_to_root'] ?>modules/communication/load_module.php?module=com&area=teachers"><img src="<?php echo $_SESSION['__modules__']['com']['path_to_root'] ?>images/57.png" style="margin-right: 10px; position: relative; top: 5%" />Comunicazioni</a></div>
		<?php } ?>
	</div>
	<?php if (isset($_SESSION['__sudoer__'])): ?>
		<div class="drawer_lastlink"><a href="<?php echo $_SESSION['__modules__']['com']['path_to_root'] ?>intranet/<?php echo $_SESSION['__mod_area__'] ?>/admin/sudo_manager.php?action=back"><img src="<?php echo $_SESSION['__modules__']['com']['path_to_root'] ?>images/14.png" style="margin-right: 10px; position: relative; top: 5%" />DeSuDo</a></div>
	<?php endif; ?>
	<div class="drawer_lastlink"><a href="<?php echo $_SESSION['__modules__']['com']['path_to_root'] ?>shared/do_logout.php"><img src="<?php echo $_SESSION['__modules__']['com']['path_to_root'] ?>images/51.png" style="margin-right: 10px; position: relative; top: 5%" />Logout</a></div>
</div>
</body>
</html>
