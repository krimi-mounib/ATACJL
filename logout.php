<?php declare(strict_types=1);require_once __DIR__.'/includes/functions.php';if(post())verifyCsrf();logoutUser();redirect('index.php');
