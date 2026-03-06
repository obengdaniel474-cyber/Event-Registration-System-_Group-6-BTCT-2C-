<?php
session_start();

$event_id = $_POST['event_id'] ?? null;

if (!$event_id) {
    header("Location: index.html");
    exit;
}

if (!isset($_SESSION['user'])) {
    $_SESSION['pending_event'] = $event_id;
    header("Location: login.php");
    exit;
}

$_SESSION['registered_events'][] = (int)$event_id;

header("Location: dashboard.php");
exit;