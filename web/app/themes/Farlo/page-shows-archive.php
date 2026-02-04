<?php
/**
 * Template Name: Shows Archive
 *
 * Displays a grid of show cards.
 */

declare(strict_types=1);

get_header();

$paged = get_query_var('paged') ?: 1;

$args = [
    'post_type'      => 'show',
    'posts_per_page' => 12,
    'paged'          => $paged,
    'post_status'    => 'publish',
    'orderby'        => 'meta_value',
    'meta_key'       => 'show_opening_night',
    'order'          => 'ASC',
];

$shows = new WP_Query($args);
?>

<div class="shows-archive">
    <div class="shows-archive__header">
        <h1 class="shows-archive__title">Upcoming Events</h1>
        <p class="shows-archive__subtitle">Discover the best entertainment in your city this month.</p>
    </div>

    <?php if ($shows->have_posts()) : ?>
        <div class="shows-grid">
            <?php while ($shows->have_posts()) : $shows->the_post(); ?>
                <?php
                $start_date_raw = get_field('show_opening_night');
                $end_date_raw = get_field('end_date');
                $min_price = get_field('minimum_price');
                $ticket_urls = get_field('show_ticket_urls');

                // Format dates
                $date_display = '';
                if ($start_date_raw && $end_date_raw) {
                    $start = DateTime::createFromFormat('d/m/Y', $start_date_raw);
                    $end = DateTime::createFromFormat('d/m/Y', $end_date_raw);
                    if ($start && $end) {
                        $date_display = $start->format('M d') . ' - ' . $end->format('M d Y');
                    }
                }

                // Get first booking URL
                $booking_url = '';
                if (!empty($ticket_urls)) {
                    $urls = explode(',', $ticket_urls);
                    $booking_url = trim($urls[0]);
                }

                // Format price
                $price_display = '';
                if ($min_price !== '' && $min_price !== null) {
                    $price_display = '£' . number_format((float) $min_price, 2);
                }
                ?>

                <article class="show-card">
                    <div class="show-card__image">
                        <?php if (has_post_thumbnail()) : ?>
                            <?php the_post_thumbnail('medium_large'); ?>
                        <?php else : ?>
                            <div class="show-card__image-placeholder"></div>
                        <?php endif; ?>
                    </div>

                    <div class="show-card__content">
                        <h2 class="show-card__title"><?php the_title(); ?></h2>

                        <?php if ($date_display) : ?>
                            <p class="show-card__dates"><?php echo esc_html($date_display); ?></p>
                        <?php endif; ?>

                        <?php if ($booking_url && $price_display) : ?>
                            <a href="<?php echo esc_url($booking_url); ?>" class="show-card__cta" target="_blank" rel="noopener noreferrer">
                                Tickets From <?php echo esc_html($price_display); ?>
                            </a>
                        <?php elseif ($booking_url) : ?>
                            <a href="<?php echo esc_url($booking_url); ?>" class="show-card__cta" target="_blank" rel="noopener noreferrer">
                                Book Tickets
                            </a>
                        <?php endif; ?>
                    </div>
                </article>

            <?php endwhile; ?>
        </div>

        <?php if ($shows->max_num_pages > 1) : ?>
            <nav class="shows-pagination">
                <?php
                echo paginate_links([
                    'total'     => $shows->max_num_pages,
                    'current'   => $paged,
                    'prev_text' => '&lsaquo;',
                    'next_text' => '&rsaquo;',
                ]);
                ?>
            </nav>
        <?php endif; ?>

        <?php wp_reset_postdata(); ?>

    <?php else : ?>
        <p class="shows-archive__no-results">No shows found.</p>
    <?php endif; ?>
</div>

<?php get_footer(); ?>
