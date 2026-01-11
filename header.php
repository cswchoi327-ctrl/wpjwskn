<?php
/**
 * Header Template
 * Updated for compatibility with functions.php
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<div class="main-wrapper">
    <header id="header">
        <div class="header">
            <div class="container">
                <div class="logo">
                    <img src="https://blogger.googleusercontent.com/img/b/R29vZ2xl/AVvXsEhwxd_YGfZiM_d9LPozylA_vt2w36-eanzKSgvMQm2zkh-s41pKzT2FDyyqB9cz713Tm3nRFVbtRR8GGXlEQh7UDr4BDteEwfQ_JDV0Yl_xYA5uBGWrqyhDLH_PNEa9cJmNLOhhFc7XKAJChRiR9_6KZbraUo8FpA2IGMxbgMNGAtnoi-WlBnWYpnm0FKw/w945-h600-p-k-no-nu/img.png" alt="<?php bloginfo('name'); ?>">
                </div>
                <h1 class="logo-text"><?php bloginfo('name'); ?></h1>
            </div>
        </div>
    </header>

    <div class="tab-wrapper">
        <div class="container">
            <nav class="tab-container">
                <ul class="tabs">
                    <?php
                    // functions.php에서 저장한 탭 데이터 불러오기
                    $tabs = get_option('sup_final_tabs_data', []);
                    
                    if (!empty($tabs)) {
                        $is_first = true;
                        foreach ($tabs as $tab) {
                            // 데이터가 비어있으면 건너뛰기
                            if (empty($tab['name'])) continue;

                            $name = isset($tab['name']) ? esc_html($tab['name']) : '';
                            $link = isset($tab['link']) ? esc_url($tab['link']) : '#';
                            $active_class = $is_first ? ' active' : ''; // 첫 번째 탭 활성화 (예시)
                            
                            echo '<li class="tab-item">';
                            echo '<a class="tab-link' . $active_class . '" href="' . $link . '">' . $name . '</a>';
                            echo '</li>';
                            
                            $is_first = false;
                        }
                    } else {
                        // 데이터 없을 경우 기본값
                        echo '<li class="tab-item"><a class="tab-link active" href="#">전체</a></li>';
                    }
                    ?>
                </ul>
            </nav>
        </div>
    </div>
