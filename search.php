<?php get_header(); ?>

<section class="search-results">
    <div class="container">
        <h1>Результаты поиска по запросу: «<?php echo get_search_query(); ?>»</h1>

        <?php if (have_posts()) : ?>
            <ul class="search-results__list">
                <?php while (have_posts()) : the_post(); ?>
                    <li>
                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        <p><?php the_excerpt(); ?></p>
                    </li>
                <?php endwhile; ?>
            </ul>

            <?php the_posts_pagination(); ?>
        <?php else : ?>
            <p>Ничего не найдено. Попробуйте изменить запрос.</p>
        <?php endif; ?>
    </div>
</section>

<?php get_footer(); ?>
