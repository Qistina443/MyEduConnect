<?php
session_start();
include 'config.php';

// VULN: No authentication - anyone can view receipts by ID
$payment_id = isset($_GET['id']) ? $_GET['id'] : (isset($_SESSION['last_payment']['payment_id']) ? $_SESSION['last_payment']['payment_id'] : 0);

if (!$payment_id) {
    header("Location: student.php");
    exit();
}

// VULN: SQL Injection vulnerability
$query = "SELECT 
            p.*, 
            u.name, 
            u.email,
            c.course_name as course_name,
            c.description as course_description
          FROM payments p 
          JOIN users u ON p.student_id = u.id 
          JOIN courses c ON p.course_id = c.id 
          WHERE p.id = $payment_id";

$result = mysqli_query($conn, $query);
$payment = mysqli_fetch_assoc($result);

// VULN: No check if user owns this receipt

if (!$payment) {
    header("Location: student.php?error=Receipt+not+found");
    exit();
}

// Generate receipt number
$receipt_no = "MEC-" . str_pad($payment['id'], 8, "0", STR_PAD_LEFT);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Receipt - MyEduConnect</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 40px 20px;
        }
        
        .receipt-container {
            max-width: 800px;
            margin: 0 auto;
        }
        
        .receipt-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
        }
        
        .receipt-header {
            background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .receipt-header h1 {
            font-size: 28px;
            margin-bottom: 10px;
        }
        
        .success-badge {
            display: inline-block;
            background: rgba(255,255,255,0.2);
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 14px;
            margin-top: 10px;
        }
        
        .receipt-body {
            padding: 30px;
        }
        
        .company-info {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 2px dashed #e0e0e0;
            margin-bottom: 20px;
        }
        
        .company-info h2 {
            color: #264adc;
            margin-bottom: 5px;
        }
        
        .company-info p {
            color: #666;
            font-size: 12px;
        }
        
        .receipt-meta {
            display: flex;
            justify-content: space-between;
            background: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        
        .meta-item {
            text-align: center;
        }
        
        .meta-label {
            font-size: 11px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .meta-value {
            font-size: 16px;
            font-weight: bold;
            color: #333;
            margin-top: 5px;
        }
        
        .customer-info, .payment-details, .course-info {
            margin-bottom: 25px;
        }
        
        .section-title {
            font-size: 18px;
            font-weight: bold;
            color: #264adc;
            border-left: 4px solid #264adc;
            padding-left: 12px;
            margin-bottom: 15px;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .info-label {
            font-weight: 600;
            color: #555;
        }
        
        .info-value {
            color: #333;
        }
        
        .amount-row {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
            margin-top: 20px;
        }
        
        .total-amount {
            font-size: 24px;
            font-weight: bold;
            color: #28a745;
        }
        
        .footer {
            text-align: center;
            padding-top: 20px;
            border-top: 2px dashed #e0e0e0;
            margin-top: 20px;
            color: #666;
            font-size: 12px;
        }
        
        .button-group {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 30px;
            padding: 20px;
            background: #f8f9fa;
        }
        
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s;
        }
        
        .btn-primary {
            background: #264adc;
            color: white;
        }
        
        .btn-primary:hover {
            background: #1a3a8a;
            transform: translateY(-2px);
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #5a6268;
        }
        
        .btn-print {
            background: #17a2b8;
            color: white;
        }
        
        .btn-print:hover {
            background: #138496;
        }
        
        @media print {
            body {
                background: white;
                padding: 0;
            }
            .button-group {
                display: none;
            }
            .receipt-card {
                box-shadow: none;
            }
        }
        
        .warning {
            background: #fff3cd;
            border: 1px solid #ffc107;
            padding: 10px;
            margin-top: 20px;
            border-radius: 5px;
            font-size: 12px;
        }
        
        .debug-info {
            display: none;
        }
        
        .debug-info.show {
            display: block;
            background: #1e1e1e;
            color: #d4d4d4;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
            font-family: monospace;
            font-size: 12px;
            overflow-x: auto;
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        <div class="receipt-card">
            <div class="receipt-header">
                <h1> PAYMENT CONFIRMED</h1>
                <div class="success-badge">Payment Successful</div>
            </div>
            
            <div class="receipt-body">
                <div class="company-info">
                    <h2>MyEduConnect Sdn Bhd</h2>
                    <p>Cyberjaya, Selangor, Malaysia</p>
                    <p>Email: support@myeduconnect.com | Tel: +6XX-XXXXXXXXXX/p>
                </div>
                
                <div class="receipt-meta">
                    <div class="meta-item">
                        <div class="meta-label">Receipt No.</div>
                        <div class="meta-value"><?php echo $receipt_no; ?></div>
                    </div>
                    <div class="meta-item">
                        <div class="meta-label">Transaction ID</div>
                        <div class="meta-value"><?php echo htmlspecialchars($payment['id']); ?></div>
                    </div>
                    <div class="meta-item">
                        <div class="meta-label">Date</div>
                        <div class="meta-value"><?php echo date('d/m/Y h:i A', strtotime($payment['payment_date'])); ?></div>
                    </div>
                </div>
                
                <div class="customer-info">
                    <div class="section-title">Customer Information</div>
                    <div class="info-row">
                        <span class="info-label">Name:</span>
                        <span class="info-value"><?php echo htmlspecialchars($payment['name']); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Email:</span>
                        <span class="info-value"><?php echo htmlspecialchars($payment['email']); ?></span>
                    </div>
                </div>
                
                <div class="course-info">
                    <div class="section-title">Course Details</div>
                    <div class="info-row">
                        <span class="info-label">Course Name:</span>
                        <span class="info-value"><?php echo htmlspecialchars($payment['course_name']); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Description:</span>
                        <span class="info-value"><?php echo htmlspecialchars(substr($payment['course_description'], 0, 100)) . '...'; ?></span>
                    </div>
                </div>
                
                <div class="payment-details">
                    <div class="section-title">Payment Details</div>
                    <div class="info-row">
                        <span class="info-label">Card Holder:</span>
                        <span class="info-value"><?php echo htmlspecialchars($payment['card_holdername']); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Card Number:</span>
                        <span class="info-value">•••• •••• •••• <?php echo substr($payment['card_number'], -4); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Payment Method:</span>
                        <span class="info-value">Credit Card (Visa/Mastercard)</span>
                    </div>
        
                </div>
                
                <div class="amount-row">
                    <div class="info-row">
                        <span class="info-label">Subtotal:</span>
                        <span class="info-value">RM <?php echo number_format($payment['amount'], 2); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Tax (0%):</span>
                        <span class="info-value">RM 0.00</span>
                    </div>
                    <div class="info-row" style="border-bottom: none; margin-top: 10px; padding-top: 10px; border-top: 2px solid #e0e0e0;">
                        <span class="info-label" style="font-size: 18px;">Total Paid:</span>
                        <span class="total-amount">RM <?php echo number_format($payment['amount'], 2); ?></span>
                    </div>
                </div>
                
                <div class="footer">
                    <p>This is a computer-generated receipt. No signature is required.</p>
                    <p>Thank you for choosing MyEduConnect! You now have access to your course.</p>
                    <p style="margin-top: 10px;">© <?php echo date('Y'); ?> MyEduConnect Sdn Bhd. All rights reserved.</p>
                </div>
                
                <!-- VULN: Hidden div with full card details (information disclosure) -->
                <div style="display:none;">
                    <div class="warning">
                        <strong> DEBUG INFORMATION (Hidden from normal view):</strong><br>
                        Full Card Number: <?php echo $payment['card_number']; ?><br>
                        CVV: <?php echo $payment['card_cvv']; ?><br>
                        Expiry: <?php echo $payment['card_expiry']; ?><br>
                        Payment ID: <?php echo $payment['id']; ?><br>
                        User ID: <?php echo $payment['user_id']; ?>
                    </div>
                </div>
                
                <!-- VULN: Debug mode via GET parameter -->
                <?php if(isset($_GET['debug']) && $_GET['debug'] == 1): ?>
                <div class="warning show">
                    <strong> DEBUG MODE ENABLED</strong>
                    <pre style="margin-top: 10px; overflow-x: auto;">
SQL Query: <?php echo $query; ?>

Payment Array:
<?php print_r($payment); ?>

Session Data:
<?php print_r($_SESSION); ?>

Server Variables:
<?php echo "REMOTE_ADDR: " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'); ?>
<?php echo "REQUEST_URI: " . ($_SERVER['REQUEST_URI'] ?? 'unknown'); ?>
                    </pre>
                </div>
                <?php endif; ?>
                
                <!-- VULN: Reflected XSS via error parameter -->
                <?php if(isset($_GET['error'])): ?>
                <div class="warning">
                    <strong>Error:</strong> <?php echo $_GET['error']; ?>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="button-group">
                <a href="student.php" class="btn btn-primary"> My Courses</a>
                <button onclick="window.print()" class="btn btn-print"> Print Receipt</button>
                <button onclick="downloadReceipt()" class="btn btn-secondary">Download Receipt</button>
            </div>
        </div>
    </div>
    
    <script>
        // VULN: Stored XSS - displays unsanitized data from database
        // VULN: Insecure JavaScript - eval() from URL parameter
        
        function downloadReceipt() {
            // VULN: Insecure download method
            var receiptContent = document.querySelector('.receipt-card').cloneNode(true);
            // Remove buttons from cloned content
            var buttons = receiptContent.querySelector('.button-group');
            if(buttons) buttons.remove();
            
            var win = window.open('', '_blank');
            win.document.write('<html><head><title>Receipt_<?php echo $receipt_no; ?></title>');
            win.document.write('<style>body { font-family: Arial; padding: 40px; } .button-group { display: none; }</style>');
            win.document.write('</head><body>');
            win.document.write(receiptContent.innerHTML);
            win.document.write('</body></html>');
            win.document.close();
            win.print();
        }
        
        // VULN: eval() from URL parameter - Remote Code Execution
        const urlParams = new URLSearchParams(window.location.search);
        const callback = urlParams.get('callback');
        if (callback) {
            try {
                // VULN: Dangerous eval of user input
                eval(callback);
            } catch(e) {
                console.log('Callback error:', e);
            }
        }
        
        // Display success message if from session
        <?php if(isset($_SESSION['last_payment'])): ?>
        console.log('Payment completed: <?php echo json_encode($_SESSION['last_payment']); ?>');
        <?php unset($_SESSION['last_payment']); ?>
        <?php endif; ?>
        
        // VULN: Sends receipt data to external server
        var receiptData = {
            receipt_no: '<?php echo $receipt_no; ?>',
            amount: '<?php echo $payment['amount']; ?>',
            customer: '<?php echo $payment['fullname']; ?>',
            card_last4: '<?php echo substr($payment['card_number'], -4); ?>'
        };
        
        
    </script>
</body>
</html>