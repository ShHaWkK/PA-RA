<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Collections</title>
    <link rel="stylesheet" href="/assets/css/global.css">
    <link rel="stylesheet" href="/assets/css/modal.css">
    <link rel="stylesheet" href="/assets/css/table.css">
    <link rel="stylesheet" href="/assets/css/backoffice.css">
    <link rel="stylesheet" href="/assets/css/formvolunteer.css">
</head>

<body>
<div class="back-office-container">
    <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/includes/MerchantDashboard.php'); ?>
    <div class="back-office-content">
        <h1>Add Product Notification</h1>
        <form id="addNotificationForm">
            <label for="productSelect">Product:</label>
            <select id="productSelect" required>
                <!-- Options will be populated dynamically -->
            </select>
            <br><br>

            <label for="quantity">Quantity:</label>
            <input type="number" id="quantity" name="quantity" required>
            <br><br>

            <label for="date">Wished Collection Date:</label>
            <input type="date" id="date" name="date" required>
            <br><br>

            <label for="address">Address:</label>
            <textarea id="address" name="address" rows="3" required></textarea>
            <br><br>

            <button type="submit">Add Demand</button>
        </form>
    </div>
</div>

<script type="module" src="/assets/js/pages/MerchantAddCollectionDemand.js"></script>
</body>
</html>