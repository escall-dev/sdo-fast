<?php
/**
 * Contact Us page for SDO FAST.
 * Provides quick access to the ICT Helpdesk Support and Client Satisfaction Measurement survey.
 */

$currentPage = 'contact';
$pageTitle = 'Contact Us';
$pageHeader = 'Contact Us';

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
require_once __DIR__ . '/../includes/sidebar.php';

$baseUrl = env('APP_URL');
?>

<!-- Custom Premium CSS for Contact Us Cards -->
<style>
.contact-container {
    max-width: 1000px;
    margin: 0 auto;
    padding: 1.5rem 0;
}
.contact-card {
    border-radius: 14px;
    background-color: #ffffff;
    border: 1px solid #e2e8f0;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.02);
    transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.25s ease;
    height: 100%;
    overflow: hidden;
}
.contact-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 28px rgba(15, 45, 92, 0.07);
}
.contact-card-header {
    background-color: #ffffff;
    border-bottom: 1px solid #f1f5f9;
    padding: 1.1rem 1.5rem;
    font-size: 0.95rem;
    font-weight: 700;
    color: var(--text-main);
    display: flex;
    align-items: center;
    gap: 8px;
}
.contact-card-body {
    padding: 3rem 1.75rem;
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
}
.contact-icon-circle {
    width: 72px;
    height: 72px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 1.5rem;
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.04);
    transition: transform 0.25s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}
.contact-card:hover .contact-icon-circle {
    transform: scale(1.08);
}
.contact-icon-circle.helpdesk {
    background-color: #1b4a9a;
    color: #ffffff;
}
.contact-icon-circle.csm {
    background-color: #10b981;
    color: #ffffff;
}
.contact-card-title {
    font-size: 1.45rem;
    font-weight: 700;
    color: var(--color-primary-dark);
    margin-bottom: 0.75rem;
    letter-spacing: -0.2px;
}
.contact-card-text {
    font-size: 0.92rem;
    color: var(--text-muted);
    line-height: 1.6;
    margin-bottom: 2rem;
    max-width: 330px;
}
.btn-contact {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    font-weight: 600;
    font-size: 0.92rem;
    padding: 0.65rem 1.75rem;
    border-radius: 6px;
    min-height: 44px;
    text-decoration: none;
    transition: all 0.25s ease;
    border: none;
}
.btn-contact.helpdesk-btn {
    background-color: #1b4a9a;
    color: #ffffff;
}
.btn-contact.helpdesk-btn:hover {
    background-color: #113470;
    color: #ffffff;
    box-shadow: 0 6px 18px rgba(27, 74, 154, 0.2);
}
.btn-contact.csm-btn {
    background-color: #10b981;
    color: #ffffff;
}
.btn-contact.csm-btn:hover {
    background-color: #0d9668;
    color: #ffffff;
    box-shadow: 0 6px 18px rgba(16, 185, 129, 0.2);
}
</style>

<div class="contact-container container-fluid" id="contactUsContainer">
    <div class="row justify-content-center g-4">
        <!-- ICT Helpdesk Support Card -->
        <div class="col-12 col-md-6 col-lg-5">
            <div class="contact-card" id="cardHelpdesk">
                <div class="contact-card-header">
                    <span>🎧</span>
                    <span>Need Help?</span>
                </div>
                <div class="contact-card-body">
                    <div class="contact-icon-circle helpdesk">
                        <span class="fw-bold" style="font-size: 2.2rem; line-height: 1; font-family: 'Plus Jakarta Sans', sans-serif;">?</span>
                    </div>
                    <h4 class="contact-card-title">ICT Helpdesk Support</h4>
                    <p class="contact-card-text">
                        For technical difficulties and system concerns, connect with our ICT Helpdesk through the support portal.
                    </p>
                    <a href="https://wfh-sdospc.com/ICTHelpdesk-Online/login.php" target="_blank" rel="noopener noreferrer" class="btn-contact helpdesk-btn" id="btnHelpdeskConnect">
                        <i class="bi bi-box-arrow-up-right"></i>
                        <span>Connect with Us</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Client Satisfaction Measurement Card -->
        <div class="col-12 col-md-6 col-lg-5">
            <div class="contact-card" id="cardClientSatisfaction">
                <div class="contact-card-header">
                    <span>⭐</span>
                    <span>Client Satisfaction</span>
                </div>
                <div class="contact-card-body">
                    <div class="contact-icon-circle csm">
                        <i class="bi bi-star-fill" style="font-size: 1.8rem; line-height: 1;"></i>
                    </div>
                    <h4 class="contact-card-title">Client Satisfaction Measurement</h4>
                    <p class="contact-card-text">
                        Your feedback helps us improve the system. Please share your experience through our survey form.
                    </p>
                    <a href="https://wfh-sdospc.com/csm/csm.php" target="_blank" rel="noopener noreferrer" class="btn-contact csm-btn" id="btnTakeSurvey">
                        <i class="bi bi-clipboard2-check"></i>
                        <span>Take the Survey</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . '/../includes/footer.php';
?>
