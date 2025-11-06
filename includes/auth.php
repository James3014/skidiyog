<?php
session_start();

if(!isset($_SESSION['SKIDIY']['login'])){
	header('Location: index.php');
}