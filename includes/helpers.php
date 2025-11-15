<?php
require_once 'config.php';

function runQuery($sql, $params = []) {
    global $pdo;
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt;
}

function wrapResultsInTable($stmt) {
    if ($stmt->rowCount()=== 0) {
        return "<p>No results found.</p>";
    }

    $html = "<table border ='1'><tr>";
    $columns = array_keys($stmt->fetch(PDO::FETCH_ASSOC));
    foreach ($columns as $col) {
        $html .= "<th>" . htmlspecialchars($col) . "</th>";
    }
    $html .= "</tr>";

    $stmt->execute();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $html .= "<tr>";
        foreach ($row as $value) {
            $html .= "<td>" . htmlspecialchars($value) . "</td>";
        }
        $html .= "</tr>";
    }
    $html .= "</table";
    return $html;
}
?>
