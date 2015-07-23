<?php
//INCLUDES
$parse_uri = explode('content', __FILE__);
require_once( $parse_uri[0] . 'wp-load.php' );


if (!isset($_GET['uid']) || empty($_GET['uid'])) {
    die();
}
$theuser = $wpdb->get_row($wpdb->prepare("SELECT * FROM eranker_users WHERE  id = %d", $_GET['uid']));
if (!empty($theuser)) {
    $usersettings = $wpdb->get_row($wpdb->prepare("SELECT * FROM eranker_usersettings WHERE  user_id = %d", $theuser->ID));
    if (!empty($usersettings)) {
        $planUser = $wpdb->get_row($wpdb->prepare("SELECT * FROM eranker_plan WHERE  id = %d", $usersettings->plan_id));
    }
}


if (empty($theuser) || empty($planUser->is_whitelabel_available)) {
    $logo = 'http://www.eranker.com/content/themes/eranker/img/logo-blue.png';
    $h = 'eRanker - Check how to rank your website better';
    $sh = '<a href="http://www.eranker.com">www.eRanker.com</a>';
} else {

    if (!empty($usersettings) && isset($usersettings->wl_userlogo) && !empty($usersettings->wl_userlogo)) {
        $logo = $_e->theme_path . "/uploads/" . $usersettings->wl_userlogo;
    } else {
        $logo = '';
    }
    $h = $usersettings->wl_heading;
    $sh = $usersettings->wl_subheading;
}
?><!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
    </head>
    <body>
        <div  style="margin-bottom: 20px;">
            <?php if (!empty($logo)) { ?>
                <img src="<?PHP echo $logo ?>" alt="" style="float:left; max-height: 1.0cm" />
            <?php } ?>
            <div style="font: 400 13px 'Open Sans',HelveticaNeue,Helvetica,Arial; float: right; color: #333;text-align: right;"> 
                <strong><?PHP echo $h ?></strong>
                <br />
                <font size="-1"><?PHP echo $sh ?></font>
            </div>
        </div>
    </body>
</html>