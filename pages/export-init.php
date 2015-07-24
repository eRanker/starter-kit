<?php

global $erapi;

require_once (dirname(dirname(__FILE__)) . '/libs/other/wkhtmltopdf/wkhtmltopdf.php');


if (isset($_GET['type']) && !empty($_GET['type'])) {
    $type = trim($_GET['type']);
} else {
    header("Location: /404");
    exit;
}

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = trim($_GET['id']);
} else {
    header("Location: /404");
    exit;
}

$report = $erapi->report($_GET['id']);

if (empty($report)) {
    header('Location: /404');
    exit;
}

$user = !empty($current_user) ? $current_user->ID : '';


switch ($type) {
    case 'pdf':
        $reporthtml = file_get_contents('http://127.0.0.1/index.php?p=report&id=' . $report->id . '&pdf=1');
        $reportheader = file_get_contents(dirname(dirname(__FILE__)) . '/libs/report-pdf-header.php');
        $reportfooter = file_get_contents(dirname(dirname(__FILE__)) . '/libs/report-pdf-footer.php');   
        $pdf = new WKPDF();
        $pdf->set_html($reporthtml);
        $pdf->set_header($reportheader);
        $pdf->set_footer($reportfooter);
        $pdf->set_toc(false);
        $pdf->set_zoom(0.75);
        $pdf->set_page_size("A4");
        $pdf->render();
        $pdf->output(WKPDF::$PDF_DOWNLOAD, 'report-' . $report->domain . '.pdf');
        break;
    default:
        header("Location: /404");
        exit;
}
exit;
