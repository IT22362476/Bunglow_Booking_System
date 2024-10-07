<?php
require 'Mysqlconnection.php'; // Include your database connection file

function updateLinenCharges($id, $price) {
    global $connection;

    // Prepare a statement to update linen charges
    $stmt = $connection->prepare("UPDATE linencharges SET price = ? WHERE idlinencharges = ?");
    $stmt->bind_param("di", $price, $id);
    $result = $stmt->execute();
    $stmt->close();

    return $result;
}

// Fetching existing linen charges
$items = $connection->query("SELECT * FROM linencharges");

// Handle AJAX request to update price
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['ajax'])) {
    $id = $_POST['id'];
    $price = $_POST['price'];
    $result = updateLinenCharges($id, $price);
    echo json_encode(["success" => $result]);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Linen Charges</title>
    <style>
        /* General styling for the page */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }

        .container {
            padding: 20px;
            transition: margin-left 0.3s;
        }

        /* Sidebar styling */
        .sidebar {
            width: 250px;
            background-color: #4CAF50;
            color: white;
            position: fixed;
            height: 100%;
            top: 0;
            left: -250px;
            overflow: hidden;
            transition: left 0.3s;
            z-index: 1000;
        }

        .sidebar.active {
            left: 0;
        }

        .sidebar .nav-list {
            padding: 20px;
        }

        .sidebar .nav-items {
            list-style-type: none;
            margin: 20px 0;
        }

        .sidebar .nav-items a {
            text-decoration: none;
            color: white;
            display: block;
            padding: 10px;
            border-radius: 4px;
            transition: background-color 0.3s;
        }

        .sidebar .nav-items a:hover {
            background-color: #45a049;
        }

        /* Toggle button styling */
        .menu-toggle {
            position: fixed;
            top: 10px;
            left: 10px;
            font-size: 24px;
            cursor: pointer;
            color: #235428;
            z-index: 1001;
        }

        /* Form styling */
        .form-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border: 2px solid #4CAF50;
            border-radius: 8px;
            background-color: #f9f9f9;
        }

        .form-container label, .form-container input, .form-container button, .form-container select {
            display: block;
            width: 100%;
            margin-top: 10px;
        }

        .form-container label {
            margin-bottom: 5px;
        }

        .form-container input[type="number"], .form-container select {
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .form-container button {
            background-color: #4CAF50;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .form-container button:hover {
            background-color: #45a049;
        }

        .success-message {
            color: green;
            display: none;
        }
    </style>

    <script>
        function updatePriceDisplay() {
            var select = document.getElementById('id');
            var currentPrice = select.options[select.selectedIndex].getAttribute('data-price');
            document.getElementById('currentPrice').value = currentPrice || '';
        }

        function updatePrice() {
            var id = document.getElementById('id').value;
            var price = document.getElementById('price').value;

            if (id === 'default' || price === '') {
                alert("Please select an item and enter a new price.");
                return;
            }

            var xhr = new XMLHttpRequest();
            xhr.open("POST", "Linen.php", true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    var response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        document.getElementById('successMessage').style.display = 'block';
                        document.getElementById('successMessage').innerText = 'Price updated successfully!';
                        
                        // Update the current price to the new value
                        document.getElementById('currentPrice').value = price;
                    } else {
                        alert('Error updating price.');
                    }
                }
            };

            var params = "id=" + id + "&price=" + price + "&ajax=1";
            xhr.send(params);
        }

        // Function to toggle the sidebar
        function toggleSidebar() {
            var sidebar = document.getElementById("sidebar");
            var container = document.getElementById("main-container");
            if (sidebar.classList.contains("active")) {
                sidebar.classList.remove("active");
                container.style.marginLeft = "0";
            } else {
                sidebar.classList.add("active");
                container.style.marginLeft = "250px";
            }
        }
    </script>
</head>
<body>
    <!-- Menu toggle icon -->
    <div class="menu-toggle" onclick="toggleSidebar()">&#9776;</div>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <nav>
            <ul class="nav-list">
                <li class="nav-items"><a href="Superadmindashboard.php">Employee Details</a></li>
                <li class="nav-items"><a href="Superadminreservations.php">Reservation Details</a></li>
                <li class="nav-items"><a href="Supercalendaradmin.php">Calendar</a></li>
                <li class="nav-items"><a href="Superblocked.php">Blocked Days</a></li>
                <li class="nav-items"><a href="Superupdatetrack.php">Update Tracker</a></li>
                <li class="nav-items"><a href="Superexecutives.php">Executives</a></li>
                <li class="nav-items"><a href="Superlinen.php">Linen charges</a></li>
                <li class="nav-items"><a href="Viewhistories.php">View History</a></li>
            </ul>
        </nav>
    </div>


    <!-- Main content area -->
    <div class="container" id="main-container">
        <div class="form-container">
            <h2>Update Linen Charges</h2>
            <form onsubmit="event.preventDefault(); updatePrice();">
                <label for="id">Select Linen Item:</label>
                <select id="id" name="id" required onchange="updatePriceDisplay()">
                    <option value="default" selected>Select item</option>
                    <?php
                    if ($items) {
                        while ($row = $items->fetch_assoc()) {
                            echo "<option value='" . htmlspecialchars($row['idlinencharges']) . "' data-price='" . htmlspecialchars($row['price']) . "'>" . htmlspecialchars($row['item']) . "</option>";
                        }
                    }
                    ?>
                </select>

                <label for="currentPrice">Current Price:</label>
                <input type="number" id="currentPrice" name="currentPrice" readonly>

                <label for="price">New Price:</label>
                <input type="number" id="price" name="price" required step="0.01">

                <button type="submit">Update Price</button>
            </form>

            <p id="successMessage" class="success-message"></p>
        </div>
    </div>
</body>
</html>
