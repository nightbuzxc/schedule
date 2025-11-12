<?php
include 'db.php';

$grouped = [];
$groups_result = $conn->query("SELECT id, name FROM `groups`");
while ($row = $groups_result->fetch_assoc()) {
    $parts = explode('-', $row['name']);
    $main = $parts[0];
    $grouped[$main][] = $row;
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Групи</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .main-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    grid-auto-rows: auto;
    gap: 20px;
    justify-content: center;
    align-items: start;    
    text-align: center;            

.dropdown {
    display: flex;
    flex-direction: column;
    align-items: center;
}

.dropdown-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 10px;
    padding: 10px;
    justify-items: center;
}

.dropdown-menu {
    padding: 0.5rem;
    border-radius: 10px;
}

.dropdown-grid a button {
    width: 87px;
}

    </style>
</head>
<body class="bg-light py-4">
<div class="container">
    <h2 class="mb-4 text-center">Оберіть групу:</h2>

    <div class="main-grid">
        <?php foreach ($grouped as $main => $subgroups): ?>
            <div class="dropdown">
                <button class="btn btn-primary dropdown-toggle w-100" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <?= htmlspecialchars($main) ?>
                </button>
                <ul class="dropdown-menu">
                    <div class="dropdown-grid">
                        <?php foreach ($subgroups as $group): ?>
                            <a href="homepage.php?group_id=<?= $group['id'] ?>">
                                <button class="btn btn-outline-secondary"><?= htmlspecialchars($group['name']) ?></button>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </ul>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
