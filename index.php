<?php
/**
 * Main Template
 */
get_header();

$cards = get_support_cards();
$config = get_site_config();
?>

<div class="container">
    <!-- 상단 인트로 -->
    <div class="intro-section">
        <span class="intro-badge">신청마감 D-3일</span>
        <p class="intro-sub">숨은 보험금 1분만에 찾기!</p>
        <h2 class="intro-title">숨은 지원금 찾기</h2>
    </div>

    <!-- 정보 박스 -->
    <div class="info-box">
        <div class="info-box-header">
            <span class="info-box-icon">🏷️</span>
            <span class="info-box-title">신청 안하면 절대 못 받아요</span>
        </div>
        <div class="info-box-amount">1인 평균 127만원 환급</div>
        <p class="info-box-desc">대한민국 92%가 놓치고 있는 정부 지원금! 지금 확인하고 혜택 놓치지 마세요.</p>
    </div>

    <!-- 카드 그리드 -->
    <div class="info-card-grid">
        <?php
        $ad_positions = array(0, 3, 6); // 광고 위치
        foreach ($cards as $index => $card) {
            // 광고 카드 (필요시 추가)
            if (in_array($index, $ad_positions)) {
                // 여기에 광고 코드 추가 가능
            }

            $featured_class = $card['featured'] ? ' featured' : '';
            ?>
            <!-- <?php echo esc_html($card['keyword']); ?> 카드 -->
            <a class="info-card<?php echo $featured_class; ?>" href="<?php echo esc_url($card['link']); ?>">
                <div class="info-card-highlight">
                    <?php if ($card['featured']): ?>
                        <span class="info-card-badge">🔥 인기</span>
                    <?php endif; ?>
                    <div class="info-card-amount"><?php echo esc_html($card['amount']); ?></div>
                    <div class="info-card-amount-sub"><?php echo esc_html($card['amountSub']); ?></div>
                </div>
                <div class="info-card-content">
                    <h3 class="info-card-title"><?php echo esc_html($card['keyword']); ?></h3>
                    <p class="info-card-desc"><?php echo esc_html($card['description']); ?></p>
                    <div class="info-card-details">
                        <div class="info-card-row">
                            <span class="info-card-label">지원대상</span>
                            <span class="info-card-value"><?php echo esc_html($card['target']); ?></span>
                        </div>
                        <div class="info-card-row">
                            <span class="info-card-label">신청시기</span>
                            <span class="info-card-value"><?php echo esc_html($card['period']); ?></span>
                        </div>
                    </div>
                    <div class="info-card-btn">
                        지금 바로 신청하기 <span class="btn-arrow">→</span>
                    </div>
                </div>
            </a>
            <?php
        }
        ?>
    </div>

    <!-- 히어로 섹션 -->
    <div class="hero-section">
        <div class="hero-content">
            <span class="hero-urgent">🔥 신청마감 D-3일</span>
            <p class="hero-sub">숨은 지원금 1분만에 찾기!</p>
            <h2 class="hero-title">
                나의 <span class="hero-highlight">숨은 지원금</span> 찾기
            </h2>
            <p class="hero-amount">신청자 <strong>1인 평균 127만원</strong> 수령</p>
            <a class="hero-cta" href="<?php echo esc_url($config['default_link']); ?>">
                30초만에 내 지원금 확인 <span>→</span>
            </a>
            <div class="hero-trust">
                <span class="trust-item">✓ 무료 조회</span>
                <span class="trust-item">✓ 30초 완료</span>
                <span class="trust-item">✓ 개인정보 보호</span>
            </div>
            <div class="hero-notice">
                <div class="notice-title">💡 신청 안하면 못 받아요</div>
                <p class="notice-desc">대한민국 92%가 놓치고 있는 정부 지원금, 지금 확인하고 혜택 놓치지 마세요!</p>
            </div>
        </div>
    </div>
</div>

<?php get_footer(); ?>
