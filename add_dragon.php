<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Dragon</title>
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

        h1 {
            margin: 20px;
        }

        form {
            margin: 20px;
        }

        input,
        textarea,
        select,
        button {
            display: block;
            margin: 10px 0;
            width: 100%;
            max-width: 400px;
            padding: 10px;
        }
    </style>
</head>

<body>
    <div class="navbar">
        <a href="add_dragon.php" class="active">Add Dragon</a>
        <a href="manage_dragons.php">Manage Dragons</a>
        <a href="dragon_book.php">Digital Book Of Dragons</a>
    </div>

    <h1>Add a New Dragon</h1>

    <?php
    // Database connection
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "dragon_database";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $name = $_POST['name'];
        $class = $_POST['class'];
        $details = $_POST['details'];
        $image_url = $_POST['image_url'];
        $fandom_link = $_POST['fandom_link'];

        // Prepare and bind
        $stmt = $conn->prepare("INSERT INTO dragons (name, class, details, image_url, fandom_link) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $name, $class, $details, $image_url, $fandom_link);

        if ($stmt->execute()) {
            echo "<p style='color: green;'>Dragon added successfully!</p>";
        } else {
            echo "<p style='color: red;'>Error: " . $stmt->error . "</p>";
        }

        $stmt->close();
    }

    $conn->close();
    ?>

    <form action="add_dragon.php" method="POST">
        <label for="name">Dragon Name:</label>
        <input type="text" id="name" name="name" required>

        <label for="class">Dragon Class:</label>
        <select id="class" name="class" required>
            <option value="">Select a Class</option>
            <option value="Stoker">Stoker</option>
            <option value="Boulder">Boulder</option>
            <option value="Tracker">Tracker</option>
            <option value="Sharp">Sharp</option>
            <option value="Tidal">Tidal</option>
            <option value="Mystery">Mystery</option>
            <option value="Strike">Strike</option>
            <option value="Unknown">Unknown</option>
        </select>

        <label for="details">Dragon Details:</label>
        <textarea id="details" name="details" rows="4" required></textarea>

        <label for="image_url">Image URL:</label>
        <input type="url" id="image_url" name="image_url" required>

        <label for="fandom_link">Fandom Link:</label>
        <input type="url" id="fandom_link" name="fandom_link" required>

        <button type="submit">Add Dragon</button>
    </form>
</body>

</html>