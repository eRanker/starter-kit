<?php

$page_title = "";
$page_desc = "SEO Report";

$report_id = (isset($_REQUEST['id']) ? (int) $_REQUEST['id'] : '');
if (empty($report_id)) {
    header('Location: /404');
    exit;
}

$report = $erapi->report($report_id);
if (empty($report)) {
    header('Location: /404');
    exit;
}
if (isset($seocheck_reportobj->msg)) {
    $seocheck_error = TRUE;
    $seocheck_error_msg = $report->msg . '<br/>' . $report->solution;
}
if (isset($seocheck_reportobj->url)) {
    $page_desc .=" - " . $seocheck_reportobj->url;
}

$report_scores = $erapi->reportscores($report_id, 'en');
if (empty($report_scores)) {
    header('Location: /404');
    exit;
}
if (isset($report_scores->msg)) {
    $seocheck_error = TRUE;
    $seocheck_error_msg = $report_scores->msg . '<br/>' . $report_scores->solution;
}

//AJAX requests
if (isset($_GET['ajax']) && !empty($_GET['ajax']) && isset($_GET['factors']) && !empty($_GET['factors'])) {
    $ajaxObj = eRankerCommons::ajaxReport($report, $erapi_allfactors, $report_scores, $_GET['factors'], true);
    header('Content-Type: application/json');
    echo json_encode($ajaxObj, JSON_PRETTY_PRINT);
    exit;
}