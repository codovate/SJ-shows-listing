<?php
/**
 * Single Show Template
 *
 * Displays a single show with full details.
 */

declare(strict_types=1);

get_header();

if (have_posts()) :
    while (have_posts()) :
        the_post();

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
                $date_display = $start->format('M d, Y') . ' - ' . $end->format('M d, Y');
            }
        }

        // Parse booking URLs into array
        $booking_links = [];
        if (!empty($ticket_urls)) {
            $urls = explode(',', $ticket_urls);
            foreach ($urls as $url) {
                $url = trim($url);
                if (!empty($url)) {
                    $booking_links[] = $url;
                }
            }
        }

        // Get first booking URL
        $primary_booking_url = !empty($booking_links) ? $booking_links[0] : '';
        ?>

        <article class="single-show">
            <?php if (has_post_thumbnail()) : ?>
                <div class="single-show__hero">
                    <?php the_post_thumbnail('full'); ?>
                </div>
            <?php endif; ?>

            <div class="single-show__container">
                <div class="single-show__main">
                    <h1 class="single-show__title"><?php the_title(); ?></h1>

                    <?php if ($date_display) : ?>
                        <p class="single-show__dates">
                            <svg class="single-show__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                            </svg>
                            <?php echo esc_html($date_display); ?>
                        </p>
                    <?php endif; ?>

                    <?php if ($primary_booking_url) : ?>
                        <a href="<?php echo esc_url($primary_booking_url); ?>" class="single-show__book-btn" target="_blank" rel="noopener noreferrer">
                            Book Now
                        </a>
                    <?php endif; ?>

                    <div class="single-show__description">
                        <?php the_content(); ?>
                    </div>
                </div>
            </div>
        </article>

    <?php endwhile;
endif;

get_footer();
?>
