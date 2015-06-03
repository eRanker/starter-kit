<?php

//Get the page name
$page = "index";
if (isset($_GET['p']) && !empty($_GET['p']) && ctype_alnum($_GET['p'])) {
    $page = trim($_GET['p']);
    if (!file_exists('pages/' . $page . '-init.php')) {
        $page = "404";
    }
}

//Some default variables (can be overwritten by pages init
$page_title = "eRanker API Starter Kit";
$page_desc = "eRanker API Starter Kit - This is a DEMO project that uses eRanker API. It contains some basic features like: Create Report; View and print Report; Generate PDF; Latest Reports";


//Include the config file  (laod settings and libraries)
require_once 'config.php';

//The "init" files are always included BEFORE any output.
require_once 'pages/' . $page . '-init.php';

//Include the basic HTML header
require_once 'header.php';

//Include the current page body
require_once 'pages/' . $page . '-content.php';

//Include the basic HTML header
require_once 'footer.php';

