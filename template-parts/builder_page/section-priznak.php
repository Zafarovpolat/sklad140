<section class="section info box-bg">
    <div class="container">
        <?php if ($title = get_field('priznak_title', 'option')): ?>
            <h2 class="title-lg info__title"><?= $title; ?></h2>
        <?php endif; ?>
        <?php if ($punkts = get_field('priznak_punkts', 'option')): ?>
            <div class="info-items">
                <?php foreach ($punkts as $punkt): ?>
                    <div class="info-item">
                        <div class="info-item-content">
                            <?php if (!empty($punkt['ikonka'])): ?>
                                <img src="<?= esc_url($punkt['ikonka']['url']); ?>" alt="<?= esc_attr($punkt['ikonka']['alt']); ?>" class="info-item__icon">
                            <?php endif; ?>

                            <div class="info-item-text">
                                <?php if (!empty($punkt['Zagolovok'])): ?>
                                    <h3 class="title-md info-item__title">
                                        <span><?= esc_html($punkt['Zagolovok']); ?></span>
                                    </h3>
                                <?php endif; ?>

                                <?php if (!empty($punkt['text'])): ?>
                                    <p class="text-sm dark info-item__descr"><?= esc_html($punkt['text']); ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <?php if ($quote = get_field('priznak_text', 'option')): ?>
            <div class="info-quote">
                <p class="light info-quote__descr text-md center"><?= $quote; ?></p>
            </div>
        <?php endif; ?>
    </div>
</section>