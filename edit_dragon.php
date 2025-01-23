<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dragon_database";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch dragon data
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $query = "SELECT * FROM dragons WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $dragon = $result->fetch_assoc();

    if (!$dragon) {
        die("Dragon not found.");
    }
} else {
    die("Invalid request.");
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $class = $_POST['class'];
    $details = $_POST['details'];
    $image_url = $_POST['image_url'];
    $fandom_link = $_POST['fandom_link'];

    $updateQuery = "UPDATE dragons SET name = ?, class = ?, details = ?, image_url = ?, fandom_link = ? WHERE id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("sssssi", $name, $class, $details, $image_url, $fandom_link, $id);

    if ($stmt->execute()) {
        header("Location: manage_dragons.php");
        exit;
    } else {
        echo "Error updating dragon: " . $conn->error;
    }
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Dragon</title>
    <link href="favicon.ico" rel="icon" type="image/x-icon">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .navbar {
            background-color: #333;
            overflow: hidden;
            display: flex;
        }

        .navbar a {
            color: white;
            text-decoration: none;
            padding: 14px 20px;
            display: block;
        }

        .navbar a:hover {
            background-color: #575757;
        }

        .navbar a.active {
            background-color: #4CAF50;
        }

        .container {
            padding: 20px;
            max-width: 600px;
            margin: auto;
        }

        h1 {
            text-align: center;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            margin: 10px 0 5px;
        }

        input,
        select,
        textarea {
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button {
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #45a049;
        }
    </style>
</head>

<body>
    <div class="navbar">
        <a href="add_dragon.php">Add Dragon</a>
        <a href="manage_dragons.php">Manage Dragons</a>
    </div>

    <div class="container">
        <h1>Edit Dragon</h1>
        <form action="" method="POST">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($dragon['name']); ?>" required>

            <label for="class">Class:</label>
            <select id="class" name="class" required>
                <option value="Stoker" <?php if ($dragon['class'] === 'Stoker') echo 'selected'; ?>>Stoker</option>
                <option value="Boulder" <?php if ($dragon['class'] === 'Boulder') echo 'selected'; ?>>Boulder</option>
                <option value="Tracker" <?php if ($dragon['class'] === 'Tracker') echo 'selected'; ?>>Tracker</option>
                <option value="Sharp" <?php if ($dragon['class'] === 'Sharp') echo 'selected'; ?>>Sharp</option>
                <option value="Tidal" <?php if ($dragon['class'] === 'Tidal') echo 'selected'; ?>>Tidal</option>
                <option value="Mystery" <?php if ($dragon['class'] === 'Mystery') echo 'selected'; ?>>Mystery</option>
                <option value="Strike" <?php if ($dragon['class'] === 'Strike') echo 'selected'; ?>>Strike</option>
                <option value="Unknown" <?php if ($dragon['class'] === 'Unknown') echo 'selected'; ?>>Unknown</option>
            </select>

            <label for="details">Details:</label>
            <textarea id="details" name="details" rows="5" required><?php echo htmlspecialchars($dragon['details']); ?></textarea>

            <label for="image_url">Image URL:</label>
            <input type="url" id="image_url" name="image_url" value="<?php echo htmlspecialchars($dragon['image_url']); ?>" required>

            <label for="fandom_link">Fandom Link:</label>
            <input type="url" id="fandom_link" name="fandom_link" value="<?php echo htmlspecialchars($dragon['fandom_link']); ?>" required>

            <button type="submit">Save Changes</button>
        </form>
    </div>
</body>

</html>