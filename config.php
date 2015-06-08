<?php

//CHANGE THIS SETTINGS - http://www.eranker.com/settings
$eranker_apiemail = "renan@georanker.com";
$eranker_apikey = "ee9978e1e5280ace6c0a3e99a77ea48c";

//Required Files
require_once 'libs/eRankerAPI.class.php';
require_once 'libs/eRankerCommons.php';

eRankerCommons::$factorCreateImageFolder = "/libs/";
eRankerCommons::$imgfolder = "/img/";

//Some default variables (can be overwritten by pages init)
$page_title = "Home";
$page_desc = "This is a DEMO project that uses eRanker API. It contains some basic features like: Create Report; View and print Report; Generate PDF; Latest Reports";
$project_name = "eRanker API Starter Kit";

//Init the eranker object and fill the user and factors data
global $erapi;
$erapi = new eRankerAPI($eranker_apiemail, $eranker_apikey, true);
$erapi_accountinfo = $erapi->account();
$erapi_allfactors = $erapi->factors('en');
$seocheck_error = false;
