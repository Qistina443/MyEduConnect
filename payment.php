<?php
session_start();
include 'config.php';

// VULN: No proper authentication check
if (!isset($_SESSION['user_id'])) {
    // Weak redirect
    header('Location: login.php');
    // VULN: No exit after header
}

// VULN: SQL Injection - Fetch all courses for dropdown (no filtering by active status)
// This query excludes soft-deleted courses if you have deleted_courses table
$query = "SELECT * FROM courses 
          WHERE id NOT IN (SELECT course_id FROM deleted_courses)
          ORDER BY course_name ASC";
$result = mysqli_query($conn, $query);

// Handle form submission
$selected_course = null;
$error = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['course_id'])) {
    $course_id = $_POST['course_id'];
    
    // VULN: SQL Injection in course fetch
    $course_query = "SELECT * FROM courses WHERE id = $course_id";
    $course_result = mysqli_query($conn, $course_query);
    $selected_course = mysqli_fetch_assoc($course_result);
    
    if (!$selected_course) {
        $error = "Course not found!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment - MyEduConnect</title>
    <link rel="stylesheet" href="assets/style.css?v=1000">
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
        
        .container {
            max-width: 800px;
            margin: 0 auto;
        }
        
        .payment-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
        }
        
        .payment-header {
            background: linear-gradient(135deg, #264adc 0%, #1a3a8a 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .payment-header h1 {
            font-size: 28px;
            margin-bottom: 10px;
        }
        
        .payment-header p {
            opacity: 0.9;
        }
        
        .payment-body {
            padding: 30px;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            color: #333;
        }
        
        select, input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 16px;
            transition: all 0.3s;
        }
        
        select:focus, input:focus {
            outline: none;
            border-color: #264adc;
        }
        
        .course-preview {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 20px;
            margin: 20px 0;
            display: none;
        }
        
        .course-preview.show {
            display: block;
            animation: fadeIn 0.5s;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .course-title {
            font-size: 20px;
            font-weight: bold;
            color: #264adc;
            margin-bottom: 10px;
        }
        
        .course-description {
            color: #666;
            margin-bottom: 15px;
            line-height: 1.5;
        }
        
        .course-price {
            font-size: 28px;
            font-weight: bold;
            color: #28a745;
        }
        
        .course-price small {
            font-size: 14px;
            font-weight: normal;
            color: #666;
        }
        
        .payment-details {
            margin-top: 25px;
            padding-top: 25px;
            border-top: 2px dashed #e0e0e0;
        }
        
        .payment-details h3 {
            margin-bottom: 20px;
            color: #333;
        }
        
        .card-input {
            position: relative;
        }
        
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        
        button.payment-btn {
            width: 100%;
            background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%);
            color: white;
            border: none;
            padding: 15px;
            border-radius: 10px;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 20px;
        }
        
        button.payment-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
        }
        
        button.payment-btn:disabled {
            background: #ccc;
            cursor: not-allowed;
        }
        
        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #f5c6cb;
        }
        
        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #264adc;
            text-decoration: none;
        }
        
        .back-link:hover {
            text-decoration: underline;
        }
        
        footer {
            text-align: center;
            margin-top: 20px;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="payment-card">
            <div class="payment-header">
                <h1> Payment Portal</h1>
                <p>Select a course to continue with payment</p>
            </div>
            
            <div class="payment-body">
                <?php if($error): ?>
                    <div class="error-message">
                         <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>
                
                <!-- Course Selection Form -->
                <form method="POST" action="" id="courseSelectForm">
                    <div class="form-group">
                        <label>📚 Select Course to Pay For</label>
                        <select name="course_id" id="courseSelect" required>
                            <option value="">-- Choose a course --</option>
                            <?php while($course = mysqli_fetch_assoc($result)): ?>
                                <option value="<?php echo $course['id']; ?>" 
                                        data-price="<?php echo $course['amount']; ?>"
                                        data-title="<?php echo htmlspecialchars($course['course_name']); ?>"
                                        data-description="<?php echo htmlspecialchars($course['description']); ?>">
                                    <?php echo htmlspecialchars($course['course_name']); ?> - RM <?php echo number_format($course['amount'], 2); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    
                    <button type="submit" class="payment-btn" id="viewCourseBtn">
                        View Course Details
                    </button>
                </form>
                
                <!-- Course Preview Section -->
                <div id="coursePreview" class="course-preview">
                    <?php if($selected_course): ?>
                        <div class="course-title"> <?php echo htmlspecialchars($selected_course['course_name']); ?></div>
                        <div class="course-description"><?php echo nl2br(htmlspecialchars($selected_course['description'])); ?></div>
                        <div class="course-price">RM <?php echo number_format($selected_course['amount'], 2); ?> <small>one-time payment</small></div>
                    <?php endif; ?>
                </div>
                
                <!-- Payment Form (shows only when course is selected) -->
                <div id="paymentForm" style="display: <?php echo $selected_course ? 'block' : 'none'; ?>;">
                    <div class="payment-details">
                        <h3>💳 Payment Details</h3>
                        
                        <!-- VULN: Form submits over HTTP (no TLS) -->
                        <!-- VULN: No CSRF token -->
                        <form method="POST" action="paymentprocess.php" id="paymentSubmitForm">
                            <input type="hidden" name="course_id" id="hiddenCourseId" value="<?php echo $selected_course['id'] ?? ''; ?>">
                            <input type="hidden" name="amount" id="hiddenAmount" value="<?php echo $selected_course['amount'] ?? ''; ?>">
                            <input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id']; ?>">
                            
                            <div class="form-group">
                                <label>Cardholder Name</label>
                                <input type="text" name="card_name" placeholder="Name on card" required 
                                       value="<?php echo isset($_SESSION['name']) ? htmlspecialchars($_SESSION['fullname']) : ''; ?>">
                            </div>
                            
                            <div class="form-group">
                                <label>Card Number</label>
                                <div class="card-input">
                                    <input type="text" name="card_number" placeholder="1234 5678 9012 3456" 
                                           maxlength="19" required>
                                 
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Expiry Date</label>
                                    <input type="text" name="expiry" placeholder="MM/YY" required>
                                </div>
                                <div class="form-group">
                                    <label>CVV</label>
                                    <input type="text" name="cvv" placeholder="123" maxlength="4" required>
                                </div>
                            </div>
                            
                            <button type="submit" class="payment-btn">
                                 Pay Now
                            </button>
                        </form>
                    </div>
                </div>
                
                <a href="dashboard.php" class="back-link">← Back to Dashboard</a>
            </div>
        </div>
        <footer>
            <small>Secure payment powered by MyEduConnect </small>
        </footer>
    </div>
    
    <script>
        // Get DOM elements
        const courseSelect = document.getElementById('courseSelect');
        const viewCourseBtn = document.getElementById('viewCourseBtn');
        const coursePreview = document.getElementById('coursePreview');
        const paymentFormDiv = document.getElementById('paymentForm');
        const hiddenCourseId = document.getElementById('hiddenCourseId');
        const hiddenAmount = document.getElementById('hiddenAmount');
        
        // VULN: Client-side validation only (easily bypassed)
        // VULN: No CSRF protection
        
        // Handle course selection and preview
        viewCourseBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
            const selectedOption = courseSelect.options[courseSelect.selectedIndex];
            const courseId = courseSelect.value;
            
            if (!courseId) {
                alert('Please select a course first!');
                return;
            }
            
            // Get course data from selected option
            const courseTitle = selectedOption.getAttribute('data-title');
            const courseDescription = selectedOption.getAttribute('data-description');
            const coursePrice = selectedOption.getAttribute('data-price');
            
            // Update preview section
            coursePreview.innerHTML = `
                <div class="course-title">📘 ${escapeHtml(courseTitle)}</div>
                <div class="course-description">${escapeHtml(courseDescription)}</div>
                <div class="course-price">RM ${parseFloat(coursePrice).toFixed(2)} <small>one-time payment</small></div>
            `;
            coursePreview.classList.add('show');
            
            // Update hidden fields
            hiddenCourseId.value = courseId;
            hiddenAmount.value = coursePrice;
            
            // Show payment form
            paymentFormDiv.style.display = 'block';
            
            // Scroll to payment form
            paymentFormDiv.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
        
        // Escape HTML to prevent XSS (but VULN: still possible via other vectors)
        function escapeHtml(str) {
            if (!str) return '';
            return str.replace(/[&<>]/g, function(m) {
                if (m === '&') return '&amp;';
                if (m === '<') return '&lt;';
                if (m === '>') return '&gt;';
                return m;
            });
        }
        
        // Format card number input
        const cardNumberInput = document.querySelector('input[name="card_number"]');
        if (cardNumberInput) {
            cardNumberInput.addEventListener('input', function(e) {
                let value = this.value.replace(/\D/g, '');
                if (value.length > 16) value = value.slice(0, 16);
                let formatted = '';
                for (let i = 0; i < value.length; i++) {
                    if (i > 0 && i % 4 === 0) formatted += ' ';
                    formatted += value[i];
                }
                this.value = formatted;
            });
        }
        
        // Validate expiry date format
        const expiryInput = document.querySelector('input[name="expiry"]');
        if (expiryInput) {
            expiryInput.addEventListener('input', function(e) {
                let value = this.value.replace(/\D/g, '');
                if (value.length >= 2) {
                    value = value.slice(0, 2) + '/' + value.slice(2, 4);
                }
                this.value = value;
            });
        }
        
        // VULN: Client-side validation only
        document.getElementById('paymentSubmitForm')?.addEventListener('submit', function(e) {
            const cardNum = document.querySelector('input[name="card_number"]').value.replace(/\s/g, '');
            const cvv = document.querySelector('input[name="cvv"]').value;
            const expiry = document.querySelector('input[name="expiry"]').value;
            
            if (cardNum.length < 16) {
                alert('Please enter a valid 16-digit card number');
                e.preventDefault();
                return false;
            }
            
            if (cvv.length < 3) {
                alert('Please enter a valid CVV');
                e.preventDefault();
                return false;
            }
            
            if (!expiry.match(/^\d{2}\/\d{2}$/)) {
                alert('Please enter expiry date in MM/YY format');
                e.preventDefault();
                return false;
            }
            
            // VULN: No actual payment validation
            return true;
        });
    </script>
</body>
</html>