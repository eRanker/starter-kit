<?php

//INCLUDES
$parse_uri = explode('content', __FILE__);
require_once( $parse_uri[0] . 'wp-load.php' );

global $geodb;

if (isset($_GET['rid']) && !empty($_GET['rid'])) {
$report = $wpdb->get_row($wpdb->prepare("SELECT * FROM eranker_report WHERE  id = %d", $_GET['rid']));
    if(empty($report)){
        exit;
    }
} else {
    exit;
}
?><!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
    </head>
    <body>
        <div style="font: 300 12px 'Open Sans',HelveticaNeue,Helvetica,Arial; float: right; color: #333;">Generated at <?PHP echo $report->date_generated?> </div>
    </body>
</html>