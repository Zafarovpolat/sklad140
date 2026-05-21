<?php
get_header();
?>
<?php
global $wp_query;
query_posts([
    'cat' => get_queried_object_id(),
    'posts_per_page' => -1
]);
?>
<link rel="stylesheet" href="<?= get_template_directory_uri(); ?>/css/articles.min.css" />

<div class="articles">
    <div class="container">
        <h1 class="articles__title">Новости и публикации</h1>

        <?php if (have_posts()): ?>
            <?php $all_posts = $wp_query->posts; ?>
            <div class="articles__inner">
                <?php
                $shown = 0;
                while (have_posts()): the_post();
                    $shown++;
                    if ($shown > 8) { continue; }
                    $date = get_the_date('d.m.Y');
                    $excerpt = wp_trim_words(strip_tags(get_the_content()), 35, '...');
                    $thumb_id = get_post_thumbnail_id();
                    $img1 = $thumb_id ? wp_get_attachment_image_src($thumb_id, 'large') : null;
                    $img2 = $thumb_id ? wp_get_attachment_image_src($thumb_id, 'full') : null;
                    $ph   = get_template_directory_uri() . '/images/content/news/placeholder.webp';
                    $src1 = $img1[0] ?? $ph;
                    $src2 = $img2[0] ?? $src1;
                    $alt  = $thumb_id ? ( get_post_meta($thumb_id, '_wp_attachment_image_alt', true) ?: get_the_title() ) : get_the_title();
                ?>
                    <article class="news-card">
                        <div class="news-card__info">
                            <p class="news-card__date"><?= esc_html($date); ?></p>
                            <a href="<?= esc_url(get_permalink()); ?>" class="news-card__title"><?= esc_html(get_the_title()); ?></a>
                            <p class="news-card__description"><?= esc_html($excerpt); ?></p>
                        </div>
                        <div class="news-card__img">
                            <a href="<?= esc_url(get_permalink()); ?>">
                                <img src="<?= esc_url($src1); ?>"
                                     srcset="<?= esc_url($src1); ?> 1x, <?= esc_url($src2); ?> 2x"
                                     alt="<?= esc_attr($alt); ?>">
                            </a>
                        </div>
                    </article>
                <?php endwhile; ?>
            </div>
            <div id="sentinel"></div>
        <?php else: ?>
            <p>Записей пока нет.</p>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded",function(){
    const articlesData = <?= wp_json_encode(array_values(array_map(function($p){
        $id    = $p->ID;
        $title = get_the_title($id);
        $date  = get_the_date('d.m.Y', $id);
        $desc  = wp_trim_words(strip_tags(get_post_field('post_content', $id)), 35, '...');
        $thumb_id = get_post_thumbnail_id($id);
        $img1 = $thumb_id ? wp_get_attachment_image_src($thumb_id, 'large')[0] : get_template_directory_uri().'/images/content/news/placeholder.webp';
        $img2 = $thumb_id ? wp_get_attachment_image_src($thumb_id, 'full')[0]  : $img1;
        $link = get_permalink($id);
        $alt  = esc_attr($title);
        return [
            'id'    => $id,
            'title' => $title,
            'date'  => $date,
            'description' => $desc,
            'link'  => $link,
            'img1'  => $img1,
            'img2'  => $img2,
            'alt'   => $alt,
        ];
    }, $all_posts)), JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES); ?>;

    const container = document.querySelector(".articles__inner");
    const sentinel  = document.getElementById("sentinel");

    let n = Math.min(8, articlesData.length);
    const total = articlesData.length;
    const batch = 8;

    function showSkeleton(count){
        for(let i=0;i<count;i++){
            const b=document.createElement("div");
            b.className="skeleton-block";
            ["skeleton-title","skeleton-text short","skeleton-text medium","skeleton-text long"].forEach(c=>{
                const d=document.createElement("div"); d.className=c; b.appendChild(d);
            });
            container.appendChild(b);
        }
    }

    function loadArticles(count){
        document.querySelectorAll(".skeleton-block").forEach(e=>e.remove());
        for(let i=0;i<count;i++){
            if(n>=total) return;
            const a=articlesData[n++];

            const article=document.createElement("article");
            article.className="news-card";

            const info=document.createElement("div");
            info.className="news-card__info";

            const dateP=document.createElement("p");
            dateP.className="news-card__date";
            dateP.textContent=a.date;

            const titleA=document.createElement("a");
            titleA.className="news-card__title";
            titleA.href=a.link;
            titleA.textContent=a.title;

            const descP=document.createElement("p");
            descP.className="news-card__description";
            descP.textContent=a.description;

            info.appendChild(dateP);
            info.appendChild(titleA);
            info.appendChild(descP);

            const imgDiv=document.createElement("div");
            imgDiv.className="news-card__img";

            const imgA=document.createElement("a");
            imgA.href=a.link;

            const imgEl=document.createElement("img");
            imgEl.src=a.img1;
            imgEl.srcset=a.img1+" 1x, "+a.img2+" 2x";
            imgEl.alt=a.alt;
            imgEl.loading="lazy";
            imgEl.decoding="async";

            imgA.appendChild(imgEl);
            imgDiv.appendChild(imgA);

            article.appendChild(info);
            article.appendChild(imgDiv);
            container.appendChild(article);
        }
    }

    function loadBatch(){
        if(n>=total) return;
        showSkeleton(batch);
        setTimeout(()=>loadArticles(batch), 800);
    }

    new IntersectionObserver(entries=>{
        entries.forEach(entry=>{
            if(entry.isIntersecting && n<total) loadBatch();
        });
    },{rootMargin:"100px"}).observe(sentinel);

    if(total > n) loadBatch();
});
</script>

<?php
get_footer();
