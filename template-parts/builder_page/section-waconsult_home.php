<?php
$wa_nadz = get_field('wa_nadzgolovok', 'option');
$wa_title = get_field('wa_title', 'option');
$wa_pic = get_field('wa_pic', 'option');
$wa_pic_mob = get_field('wa_pic_mob', 'option');
$wa_text = get_field('wa_tekstknopki', 'option');
$wa_link = get_field('wa_link', 'option');
?>

<!-- Проконсультируем -->
<section class="consultation">
    <div class="container">
        <div class="consultation__holder bg-brand-blue text-white rounded-[40px] relative p-8 overflow-hidden">
            <div class="consultation__text relative z-10 w-full max-w-112.5">
                <div class="consultation-header flex items-center gap-4">
                    <div class="consultation-header__icon bg-[#4D96FF] rounded-xl px-4 py-2">
                        <img src="<?= get_template_directory_uri(); ?>/images/content/others/chat-emoji.png" alt="Chat emoji" />
                    </div>
                    <?php if ($wa_nadz): ?>
                        <p class="consultation-header__title"><?= esc_html($wa_nadz); ?></p>
                    <?php endif; ?>
                </div>
                <?php if ($wa_title): ?>
                    <p class="consultation__title font-medium text-[32px]/[110%]">
                        <?= esc_html($wa_title); ?>
                    </p>
                <?php endif; ?>
                <?php if ($wa_link && $wa_text): ?>
                    <a href="<?= esc_url($wa_link); ?>" target="_blank" class="button button--dark"><?= esc_html($wa_text); ?></a>
                <?php endif; ?>
            </div>
            <div class="consultation__circle size-68 bg-[#3881FF] rounded-full absolute top-0 left-0 -translate-x-1/4 -translate-y-1/2 z-0"></div>
            <div class="consultation__circle consultation__circle--big size-144 border border-[#4D96FF] rounded-full absolute bottom-0 right-10 translate-y-1/2 z-0 p-8">
                <div class="size-full border border-[#4D96FF] rounded-full p-8">
                    <div class="size-full rounded-full bg-[#3881FF]"></div>
                </div>
            </div>
            <?php if (!empty($wa_pic)): ?>
                <img loading="lazy" class="consultation__img absolute z-10 bottom-0 right-15 h-90"
                    src="<?= esc_url($wa_pic['url']); ?>"
                    srcset="<?= esc_url($wa_pic['url']); ?> 1x, <?= esc_url($wa_pic['url']); ?> 2x"
                    alt="<?= esc_attr($wa_pic['alt']); ?>" />
            <?php endif; ?>
            <?php if (!empty($wa_pic_mob)): ?>
                <img loading="lazy" class="consultation__img--mobile absolute bottom-0 right-0"
                    src="<?= esc_url($wa_pic_mob['url']); ?>"
                    srcset="<?= esc_url($wa_pic_mob['url']); ?> 1x, <?= esc_url($wa_pic_mob['url']); ?> 2x"
                    alt="<?= esc_attr($wa_pic_mob['alt']); ?>" />
            <?php endif; ?>
        </div>
    </div>
</section>