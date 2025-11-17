<?php
require_once '/config.php';

function runQuery($sql, $params = []) {
    global $pdo;
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt;
}

function wrapResultsInTable($stmt) {
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($rows) === 0) {
        return "<table class='results'><tr><td>No results found.</td></tr></table>";
    }

    $html = "<table class='results'><tr>";
    $columns = array_keys($rows[0]);
    foreach ($columns as $col) {
        $html .= "<th>" . htmlspecialchars($col) . "</th>";
    }
    $html .= "</tr>";

    foreach ($rows as $row) {
        $html .= "<tr>";
        foreach ($row as $value) {
            $html .= "<td>" . htmlspecialchars($value) . "</td>";
        }
        $html .= "</tr>";
    }

    $html .= "</table>";
    return $html;
}
?>
