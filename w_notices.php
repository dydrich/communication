<?php

$sel_ev_docs = "SELECT COUNT(id) FROM rb_documents WHERE evidenziato IS NOT NULL AND evidenziato > NOW()";
$res_ev = $db->executeCount($sel_ev_docs);

$sel_notices = "SELECT * FROM rb_com_avvisi WHERE data_scadenza >= NOW()";
$res_notices = $db->executeQuery($sel_notices);

if ($res_ev > 0 || $res_notices->num_rows > 0){
?>
	<div class="welcome">
		<p id="w_head">Avviso <?php echo date("d/m/Y") ?></p>
		<?php 
		if ($res_notices->num_rows > 0){ 
			while ($notice = $res_notices->fetch_assoc()){
		?>
		<p class="w_text attention" style="font-weight: bold">&middot; <?php echo $notice['testo'] ?></p>
		<?php 
			}
		}
		?>
		<?php if ($res_ev > 0): ?>
		<p class="w_text attention" style="">&middot; Sono presenti dei documenti segnalati come importanti &middot;</p>
		<?php endif; ?>
	</div>
<?php 
}
?>
