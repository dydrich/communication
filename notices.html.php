<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<title><?php print $_SESSION['__config__']['intestazione_scuola'] ?>:: avvisi del DS</title>
    <link rel="stylesheet" href="../../font-awesome/css/font-awesome.min.css">
	<link href='http://fonts.googleapis.com/css?family=Source+Sans+Pro:400,300,400italic,600,600italic,700,700italic,900,200' rel='stylesheet' type='text/css'>
	<link rel="stylesheet" href="../../css/site_themes/<?php echo getTheme() ?>/reg.css" type="text/css" media="screen,projection" />
	<link rel="stylesheet" href="../../css/general.css" type="text/css" media="screen,projection" />
	<link rel="stylesheet" href="../../css/site_themes/<?php echo getTheme() ?>/communication.css" type="text/css" media="screen,projection" />
	<link rel="stylesheet" href="../../css/site_themes/<?php echo getTheme() ?>/jquery-ui.min.css" type="text/css" media="screen,projection" /><script type="text/javascript" src="../../js/jquery-2.0.3.min.js"></script>
	<script type="text/javascript" src="../../js/jquery-ui-1.10.3.custom.min.js"></script>
	<script type="text/javascript" src="../../js/jquery.show_char_limit-1.2.0.js"></script>
	<script type="text/javascript" src="../../js/page.js"></script>
	<script type="text/javascript">
        var id = 0;
        var del_news = function(){
            $('#confirm').fadeOut(10);
            $.ajax({
                type: "POST",
                url: "notice_manager.php",
                data: {action: 2, _i: id},
                dataType: 'json',
                error: function(data, status, errore) {
                    j_alert("error", "Si è verificato un errore");
                    return false;
                },
                succes: function(result) {
                    j_alert("alert", "ok");
                },
                complete: function(data, status){
                    r = data.responseText;
                    var json = $.parseJSON(r);
                    if(json.status == "kosql"){
                        j_alert("error", "Errore SQL. \nQuery: "+json.query+"\nErrore: "+json.message);
                        return;
                    }
                    else {
                        j_alert("alert", json.message);
                        window.setTimeout(function() {
                            $('#row_'+id).hide();
                        }, 2000);
                    }
                }
            });
        }

        $(function(){
            load_jalert();
            setOverlayEvent();
            $('a.del_link').click(function(event){
                event.preventDefault();
                var strs = $(this).parent().attr("id").split("_");
                id = strs[1];
                j_alert("confirm", "Eliminare questo avviso?");
            });

            $('a.mod_link').on('click', function (event) {
                event.preventDefault();
                var idn = $(this).data('id');
                var tipo = $(this).data('type');
                if(tipo == 2) {
                    j_alert("error", "impossibile modificare questo tipo di avvisi");
                }
                else {
                    document.location.href = "notice.php?idn="+idn;
                }
            });

            $('#okbutton').on('click', function (event) {
                event.preventDefault();
                del_news();
            });

            $('#top_btn').click(function() {
                $('html,body').animate({
                    scrollTop: 0
                }, 700);
                return false;
            });

            var amountScrolled = 200;

            $(window).scroll(function() {
                if ($(window).scrollTop() > amountScrolled) {
                    $('#plus_btn').fadeOut('slow');
                    $('#float_btn').fadeIn('slow');
                    $('#top_btn').fadeIn('slow');
                } else {
                    $('#float_btn').fadeOut('slow');
                    $('#plus_btn').fadeIn();
                    $('#top_btn').fadeOut('slow');
                }
            });
        });

        var change_zone = function(zone){
            document.location.href="notices.php?zone="+zone;
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
    <?php if(!isset($_SESSION['__role__']) || $_SESSION['__role__'] != 'DSGA'): ?>
    <div class="mdtabs" style="top: -20px">
        <div class="mdtab<?php if ($zone == 2) echo " mdselected_tab" ?>">
            <a href="#" onclick="change_zone(2)"><span>Docenti</span></a>
        </div>
        <div class="mdtab<?php if ($zone == 8) echo " mdselected_tab" ?>">
            <a href="#" onclick="change_zone(8)"><span>Genitori</span></a>
        </div>
        <div class="mdtab<?php if ($zone == 4) echo " mdselected_tab" ?>">
            <a href="#" onclick="change_zone(4)"><span>ATA</span></a>
        </div>
        <div class="mdtab<?php if ($zone == 0) echo " mdselected_tab" ?>">
            <a href="#" onclick="change_zone(0)"><span>Tutti</span></a>
        </div>
    </div>
    <?php endif; ?>
	<div style="position: absolute; top: <?php if(!isset($_SESSION['__role__']) || $_SESSION['__role__'] != 'DSGA') echo '90'; else echo '75'; ?>px; margin-left: 625px; margin-bottom: -5px" class="rb_button">
		<a href="notice.php?idn=0">
			<img src="<?php echo $_SESSION['__modules__']['com']['path_to_root'] ?>images/39.png" style="padding: 12px 0 0 12px" />
		</a>
	</div>
	<div class="card_container" style="margin-top: 20px">
    <?php
    while($notice = $res_notices->fetch_assoc()){
	    $today = date("Y-m-d");
	    setlocale(LC_TIME, "it_IT.utf8");
	    $date_string = strftime("%A %d %B %Y", strtotime($notice['data_scadenza']));
	    if ($notice['data_scadenza'] < $today) {
		    $string_due = "Scaduto ".$date_string;
	    }
	    else if ($today == $notice['data_scadenza']) {
		    $string_due = "In scadenza oggi";
	    }
	    else {
		    $string_due = "Scade ".$date_string;
	    }
    ?>
	    <div class="card" id="row_<?php echo $notice['id'] ?>">
		    <div class="card_title">
			    <a href="notice.php" data-id="<?php print $notice['id'] ?>" data-type="<?php echo $notice['tipo'] ?>" class="mod_link" style="<?php if($notice['tipo'] == 2) echo "color: #9E9E9E !important;" ?>">
				    <?php print truncateString($notice['testo'], 72) ?>
			    </a>
			    <div style="float: right; margin-right: 20px" id="del_<?php print $notice['id'] ?>">
				    <a href="notices_manager.php?action=2&_id=<?php if(isset($notice)) echo $notice['id'] ?>" class="del_link">
					    <img src="../../images/51.png" style="position: relative; bottom: 2px" />
				    </a>
			    </div>
		    </div>
		    <div class="card_minicontent">
			    <div class="minicard">
				    <?php echo $string_due ?>
			    </div>
		    </div>
	    </div>
        <?php
        }
        ?>
		</div>
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
<a href="notice.php?idn=0" id="float_btn" class="rb_button float_button">
    <i class="fa fa-pencil"></i>
</a>
<a href="#" id="top_btn" class="rb_button float_button top_button">
    <i class="fa fa-arrow-up"></i>
</a>
</body>
</html>
