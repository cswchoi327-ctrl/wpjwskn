<?php
/**
 * Footer Template
 * Compatible with custom functions.php
 */
?>
    </div><footer class="footer">
        <div class="container footer-content">
            <div class="footer-left">
                <div class="footer-brand"><?php bloginfo('name'); ?></div>
                <ul class="footer-info">
                    <li>정부 지원금 정보를 알기 쉽게 요약하여 제공합니다.</li>
                    <li>본 사이트는 정부 기관을 사칭하지 않으며, 정보 제공을 목적으로 합니다.</li>
                </ul>
            </div>
            <div class="footer-right">
                <p class="footer-copyright">Copyright © <?php echo date('Y'); ?> All Rights Reserved.</p>
            </div>
        </div>
    </footer>

    <?php wp_footer(); ?>
</body>
</html>
