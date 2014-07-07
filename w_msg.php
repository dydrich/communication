		<div id="welcome">
			<p id="w_head">Messaggi e file</p>
			<p class="w_text">
			<?php
			$user_type = $_SESSION['user_type'];
			$uid = $_SESSION['__user__']->getUid();
			$sel_msg = "SELECT COUNT(mid) FROM rb_com_messages, rb_com_threads WHERE rb_com_messages.tid = rb_com_threads.tid AND target = {$_SESSION['__user__']->getUid()} && ((user1 = {$uid} AND user1_group = '{$user_type}') OR (user2 = {$uid} AND user2_group = '{$user_type}')) AND read_timestamp IS NULL";
			$unread = $db->executeCount($sel_msg);
			if($unread < 1) {
				echo "<span>Nessun nuovo messagggio</span>";
			}
			else {				
			?>
				<a href="<?php echo $_SESSION['__path_to_root__'] ?>modules/communication/load_module.php?module=com&area=teachers&page=threads">Ci sono <?php echo $unread ?> nuovi messaggi</a><br />
			<?php 
			}
			?>
			<br />
			<?php 
			$sel_files = "SELECT COUNT(id) FROM rb_com_files WHERE destinatario = {$_SESSION['__user__']->getUid()} AND data_download IS NULL";
			$not_downl = $db->executeCount($sel_files);
			if($not_downl < 1) {
				echo "<span>Nessun nuovo file</span>";
			}
			else {				
			?>
				<a href="<?php echo $_SESSION['__path_to_root__'] ?>modules/communication/load_module.php?module=com&area=teachers&page=files">Ci sono <?php echo $not_downl ?> nuovi file</a><br />
			<?php 
			}
			?>
			</p>
		</div>