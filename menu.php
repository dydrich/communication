<div class="smallbox" id="working">
<h2 class="menu_head">Menu</h2>
	<p class="menu_label act_icon">Comunicazioni</p>
	<ul class="menublock" style="" dir="rtl">
		<li><a href="threads.php" style="text-decoration: none">Messaggi</a></li>
		<li><a href="files.php" style="text-decoration: none">File privati</a></li>
	</ul>
	<?php if($_SESSION['__user__']->check_perms(DIR_PERM|DSG_PERM|SEG_PERM|DOC_PERM) == true): ?>
	<p class="menu_label schedule_icon">Eventi e news</p>
	<ul class="menublock" style="" dir="rtl">
		<li><a href="events.php" style="text-decoration: none">Eventi</a></li>
		<li><a href="news_list.php" style="text-decoration: none">News</a></li>
		<li><a href="notices.php" style="text-decoration: none">Avvisi</a></li>
	</ul>
	<?php endif; ?>
	<?php if($_SESSION['__user__']->check_perms(DIR_PERM|DSG_PERM|SEG_PERM|DOC_PERM) == true): ?>
	<p class="menu_label data_icon">Circolari</p>
	<ul class="menublock" style="" dir="rtl">
	<?php if($_SESSION['__user__']->check_perms(DOC_PERM) == true): ?>
		<li><a href="vedi_circolari.php" style="text-decoration: none">Leggi circolari</a></li>
	<?php endif; ?>
	<?php if($_SESSION['__user__']->check_perms(DIR_PERM|DSG_PERM|SEG_PERM) == true): ?>
		<li><a href="circolari.php" style="text-decoration: none">Circolari</a></li>
	<?php endif; ?>
	<?php endif; ?>
	</ul>
</div> 