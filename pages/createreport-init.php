<?php

//Overwrite some variables (title and meta description)
$page_title = "Create Report - eRanker API Starter Kit";
$page_desc = "Create a new Report on eRanker - eRanker API Starter Kit";

//init the error variable
$seocheck_error = FALSE;
$seocheck_error_msg = '';

if (isset($_REQUEST['sc_url'])) {

    $seocheck_newreporteranker_url = isset($_REQUEST['sc_url']) ? $_REQUEST['sc_url'] : "";
    $checkbox_factors = isset($_REQUEST['factorsGroup']) && !empty($_REQUEST['factorsGroup']) ? $_REQUEST['factorsGroup'] : array();
    if (empty($checkbox_factors)) {
        $checkbox_factors = $seocheck_accountinfo->plan->default_factors;
    }

    //Check URL
    if (isset($seocheck_newreporteranker_url)) {
        if (strlen($seocheck_newreporteranker_url) < 5 || strpos($seocheck_newreporteranker_url, ".") === FALSE) {
            $seocheck_error = TRUE;
            $seocheck_error_msg = "Is not a valid url. Must possess the url http:// or https://";
            $seocheck_newreporteranker_url = null;
        } else {
            if (strpos($seocheck_newreporteranker_url, "http://") === FALSE || strpos($seocheck_newreporteranker_url, "https://") === FALSE) {
                $seocheck_newreporteranker_url = 'http://' . $seocheck_newreporteranker_url;
            }
        }
    } else {
        $seocheck_newreporteranker_url = null;
    }
    if (empty($seocheck_newreporteranker_url)) {
        $seocheck_error = TRUE;
        $seocheck_error_msg = "You must specify a valid URL or domain. <br/>A URL must have at least 5 caracters and a dot (.).";
    }

    //Check Factors
    if (empty($checkbox_factors)) {
        $seocheck_error = TRUE;
        $seocheck_error_msg = "You need select at least one factor from the list.";
    }


    if ($seocheck_error === FALSE) {
        $seocheck_reportobj = $erapi->reportnew($seocheck_newreporteranker_url, $checkbox_factors);
        if (empty($seocheck_reportobj)) {
            $seocheck_error = TRUE;
            $seocheck_error_msg = 'Could not create a report.<br/>An unknown error occurred';
        } else {
            if (isset($seocheck_reportobj->msg)) {
                $seocheck_error = TRUE;
                $seocheck_error_msg = $seocheck_report->msg . '<br/>' . $seocheck_reportobj->solution;
            } else {
                header("Location: /index.php?p=report&id=" . $seocheck_reportobj->id);
                exit;
            }
        }
    }
}
