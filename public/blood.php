<?php

$reponse = new stdClass();

$reponse->errors = [];
$reponse->success = 0;
$reponse->msg = "";

if(isset($_GET['action']))
{
	call_user_func($_GET['action']);
}


function login()
{
	print_r($_POST);
}