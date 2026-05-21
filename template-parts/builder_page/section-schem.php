<section class="section scheme">
    <div class="container">
        <?php if ($title = get_field('obman_title', 'option')): ?>
            <h2 class="title-lg scheme__title dark center"><?= $title; ?></h2>
        <?php endif; ?>
        <?php if ($punkts = get_field('obman_punkts', 'option')): ?>
            <div class="scheme-block">
                <?php foreach ($punkts as $punkt): ?>
                    <div class="scheme-item box-bg">
                        <?php if (!empty($punkt['ikonka'])): ?>
                            <img src="<?= esc_url($punkt['ikonka']['url']); ?>" alt="<?= esc_attr($punkt['ikonka']['alt']); ?>">
                        <?php endif; ?>

                        <?php if (!empty($punkt['Zagolovok'])): ?>
                            <h4 class="title-md scheme-item__title">
                                <span><?= esc_html($punkt['Zagolovok']); ?></span>
                            </h4>
                        <?php endif; ?>

                        <?php if (!empty($punkt['text'])): ?>
                            <p class="text-sm dark scheme-item__descr"><?= esc_html($punkt['text']); ?></p>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <?php if ($after_text = get_field('obman_text', 'option')): ?>
            <p class="title-md dark scheme__descr center">
                <?= $after_text; ?>
            </p>
        <?php endif; ?>
    </div>
</section>
