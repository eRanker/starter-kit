<?php

if ($seocheck_error) {
    include '404-content.php';
    return;
}

$show_header = TRUE;
$show_title = TRUE;
$show_category = TRUE;

echo eRankerCommons::getReportHTML($report, $report_scores, $erapi_allfactors, true, false, true, $show_header, $show_title, $show_category);

