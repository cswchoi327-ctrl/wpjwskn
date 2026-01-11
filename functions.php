<?php
/**
 * 지원금 테마 최종 통합본 (기능 보강 및 광고 규칙 적용)
 */

if (!defined('SUPPORT_AI_API_KEY')) {
    define('SUPPORT_AI_API_KEY', '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');
}

add_action('after_setup_theme', function() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
});

// 2. 관리자 메뉴
add_action('admin_menu', function() {
    // 메인 메뉴
    add_menu_page('지원금 관리', '지원금 관리', 'manage_options', 'sup-final-manager', 'sup_final_cards_page', 'dashicons-money-alt', 30);
    
    // 서브 메뉴
    add_submenu_page('sup-final-manager', '기본 설정', '기본 설정', 'manage_options', 'sup-final-basic', 'sup_final_basic_page'); // 사이트 이름 설정
    add_submenu_page('sup-final-manager', '탭 설정', '탭 설정', 'manage_options', 'sup-final-tabs', 'sup_final_tabs_page');
    add_submenu_page('sup-final-manager', '광고 설정', '광고 설정', 'manage_options', 'sup-final-ads', 'sup_final_ads_page');
});

// 3. 광고 데이터 파싱 헬퍼 함수 (규칙 문서 적용)
function sup_get_ad_config() {
    $raw_code = stripslashes(get_option('sup_final_ad_code', ''));
    $pub_id = '';
    $slot_id = '';

    if (!empty($raw_code)) {
        // 정규식으로 data-ad-client (Pub ID) 추출
        preg_match('/data-ad-client=["\']([^"\']+)["\']/', $raw_code, $client_matches);
        if (isset($client_matches[1])) {
            $pub_id = $client_matches[1];
        }

        // 정규식으로 data-ad-slot (Slot ID) 추출
        preg_match('/data-ad-slot=["\']([^"\']+)["\']/', $raw_code, $slot_matches);
        if (isset($slot_matches[1])) {
            $slot_id = $slot_matches[1];
        }
    }

    return [
        'raw' => $raw_code,
        'pub_id' => $pub_id,
        'slot_id' => $slot_id,
        'has_ad' => !empty($raw_code) && !empty($pub_id)
    ];
}

// 4. AI 자동 입력 AJAX
add_action('wp_ajax_sup_final_fetch', function() {
    check_ajax_referer('sup_final_nonce', 'security');
    $kw = sanitize_text_field($_POST['keyword']);
    
    $response = wp_remote_post('https://openrouter.ai/api/v1/chat/completions', [
        'headers' => ['Authorization' => 'Bearer ' . SUPPORT_AI_API_KEY, 'Content-Type' => 'application/json'],
        'body'    => json_encode([
            'model' => 'openai/gpt-4o-mini',
            'messages' => [['role' => 'user', 'content' => "정책 '{$kw}' 정보를 JSON으로 요약해줘. 필드: amount, amountSub, description, target, period. 한국어로."]],
            'temperature' => 0.3
        ]),
        'timeout' => 15
    ]);

    if (is_wp_error($response)) wp_send_json_error();
    $body = json_decode(wp_remote_retrieve_body($response), true);
    // 마크다운 제거
    $content = preg_replace('/```json\s*|\s*```/', '', $body['choices'][0]['message']['content']);
    wp_send_json_success(json_decode($content, true));
});

// 5. 스타일 및 스크립트 로드
function sup_final_styles() {
    wp_enqueue_style('google-fonts', 'https://fonts.googleapis.com/css2?family=Noto+Sans+KR:wght@400;500;700&display=swap');
    // style.css는 테마 루트에 있으므로 자동 로드됨
}
add_action('wp_enqueue_scripts', 'sup_final_styles');


// ================= 관리자 페이지 함수들 =================

// [A] 지원금 카드 관리
function sup_final_cards_page() {
    if (isset($_POST['save_sup_cards']) && check_admin_referer('sup_final_save')) {
        update_option('sup_final_cards_data', $_POST['cards']);
        echo '<div class="notice notice-success"><p>저장되었습니다.</p></div>';
    }
    $cards = get_option('sup_final_cards_data', []);
    ?>
    <div class="wrap">
        <h1>지원금 카드 관리</h1>
        <form method="post">
            <?php wp_nonce_field('sup_final_save'); ?>
            <div id="sup-final-container">
                <?php foreach ($cards as $i => $c): ?>
                <div class="sup-final-item" style="background:#fff; padding:20px; border:1px solid #ccc; margin-bottom:15px; border-radius:10px;">
                    <input type="text" name="cards[<?php echo $i; ?>][keyword]" value="<?php echo esc_attr($c['keyword']); ?>" class="kw-in" style="width:70%; font-weight:bold;" placeholder="지원금 이름">
                    <button type="button" class="ai-final-btn button button-primary">AI 자동채우기</button>
                    <table class="form-table">
                        <tr><th>금액</th><td><input type="text" name="cards[<?php echo $i; ?>][amount]" value="<?php echo esc_attr($c['amount']); ?>" class="in-amt" style="width:100%"></td></tr>
                        <tr><th>부연설명</th><td><input type="text" name="cards[<?php echo $i; ?>][amountSub]" value="<?php echo esc_attr($c['amountSub']); ?>" style="width:100%"></td></tr>
                        <tr><th>지원대상</th><td><input type="text" name="cards[<?php echo $i; ?>][target]" value="<?php echo esc_attr($c['target']); ?>" class="in-target" style="width:100%"></td></tr>
                        <tr><th>신청시기</th><td><input type="text" name="cards[<?php echo $i; ?>][period]" value="<?php echo esc_attr($c['period']); ?>" class="in-period" style="width:100%"></td></tr>
                        <tr><th>설명</th><td><textarea name="cards[<?php echo $i; ?>][description]" style="width:100%"><?php echo esc_textarea($c['description']); ?></textarea></td></tr>
                        <tr><th>링크</th><td><input type="url" name="cards[<?php echo $i; ?>][link]" value="<?php echo esc_url($c['link']); ?>" style="width:100%"></td></tr>
                    </table>
                    <button type="button" onclick="this.parentElement.remove()" class="button">삭제</button>
                </div>
                <?php endforeach; ?>
            </div>
            <button type="button" id="add-final-card" class="button">➕ 카드 추가</button>
            <input type="submit" name="save_sup_cards" class="button button-primary" value="💾 모든 카드 저장">
        </form>
    </div>
    <script>
    jQuery(document).ready(function($){
        $(document).on('click', '.ai-final-btn', function(){
            var btn = $(this); var p = btn.parent(); var kw = p.find('.kw-in').val();
            if(!kw) return alert('이름을 입력하세요.');
            btn.text('가져오는 중...');
            $.post(ajaxurl, {action:'sup_final_fetch', keyword:kw, security:'<?php echo wp_create_nonce("sup_final_nonce"); ?>'}, function(res){
                if(res.success){
                    p.find('.in-amt').val(res.data.amount);
                    p.find('.in-target').val(res.data.target);
                    p.find('.in-period').val(res.data.period);
                    p.find('textarea').val(res.data.description);
                    p.find('.in-amt').next().val(res.data.amountSub); // amountSub 처리
                }
                btn.text('AI 자동채우기');
            });
        });
        $('#add-final-card').click(function(){
            var i = Date.now();
            $('#sup-final-container').append('<div class="sup-final-item" style="background:#fff; padding:20px; border:1px solid #ccc; margin-bottom:15px; border-radius:10px;"><input type="text" name="cards['+i+'][keyword]" class="kw-in" style="width:70%;"> <button type="button" class="ai-final-btn button">AI</button><button type="button" onclick="this.parentElement.remove()" class="button">삭제</button></div>');
        });
    });
    </script>
    <?php
}

// [B] 기본 설정 (사이트 이름)
function sup_final_basic_page() {
    if (isset($_POST['save_basic'])) {
        update_option('sup_final_site_title', sanitize_text_field($_POST['site_title']));
        echo '<div class="notice notice-success"><p>기본 설정이 저장되었습니다.</p></div>';
    }
    $site_title = get_option('sup_final_site_title', get_bloginfo('name'));
    ?>
    <div class="wrap">
        <h1>기본 설정</h1>
        <form method="post">
            <table class="form-table">
                <tr>
                    <th scope="row"><label for="site_title">사이트 이름 (헤더 표시)</label></th>
                    <td>
                        <input name="site_title" type="text" id="site_title" value="<?php echo esc_attr($site_title); ?>" class="regular-text">
                        <p class="description">헤더와 타이틀바에 표시될 사이트 이름을 입력하세요.</p>
                    </td>
                </tr>
            </table>
            <input type="submit" name="save_basic" class="button button-primary" value="저장">
        </form>
    </div>
    <?php
}

// [C] 탭 설정
function sup_final_tabs_page() {
    if (isset($_POST['save_tabs'])) {
        update_option('sup_final_tabs_data', $_POST['tabs']);
        echo '<div class="notice notice-success"><p>탭 설정이 저장되었습니다.</p></div>';
    }
    $tabs = array_slice(array_merge(get_option('sup_final_tabs_data', []), array_fill(0, 3, ['name'=>'', 'link'=>''])), 0, 3);
    ?>
    <div class="wrap"><h1>탭 메뉴 설정 (3개)</h1><form method="post"><table class="widefat">
        <?php for($i=0; $i<3; $i++): ?>
        <tr><td><input type="text" name="tabs[<?php echo $i; ?>][name]" value="<?php echo esc_attr($tabs[$i]['name']); ?>" placeholder="탭 이름"></td>
        <td><input type="url" name="tabs[<?php echo $i; ?>][link]" value="<?php echo esc_url($tabs[$i]['link']); ?>" placeholder="링크 URL"></td></tr>
        <?php endfor; ?>
    </table><input type="submit" name="save_tabs" class="button button-primary" value="저장"></form></div>
    <?php
}

// [D] 광고 설정
function sup_final_ads_page() {
    if (isset($_POST['save_ads'])) {
        // stripslashes를 사용하여 따옴표 이스케이프 문제 해결
        update_option('sup_final_ad_code', stripslashes($_POST['ad_code']));
        echo '<div class="notice notice-success"><p>광고 코드가 저장되었습니다.</p></div>';
    }
    $ad_code = get_option('sup_final_ad_code', '');
    ?>
    <div class="wrap">
        <h1>광고 설정 (애드센스)</h1>
        <p>애드센스에서 발급받은 전체 코드를 아래에 입력하세요. 시스템이 자동으로 ID를 추출하여 적절한 위치에 배치합니다.</p>
        <form method="post">
            <textarea name="ad_code" style="width:100%; height:200px; font-family:monospace;" placeholder="<script...></script> <ins...></ins> ..."><?php echo esc_textarea($ad_code); ?></textarea>
            <input type="submit" name="save_ads" class="button button-primary" value="광고 코드 저장">
        </form>
    </div>
    <?php
}
?>
