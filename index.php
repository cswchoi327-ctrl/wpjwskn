<?php
/**
 * Main Template
 * Compatible with custom functions.php
 */
get_header();

// DB에서 카드 데이터와 광고 코드 가져오기
$cards = get_option('sup_final_cards_data', []);
$ad_code = stripslashes(get_option('sup_final_ad_code', ''));
?>

<div class="container">
    <div class="intro-section">
        <span class="intro-badge">신청마감 임박</span>
        <h2 class="intro-title">숨은 정부지원금<br>지금 바로 확인하세요</h2>
        <p class="intro-sub">1분 만에 내 지원금 찾기</p>
    </div>

    <div class="info-box">
        <div class="info-box-header">
            <span class="info-box-icon">💡</span>
            <span class="info-box-title">알려드립니다</span>
        </div>
        <div class="info-box-amount">1인 평균 127만원 환급</div>
        <p class="info-box-desc">국민 92%가 모르고 놓치는 숨은 돈, 기간 지나면 소멸됩니다. 지금 조회해보세요.</p>
    </div>

    <div class="info-card-grid">
        <?php
        if (!empty($cards)) {
            $count = 0;
            foreach ($cards as $index => $card) {
                // 필수 데이터가 없으면 건너뛰기
                if (empty($card['keyword'])) continue;
                
                // 변수 할당 (오류 방지를 위한 isset 체크)
                $keyword = isset($card['keyword']) ? $card['keyword'] : '';
                $amount = isset($card['amount']) ? $card['amount'] : '';
                $amountSub = isset($card['amountSub']) ? $card['amountSub'] : '';
                $desc = isset($card['description']) ? $card['description'] : '';
                $target = isset($card['target']) ? $card['target'] : '-';
                $period = isset($card['period']) ? $card['period'] : '-';
                $link = isset($card['link']) ? $card['link'] : '#';
                
                // 첫 번째 카드는 강조 스타일 적용 (Featured 데이터가 없으므로 로직 대체)
                $featured_class = ($index === 0) ? ' featured' : '';
                ?>
                
                <a class="info-card<?php echo $featured_class; ?>" href="<?php echo esc_url($link); ?>" target="_blank">
                    <div class="info-card-highlight">
                        <?php if ($index === 0): ?>
                            <span class="info-card-badge">🔥 인기 지원금</span>
                        <?php endif; ?>
                        <div class="info-card-amount"><?php echo esc_html($amount); ?></div>
                        <div class="info-card-amount-sub"><?php echo esc_html($amountSub); ?></div>
                    </div>
                    <div class="info-card-content">
                        <h3 class="info-card-title"><?php echo esc_html($keyword); ?></h3>
                        <p class="info-card-desc"><?php echo esc_html($desc); ?></p>
                        <div class="info-card-details">
                            <div class="info-card-row">
                                <span class="info-card-label">대상</span>
                                <span class="info-card-value"><?php echo esc_html($target); ?></span>
                            </div>
                            <div class="info-card-row">
                                <span class="info-card-label">기간</span>
                                <span class="info-card-value"><?php echo esc_html($period); ?></span>
                            </div>
                        </div>
                        <div class="info-card-btn">
                            신청하러 가기 →
                        </div>
                    </div>
                </a>

                <?php
                $count++;
                // 4번째 카드마다 광고 삽입 (2열 그리드이므로 짝수 뒤에 배치 고려)
                if ($ad_code && $count % 4 == 0) {
                    echo '<div class="ad-container">' . $ad_code . '</div>';
                }
            }
        } else {
            echo '<p style="text-align:center; width:100%; grid-column:1/-1;">등록된 지원금 정보가 없습니다. 관리자 페이지에서 카드를 추가해주세요.</p>';
        }
        ?>
    </div>

    <div class="hero-section">
        <div class="hero-content">
            <h2 class="hero-title">
                나의 <span class="hero-highlight">숨은 지원금</span> 찾기
            </h2>
            <p class="hero-sub">신청자 1인 평균 <strong>127만원</strong> 수령</p>
            <a class="hero-cta" href="#">
                내 지원금 한꺼번에 조회하기
            </a>
        </div>
    </div>
</div>

<?php get_footer(); ?>
