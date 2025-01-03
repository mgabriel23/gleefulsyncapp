<?php
    header('Content-Type: application/json');
    header('Cache-Control: no-cache');

    include $_SERVER['DOCUMENT_ROOT'] . '/gleefulsync/config.php';
    include DB_CONNECTION_LINK;

    $query = "SELECT id, name FROM members_tbl";

    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $members_lists = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($members_lists);
?>