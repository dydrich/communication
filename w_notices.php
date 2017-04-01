<?php

$groups = 2;
if ($_SESSION['__area__'] == 'ata') {
	$groups = 4;
}
else if ($_SESSION['__area__'] == 'parents'){
    $groups = 8;
}

$sel_notices = "SELECT * FROM rb_com_avvisi WHERE data_scadenza >= NOW() AND gruppi = {$groups}";
$res_notices = $db->executeQuery($sel_notices);

if ($res_notices->num_rows > 0){
?>
	<div class="welcome">
		<p id="w_head" class="attention">Importante <?php echo date("d/m/Y") ?></p>
		<?php 
		if ($res_notices->num_rows > 0){ 
			while ($notice = $res_notices->fetch_assoc()){
			    $show = true;
			    if($notice['ordine_di_scuola'] != "") {
			        $so =  $_SESSION['__user__']->getSchoolOrder();
			        if(is_array($so)) {
			            if(!in_array($notice['ordine_di_scuola'], $so)) {
			                //$show = false;
                            continue;
                        }
                    }
                    else {
						if($notice['ordine_di_scuola'] != $so) {
							//$show = false;
                            continue;
                        }
                    }
                }
				$cls = null;
				if(isset($notice) && $notice['classe'] != "" && $notice['classe'] != 0) {
				    $cls = explode(",", $notice['classe']);
					$_cl = $_SESSION['__user__']->getClasses();
					$cl = array_keys($_cl);
					$intersect = array_intersect($cl, $cls);
					if(count($intersect) == 0) {
					    //$show = false;
                        continue;
                    }
				}
		?>
		<p class="w_text attention" style="font-weight: bold">&middot; <?php echo $notice['testo'] ?></p>
		<?php 
			}
		}
		?>
	</div>
<?php 
}
?>
