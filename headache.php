<?php
if(empty($_GET['user'])) die(show_source(__FILE__));
$user = ['admin', 'asdf'];
if($_GET['user'] === $user && $_GET['user'][0] != 'admin'){echo $flag;}