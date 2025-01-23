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

// Handle delete request
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $deleteQuery = "DELETE FROM dragons WHERE id = ?";
    $stmt = $conn->prepare($deleteQuery);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: manage_dragons.php"); // Redirect to avoid resubmission
    exit;
}

// Initialize filters
$search = isset($_GET['search']) ? $_GET['search'] : '';
$classFilter = isset($_GET['class']) ? $_GET['class'] : '';

// Build query with filters
$query = "SELECT * FROM dragons WHERE 1=1";
$params = [];
$types = '';

if (!empty($search)) {
    $query .= " AND (name LIKE ? OR details LIKE ?)";
    $searchParam = "%" . $search . "%";
    $params[] = $searchParam;
    $params[] = $searchParam;
    $types .= "ss";
}

if (!empty($classFilter)) {
    $query .= " AND class = ?";
    $params[] = $classFilter;
    $types .= "s";
}

$stmt = $conn->prepare($query);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="favicon.ico" rel="icon" type="image/x-icon">
    <title>Digital Book Of Dragons</title>
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

        .filters {
            margin: 20px;
            display: flex;
            gap: 10px;
            align-items: center;
            align-content: center;
        }

        .filters input[type="text"],
        .filters select {
            padding: 8px;
            font-size: 14px;
        }

        .filters button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 8px 12px;
            cursor: pointer;
            font-size: 14px;
        }

        .filters button:hover {
            background-color: #45a049;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        table,
        th,
        td {
            border: 1px solid #ddd;
        }

        th,
        td {
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #f4f4f4;
        }

        img {
            max-width: 150px;
            height: auto;
        }

        .actions a {
            margin-right: 10px;
            text-decoration: none;
            color: #007BFF;
        }

        .actions a:hover {
            text-decoration: underline;
        }

        .actions .edit-button {
            background-color: #ffc107;
            color: white;
            padding: 5px 14.5px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            font-family: 'RunesFont', sans-serif;
            /* Fallback to sans-serif */
            font-size: 1em;
            /* Adjust size as needed */
            text-align: center;
            align-items: center;
            margin-bottom: 10px;

        }

        .actions .edit-button:hover {
            background-color: #e0a800;
        }

        .actions .delete-button {
            background-color: #dc3545;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            font-family: 'RunesFont', sans-serif;
            /* Fallback to sans-serif */
            font-size: 1em;
            /* Adjust size as needed */
            text-align: center;
        }

        @font-face {
            font-family: 'RunesFont';
            src: url('fonts/ComicRunes.otf') format('opentype');
        }

        .runes {
            font-family: 'RunesFont', sans-serif;
            /* Fallback to sans-serif */
            font-size: 2.5em;
            /* Adjust size as needed */
            text-align: center;
            margin-top: 20px;
        }


        .runes2 {
            font-family: 'RunesFont', sans-serif;
            /* Fallback to sans-serif */
            font-size: 1em;
            /* Adjust size as needed */
            text-align: center;
        }


        .actions .delete-button:hover {
            background-color: #c82333;
        }

        .filters {
            display: flex;
            /* Use flexbox for layout */
            justify-content: center;
            /* Center items horizontally */
            align-items: center;
            /* Center items vertically */
            gap: 10px;
            /* Add spacing between elements */
            margin: 20px 0;
            /* Add optional margin for spacing around the container */
        }

        .filters form {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            /* Spacing between form elements */
        }

        .filters input[type="text"] {
            padding: 8px;
            /* Add padding for better spacing */
            font-size: 14px;
            /* Adjust font size */
            font-family: 'RunesFont', sans-serif;
            /* Apply your custom font */
            width: 160px;
            /* Set an appropriate width */
            box-sizing: border-box;
            /* Ensures padding is included in width */
        }

        .filters input[type="text"]::placeholder {
            white-space: nowrap;
            /* Prevent placeholder text from wrapping */
            overflow: hidden;
            /* Hide overflow if necessary */
            text-overflow: ellipsis;
            /* Add ellipsis if text overflows */
        }

        /* .filters input[type="text"] {
            padding: 8px;
            font-size: 14px;
            padding-right: 50px;
            font-family: 'RunesFont', sans-serif;
        } */

        .filters select {
            padding: 8px;
            font-size: 14px;
            font-family: 'RunesFont', sans-serif;
            /* Fallback to sans-serif */
            /* Adjust size as needed */
            text-align: center;
        }

        .filters button {
            padding: 8px 12px;
            font-size: 14px;
            cursor: pointer;
            font-family: 'RunesFont', sans-serif;
            /* Fallback to sans-serif */
            /* Adjust size as needed */
            text-align: center;
            border-radius: 3px;
        }
    </style>
</head>

<body>
    <div class="navbar">
        <a href="add_dragon.php">Add Dragon</a>
        <a href="manage_dragons.php" class="active">Manage Dragons</a>
        <a href="dragon_book.php">Digital Book Of Dragons</a>
    </div>

    <h1 class="runes">Digital Book Of Dragons</h1>

    <div class="filters">
        <form method="GET" action="manage_dragons.php">
            <input type="text" name="search" placeholder="Search by name or details" value="<?php echo htmlspecialchars($search); ?>">
            <select name="class">
                <option value="">Filter by class</option>
                <option value="Stoker" <?php echo $classFilter === "Stoker" ? "selected" : ""; ?>>Stoker</option>
                <option value="Boulder" <?php echo $classFilter === "Boulder" ? "selected" : ""; ?>>Boulder</option>
                <option value="Tracker" <?php echo $classFilter === "Tracker" ? "selected" : ""; ?>>Tracker</option>
                <option value="Sharp" <?php echo $classFilter === "Sharp" ? "selected" : ""; ?>>Sharp</option>
                <option value="Tidal" <?php echo $classFilter === "Tidal" ? "selected" : ""; ?>>Tidal</option>
                <option value="Mystery" <?php echo $classFilter === "Mystery" ? "selected" : ""; ?>>Mystery</option>
                <option value="Strike" <?php echo $classFilter === "Strike" ? "selected" : ""; ?>>Strike</option>
                <option value="Unknown" <?php echo $classFilter === "Unknown" ? "selected" : ""; ?>>Unknown</option>
            </select>
            <button type="submit">Apply Filters</button>
        </form>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Class</th>
                <th>Details</th>
                <th>Image</th>
                <th>Fandom Link</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['id']); ?></td>
                    <td class="runes2"><?php echo htmlspecialchars($row['name']); ?></td>
                    <td class="runes2"><?php echo htmlspecialchars($row['class']); ?></td>
                    <td><?php echo htmlspecialchars($row['details']); ?></td>
                    <td>
                        <?php if (!empty($row['image_url'])): ?>
                            <img src="<?php echo htmlspecialchars($row['image_url']); ?>" alt="Dragon Image">
                        <?php else: ?>
                            <span>No Image Available</span>
                        <?php endif; ?>
                    </td>
                    <td class="runes2"><a href="<?php echo htmlspecialchars($row['fandom_link']); ?>" target="_blank">View Fandom Page</a></td>
                    <td class="actions">
                        <form class="runes2" action="edit_dragon.php" method="GET" style="display: inline;">
                            <input class="runes2" type="hidden" name="id" value="<?php echo $row['id']; ?>">
                            <button type="submit" class="edit-button">Edit</button>
                        </form>
                        <a href="manage_dragons.php?delete=<?php echo $row['id']; ?>" class="delete-button" onclick="return confirm('Are you sure you want to delete this dragon?');">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <?php $stmt->close();
    $conn->close(); ?>
</body>

</html>