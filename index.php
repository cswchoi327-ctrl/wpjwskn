<?php
/**
 * Main Template
 */
get_header();

// 데이터 로드
$cards = get_option('sup_final_cards_data', []);
$ad_config = sup_get_ad_config(); // 광고 데이터 (raw, pub_id, slot_id)
?>

<div class="container">
    <div class="intro-section">
        <span class="intro-badge">신청마감 임박</span>
        <p class="intro-sub">숨은 정부지원금 1분만에 찾기</p>
        <h2 class="intro-title">나의 숨은 지원금 찾기</h2>
    </div>

    <div class="info-box">
        <div class="info-box-header">
            <span class="info-box-icon">💡</span>
            <span class="info-box-title">신청 안하면 못 받아요</span>
        </div>
        <div class="info-box-amount">1인 평균 127만원 환급</div>
        <p class="info-box-desc">대한민국 92%가 놓치고 있는 정부 지원금! 지금 확인하고 혜택 놓치지 마세요.</p>
    </div>

    <?php if ($ad_config['has_ad']): ?>
    <div class="top-ad-section">
        <?php echo $ad_config['raw']; ?>
    </div>
    <?php else: ?>
    <?php endif; ?>

    <div class="info-card-grid">
        <?php
        if (!empty($cards)) {
            foreach ($cards as $index => $card) {
                if (empty($card['keyword'])) continue;

                // [규칙 3-3] 카드 사이 광고 삽입 (0, 3, 6번째 카드 '전'에 삽입)
                // 조건: 광고 설정이 있고, 인덱스가 0, 3, 6일 때
                if ($ad_config['has_ad'] && in_array($index, [0, 3, 6])) {
                    ?>
                    <div class="ad-card">
                        <div style="display:flex; justify-content:center; width:100%;">
                            <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=<?php echo esc_attr($ad_config['pub_id']); ?>"
                                    crossorigin="anonymous"></script>
                            <ins class="adsbygoogle"
                                 style="display:inline-block;width:336px;height:280px"
                                 data-ad-client="<?php echo esc_attr($ad_config['pub_id']); ?>"
                                 data-ad-slot="<?php echo esc_attr($ad_config['slot_id']); ?>"></ins>
                            <script>
                                (adsbygoogle = window.adsbygoogle || []).push({});
                            </script>
                        </div>
                    </div>
                    <?php
                }

                // 일반 카드 출력
                $featured_class = ($index === 0) ? ' featured' : '';
                ?>
                <a class="info-card<?php echo $featured_class; ?>" href="<?php echo esc_url($card['link']); ?>">
                    <div class="info-card-highlight">
                        <?php if ($index === 0): ?>
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
        }
        ?>
    </div>

    <div class="hero-section">
        <div class="hero-content">
            <span class="hero-urgent">🔥 신청마감 D-3일</span>
            <h2 class="hero-title">
                나의 <span class="hero-highlight">숨은 지원금</span> 찾기
            </h2>
            <p class="hero-amount">신청자 <strong>1인 평균 127만원</strong> 수령</p>
            <a class="hero-cta" href="#">
                30초만에 내 지원금 확인 <span>→</span>
            </a>
        </div>
    </div>
</div>

<?php get_footer(); ?>
