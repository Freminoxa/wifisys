<?php

require_once 'config.php';
class MpesaAPI {
    private $consumer_key;
    private $consumer_secret;
    private $passkey;
    private $businessShortCode;
    private $environment = 'sandbox'; // Change to 'production' for live environment
    
    public function __construct() {
        $this->consumer_key = 'YOUR_CONSUMER_KEY';
        $this->consumer_secret = 'YOUR_CONSUMER_SECRET';
        $this->passkey = 'YOUR_PASSKEY';
        $this->businessShortCode = 'YOUR_SHORTCODE';
    }

// Database connection
$db = new mysqli('localhost', 'username', 'password', 'your_database');

function getUserByPhone($db, $phone) {
    $stmt = $db->prepare("SELECT * FROM users WHERE phone_number = ?");
    $stmt->bind_param("s", $phone);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

// Function to get bundle details by ID
function getBundleById($db, $bundle_id) {
    $stmt = $db->prepare("SELECT * FROM bundles WHERE id = ?");
    $stmt->bind_param("i", $bundle_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

// Display available bundles
$bundles = $db->query("SELECT * FROM bundles ORDER BY price ASC");
?>

<!DOCTYPE html>
<html>
<head>
    <!-- HTML and CSS remain the same -->
</head>
<body>
    <h2>Available WiFi Bundles</h2>
    <div id="bundles-container">
        <?php while ($bundle = $bundles->fetch_assoc()): ?>
            <div class="bundle-card" data-price="<?= $bundle['price'] ?>" data-id="<?= $bundle['id'] ?>">
                <h3><?= htmlspecialchars($bundle['name']) ?></h3>
                <p>Price: KES <?= $bundle['price'] ?></p>
                <p>Data: <?= $bundle['data_limit'] ?> MB</p>
                <p>Valid for: <?= $bundle['validity_hours'] ?> hours</p>
                <button onclick="selectBundle(<?= $bundle['id'] ?>, <?= $bundle['price'] ?>)">Select</button>
            </div>
        <?php endwhile; ?>
    </div>

    <div id="payment-form" style="display: none;">
        <h3>Complete Payment</h3>
        <form id="mpesaPaymentForm">
            <input type="tel" name="phone" placeholder="Phone Number (254XXX)" required>
            <input type="hidden" name="bundle_id" id="selected_bundle_id">
            <input type="hidden" name="amount" id="selected_amount">
            <button type="submit">Pay with M-PESA</button>
        </form>
    </div>

    <script>
    function selectBundle(bundleId, price) {
        // Same as before
        document.getElementById('selected_bundle_id').value = bundleId;
        document.getElementById('selected_amount').value = price;
        document.getElementById('payment-form').style.display = 'block';
    }

    document.getElementById('mpesaPaymentForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        
        fetch('process_payment.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
            if (data.success) {
                // Optionally redirect to a status page
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        });
    });
    </script>
</body>
</html>