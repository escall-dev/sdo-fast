<?php
/**
 * Employee Registration Page for SDO FAST.
 * Matches requested DepEd dark card theme layout over a #081121 background.
 */

require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/config/env.php';

// Redirect if already logged in
if (isLoggedIn()) {
    header('Location: ' . env('APP_URL') . '/views/dashboard/index.php');
    exit;
}

// Generate CSRF token for the registration form
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account | SDO FAST</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        :root {
            --primary: #1b4a9a;
            --primary-light: #1b4a9a;
            --primary-dark: #1b4a9a;
            --accent: #1b4a9a;
            --gold: #d4af37;
            --bg-dark: #0a1628;
            --bg-card: #111d2e;
            --text: #e8f1f8;
            --text-muted: #7a9bb8;
            --border: rgba(27, 74, 154, 0.12);
            --error: #ef4444;
            --success: #10b981;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--bg-dark);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .register-container {
            width: 100%;
            max-width: 420px;
        }
        
        .register-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 24px 24px;
            backdrop-filter: blur(20px);
        }
        
        .register-header {
            text-align: center;
            margin-bottom: 24px;
        }

        .logo-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 76px;
            height: 76px;
            background: transparent;
            border-radius: 50%;
            margin-bottom: 12px;
            position: relative;
            overflow: hidden;
        }

        .logo-badge img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
        }
        
        .register-header h1 {
            color: var(--text);
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 6px;
        }
        
        .register-header p {
            color: var(--text-muted);
            font-size: 0.8rem;
            line-height: 1.5;
        }
        
        .form-group {
            margin-bottom: 14px;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }
        
        .form-label {
            display: block;
            color: var(--text);
            font-size: 0.75rem;
            font-weight: 500;
            margin-bottom: 4px;
        }
        
        .form-label .required {
            color: var(--error);
        }
        
        .form-control {
            width: 100%;
            padding: 8px 10px;
            font-size: 0.85rem;
            font-family: inherit;
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid var(--border);
            border-radius: 8px;
            color: var(--text);
            transition: all 0.2s ease;
        }
        
        .form-control::placeholder {
            color: var(--text-muted);
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--primary-light);
            background: rgba(0, 0, 0, 0.4);
        }
        
        select.form-control {
            cursor: pointer;
        }
        
        .error-message {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #fca5a5;
            padding: 12px 16px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .info-box {
            background: rgba(27, 74, 154, 0.12);
            border: 1px solid rgba(27, 74, 154, 0.28);
            color: rgba(255, 255, 255, 0.9);
            padding: 12px 16px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 0.8rem;
        }
        
        .info-box i {
            margin-right: 8px;
        }
        
        .btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            width: 100%;
            padding: 10px 16px;
            font-size: 0.9rem;
            font-weight: 600;
            font-family: inherit;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s ease;
            border: none;
            text-decoration: none;
            margin-top: 6px;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-light) 0%, var(--primary) 100%);
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(27, 74, 154, 0.4);
        }
        
        .btn-secondary {
            background: rgba(255, 255, 255, 0.05);
            color: var(--text);
            border: 1px solid var(--border);
            margin-top: 12px;
        }
        
        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.1);
        }
        
        .form-hint {
            display: block;
            font-size: 0.75rem;
            color: #9cb4c9;
            margin-top: 6px;
        }
        
        @media (max-width: 640px) {
            body {
                padding: 14px;
                align-items: flex-start;
            }

            .register-container {
                max-width: 380px;
            }

            .register-card {
                border-radius: 16px;
                padding: 20px 16px;
            }

            .register-header {
                margin-bottom: 16px;
            }

            .register-header h1 {
                font-size: 1.2rem;
                margin-bottom: 6px;
            }

            .register-header p {
                font-size: 0.77rem;
            }

            .form-row {
                grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
                gap: 10px;
            }

            .form-group {
                margin-bottom: 12px;
            }

            .form-label {
                font-size: 0.75rem;
                margin-bottom: 5px;
            }

            .form-control {
                padding: 9px 10px;
                font-size: 0.85rem;
                border-radius: 8px;
            }

            .form-hint {
                font-size: 0.68rem;
            }

            .info-box,
            .error-message {
                padding: 10px 12px;
                border-radius: 8px;
                margin-bottom: 14px;
                font-size: 0.74rem;
            }

            .btn {
                margin-top: 6px;
                padding: 10px 12px;
                font-size: 0.9rem;
                border-radius: 9px;
            }

            .btn-secondary {
                margin-top: 10px;
            }
        }

        @media (max-width: 380px) {
            .register-card {
                padding: 18px 14px;
            }

            .form-row {
                gap: 8px;
            }

            .register-header h1 {
                font-size: 1.1rem;
            }

            .form-control,
            .btn {
                font-size: 0.82rem;
            }
        }

        .otp-display-banner {
            background-color: rgba(33, 77, 162, 0.15);
            border: 1px dashed rgba(33, 77, 162, 0.4);
            border-radius: 8px;
            color: #93c5fd;
            font-size: 0.85rem;
            padding: 10px 15px;
            margin-bottom: 20px;
            text-align: center;
        }

        .page-footer {
            margin-top: 1.5rem;
            text-align: center;
            color: rgba(255, 255, 255, 0.4);
            font-size: 0.75rem;
        }
    </style>
</head>
<body>

<div class="register-container">
    <div class="register-card">
        
        <!-- STEP 1: Registration Form -->
        <div id="registerStep">
            <div class="register-header">
                <h1>SDO FAST</h1>
                <p>Create Account</p>
                <p style="margin-top: 6px;">Register as an SDO Employee to file travel requests</p>
            </div>

            <div class="info-box">
                <i class="fas fa-info-circle"></i>
                An OTP will be sent to your email for verification. After verifying, your account will be created and ready to use immediately.
            </div>

            <div id="registerAlert" class="error-message" style="display: none;">
                <i class="fas fa-exclamation-triangle"></i>
                <span id="registerAlertText"></span>
            </div>

            <form id="registrationForm" onsubmit="handleRegisterSubmit(event)">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="fullNameInput" class="form-label">Full Name <span class="required">*</span></label>
                        <input type="text" name="full_name" class="form-control" id="fullNameInput" placeholder="Juan Dela Cruz" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="emailInput" class="form-label">Email <span class="required">*</span></label>
                        <input type="email" name="email" class="form-control" id="emailInput" placeholder="user@deped.gov.ph" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="officeSelect" class="form-label">Office <span class="required">*</span></label>
                        <select name="office" class="form-control" id="officeSelect" onchange="handleOfficeChange()" required>
                            <option value="">-- Select Office --</option>
                            <option value="OSDS">OSDS (Office of the SDS Staff)</option>
                            <option value="SGOD">SGOD (School Governance and Operations Division)</option>
                            <option value="CID">CID (Curriculum Implementation Division)</option>
                        </select>
                        <span class="form-hint">OSDS, SGOD, or CID</span>
                    </div>
                    
                    <div class="form-group">
                        <label for="unitSelect" class="form-label">Unit/Section <span class="required">*</span></label>
                        <select name="unit_section" class="form-control" id="unitSelect" required disabled>
                            <option value="">-- Select Unit/Section --</option>
                        </select>
                        <span id="unitSubtext" class="form-hint">Select an Office first</span>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="passwordInput" class="form-label">Password <span class="required">*</span></label>
                        <input type="password" name="password" class="form-control" id="passwordInput" placeholder="Min. 8 characters" required minlength="8">
                        <span class="form-hint">Minimum 8 characters</span>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirmPasswordInput" class="form-label">Confirm Password <span class="required">*</span></label>
                        <input type="password" name="confirm_password" class="form-control" id="confirmPasswordInput" placeholder="Re-enter password" required minlength="8">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="employeeNoInput" class="form-label">Employee No. <span class="required">*</span></label>
                        <input type="text" name="employee_no" class="form-control" id="employeeNoInput" placeholder="E-12345" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="positionInput" class="form-label">Position <span class="required">*</span></label>
                        <input type="text" name="position" class="form-control" id="positionInput" placeholder="Teacher I" required>
                    </div>
                </div>

                <button type="submit" id="btnRegisterSubmit" class="btn btn-primary">
                    <i class="fas fa-envelope"></i> <span>Verify Email &amp; Register</span>
                </button>
                
                <a href="login.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Login
                </a>
            </form>
        </div>

        <!-- STEP 2: OTP Verification Card (Initially Hidden) -->
        <div id="otpStep" style="display: none;">
            <div class="register-header">
                <h1><i class="fas fa-shield-alt" style="color: var(--gold);"></i> Email Verification</h1>
                <p>Enter the One-Time Password (OTP) sent to your email</p>
            </div>

            <!-- Dev OTP Display (Shows up for local testing convenience) -->
            <div id="devOtpBanner" class="otp-display-banner" style="display: none;"></div>

            <div id="otpAlert" class="error-message" style="display: none;">
                <i class="fas fa-exclamation-triangle"></i>
                <span id="otpAlertText"></span>
            </div>

            <form id="otpForm" onsubmit="handleOtpSubmit(event)">
                <div class="form-group" style="text-align: center; margin-bottom: 24px;">
                    <label for="otpCodeInput" class="form-label" style="text-align: center;">Enter Verification Code</label>
                    <input type="text" class="form-control" id="otpCodeInput" placeholder="123456" maxlength="6" pattern="\d{6}" required style="font-size: 1.5rem; letter-spacing: 0.4rem; text-align: center; max-width: 200px; margin: 0 auto;">
                    <span class="form-hint" style="text-align: center;">Check your email client or look for <code>otp_log.txt</code> inside the scratch directory.</span>
                </div>

                <button type="submit" id="btnOtpSubmit" class="btn btn-primary">
                    <i class="fas fa-check-circle"></i> <span>Verify &amp; Complete Registration</span>
                </button>
                
                <button type="button" class="btn btn-secondary" onclick="goBackToRegister()">
                    <i class="fas fa-arrow-left"></i> Cancel &amp; Back
                </button>
            </form>
        </div>

    </div>
    
    <!-- Footer -->
    <div class="page-footer">
        <div>DepEd — Schools Division Office of San San Pedro City</div>
        <div style="opacity: 0.5; font-size: 0.7rem; margin-top: 4px;">© 2026 ICT Unit</div>
    </div>
</div>

<script>
// Dynamic Dropdowns Configuration
const officeUnits = {
    "OSDS": [
        "OSDS (Office of the SDS Staff)",
        "OASDS (Office of the ASDS Staff)",
        "Personnel",
        "Property and Supply",
        "Records",
        "Cash",
        "Procurement",
        "General Services",
        "Legal",
        "ICT",
        "Accounting (Finance - Accounting)",
        "Budget (Finance - Budget)",
        "Administrative"
    ],
    "SGOD": [
        "SGOD (School Governance and Operations Division)",
        "SMME (School Management Monitoring and Evaluation)",
        "HRD (Human Resource Development)",
        "SMN (Social Mobilization and Networking)",
        "PR (Planning and Research)",
        "DRRM (Disaster Risk Reduction and Management)",
        "EF (Education Facilities)",
        "SHN_DENTAL (School Health and Nutrition - Dental)",
        "SHN_MEDICAL (School Health and Nutrition - Medical)",
        "SHN (School Health and Nutrition)"
    ],
    "CID": [
        "CID (Curriculum Implementation Division)",
        "IM (Instructional Management)",
        "LRM (Learning Resource Management)",
        "ALS (Alternative Learning System)",
        "DIS (District Instructional Supervision)"
    ]
};

function handleOfficeChange() {
    const officeSelect = document.getElementById('officeSelect');
    const unitSelect = document.getElementById('unitSelect');
    const unitSubtext = document.getElementById('unitSubtext');
    
    const selectedOffice = officeSelect.value;
    
    // Clear current items
    unitSelect.innerHTML = '<option value="">-- Select Unit/Section --</option>';
    
    if (selectedOffice && officeUnits[selectedOffice]) {
        unitSelect.removeAttribute('disabled');
        unitSubtext.textContent = 'Choose matching division section';
        
        // Populates elements
        officeUnits[selectedOffice].forEach(unit => {
            const opt = document.createElement('option');
            opt.value = unit;
            opt.textContent = unit;
            unitSelect.appendChild(opt);
        });
    } else {
        unitSelect.setAttribute('disabled', 'true');
        unitSubtext.textContent = 'Select an Office first';
    }
}

// Handler for registration form submission
function handleRegisterSubmit(event) {
    event.preventDefault();
    
    const alertBox = document.getElementById('registerAlert');
    const alertText = document.getElementById('registerAlertText');
    const submitBtn = document.getElementById('btnRegisterSubmit');
    
    // Hide alert
    alertBox.style.display = 'none';
    
    // Validate passwords matching
    const pass = document.getElementById('passwordInput').value;
    const confirmPass = document.getElementById('confirmPasswordInput').value;
    
    if (pass !== confirmPass) {
        alertText.textContent = "Passwords do not match.";
        alertBox.style.display = 'flex';
        return;
    }
    
    if (pass.length < 8) {
        alertText.textContent = "Password must be at least 8 characters.";
        alertBox.style.display = 'flex';
        return;
    }
    
    // Gather form data
    const formData = new FormData(document.getElementById('registrationForm'));
    
    // Disable button to prevent double-submit
    submitBtn.setAttribute('disabled', 'true');
    submitBtn.querySelector('span').textContent = 'Sending Verification Code...';
    
    fetch('api/auth/register-request.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        submitBtn.removeAttribute('disabled');
        submitBtn.querySelector('span').textContent = 'Verify Email & Register';
        
        if (data.success) {
            // Success, transfer view to OTP verification step
            document.getElementById('registerStep').style.display = 'none';
            document.getElementById('otpStep').style.display = 'block';
            
            // Show dev OTP banner if local env output it
            const devBanner = document.getElementById('devOtpBanner');
            if (data.dev_otp) {
                devBanner.innerHTML = `<i class="fas fa-bug" style="margin-right: 6px;"></i><strong>[Local Dev Mode]</strong> Generated verification OTP: <code>${data.dev_otp}</code>`;
                devBanner.style.display = 'block';
            } else {
                devBanner.style.display = 'none';
            }
        } else {
            // Show error message
            alertText.textContent = data.message || "An error occurred. Please try again.";
            alertBox.style.display = 'flex';
        }
    })
    .catch(err => {
        submitBtn.removeAttribute('disabled');
        submitBtn.querySelector('span').textContent = 'Verify Email & Register';
        alertText.textContent = "Unable to connect to registration server.";
        alertBox.style.display = 'flex';
        console.error(err);
    });
}

// Handler for OTP verification form submission
function handleOtpSubmit(event) {
    event.preventDefault();
    
    const alertBox = document.getElementById('otpAlert');
    const alertText = document.getElementById('otpAlertText');
    const submitBtn = document.getElementById('btnOtpSubmit');
    const otpCode = document.getElementById('otpCodeInput').value;
    
    alertBox.style.display = 'none';
    
    if (otpCode.length !== 6 || isNaN(otpCode)) {
        alertText.textContent = "Please enter a valid 6-digit OTP code.";
        alertBox.style.display = 'flex';
        return;
    }
    
    submitBtn.setAttribute('disabled', 'true');
    submitBtn.querySelector('span').textContent = 'Verifying Account...';
    
    const formData = new FormData();
    formData.append('otp', otpCode);
    
    fetch('api/auth/verify-register.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        submitBtn.removeAttribute('disabled');
        submitBtn.querySelector('span').textContent = 'Verify & Complete Registration';
        
        if (data.success) {
            // Success redirect back to login page
            window.location.href = 'login.php';
        } else {
            // Show verification error
            alertText.textContent = data.message || "Verification failed.";
            alertBox.style.display = 'flex';
        }
    })
    .catch(err => {
        submitBtn.removeAttribute('disabled');
        submitBtn.querySelector('span').textContent = 'Verify & Complete Registration';
        alertText.textContent = "Unable to connect to verification server.";
        alertBox.style.display = 'flex';
        console.error(err);
    });
}

function goBackToRegister() {
    document.getElementById('otpStep').style.display = 'none';
    document.getElementById('registerStep').style.display = 'block';
}
</script>

</body>
</html>
