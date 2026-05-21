<?php
$about_zag_one = get_field('about_zag_one', 'option');
$about_zag_b = get_field('about_zag_b', 'option');
$zagolovokblokayoutube = get_field('zagolovokblokayoutube', 'option');
$tekstblokayoutube = get_field('tekstblokayoutube', 'option');
$about_tekstknopki = get_field('about_tekstknopki', 'option');
$ssylkanakanalyt = get_field('ssylkanakanalyt', 'option');
$ssylkanarolikyt = get_field('ssylkanarolikyt', 'option');
$animaczionnyjfon_yt = get_field('animaczionnyjfon_yt', 'option');
$about_tekstovayainfo = get_field('about_tekstovayainfo', 'option');
?>

<!-- О нас -->
<section class="about">
    <div class="container">
        <div class="section-header">
            <div class="section-header__left flex items-center gap-4">
                <div class="section-header__icon">
                    <img src="<?= get_template_directory_uri(); ?>/images/content/others/box-emoji.png" alt="Box emoji" />
                </div>
                <?php if ($about_zag_one): ?>
                    <h2 class="section-header__title"><?= esc_html($about_zag_one); ?></h2>
                <?php endif; ?>
            </div>
        </div>

        <div class="relative rounded-[40px] overflow-hidden mb-6">
            <div class="about__content grid grid-cols-2 gap-5 relative z-10 size-full bg-[#2B2D33]/35 text-white pl-10 pt-6 pb-5 pr-6">
                <!-- Left -->
                <div class="about-left max-w-120 w-full h-full flex flex-col justify-between py-4">
                    <div class="flex items-center">
                        <div class="flex flex-col gap-6">
                            <svg width="24" height="24"><use xlink:href="<?= get_template_directory_uri(); ?>/images/sprite.svg#corner-top-left"></use></svg>
                            <svg width="24" height="24"><use xlink:href="<?= get_template_directory_uri(); ?>/images/sprite.svg#corner-bottom-left"></use></svg>
                        </div>
                        <?php if ($about_zag_b): ?>
                            <h3 class="about__title font-semibold text-main-40 whitespace-nowrap"><?= esc_html($about_zag_b); ?></h3>
                        <?php endif; ?>
                        <div class="flex flex-col gap-6">
                            <svg width="24" height="24"><use xlink:href="<?= get_template_directory_uri(); ?>/images/sprite.svg#corner-top-right"></use></svg>
                            <svg width="24" height="24"><use xlink:href="<?= get_template_directory_uri(); ?>/images/sprite.svg#corner-bottom-right"></use></svg>
                        </div>
                    </div>

                    <?php if ($ssylkanarolikyt): ?>
                        <a href="<?= esc_url($ssylkanarolikyt); ?>" target="_blank" class="about-left__play size-15 rounded-full overflow-hidden bg-brand-blue flex items-center justify-center">
                            <svg width="20" height="20"><use xlink:href="<?= get_template_directory_uri(); ?>/images/sprite.svg#play"></use></svg>
                        </a>
                    <?php endif; ?>
                </div>

                <!-- Right -->
                <div class="about-right ml-auto max-w-100 w-full bg-brand-blue rounded-3xl p-6">
                    <div>
                        <svg width="182" height="40"><use xlink:href="<?= get_template_directory_uri(); ?>/images/sprite.svg#youtube"></use></svg>
                    </div>
                    <?php if ($zagolovokblokayoutube): ?>
                        <h5 class="about-right__title font-medium text-[28px]/[120%]"><?= esc_html($zagolovokblokayoutube); ?></h5>
                    <?php endif; ?>
                    <?php if ($tekstblokayoutube): ?>
                        <p class="about-right__text text-lg/[120%]"><?= esc_html($tekstblokayoutube); ?></p>
                    <?php endif; ?>
                    <?php if ($ssylkanakanalyt && $about_tekstknopki): ?>
                        <a href="<?= esc_url($ssylkanakanalyt); ?>" target="_blank" class="button button--dark"><?= esc_html($about_tekstknopki); ?></a>
                    <?php endif; ?>
                </div>
            </div>

            <?php if (!empty($animaczionnyjfon_yt)): ?>
                <div id="about-gif" class="absolute size-full z-0 left-0 top-0">
                    <img class="size-full object-cover" src="<?= esc_url($animaczionnyjfon_yt['url']); ?>" loading="lazy" alt="<?= esc_attr($animaczionnyjfon_yt['alt']); ?>" />
                </div>
            <?php endif; ?>
        </div>

        <!-- Right Mobile -->
        <div class="about-right about-right--mobile ml-auto max-w-100 w-full bg-brand-blue rounded-3xl p-6">
            <div>
                <svg class="about-right__logo" width="182" height="40"><use xlink:href="<?= get_template_directory_uri(); ?>/images/sprite.svg#youtube"></use></svg>
            </div>
            <?php if ($zagolovokblokayoutube): ?>
                <h5 class="about-right__title font-medium text-[28px]/[120%]"><?= esc_html($zagolovokblokayoutube); ?></h5>
            <?php endif; ?>
            <?php if ($tekstblokayoutube): ?>
                <p class="about-right__text text-lg/[120%]"><?= esc_html($tekstblokayoutube); ?></p>
            <?php endif; ?>
            <?php if ($ssylkanakanalyt && $about_tekstknopki): ?>
                <a href="<?= esc_url($ssylkanakanalyt); ?>" target="_blank" class="button button--dark"><?= esc_html($about_tekstknopki); ?></a>
            <?php endif; ?>
        </div>

        <?php if (!empty($about_tekstovayainfo)): ?>
            <div class="about-text bg-white rounded-3xl p-6">
                <?php if (!empty($about_tekstovayainfo['zagolovok'])): ?>
                    <h3 class="about-text__title max-w-250 font-semibold text-main-40/[110%] mb-6"><?= $about_tekstovayainfo['zagolovok']; ?></h3>
                <?php endif; ?>

                <div class="about-text__holder text-lg/[140%] flex gap-6 mb-6">
                    <div class="about-text__row">
                        <?php if (!empty($about_tekstovayainfo['tekst_1_sleva'])): ?>
                            <p class="about-text__desc max-w-150"><?= $about_tekstovayainfo['tekst_1_sleva']; ?></p>
                        <?php endif; ?>
                        <?php if (!empty($about_tekstovayainfo['tekst_2_sprava'])): ?>
                            <p class="about-text__desc max-w-112.5"><?= $about_tekstovayainfo['tekst_2_sprava']; ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="about-text-accordion__wrapper">
                        <?php
                        $tabs = [
                            $about_tekstovayainfo['tab_s_tekstom_sleva'],
                            $about_tekstovayainfo['tab_s_tekstom_sprava']
                        ];
                        foreach ($tabs as $tab):
                            if (empty($tab)) continue;
                            ?>
                            <div class="about-text-accordion">
                                <div class="about-text-accordion__header">
                                    <div class="about-text-accordion__icon">
                                        <svg width="16" height="16"><use xlink:href="<?= get_template_directory_uri(); ?>/images/sprite.svg#question-mark"></use></svg>
                                    </div>
                                    <?php if (!empty($tab['zagolovok'])): ?>
                                        <p class="about-text-accordion__title"><?= esc_html($tab['zagolovok']); ?></p>
                                    <?php endif; ?>
                                    <div class="about-text-accordion__arrow">
                                        <svg width="12" height="12" style="transform: rotate(0deg)"><use xlink:href="<?= get_template_directory_uri(); ?>/images/sprite.svg#arrow-blue"></use></svg>
                                    </div>
                                </div>
                                <div class="about-text-accordion__content">
                                    <?php
                                    if (!empty($tab['tekst'])) {
                                        $content = $tab['tekst'];
                                        if (strip_tags($content) === $content) {
                                            echo '<p>' . nl2br(esc_html($content)) . '</p>';
                                        } else {
                                            echo $content;
                                        }
                                    }
                                    ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <button class="about-text__btn font-medium text-brand-blue bg-brand-primary rounded-xl w-full flex items-center justify-center gap-3 py-3.5 px-10">
                    <span>Читать подробнее</span>
                    <svg width="10" height="6"><use xlink:href="<?= get_template_directory_uri(); ?>/images/sprite.svg#arrow-blue"></use></svg>
                </button>
            </div>
        <?php endif; ?>
    </div>
</section>