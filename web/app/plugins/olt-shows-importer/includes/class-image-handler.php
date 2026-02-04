<?php

declare(strict_types=1);

namespace OLT\ShowsImporter;

class Image_Handler
{
    /**
     * Import and attach featured image to post.
     *
     * @param int $post_id Local post ID
     * @param array $media_data Media data from API
     * @return int|false Attachment ID or false on failure
     */
    public function import_featured_image(int $post_id, array $media_data): int|false
    {
        $source_url = $media_data['source_url'] ?? '';

        if (empty($source_url)) {
            return false;
        }

        // Check if we already have this image by source URL
        $existing = $this->find_existing_attachment($source_url);

        if ($existing) {
            set_post_thumbnail($post_id, $existing);
            return $existing;
        }

        // Require media functions for sideload
        require_once ABSPATH . 'wp-admin/includes/media.php';
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';

        // Build description from alt text or title
        $description = $media_data['alt_text'] ?? '';

        if (empty($description) && isset($media_data['title']['rendered'])) {
            $description = $media_data['title']['rendered'];
        }

        // Sideload the image
        $attachment_id = media_sideload_image($source_url, $post_id, $description, 'id');

        if (is_wp_error($attachment_id)) {
            return false;
        }

        // Set as featured image
        set_post_thumbnail($post_id, $attachment_id);

        // Store OLT media ID for future reference
        if (isset($media_data['id'])) {
            update_post_meta($attachment_id, '_olt_media_id', $media_data['id']);
        }

        return $attachment_id;
    }

    /**
     * Check if image needs updating based on OLT media ID.
     *
     * @param int $post_id Local post ID
     * @param int $olt_media_id Remote media ID
     * @return bool True if update needed
     */
    public function needs_update(int $post_id, int $olt_media_id): bool
    {
        $thumbnail_id = get_post_thumbnail_id($post_id);

        if (!$thumbnail_id) {
            return true;
        }

        $stored_olt_id = get_post_meta($thumbnail_id, '_olt_media_id', true);

        return (int) $stored_olt_id !== $olt_media_id;
    }

    /**
     * Find existing attachment by source URL.
     *
     * @param string $source_url Original image URL
     * @return int|false Attachment ID or false if not found
     */
    private function find_existing_attachment(string $source_url): int|false
    {
        global $wpdb;

        // WordPress stores source URL in _source_url meta (added by media_sideload_image)
        $attachment_id = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT post_id FROM {$wpdb->postmeta}
                 WHERE meta_key = '_source_url' AND meta_value = %s
                 LIMIT 1",
                $source_url
            )
        );

        return $attachment_id ? (int) $attachment_id : false;
    }
}
