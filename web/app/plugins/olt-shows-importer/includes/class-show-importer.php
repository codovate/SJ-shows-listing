<?php

declare(strict_types=1);

namespace OLT\ShowsImporter;

class Show_Importer
{
    private API_Client $api;
    private Field_Mapper $mapper;
    private Image_Handler $image_handler;

    /** @var callable|null */
    private $progress_callback = null;

    public function __construct()
    {
        $this->api = new API_Client();
        $this->mapper = new Field_Mapper();
        $this->image_handler = new Image_Handler();
    }

    /**
     * Set progress callback for CLI feedback.
     *
     * @param callable $callback Callback function
     */
    public function set_progress_callback(callable $callback): void
    {
        $this->progress_callback = $callback;
    }

    /**
     * Run the import process.
     *
     * @return array{created: int, updated: int, skipped: int, errors: array}
     */
    public function import(): array
    {
        $stats = [
            'created' => 0,
            'updated' => 0,
            'skipped' => 0,
            'errors' => [],
        ];

        $shows_to_process = [];
        $media_ids = [];

        // Phase 1: Collect all shows and media IDs
        $this->log('Fetching shows from OLT API...');

        try {
            foreach ($this->api->get_all_shows() as $show) {
                $shows_to_process[] = $show;

                if (!empty($show['featured_media'])) {
                    $media_ids[] = (int) $show['featured_media'];
                }
            }
        } catch (\RuntimeException $e) {
            throw $e;
        }

        $total = count($shows_to_process);
        $this->log(sprintf('Found %d shows to process', $total));

        if ($total === 0) {
            return $stats;
        }

        // Phase 2: Batch fetch media data
        $this->log('Fetching media data...');
        $media_ids = array_unique($media_ids);
        $media_data = $this->fetch_media_in_batches($media_ids);
        $this->log(sprintf('Fetched %d media items', count($media_data)));

        // Phase 3: Process each show
        $this->start_progress($total);

        foreach ($shows_to_process as $index => $show) {
            $title = $show['title']['rendered'] ?? 'Unknown';
            $this->tick_progress($index + 1, $total, $title);

            try {
                $result = $this->process_show($show, $media_data);
                $stats[$result]++;
            } catch (\Exception $e) {
                $stats['errors'][] = [
                    'olt_id' => $show['id'] ?? 0,
                    'title' => $title,
                    'error' => $e->getMessage(),
                ];
            }
        }

        $this->finish_progress();

        return $stats;
    }

    /**
     * Process a single show.
     *
     * @param array $show Remote show data
     * @param array $media_data Map of media ID to media data
     * @return string 'created'|'updated'|'skipped'
     * @throws \RuntimeException On failure
     */
    private function process_show(array $show, array $media_data): string
    {
        $mapped = $this->mapper->map($show);
        $olt_show_id = $mapped['meta']['olt_show_id'];

        if ($olt_show_id === 0) {
            throw new \RuntimeException('Missing show ID');
        }

        // Find existing post by olt_show_id
        $existing_post_id = $this->find_existing_post($olt_show_id);

        if ($existing_post_id) {
            // Update existing post
            $mapped['post_data']['ID'] = $existing_post_id;
            $post_id = wp_update_post($mapped['post_data'], true);
            $action = 'updated';
        } else {
            // Create new post
            $post_id = wp_insert_post($mapped['post_data'], true);
            $action = 'created';
        }

        if (is_wp_error($post_id)) {
            throw new \RuntimeException($post_id->get_error_message());
        }

        // Update meta fields
        foreach ($mapped['meta'] as $key => $value) {
            update_post_meta($post_id, $key, $value);
        }

        // Handle featured image
        $featured_media_id = $mapped['featured_media_id'];

        if ($featured_media_id > 0 && isset($media_data[$featured_media_id])) {
            // Only update image if it has changed
            if ($this->image_handler->needs_update($post_id, $featured_media_id)) {
                $this->image_handler->import_featured_image(
                    $post_id,
                    $media_data[$featured_media_id]
                );
            }
        }

        return $action;
    }

    /**
     * Find existing post by olt_show_id meta.
     *
     * @param int $olt_show_id Remote show ID
     * @return int|null Local post ID or null
     */
    private function find_existing_post(int $olt_show_id): ?int
    {
        $query = new \WP_Query([
            'post_type' => 'show',
            'post_status' => 'any',
            'posts_per_page' => 1,
            'fields' => 'ids',
            'meta_query' => [
                [
                    'key' => 'olt_show_id',
                    'value' => $olt_show_id,
                    'compare' => '=',
                    'type' => 'NUMERIC',
                ],
            ],
            'no_found_rows' => true,
            'update_post_meta_cache' => false,
            'update_post_term_cache' => false,
        ]);

        $posts = $query->posts;

        return !empty($posts) ? (int) $posts[0] : null;
    }

    /**
     * Fetch media in batches (API limit ~100 per request).
     *
     * @param int[] $media_ids Array of media IDs
     * @return array<int, array> Map of media ID to media data
     */
    private function fetch_media_in_batches(array $media_ids): array
    {
        if (empty($media_ids)) {
            return [];
        }

        $result = [];
        $chunks = array_chunk($media_ids, 100);

        foreach ($chunks as $chunk) {
            $batch = $this->api->get_media_batch($chunk);
            // Use + operator to preserve numeric keys (array_merge re-indexes)
            $result = $result + $batch;
        }

        return $result;
    }

    /**
     * Log a message via callback.
     */
    private function log(string $message): void
    {
        if ($this->progress_callback) {
            call_user_func($this->progress_callback, 'log', $message);
        }
    }

    /**
     * Start progress tracking.
     */
    private function start_progress(int $total): void
    {
        if ($this->progress_callback) {
            call_user_func($this->progress_callback, 'start', $total);
        }
    }

    /**
     * Tick progress.
     */
    private function tick_progress(int $current, int $total, string $item): void
    {
        if ($this->progress_callback) {
            call_user_func($this->progress_callback, 'tick', $current, $total, $item);
        }
    }

    /**
     * Finish progress tracking.
     */
    private function finish_progress(): void
    {
        if ($this->progress_callback) {
            call_user_func($this->progress_callback, 'finish');
        }
    }
}
