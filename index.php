<?php

//Get the page name
$page = "home";
if (isset($_GET['p']) && !empty($_GET['p']) && ctype_alnum($_GET['p'])) {
    $page = trim($_GET['p']);
    if (!file_exists('pages/' . $page . '-init.php')) {
        $page = "404";
    }
}

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

