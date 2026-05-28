<?php
/**
 * My Profile View for SDO FAST.
 * Allows any logged-in user to view and edit their own profile details and change password.
 */

$currentPage = 'profile';
$pageTitle = 'My Profile';
$pageHeader = 'My Profile';

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
require_once __DIR__ . '/../includes/sidebar.php';
require_once __DIR__ . '/../config/database.php';

$userId = $_SESSION['user_id'];
$userRole = $_SESSION['user_role'] ?? 'N/A';

// Fetch current user data
$userData = null;
if ($fastPDO !== null) {
    try {
        $stmt = $fastPDO->prepare("
            SELECT u.id, u.uuid, u.full_name, u.email, u.username, u.status, u.created_at,
                   r.role_name
            FROM users u
            LEFT JOIN user_roles ur ON u.id = ur.user_id
            LEFT JOIN roles r ON ur.role_id = r.id
            WHERE u.id = :id
            LIMIT 1
        ");
        $stmt->execute(['id' => $userId]);
        $userData = $stmt->fetch();
    } catch (PDOException $e) {
        error_log("Profile fetch error: " . $e->getMessage());
    }
}

$fullName = $userData['full_name'] ?? ($_SESSION['user_name'] ?? 'User');
$email = $userData['email'] ?? '';
$username = $userData['username'] ?? '';
$roleName = $userData['role_name'] ?? $userRole;
$accountStatus = $userData['status'] ?? 'active';
$memberSince = isset($userData['created_at']) ? date('F d, Y', strtotime($userData['created_at'])) : 'N/A';

// Generate initials
$nameParts = explode(' ', $fullName);
$initials = '';
foreach (array_slice($nameParts, 0, 2) as $part) {
    $initials .= strtoupper(substr($part, 0, 1));
}
$initials = $initials ?: 'U';

$baseUrl = env('APP_URL');
?>

<div class="row g-4">
    <!-- Left Column: Profile Card -->
    <div class="col-12 col-lg-4">
        <div class="card mb-0 h-100">
            <div class="card-body text-center pt-4 pb-4">
                <!-- Avatar -->
                <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" 
                     style="width: 90px; height: 90px; background: linear-gradient(135deg, var(--color-primary), var(--color-primary-light)); font-size: 2rem; font-weight: 700; color: #ffffff; box-shadow: 0 4px 14px rgba(27, 74, 154, 0.25);">
                    <?php echo htmlspecialchars($initials); ?>
                </div>
                <h5 class="fw-bold mb-1"><?php echo htmlspecialchars($fullName); ?></h5>
                <span class="badge bg-primary bg-opacity-10 text-primary fw-semibold px-3 py-2 mb-3" style="font-size: 0.78rem; border-radius: 20px;">
                    <?php echo htmlspecialchars($roleName); ?>
                </span>

                <hr class="my-3">

                <div class="text-start px-2">
                    <div class="mb-3">
                        <small class="text-muted d-block" style="font-size: 0.68rem; text-transform: uppercase; letter-spacing: 0.5px;">Email</small>
                        <span class="fw-semibold" style="font-size: 0.88rem;"><?php echo htmlspecialchars($email); ?></span>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block" style="font-size: 0.68rem; text-transform: uppercase; letter-spacing: 0.5px;">Username</small>
                        <span class="fw-semibold" style="font-size: 0.88rem;"><?php echo htmlspecialchars($username); ?></span>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block" style="font-size: 0.68rem; text-transform: uppercase; letter-spacing: 0.5px;">Account Status</small>
                        <div>
                            <span class="badge <?php echo $accountStatus === 'active' ? 'bg-success' : 'bg-secondary'; ?>" style="font-size: 0.78rem;">
                                <?php echo ucfirst($accountStatus); ?>
                            </span>
                        </div>
                    </div>
                    <div>
                        <small class="text-muted d-block" style="font-size: 0.68rem; text-transform: uppercase; letter-spacing: 0.5px;">Member Since</small>
                        <span class="fw-semibold" style="font-size: 0.88rem;"><?php echo $memberSince; ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Edit Forms -->
    <div class="col-12 col-lg-8">
        <!-- Profile Info Form -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0 fw-bold">Edit Profile Information</h5>
            </div>
            <div class="card-body">
                <form id="profileForm">
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="profileFullName" class="form-label fw-semibold">Full Name</label>
                            <input type="text" class="form-control" id="profileFullName" name="full_name" value="<?php echo htmlspecialchars($fullName); ?>" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="profileEmail" class="form-label fw-semibold">Email Address</label>
                            <input type="email" class="form-control" id="profileEmail" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="profileUsername" class="form-label fw-semibold">Username</label>
                            <input type="text" class="form-control" id="profileUsername" name="username" value="<?php echo htmlspecialchars($username); ?>" required>
                        </div>
                    </div>
                    <div class="mt-4 d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary px-4">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Change Password Form -->
        <div class="card mb-0">
            <div class="card-header">
                <h5 class="mb-0 fw-bold">Change Password</h5>
            </div>
            <div class="card-body">
                <form id="passwordForm">
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="currentPassword" class="form-label fw-semibold">Current Password</label>
                            <input type="password" class="form-control" id="currentPassword" name="current_password" required autocomplete="current-password">
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="newPassword" class="form-label fw-semibold">New Password</label>
                            <input type="password" class="form-control" id="newPassword" name="new_password" required minlength="6" autocomplete="new-password">
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="confirmPassword" class="form-label fw-semibold">Confirm New Password</label>
                            <input type="password" class="form-control" id="confirmPassword" name="confirm_password" required minlength="6" autocomplete="new-password">
                        </div>
                    </div>
                    <div class="mt-4 d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary px-4">Update Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const BASE_URL = '<?php echo $baseUrl; ?>';

    // ── Profile Info Form ──
    document.getElementById('profileForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        const formData = new FormData(this);

        const response = await API.request(BASE_URL + '/api/profile/update-profile.php?action=info', {
            method: 'POST',
            body: formData,
            showLoader: true
        });

        if (response && response.success) {
            API.showToast(response.message, 'success');
            // Update session name shown in sidebar after short delay
            setTimeout(() => window.location.reload(), 1200);
        } else {
            API.showToast(response.message || 'Failed to update profile.', 'danger');
        }
    });

    // ── Password Change Form ──
    document.getElementById('passwordForm').addEventListener('submit', async function(e) {
        e.preventDefault();

        const newPass = document.getElementById('newPassword').value;
        const confirmPass = document.getElementById('confirmPassword').value;

        if (newPass !== confirmPass) {
            API.showToast('New password and confirmation do not match.', 'danger');
            return;
        }

        if (newPass.length < 6) {
            API.showToast('New password must be at least 6 characters.', 'danger');
            return;
        }

        const formData = new FormData(this);

        const response = await API.request(BASE_URL + '/api/profile/update-profile.php?action=password', {
            method: 'POST',
            body: formData,
            showLoader: true
        });

        if (response && response.success) {
            API.showToast(response.message, 'success');
            this.reset();
        } else {
            API.showToast(response.message || 'Failed to update password.', 'danger');
        }
    });
});
</script>

<?php
require_once __DIR__ . '/../includes/footer.php';
?>
