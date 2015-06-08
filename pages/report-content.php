<?php
if ($seocheck_error){
    include '404-content.php';
    return;
}
echo eRankerCommons::getReportHTML($report, $report_scores, $erapi_allfactors, true, false, true);

