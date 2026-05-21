<!-- Для покупателей -->
<section class="for-customers">
    <div class="container">
        <div class="section-header">
            <div class="section-header__left">
                <div class="section-header__icon">
                    <img src="<?= get_template_directory_uri(); ?>/images/content/others/info-emoji.png" alt="Info emoji" />
                </div>
                <?php if ($title = get_field('zagolovok_pokupat', 'option')): ?>
                    <h2 class="section-header__title"><?= esc_html($title); ?></h2>
                <?php endif; ?>
            </div>
        </div>

        <?php if (have_rows('blokidlyapokupatelej', 'option')): ?>
            <div class="for-customers__items">
                <?php 
                $index = 0;
                $circle_classes = ['--yellow', '--rad', '--green', '--blue'];
                while (have_rows('blokidlyapokupatelej', 'option')): the_row();
                    $zagolovok = get_sub_field('zagolovok');
                    $ikonka = get_sub_field('ikonka');
                    $ssylka = get_sub_field('ssylka');
                    $czvet = get_sub_field('czvet_fona');
                    $suffix = $circle_classes[$index] ?? '--yellow';
                    $index++;
                ?>
                    <a href="<?= esc_url($ssylka ?: '#'); ?>" class="for-customers-item">
                        <?php if ($zagolovok): ?>
                            <p class="for-customers-item__title"><?= esc_html($zagolovok); ?></p>
                        <?php endif; ?>

                        <span class="for-customers-item__btn">
                            <svg width="7" height="12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M1 1l5 5-5 5" stroke="#031343" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>

                        <?php if (!empty($ikonka)): ?>
                            <div class="for-customers-item__img">
                                <img src="<?= esc_url($ikonka['url']); ?>" alt="<?= esc_attr($ikonka['alt']); ?>">
                            </div>
                        <?php endif; ?>

                        <div class="for-customers-item__circle for-customers-item__circle<?= esc_attr($suffix); ?>" style="background-color: <?= esc_attr($czvet ?: '#d7f9ff'); ?>;"></div>
                    </a>
                <?php endwhile; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
