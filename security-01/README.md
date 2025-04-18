# Secure a PHP App from Vulnerabilities

## Sql injection
**Description**: An attacker injects SQL code to manipulate and sometimes access to the database.

**Solution**:  
Use prepared statements with binding.

```php
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email"); // prepareing
$stmt->execute(['email' => $email]); // binding
```

## 2. XSS

**Description**: An attacker injects malicious JavaScript into your app (to steal cookies of other users for example) affecting other users.

**Solution**:  
Escape input/output when displaying user generated content.

```php
echo htmlspecialchars($userInput, ENT_QUOTES, 'UTF-8');
```

## 3. CSRF

**Description**: An attacker tricks the user into submitting an unwanted request.

**Solution**:  
Use CSRF tokens (unique secure token per each user) in forms and verify them on the server.

```php
<?php
// backend
session_start();

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
// TODO: make this as middleware 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die('CSRF token mismatch!');
    }

    // Connect to database
    $pdo = new PDO('sqlite:secure.db');
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        email TEXT NOT NULL
    )");

    $name = htmlspecialchars($_POST['name'], ENT_QUOTES, 'UTF-8');
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die('Invalid email');
    }

    $stmt = $pdo->prepare("INSERT INTO users (name, email) VALUES (:name, :email)");
    $stmt->execute(['name' => $name, 'email' => $email]);

    echo "User saved successfully.";
}
```

```php
<?php
// frontend
session_start();
$csrf = $_SESSION['csrf_token'] ?? bin2hex(random_bytes(32));
$_SESSION['csrf_token'] = $csrf;
?>

<form method="POST" action="submit.php">
  <input type="text" name="name" placeholder="Name" required><br>
  <input type="email" name="email" placeholder="Email" required><br>
  <input type="hidden" name="csrf_token" value="<?php echo $csrf; ?>">
  <button type="submit">Submit</button>
</form>
```
