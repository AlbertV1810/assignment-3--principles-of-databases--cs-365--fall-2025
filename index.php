<?php
require_once 'includes/helpers.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'insert') {
    $site_name = trim($_POST['site_name'] ?? '');
    $site_url  = trim($_POST['site_url'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $username  = trim($_POST['username'] ?? '');
    $password  = $_POST['password'] ?? '';
    $comment   = trim($_POST['comment'] ?? '');

    if ($site_name === '' || $username === '' || $password === '') {
        $message = "site name, username, and password are required.";
    } else {
        $stmt = runQuery("SELECT user_id FROM users WHERE username = :username", [':username' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user) {
            $user_id = $user['user_id'];
        } else {
            $stmt = runQuery("INSERT INTO users (first_name, last_name, username, email) VALUES ('', '', :username, :email)", [
                ':username' => $username,
                ':email' => $email
            ]);
            $user_id = $pdo->lastInsertId();
        }

        $stmt = runQuery("SELECT site_id FROM sites WHERE site_name = :site_name", [':site_name' => $site_name]);
        $site = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($site) {
            $site_id = $site['site_id'];
            if ($site_url !== '') {
                runQuery("UPDATE sites SET url = :url WHERE site_id = :site_id", [
                    ':url' => $site_url,
                    ':site_id' => $site_id
                ]);
            }
        } else {
            runQuery("INSERT INTO sites (site_name, url) VALUES (:site_name, :url)", [
                ':site_name' => $site_name,
                ':url' => $site_url
            ]);
            $site_id = $pdo->lastInsertId();
        }

        runQuery("INSERT INTO credentials (user_id, site_id, password, comment) VALUES (:user_id, :site_id, AES_ENCRYPT(:password, 'secret_key'), :comment)", [
            ':user_id' => $user_id,
            ':site_id' => $site_id,
            ':password' => $password,
            ':comment' => $comment
        ]);

        $message = "Credential inserted successfully!";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update'){
    $target_site_name = trim($_POST['update_site_name'] ?? '');
    $new_url = trim($_POST['new_url'] ?? '');

    if ($target_site_name === '' || $new_url === '') {
        $message = "Provide a site name and new URL to update.";
    } else {
        $sql = "UPDATE sites SET url = :url WHERE site_name = :site_name";
        runQuery($sql, [
            ':url' => $new_url,
            ':site_name' => $target_site_name
        ]);
        $message = "Site URL updated successfully!";
    }
}

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

$searchTableHtml = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'search') {
        $params = [];
        $sql = "
            SELECT u.username AS user_name, u.email, s.site_name, s.url,
                   CAST(AES_DECRYPT(c.password, 'secret_key') AS CHAR) AS decrypted_password,
                   c.comment, c.created_at
            FROM credentials c
            JOIN users u ON c.user_id = u.user_id
            JOIN sites s ON c.site_id = s.site_id
            WHERE 1
        ";

        if (!empty($_POST['search_site_name'])) {
            $sql .= " AND s.site_name LIKE :site_name";
            $params[':site_name'] = "%" . $_POST['search_site_name'] . "%";
        }
        if (!empty($_POST['search_url'])) {
            $sql .= " AND s.url LIKE :url";
            $params[':url'] = "%" . $_POST['search_url'] . "%";
        }
        if (!empty($_POST['search_email'])) {
            $sql .= " AND u.email LIKE :email";
            $params[':email'] = "%" . $_POST['search_email'] . "%";
        }
        if (!empty($_POST['search_username'])) {
            $sql .= " AND u.username LIKE :username";
            $params[':username'] = "%" . $_POST['search_username'] . "%";
        }
        if (!empty($_POST['search_password'])) {
            $sql .= " AND CAST(AES_DECRYPT(c.password, 'secret_key') AS CHAR) LIKE :password";
            $params[':password'] = "%" . $_POST['search_password'] . "%";
        }
        if (!empty($_POST['search_comment'])) {
            $sql .= " AND c.comment LIKE :comment";
            $params[':comment'] = "%" . $_POST['search_comment'] . "%";
        }

        $stmt = runQuery($sql, $params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($rows) > 0) {
            $searchTableHtml = "<table border='1'><tr>
                <th>Username</th><th>Email</th><th>Site Name</th><th>URL</th>
                <th>Password</th><th>Comment</th><th>Created At</th></tr>";
            foreach ($rows as $row) {
                $searchTableHtml .= "<tr>
                    <td>" . htmlspecialchars($row['user_name']) . "</td>
                    <td>" . htmlspecialchars($row['email']) . "</td>
                    <td>" . htmlspecialchars($row['site_name']) . "</td>
                    <td>" . htmlspecialchars($row['url']) . "</td>
                    <td>" . htmlspecialchars($row['decrypted_password']) . "</td>
                    <td>" . htmlspecialchars($row['comment']) . "</td>
                    <td>" . htmlspecialchars($row['created_at']) . "</td>
                </tr>";
            }
            $searchTableHtml .= "</table>";
        } else {
            $searchTableHtml = "<table border='1'><tr><td colspan='7'>No results found for your query.</td></tr></table>";
        }
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
        <p><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <h2>Insert New Credential</h2>
    <form method="POST">
        <input type="hidden" name="action" value="insert">

        <label>Site/App Name:</label>
        <input type="text" name="site_name" required>

        <label>Site URL:</label>
        <input type="text" name="site_url" required>

        <label>Email:</label>
        <input type="text" name="email" required>

        <label>Username:</label>
        <input type="text" name="username" required>

        <label>Password:</label>
        <input type="password" name="password" required>

        <label>Comment:</label>
        <textarea name="comment"></textarea>

        <button type="submit">Insert</button>
    </form>

    <h2>Search Credentials</h2>
    <form method="POST">
        <input type="hidden" name="action" value="search">

        <label>Site/App Name:</label>
        <input type="text" name="search_site_name" placeholder="e.g. GitHub"><br>

        <label>Site URL:</label>
        <input type="text" name="search_url" placeholder="e.g. https://github.com"><br>

        <label>Email Address:</label>
        <input type="text" name="search_email" placeholder="e.g. ada@math.net"><br>

        <label>Username:</label>
        <input type="text" name="search_username" placeholder="e.g. ada_code"><br>

        <label>Password:</label>
        <input type="text" name="search_password" placeholder="e.g. secure123"><br>

        <label>Comment:</label>
        <input type="text" name="search_comment" placeholder="e.g. personal account"><br>

        <button type="submit">Search</button>
    </form>

    echo $searchTableHtml;

    <h2>Update Site URL</h2>
    <form method="POST">
        <input type="hidden" name="action" value="update">

        <label>Site Name (pattern):</label>
        <input type="text" name="update_site_name" placeholder="Exact site name">

        <label>New URL:</label>
        <input type="text" name="new_url" required>

        <button type="submit">Update</button>
    </form>

    <form method="POST" style="margin: 20px 0;">
        <input type="hidden" name="action" value="clear">
        <button type="submit" style="background-color: #f44336; color: white; padding: 10px 20px; font-size: 16px;">
            Clear Results
        </button>
    </form>

</body>
</html>
