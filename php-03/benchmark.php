<?php

$db = new PDO('sqlite:users.db');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$db->exec("DROP TABLE IF EXISTS users");
$db->exec("
    CREATE TABLE users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT,
        email TEXT,
        is_active BOOL,
        created_at DATETIME
    )
");


echo "Inserting 1 million users...\n";

$db->beginTransaction();

$statuses = [true, false];
$startTime = time();

$insert = $db->prepare("INSERT INTO users (name, email, is_active, created_at) VALUES (?, ?, ?, ?)");

for ($i = 0; $i < 1000000; $i++) {
    $name = "User$i";
    $email = "user$i@example.com";
    $is_active = $statuses[array_rand($statuses)];
    $createdAt = date('Y-m-d H:i:s', $startTime - random_int(0, 10_000_000));
    $insert->execute([$name, $email, $is_active, $createdAt]);

    if ($i % 50000 == 0) echo "Inserted: $i\n";
}

$db->commit();

echo "Finished inserting.\n";

echo "Creating index...\n";
$db->exec("CREATE INDEX idx_is_active_created_at ON users (is_active, created_at DESC)");

echo "Running benchmark query...\n";

$start = microtime(true);

$stmt = $db->query("
    SELECT id, name, email, created_at
    FROM users
    WHERE is_active = 1
    ORDER BY created_at DESC
    LIMIT 50
");

$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
$elapsed = microtime(true) - $start;

echo "Query returned " . count($results) . " rows in " . round($elapsed * 1000, 3) . " ms\n";
