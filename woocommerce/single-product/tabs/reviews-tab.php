<?php
defined("ABSPATH") || exit;
global $product;

$reviews = get_comments([
    "post_id" => $product->get_id(),
    "status"  => "approve"
]);

$user = wp_get_current_user();
$is_user = is_user_logged_in();

$reviews_count = count($reviews);
?>

<div class="product-info-content product-reviews" data-content="reviews">

    <!-- ================= HEADER ================= -->
    <div class="product-reviews__header">
        <h4 class="product-info-content__title">Отзывы</h4>

        <!-- Стрелки показываем только если отзывов > 3 -->
        <?php if ($reviews_count > 3): ?>
        <div class="product-reviews__header-nav">
            <button class="swiper-button-prev__product-reviews flex items-center justify-center size-10 rounded-full bg-white">
                <svg style="transform: rotate(180deg)" width="7" height="12">
                    <path d="M1 1l5 5-5 5" stroke="#031343" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>

            <button class="swiper-button-next__product-reviews flex items-center justify-center size-10 rounded-full bg-white">
                <svg width="7" height="12">
                    <path d="M1 1l5 5-5 5" stroke="#031343" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>
        </div>
        <?php endif; ?>
    </div>

    <!-- ================= BUTTON OPEN FORM ================= -->
    <div class="product-reviews__row">
        <button id="toggle-review-form" class="button button--fill product-reviews__button">
            Оставить отзыв
        </button>
    </div>

    <!-- ================= REVIEW FORM ================= -->
    <div id="product-review-form" class="review-form-hidden">

        <form id="custom-review-form">

            <!-- РЕЙТИНГ -->
            <div class="form-group">
                <label>Ваша оценка *</label>
                <div class="rating-stars">
                    <span class="star" data-value="1">★</span>
                    <span class="star" data-value="2">★</span>
                    <span class="star" data-value="3">★</span>
                    <span class="star" data-value="4">★</span>
                    <span class="star" data-value="5">★</span>
                </div>
                <input type="hidden" name="rating" id="rating-value" required>
            </div>

            <!-- ОТЗЫВ -->
            <div class="form-group">
                <label>Ваш отзыв *</label>
                <textarea name="comment" required></textarea>
            </div>

            <!-- ИМЯ / EMAIL: разные для гостей/юзеров -->
            <?php if (!$is_user): ?>
                <div class="form-group">
                    <label>Имя *</label>
                    <input type="text" name="author" required>
                </div>

                <div class="form-group">
                    <label>Email *</label>
                    <input type="email" name="email" required>
                </div>
            <?php else: ?>
                <input type="hidden" name="author" value="<?= esc_attr($user->display_name); ?>">
                <input type="hidden" name="email" value="<?= esc_attr($user->user_email); ?>">
            <?php endif; ?>

            <input type="hidden" name="product_id" value="<?= $product->get_id(); ?>">

            <button type="submit" class="button button--fill">Отправить отзыв</button>

            <div class="review-result"></div>

        </form>
    </div>

    <!-- ================= SWIPER REVIEWS ================= -->
    <div class="swiper product-reviews-swiper">
        <div class="swiper-wrapper">

            <?php if ($reviews): ?>

                <?php foreach ($reviews as $review):

                        if ($review->comment_parent > 0) continue;

                        $rating = get_comment_meta($review->comment_ID, "rating", true);

                        $replies = get_comments([
                            'parent' => $review->comment_ID,
                            'status' => 'approve'
                        ]);
                    ?>

                    <div class="swiper-slide">
                        <div class="product-reviews-slide">

                            <div class="product-reviews-slide__header">
                                <div class="product-reviews-slide__rating">
                                    <p><?= $rating; ?></p>
                                    <svg width="16" height="16">
                                        <use xlink:href="<?= get_template_directory_uri(); ?>/images/sprite.svg#star-rounded"></use>
                                    </svg>
                                </div>
                                <p class="product-reviews-slide__date">
                                    <?= date_i18n("j F Y", strtotime($review->comment_date)); ?>
                                </p>
                            </div>

                            <div class="product-reviews-slide__review">
                                <p class="product-reviews-slide__review-name"><?= esc_html($review->comment_author); ?></p>
                                <p class="product-reviews-slide__review-text"><?= esc_html($review->comment_content); ?></p>
                            </div>

                            <?php if (!empty($replies)): ?>
                                <?php foreach ($replies as $reply): ?>
                                <div class="product-reviews-slide__response">
                                    <p class="product-reviews-slide__response-subtitle">Ответ организации</p>

                                    <div class="product-reviews-slide__response-header">
                                        <p class="product-reviews-slide__response-header__name"><?= esc_html($reply->comment_author); ?></p>
                                        <p class="product-reviews-slide__response-header__date"><?= date_i18n("j F Y", strtotime($reply->comment_date)); ?></p>
                                    </div>

                                    <p class="product-reviews-slide__response-text"><?= esc_html($reply->comment_content); ?></p>
                                </div>
                                <?php endforeach; ?>
                            <?php endif; ?>

                        </div>
                    </div>

                <?php endforeach; ?>

            <?php else: ?>

                <div class="swiper-slide">
                    <div class="product-reviews-slide">
                        <p>Отзывов пока нет</p>
                    </div>
                </div>

            <?php endif; ?>

        </div>

        <!-- scroll bar только если отзывов > 3 -->
        <?php if ($reviews_count > 3): ?>
        <div class="swiper-scrollbar__wrapper">
            <div class="swiper-scrollbar__left">
                <p class="swiper-scrollbar__subtitle">Зажми и потяни</p>
                <img class="swiper-scrollbar__icon"
                     src="<?= get_template_directory_uri(); ?>/images/gif/drag.gif" alt="">
            </div>
            <div class="swiper-scrollbar"></div>
        </div>
        <?php endif; ?>

    </div>

    <button class="button button--fill product-reviews__take-review-btn">
        Оставить отзыв
    </button>

</div>



<style>

/* === FORM WRAPPER (без пустого пространства!) === */
#product-review-form {
    background: #ffffff;
    border-radius: 20px;
    padding: 0;
    margin-top: 0;
    max-height: 0;
    overflow: hidden;
    opacity: 0;
    transform: translateY(-10px);
    transition: .35s ease;
    box-shadow: none;
}

#product-review-form.review-form-show {
    padding: 24px;
    margin-top: 20px;
    max-height: 800px;
    opacity: 1;
    transform: translateY(0);
    box-shadow: 0 8px 30px rgba(0,0,0,0.06);
}



/* === INPUTS / TEXTAREA === */
#product-review-form input,
#product-review-form textarea {
    width: 100%;
    background: #f6f8fb;
    border: 1px solid #d7dfeb;
    border-radius: 14px;
    padding: 14px 16px;
    font-size: 15px;
    transition: .2s ease;
}

#product-review-form input:focus,
#product-review-form textarea:focus {
    outline: none;
    border-color: #4D96FF;
    background: #fff;
    box-shadow: 0 0 0 3px rgba(77,150,255,0.12);
}

#product-review-form textarea {
    height: 140px;
    resize: vertical;
}

/* === LABEL === */
#product-review-form label {
    display: block;
    font-weight: 600;
    font-size: 15px;
    margin-bottom: 6px;
    color: #031343;
}

/* === STARS === */
.rating-stars {
    display: flex;
    gap: 6px;
    margin-bottom: 14px;
    cursor: pointer;
}

.rating-stars .star {
    font-size: 32px;
    color: #c7cdd8;
    transition: .2s ease;
}

.rating-stars .star:hover {
    color: #ffb234;
    transform: scale(1.1);
}

.rating-stars .star.active {
    color: #ffb234;
}

/* === SUBMIT BUTTON === */
#product-review-form .button--fill {
    width: 100%;
    padding: 16px;
    border-radius: 14px;
    font-size: 17px;
    margin-top: 12px;
    background: linear-gradient(135deg, #4D96FF, #2f7afc);
    transition: .25s ease;
    display: inline-flex;
    justify-content: center;
}

#product-review-form .button--fill:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(77,150,255,.25);
}

/* === RESULT MESSAGE === */
.review-result {
    margin-top: 14px;
    font-size: 15px;
    font-weight: 600;
    text-align: left;
}

.review-success { color: #28a745; }
.review-error { color: #d62828; }



</style>

<script>
 document.addEventListener("DOMContentLoaded", () => {

    // Toggle button
    const toggleBtn = document.querySelector(".product-reviews__button");
    const formWrap = document.querySelector("#product-review-form");

    if (toggleBtn && formWrap) {
        toggleBtn.addEventListener("click", () => {
            formWrap.classList.toggle("review-form-show");
        });
    }

    // Star rating
    const stars = document.querySelectorAll(".rating-stars .star");
    const ratingInput = document.querySelector("#rating-value");

    stars.forEach(star => {
        star.addEventListener("click", () => {
            const value = +star.dataset.value;
            ratingInput.value = value;

            stars.forEach(s => s.classList.remove("active"));
            for (let i = 0; i < value; i++) stars[i].classList.add("active");
        });
    });

    // AJAX submit
    const formElem = document.querySelector("#custom-review-form");

    if (formElem) {
        formElem.addEventListener("submit", async e => {
            e.preventDefault();

            const result = formElem.querySelector(".review-result");
            result.textContent = "Отправляем...";
            result.className = "review-result";

            const response = await fetch("/wp-admin/admin-ajax.php", {
                method: "POST",
                body: new URLSearchParams({
                    action: "submit_review",
                    ...Object.fromEntries(new FormData(formElem))
                })
            });

            const json = await response.json();

            if (json.success) {
                result.textContent = "Спасибо за отзыв. Ваш отзыв будет опубликован после модерации.";
                result.classList.add("review-success");

                formElem.reset();
                stars.forEach(s => s.classList.remove("active"));

                setTimeout(() => formWrap.classList.remove("review-form-show"), 3000);

            } else {
                result.textContent = "❌ " + json.message;
                result.classList.add("review-error");
            }
        });
    }
});




</script>