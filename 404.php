<?php get_header(); ?>

<!-- === ERROR === -->
<section class="section error">
  <div class="container">
    <div class="error-content">
      <div class="error-text">
        <p class="error__descr">упс, попробуйте<br>открыть другую страницу</p>
      </div>
      <img src="<?= get_template_directory_uri(); ?>/images/error1.webp" alt="404" class="error__img" loading="lazy" decoding="async">
    </div>
  </div>
</section>
<style>.section.feedback{display: none;}</style>

<?php get_footer(); ?>