<?php
/**
 * Header Template
 * functions.php의 데이터와 연동 및 광고 스크립트 헤더 삽입
 */

// 광고 코드에서 Pub ID 추출 (헤더 스크립트용)
$ad_code = stripslashes(get_option('sup_final_ad_code', ''));
$pub_id = '';
if (!empty($ad_code)) {
    preg_match('/data-ad-client=["\']([^"\']+)["\']/', $ad_code, $matches);
    if (isset($matches[1])) {
        $pub_id = $matches[1];
    }
}
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <?php if ($pub_id): ?>
        <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=<?php echo esc_attr($pub_id); ?>" crossorigin="anonymous"></script>
    <?php endif; ?>
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<div class="main-wrapper">
    <header id="header">
        <div class="header">
            <div class="container">
                <div class="logo">
                    <img src="https://blogger.googleusercontent.com/img/b/R29vZ2xl/AVvXsEhwxd_YGfZiM_d9LPozylA_vt2w36-eanzKSgvMQm2zkh-s41pKzT2FDyyqB9cz713Tm3nRFVbtRR8GGXlEQh7UDr4BDteEwfQ_JDV0Yl_xYA5uBGWrqyhDLH_PNEa9cJmNLOhhFc7XKAJChRiR9_6KZbraUo8FpA2IGMxbgMNGAtnoi-WlBnWYpnm0FKw/w945-h600-p-k-no-nu/img.png" alt="로고">
                </div>
                <h1 class="logo-text"><?php echo esc_html(get_bloginfo('name')); ?></h1>
            </div>
        </div>
    </header>

    <div class="tab-wrapper">
        <div class="container">
            <nav class="tab-container">
                <ul class="tabs">
                    <?php
                    // functions.php에서 저장한 탭 데이터 가져오기
                    $tabs = get_option('sup_final_tabs_data', []);
                    
                    // 데이터가 없거나 비어있을 경우를 대비한 기본 처리
                    $has_tabs = false;
                    if (!empty($tabs)) {
                        foreach ($tabs as $index => $tab) {
                            if (!empty($tab['name'])) {
                                $has_tabs = true;
                                // 첫 번째 탭을 active로 설정 (로직에 따라 변경 가능)
                                $active_class = ($index === 0) ? ' active' : '';
                                echo '<li class="tab-item">';
                                echo '<a class="tab-link' . $active_class . '" href="' . esc_url($tab['link']) . '">' . esc_html($tab['name']) . '</a>';
                                echo '</li>';
                            }
                        }
                    }
                    
                    // 탭이 하나도 설정되지 않았을 때 기본 예시
                    if (!$has_tabs) {
                        echo '<li class="tab-item"><a class="tab-link active" href="#">홈</a></li>';
                        echo '<li class="tab-item"><a class="tab-link" href="#">지원금 찾기</a></li>';
                        echo '<li class="tab-item"><a class="tab-link" href="#">자주묻는질문</a></li>';
                    }
                    ?>
                </ul>
            </nav>
        </div>
    </div>
