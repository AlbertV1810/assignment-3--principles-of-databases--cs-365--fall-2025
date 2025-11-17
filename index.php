<?php
require_once 'includes/helpers.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'insert') {
    $user_id = $_POST['user_id'];
    $site_id = $_POST['site_id'];
    $password = $_POST['password'];
    $comment = $_POST['comment'];

    $sql = "INSERT INTO credentials (user_id, site_id, password, comment) VALUES (:user_id, :site_id, AES_ENCRYPT(:password, 'secret_key'), :comment)";
    runQuery($sql, [
        ':user_id' => $user_id,
        ':site_id' => $site_id,
        ':password' => $password,
        ':comment' => $comment
    ]);

    $message = "Credential inserted successfully!";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update'){
    $site_id = $_POST['update_site_id'];
    $new_url = $_POST['new_url'];

    $sql = "UPDATE sites SET url = :url WHERE site_id = :site_id";
    runQuery($sql, [
        ':url' => $new_url,
        ':site_id' => $site_id
    ]);

    $message = "Site URL updated successfully!";
}

// Handle delete action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $del_user = $_POST['delete_user_id'] ?? '';
    $del_site = $_POST['delete_site_id'] ?? '';
    $delParams = [];
    $delSql = "DELETE FROM credentials WHERE 1";

    if (!empty($del_user)) {
        $delSql .= " AND user_id = :user_id";
        $delParams[':user_id'] = $del_user;
    }
    if (!empty($del_site)) {
        $delSql .= " AND site_id = :site_id";
        $delParams[':site_id'] = $del_site;
    }

    if (empty($delParams)) {
        $message = "Provide at least one pattern to delete.";
    } else {
        runQuery($delSql, $delParams);
        $message = "Matching credentials deleted.";
    }
}

$users = runQuery("SELECT user_id, username FROM users")->fetchAll(PDO::FETCH_ASSOC);
$sites = runQuery("SELECT site_id, site_name FROM sites")->fetchAll(PDO::FETCH_ASSOC);

// Prepare search results HTML (empty by default)
$searchTableHtml = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'search') {
        $search_user_id = $_POST['search_user_id'] ?? '';
        $search_site_id = $_POST['search_site_id'] ?? '';

        $sql = "SELECT u.username AS user_name, s.site_name,
                       CAST(AES_DECRYPT(c.password, 'secret_key') AS CHAR) AS decrypted_password,
                       c.comment, c.created_at
                FROM credentials c
                JOIN users u ON c.user_id = u.user_id
                JOIN sites s ON c.site_id = s.site_id
                WHERE 1";

        $params = [];

        if (!empty($search_user_id)) {
            $sql .= " AND c.user_id = :user_id";
            $params[':user_id'] = $search_user_id;
        }

        if (!empty($search_site_id)) {
            $sql .= " AND c.site_id = :site_id";
            $params[':site_id'] = $search_site_id;
        }

        $stmt = runQuery($sql, $params);
        $searchTableHtml = wrapResultsInTable($stmt);
    }

    if ($_POST['action'] === 'clear') {
        $searchTableHtml = '';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Password Manager</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <h1>Password Manager</h1>

    <?php if ($message): ?>
        <p><?= $message ?></p>
    <?php endif; ?>

    <h2>Insert New Credential</h2>
    <form method="POST">
        <input type="hidden" name="action" value="insert">

        <label>User:</label>
        <select name="user_id" required>
            <option value="">-- Select User --</option>
            <?php foreach ($users as $user): ?>
                <option value="<?= $user['user_id'] ?>"><?= htmlspecialchars($user['username']) ?></option>
            <?php endforeach; ?>
        </select><br><br>

        <label>Site:</label>
        <select name="site_id" required>
            <option value="">-- Select Site --</option>
            <?php foreach ($sites as $site): ?>
                <option value="<?= $site['site_id'] ?>"><?= htmlspecialchars($site['site_name']) ?></option>
            <?php endforeach; ?>
        </select><br><br>

        <label>Password:</label>
        <input type="password" name="password" required><br><br>

        <label>Comment:</label><br>
        <textarea name="comment" rows="4" cols="40" required></textarea><br><br>

        <button type="submit">Insert</button>
    </form>

    <h2>Search Stored Credentials</h2>
    <form method="POST">
        <!-- use button values for action -->
        <label>User:</label>
        <select name="search_user_id">
            <option value="">-- All Users --</option>
            <?php foreach ($users as $user): ?>
                <option value="<?= $user['user_id'] ?>"><?= htmlspecialchars($user['username']) ?></option>
            <?php endforeach; ?>
        </select><br><br>

        <label>Site:</label>
        <select name="search_site_id">
            <option value="">-- All Sites --</option>
            <?php foreach ($sites as $site): ?>
                <option value="<?= $site['site_id'] ?>"><?= htmlspecialchars($site['site_name']) ?></option>
            <?php endforeach; ?>
        </select><br><br>

        <button type="submit" name="action" value="search">Search</button>
        <button type="submit" name="action" value="clear">Clear Results</button>
    </form>

    <?php if ($searchTableHtml !== ''): ?>
        <h3>Search Results</h3>
        <?= $searchTableHtml ?>
    <?php endif; ?>

    <h2>Delete Credentials</h2>
    <form method="POST">
        <input type="hidden" name="action" value="delete">
        <label>User:</label>
        <select name="delete_user_id">
            <option value="">-- Any User --</option>
            <?php foreach ($users as $user): ?>
                <option value="<?= $user['user_id'] ?>"><?= htmlspecialchars($user['username']) ?></option>
            <?php endforeach; ?>
        </select><br><br>

        <label>Site:</label>
        <select name="delete_site_id">
            <option value="">-- Any Site --</option>
            <?php foreach ($sites as $site): ?>
                <option value="<?= $site['site_id'] ?>"><?= htmlspecialchars($site['site_name']) ?></option>
            <?php endforeach; ?>
        </select><br><br>

        <button type="submit">Delete</button>
    </form>

    <h2>Update Site URL</h2>
    <form method="POST">
        <input type="hidden" name="action" value="update">

        <label>Site:</label>
        <select name="update_site_id" required>
            <option value="">-- Select Site --</option>
            <?php foreach ($sites as $site): ?>
                <option value="<?= $site['site_id'] ?>"><?= htmlspecialchars($site['site_name']) ?></option>
            <?php endforeach; ?>
        </select><br><br>

        <label> New URL:</label>
        <input type="text" name="new_url" required><br><br>

        <button type="submit">Update URL</button>
    </form>
</body>
</html>
