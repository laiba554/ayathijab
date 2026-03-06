<style>
/* Responsive Footer Styles */
@media (max-width: 768px) {
    .footer-content {
        flex-direction: column;
        gap: 30px;
        text-align: center;
    }

    .footer-section {
        width: 100% !important;
        max-width: 100% !important;
        flex: none !important;
    }

    .footer-brand {
        padding-bottom: 20px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .footer-description {
        margin: 15px auto !important;
        max-width: 300px;
    }

    .footer-socials {
        justify-content: center !important;
        margin-top: 15px;
    }

    .footer-contact h3 {
        margin-bottom: 15px;
    }

    .footer-contact p {
        margin: 10px 0;
    }

    .footer-bottom {
        padding: 15px 0 !important;
        font-size: 13px;
    }
}

@media (max-width: 576px) {
    footer {
        padding: 30px 0 !important;
    }

    .footer-content {
        gap: 25px;
    }

    .footer-brand .logo {
        font-size: 20px !important;
    }

    .footer-description {
        font-size: 14px;
        line-height: 1.6;
    }

    .footer-socials a {
        width: 35px !important;
        height: 35px !important;
        font-size: 16px !important;
    }

    .footer-contact h3 {
        font-size: 18px;
    }

    .footer-contact p {
        font-size: 14px;
    }

    .footer-contact i {
        margin-right: 8px;
    }

    .footer-bottom {
        font-size: 12px;
    }
}
</style>

<footer>
    <div class="container">
        <div class="footer-content">
            <!-- Brand Info & Social Links -->
            <div class="footer-section footer-brand">
                <!-- <a href="<?php echo BASE_URL; ?>index.php" class="logo">
                    AYYATULHIJAB
                </a> -->
                <p class="footer-description">
                    Curating premium modest wear with timeless elegance and quality for the modern woman.
                </p>
                <div class="footer-socials">
                    <a href="https://www.instagram.com/ayaatulhijab?igsh=MXF4bGpkazk1MW81YQ=="><i class="fab fa-instagram"></i></a>
                    <a href="https://www.tiktok.com/@aqsi423?_r=1&_t=ZS-93VAQfkdFiS"><i class="fab fa-tiktok"></i></a>
                </div>
            </div>

            <!-- Contact Info -->
            <div class="footer-section footer-contact">
                <h3>Connect</h3>
                <p><i class="fas fa-envelope"></i> ayaatulhijab@gmail.com</p>
                <p><i class="fas fa-map-marker-alt"></i> Karachi, Pakistan</p>
            </div>
        </div>

        <div class="footer-bottom">
            &copy; <?php echo date('Y'); ?> AYYATULHIJAB. All rights reserved.
        </div>
    </div>
</footer>