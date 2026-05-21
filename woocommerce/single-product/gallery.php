<?php
defined('ABSPATH') || exit;
global $product;

$attachment_ids = $product->get_gallery_image_ids();
$featured_id    = get_post_thumbnail_id();

$slides = [];
if ($featured_id) $slides[] = $featured_id;
if ($attachment_ids) $slides = array_merge($slides, $attachment_ids);
$slides = array_values(array_unique(array_filter($slides)));
?>

<div class="product-swiper">
    <div class="swiper product-swiper-top">
        <div class="swiper-wrapper">
            <?php foreach ($slides as $index => $img_id): ?>
                <div class="swiper-slide" data-swiper-slide-index="<?= $index; ?>">
                    <div class="product-swiper-top-slide__img"
                         data-full-src="<?= esc_url(wp_get_attachment_image_url($img_id, 'full')); ?>">
                        <?= wp_get_attachment_image($img_id, 'large'); ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <button class="swiper-button-prev__product-top flex items-center justify-center size-10 rounded-full bg-white">
            <svg width="7" height="12" style="transform: rotate(180deg)">
                <use xlink:href="<?= get_template_directory_uri(); ?>/images/sprite.svg#arrow"></use>
            </svg>
        </button>
        <button class="swiper-button-next__product-top flex items-center justify-center size-10 rounded-full bg-white">
            <svg width="7" height="12">
                <use xlink:href="<?= get_template_directory_uri(); ?>/images/sprite.svg#arrow"></use>
            </svg>
        </button>

        <div class="swiper-dots-pagination swiper-pagination"></div>
    </div>

    <div thumbsSlider="" class="swiper product-swiper-bottom">
        <div class="swiper-wrapper">
            <?php foreach ($slides as $index => $img_id): ?>
                <div class="swiper-slide" 
                     data-slide-index="<?= $index; ?>"
                     data-swiper-slide-index="<?= $index; ?>"> <!-- ← ДОБАВЛЕНО -->
                    <div class="product-swiper-bottom-slide__img">
                        <?= wp_get_attachment_image($img_id, 'thumbnail'); ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>