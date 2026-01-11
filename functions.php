<?php
/**
 * 지원금 테마 최종 안정화 functions.php
 * - 광고 규칙 100% 반영
 * - 트러블슈팅 방지
 * - 디자인/반응형 유지
 */

/* --------------------------------------------------
 * 0. 기본 보안
-------------------------------------------------- */
if (!defined('ABSPATH')) exit;

/* --------------------------------------------------
 * 1. 테마 기본 설정
-------------------------------------------------- */
add_action('after_setup_theme', function () {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
});

/* --------------------------------------------------
 * 2. 관리자 메뉴
-------------------------------------------------- */
add_action('admin_menu', function () {
    add_menu_page(
        '지원금 관리',
        '지원금 관리',
        'manage_options',
        'sup-final-manager',
        'sup_final_cards_page',
        'dashicons-money-alt',
        30
    );

    add_submenu_page(
        'sup-final-manager',
        '기본 설정',
        '기본 설정',
        'manage_options',
        'sup-final-basic',
        'sup_final_basic_page'
    );

    add_submenu_page(
        'sup-final-manager',
        '탭 설정',
        '탭 설정',
        'manage_options',
        'sup-final-tabs',
        'sup_final_tabs_page'
    );

    add_submenu_page(
        'sup-final-manager',
        '광고 설정',
        '광고 설정',
        'manage_options',
        'sup-final-ads',
        'sup_final_ads_page'
    );
});

/* --------------------------------------------------
 * 3. 광고 코드 파싱 유틸
-------------------------------------------------- */
function sup_final_parse_adsense($code) {
    $result = [
        'pubId' => '',
        'adSlot' => '',
        'displayAdCode' => '<!-- 애드센스 코드를 입력하지 않았습니다 -->',
        'headerAdScript' => '<!-- 애드센스 코드를 입력하지 않았습니다 -->',
    ];

    if (!trim($code)) return $result;

    preg_match('/data-ad-client=["\']([^"\']+)["\']/', $code, $pub);
    preg_match('/data-ad-slot=["\']([^"\']+)["\']/', $code, $slot);

    if (!empty($pub[1])) $result['pubId'] = $pub[1];
    if (!empty($slot[1])) $result['adSlot'] = $slot[1];

    $result['displayAdCode'] = "<div>\n{$code}\n</div>";

    if ($result['pubId']) {
        $result['headerAdScript'] =
            "<script async crossorigin='anonymous' src='https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client={$result['pubId']}'></script>";
    }

    return $result;
}

/* --------------------------------------------------
 * 4. HEAD 광고 스크립트 삽입
-------------------------------------------------- */
add_action('wp_head', function () {
    $adCode = get_option('sup_final_ad_code', '');
    $parsed = sup_final_parse_adsense($adCode);
    echo $parsed['headerAdScript'];
});

/* --------------------------------------------------
 * 5. 프론트 CSS (기존 유지 + 광고 카드)
-------------------------------------------------- */
add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style(
        'sup-final-font',
        'https://fonts.googleapis.com/css2?family=Noto+Sans+KR:wght@400;500;700&display=swap'
    );

    wp_add_inline_style('wp-block-library', "
        .ad-card{
            background:#fff;
            border-radius:20px;
            box-shadow:0 4px 20px rgba(0,0,0,.06);
            border:1px solid rgba(0,0,0,.04);
            padding:16px;
            margin:24px 0;
            display:flex;
            justify-content:center;
        }
        @media(max-width:768px){
            .ad-card{
                background:transparent;
                border:none;
                box-shadow:none;
                padding:0;
                margin:16px 0;
            }
        }
    ");
});

/* --------------------------------------------------
 * 6. 숏코드 출력
-------------------------------------------------- */
add_shortcode('지원금_리스트', function () {

    $cards = get_option('sup_final_cards_data', []);
    if (!$cards) return '';

    $adCode = get_option('sup_final_ad_code', '');
    $parsed = sup_final_parse_adsense($adCode);

    ob_start();
    echo '<div class="sup-wrap">';

    // 상단 고정 광고
    echo $parsed['displayAdCode'];

    foreach ($cards as $i => $c) {

        // 카드 사이 광고 (0,3,6)
        if ($parsed['pubId'] && $parsed['adSlot'] && in_array($i, [0, 3, 6], true)) {
            echo "<div class='ad-card'>
                <ins class='adsbygoogle'
                     style='display:inline-block;width:336px;height:280px'
                     data-ad-client='{$parsed['pubId']}'
                     data-ad-slot='{$parsed['adSlot']}'></ins>
                <script>(adsbygoogle=window.adsbygoogle||[]).push({});</script>
            </div>";
        }

        ?>
        <div class="sup-card">
            <div class="sup-card-head">
                <span class="amt"><?php echo esc_html($c['amount']); ?></span>
                <span class="sub"><?php echo esc_html($c['amountSub']); ?></span>
            </div>
            <div class="sup-card-body">
                <div class="sup-card-title"><?php echo esc_html($c['keyword']); ?></div>
                <div class="sup-card-desc"><?php echo nl2br(esc_html($c['description'])); ?></div>
                <div class="sup-info-box">
                    <div class="sup-info-row">
                        <div class="sup-info-label">지원대상</div>
                        <div class="sup-info-val"><?php echo esc_html($c['target']); ?></div>
                    </div>
                    <div class="sup-info-row">
                        <div class="sup-info-label">신청시기</div>
                        <div class="sup-info-val"><?php echo esc_html($c['period']); ?></div>
                    </div>
                </div>
                <?php if (!empty($c['link'])) : ?>
                    <a href="<?php echo esc_url($c['link']); ?>" target="_blank" class="sup-card-btn">
                        지금 바로 신청하기 →
                    </a>
                <?php endif; ?>
            </div>
        </div>
        <?php
    }

    echo '</div>';
    return ob_get_clean();
});

/* --------------------------------------------------
 * 7. 기본 설정 (사이트명 / 탭 활성)
-------------------------------------------------- */
function sup_final_basic_page() {
    if (isset($_POST['save_basic']) && check_admin_referer('sup_basic')) {
        update_option('sup_site_name', sanitize_text_field($_POST['site_name']));
        update_option('sup_tabs_enabled', isset($_POST['tabs_enabled']) ? '1' : '0');
        echo '<div class="notice notice-success"><p>저장 완료</p></div>';
    }
    ?>
    <div class="wrap">
        <h1>기본 설정</h1>
        <form method="post">
            <?php wp_nonce_field('sup_basic'); ?>
            <table class="form-table">
                <tr>
                    <th>사이트 이름</th>
                    <td><input type="text" name="site_name" value="<?php echo esc_attr(get_option('sup_site_name', '')); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th>탭 메뉴 활성</th>
                    <td><label><input type="checkbox" name="tabs_enabled" <?php checked(get_option('sup_tabs_enabled', '1'), '1'); ?>> 사용</label></td>
                </tr>
            </table>
            <input type="submit" name="save_basic" class="button button-primary" value="저장">
        </form>
    </div>
    <?php
}

/* --------------------------------------------------
 * 8. 탭 설정
-------------------------------------------------- */
function sup_final_tabs_page() {
    if (isset($_POST['save_tabs'])) {
        update_option('sup_final_tabs_data', array_map('array_map', array_fill(0, 2, 'sanitize_text_field'), $_POST['tabs']));
        echo '<div class="notice notice-success"><p>저장됨</p></div>';
    }
    $tabs = array_slice(array_merge(get_option('sup_final_tabs_data', []), array_fill(0, 3, ['name'=>'','link'=>''])), 0, 3);
    ?>
    <div class="wrap">
        <h1>탭 설정 (3개)</h1>
        <form method="post">
            <table class="widefat">
                <?php foreach ($tabs as $i => $t): ?>
                    <tr>
                        <td><input type="text" name="tabs[<?php echo $i; ?>][name]" value="<?php echo esc_attr($t['name']); ?>" placeholder="탭 이름"></td>
                        <td><input type="url" name="tabs[<?php echo $i; ?>][link]" value="<?php echo esc_url($t['link']); ?>" placeholder="링크"></td>
                    </tr>
                <?php endforeach; ?>
            </table>
            <input type="submit" name="save_tabs" class="button button-primary" value="저장">
        </form>
    </div>
    <?php
}

/* --------------------------------------------------
 * 9. 광고 설정
-------------------------------------------------- */
function sup_final_ads_page() {
    if (isset($_POST['save_ads'])) {
        update_option('sup_final_ad_code', wp_kses_post($_POST['ad_code']));
        echo '<div class="notice notice-success"><p>광고 코드 저장됨</p></div>';
    }
    ?>
    <div class="wrap">
        <h1>광고 코드 설정</h1>
        <form method="post">
            <textarea name="ad_code" style="width:100%;height:220px;"><?php echo esc_textarea(get_option('sup_final_ad_code','')); ?></textarea>
            <p class="description">애드센스 원본 코드를 그대로 붙여넣으세요.</p>
            <input type="submit" name="save_ads" class="button button-primary" value="저장">
        </form>
    </div>
    <?php
}
