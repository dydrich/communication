<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<title><?php print $_SESSION['__config__']['intestazione_scuola'] ?>:: avviso del DS</title>
	<link rel="stylesheet" href="../../font-awesome/css/font-awesome.min.css">
	<link href='http://fonts.googleapis.com/css?family=Source+Sans+Pro:400,300,400italic,600,600italic,700,700italic,900,200' rel='stylesheet' type='text/css'>
	<link rel="stylesheet" href="../../css/site_themes/<?php echo getTheme() ?>/reg.css" type="text/css" media="screen,projection" />
	<link rel="stylesheet" href="../../css/general.css" type="text/css" media="screen,projection" />
	<link rel="stylesheet" href="../../css/site_themes/<?php echo getTheme() ?>/communication.css" type="text/css" media="screen,projection" />
	<link rel="stylesheet" href="../../css/site_themes/<?php echo getTheme() ?>/jquery-ui.min.css" type="text/css" media="screen,projection" />
	<script type="text/javascript" src="../../js/jquery-2.0.3.min.js"></script>
	<script type="text/javascript" src="../../js/jquery-ui-1.10.3.custom.min.js"></script>
	<script type="text/javascript" src="../../js/jquery-ui-timepicker-addon.js"></script>
	<script type="text/javascript" src="../../js/page.js"></script>
	<script type="text/javascript">
	$(function(){
		load_jalert();
		setOverlayEvent();
		$('#data').datepicker({
			dateFormat: "dd/mm/yy",
			currentText: "Oggi",
			closeText: "Chiudi"
		});

		$('#zone').on('change', function(event) {
		   if($('#zone').val() == 2 || $('#zone').val() == 8) {
		       $('#level_row').show(500);
		       $('#classes_row').show(500);
            }
           else {
		       $('#level_row').hide(500);
               $('#classes_row').hide(500);
            }
        });

		$('#tipo').on('change', function(event) {
             if($(this).val() == 2) {
                 $('#zone').val(8).trigger('change');
                 $('#level_row').hide();
                 $('#classes_row').hide();
                 $('#label_date').text('Data sciopero');
                 $('#testo').text("Si informano i sig.ri genitori che per ----- è previsto uno sciopero generale per l’intera giornata di tutto " +
                     "il personale della scuola. In conseguenza di ciò, si preavvisano i sig.ri utenti che l’erogazione del servizio scolastico " +
                     "potrà subire modifiche, ritardi e inconvenienti causati dall’astensione dei lavoratori. Pertanto, i sig.ri genitori tutti " +
                     "sono invitati ad accompagnare i propri figli per la verifica della situazione.");
             }
             else {
                 $('#zone').val(0).trigger('change');
                 $('#label_date').text('Data scadenza');
                 $('#testo').empty();
             }
        });

        $('#level').on('change', function(event) {
            get_classes($('#level').val());
        });
	});

	 var registra = function(){
		if(trim(document.forms[0].data.value) == ""){
			j_alert("error", "La data di scadenza è obbligatoria.");
			return false;
		}
		else if(trim(document.forms[0].testo.value) == ""){
			j_alert("error", "Il testo è obbligatorio");
			return false;
		}

		$.ajax({
			type: "POST",
			url: "notice_manager.php",
			data: $('#my_form').serialize(true),
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
	};

    var get_classes = function (level) {
        $.ajax({
            type: "POST",
            url: "../../shared/get_classes.php",
            data: {school_level: level},
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
                    var listitems = '';
                    listitems += "<option value='0'>Tutte</option>";
                    var classi = json.data;
                    $.each(classi, function (index, val) {
                        listitems += "<option value='"+val.id+"'>"+val.classe+"</option>";
                    });
                    $('#classes').empty().append(listitems);
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
	<div id="not1" class="notification"></div>
 	<form id="my_form" method="post" action="notice_manager.php" style="margin-top: 20px; text-align: left; width: 560px; margin-left: auto; margin-right: auto">
	<table style="width: 500px; margin-left: auto; margin-right: auto; margin-top: 30px; margin-bottom: 20px">
        <tr id="types_row">
            <td style="width: 30%">Tipo avviso</td>
            <td style="width: 70%">
                <select name="tipo" id="tipo" style="width: 350px" <?php if(isset($notice)) echo "readonly" ?>>
                    <?php
					foreach ($tipo_avviso as $sl){
						?>
                        <option <?php if(isset($notice) && $sl['id_tipo'] == $notice['tipo']) echo "selected" ?> value="<?php echo $sl['id_tipo'] ?>"><?php echo $sl['tipo'] ?></option>
						<?php
					}
					?>
                </select>
            </td>
        </tr>
        <tr>
            <td colspan="2" style="min-height: 20px;">&nbsp;</td>
        </tr>
        <tr>
            <td style="width: 30%">Area</td>
            <td style="width: 70%">
                <select name="zone" id="zone" style="width: 350px">
                    <option value="0">Tutte</option>
                    <option <?php if(isset($notice) && 2 == $notice['gruppi']) echo "selected" ?> value="2">Docenti</option>
                    <option <?php if(isset($notice) && 4 == $notice['gruppi']) echo "selected" ?> value="4">ATA</option>
                    <option <?php if(isset($notice) && 8 == $notice['gruppi']) echo "selected" ?> value="8">Genitori</option>
                </select>
            </td>
        </tr>
        <tr style="<?php if(!isset($notice) || $notice['ordine_di_scuola'] == "") echo "display: none" ?>" id="level_row">
            <td style="width: 30%">Ordine di scuola</td>
            <td style="width: 70%">
                <select name="level" id="level" style="width: 350px">
                    <option value="0">Tutti</option>
                    <?php
					foreach ($_SESSION['__school_level__'] as $k => $sl){
					?>
                    <option <?php if(isset($notice) && $k == $notice['ordine_di_scuola']) echo "selected" ?> value="<?php echo $k ?>"><?php echo $sl ?></option>
                    <?php
                    }
                    ?>
                </select>
            </td>
        </tr>
        <tr style="<?php if(!isset($notice) || $notice['classe'] == "") echo "display: none" ?>" id="classes_row">
            <td style="width: 30%">Classi</td>
            <td style="width: 70%">
                <select name="classes[]" id="classes" style="width: 350px" multiple>
                    <option value="0">Tutte</option>
					<?php
					foreach ($classi as $k => $sl){
					    $cls = null;
					    if(isset($notice) && $notice['classe'] != "") {
					        $cls = explode(",", $notice['classe']);
                        }
						?>
                        <option value="<?php echo $k ?>" <?php if($cls != null && in_array($k, $cls)) echo "selected" ?>><?php echo $sl['classe']."-".$sl['sede'] ?></option>
						<?php
					}
					?>
                </select>
            </td>
        </tr>
        <tr>
            <td colspan="2" style="min-height: 20px;">&nbsp;</td>
        </tr>
		<tr>
			<td style="width: 30%" id="label_date">Data scadenza</td>
			<td style="width: 70%">
				<input type="text" name="data" id="data" style="width: 350px" <?php if(isset($notice) && $notice['tipo'] == 2) echo "disabled" ?> value="<?php if(isset($notice)) echo format_date($notice['data_scadenza'], SQL_DATE_STYLE, IT_DATE_STYLE, "/") ?>" />
			</td> 
		</tr>
		<tr>
			<td style="width: 30%">Testo</td>
			<td style="width: 70%">
				<textarea name="testo" id="testo" style="width: 350px; height: 100px"><?php if(isset($notice)) echo $notice['testo'] ?></textarea>
			</td> 
		</tr>
		<tr>
			<td colspan="2">&nbsp;
				<input type="hidden" name="action" id="action" value="<?php echo $action ?>" />
    			<input type="hidden" name="_i" id="_i" value="<?php echo $idnotice ?>" />
			</td> 
		</tr>
		<tr>
			<td colspan="2" style="text-align: right; margin-right: 50px">
				<a href="#" onclick="registra()" style="text-decoration: none; text-transform: uppercase">Registra</a>
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
