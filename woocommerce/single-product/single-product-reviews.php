<?php
defined('ABSPATH') || exit;

global $product;

$reviews = get_comments([
    'post_id' => $product->get_id(),
    'status' => 'approve'
]);
?>

<div class="product-info-content product-reviews" data-content="reviews">

    <div class="product-reviews__header">
        <h4 class="product-info-content__title">Отзывы</h4>

        <div class="product-reviews__header-nav">
            <button
                class="swiper-button-prev__product-reviews flex items-center justify-center size-10 rounded-full bg-white">
                <svg style="transform: rotate(180deg)" width="7" height="12">
                    <use xlink:href="<?= get_template_directory_uri(); ?>/images/sprite.svg#arrow"></use>
                </svg>
            </button>
            <button
                class="swiper-button-next__product-reviews flex items-center justify-center size-10 rounded-full bg-white">
                <svg width="7" height="12">
                    <use xlink:href="<?= get_template_directory_uri(); ?>/images/sprite.svg#arrow"></use>
                </svg>
            </button>
        </div>
    </div>

    <form id="custom-review-form" class="product-review-form hidden">
        <div class="product-review-form__rating">
            <label>Ваша оценка *</label>
            <div class="rating-stars" data-rating="0">
                <?php for ($i = 1; $i <= 5; $i++): ?>
                    <span class="star" data-value="<?= $i ?>">★</span>
                <?php endfor; ?>
            </div>
            <input type="hidden" name="rating" id="rating-value">
        </div>

        <label>Ваш отзыв *</label>
        <textarea name="comment" required></textarea>

        <label>Имя *</label>
        <input type="text" name="author" required>

        <label>Email *</label>
        <input type="email" name="email" required>

        <input type="hidden" name="product_id" value="<?= $product->get_id(); ?>">

        <button class="button button--fill">Отправить</button>
        <div class="review-form-result"></div>
    </form>

    <?php if (!empty($reviews)): ?>
        <div class="swiper product-reviews-swiper">
            <div class="swiper-wrapper">

                <?php foreach ($reviews as $review):
                    $rating = intval(get_comment_meta($review->comment_ID, 'rating', true));
                    $reply = get_comment_reply($review->comment_ID);
                    ?>
                    <div class="swiper-slide">
                        <div class="product-reviews-slide">

                            <div class="product-reviews-slide__header">
                                <div class="product-reviews-slide__rating">
                                    <p><?= $rating ?></p>
                                    <svg width="16" height="16">
                                        <use xlink:href="<?= get_template_directory_uri(); ?>/images/sprite.svg#star-rounded">
                                        </use>
                                    </svg>
                                </div>
                                <p class="product-reviews-slide__date"><?= get_comment_date('j F, Y', $review) ?></p>
                            </div>

                            <div class="product-reviews-slide__review">
                                <p class="product-reviews-slide__review-name"><?= esc_html($review->comment_author); ?></p>
                                <p class="product-reviews-slide__review-text"><?= esc_html($review->comment_content); ?></p>
                            </div>

                            <?php if ($reply): ?>
                                <div class="product-reviews-slide__response">
                                    <p class="product-reviews-slide__response-subtitle">Ответ организации</p>
                                    <div class="product-reviews-slide__response-header">
                                        <p class="product-reviews-slide__response-header__name"><?= $reply->comment_author; ?></p>
                                        <p class="product-reviews-slide__response-header__date">
                                            <?= get_comment_date('j F, Y', $reply) ?>
                                        </p>
                                    </div>
                                    <p class="product-reviews-slide__response-text"><?= esc_html($reply->comment_content); ?></p>
                                </div>
                            <?php endif; ?>

                        </div>
                    </div>
                <?php endforeach; ?>

            </div>

            <div class="swiper-scrollbar__wrapper">
                <div class="swiper-scrollbar__left">
                    <p class="swiper-scrollbar__subtitle">Зажми и потяни</p>
                    <img class="swiper-scrollbar__icon" src="<?= get_template_directory_uri(); ?>/images/gif/drag.gif"
                        loading="lazy">
                </div>
                <div class="swiper-scrollbar"></div>
            </div>
        </div>
    <?php else: ?>

        <p class="no-reviews-text">Отзывов пока нет</p>

    <?php endif; ?>

    <div class="product-reviews__row">
        <button class="button button--fill product-reviews__button" id="open-review-form">Оставить отзыв</button>
    </div>

    <button class="button button--fill product-reviews__take-review-btn">Оставить отзыв</button>

</div>