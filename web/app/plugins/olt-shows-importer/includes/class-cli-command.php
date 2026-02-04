<?php

declare(strict_types=1);

namespace OLT\ShowsImporter;

class CLI_Command
{
    /** @var \cli\progress\Bar|null */
    private $progress = null;

    /**
     * Import shows from the Official London Theatre API.
     *
     * ## EXAMPLES
     *
     *     # Import all shows
     *     wp import-shows
     *
     * @when after_wp_load
     */
    public function __invoke(array $args, array $assoc_args): void
    {
        \WP_CLI::log('Starting OLT Shows Import...');
        \WP_CLI::log('');

        $importer = new Show_Importer();

        // Set up progress callback
        $importer->set_progress_callback([$this, 'handle_progress']);

        try {
            $stats = $importer->import();

            \WP_CLI::log('');
            \WP_CLI::success('Import completed!');
            \WP_CLI::log('');
            \WP_CLI::log(sprintf('  Created: %d', $stats['created']));
            \WP_CLI::log(sprintf('  Updated: %d', $stats['updated']));
            \WP_CLI::log(sprintf('  Skipped: %d', $stats['skipped']));

            if (!empty($stats['errors'])) {
                \WP_CLI::log('');
                \WP_CLI::warning(sprintf('%d errors occurred:', count($stats['errors'])));

                foreach ($stats['errors'] as $error) {
                    \WP_CLI::log(sprintf(
                        '  - [OLT ID: %d] %s: %s',
                        $error['olt_id'],
                        $error['title'],
                        $error['error']
                    ));
                }
            }
        } catch (\Exception $e) {
            \WP_CLI::error(sprintf('Import failed: %s', $e->getMessage()));
        }
    }

    /**
     * Handle progress callbacks from importer.
     *
     * @param string $type Event type: 'log', 'start', 'tick', 'finish'
     * @param mixed ...$args Additional arguments
     */
    public function handle_progress(string $type, mixed ...$args): void
    {
        switch ($type) {
            case 'log':
                \WP_CLI::log($args[0] ?? '');
                break;

            case 'start':
                $total = $args[0] ?? 0;
                if ($total > 0) {
                    $this->progress = \WP_CLI\Utils\make_progress_bar('Importing shows', $total);
                }
                break;

            case 'tick':
                if ($this->progress) {
                    $this->progress->tick();
                }
                break;

            case 'finish':
                if ($this->progress) {
                    $this->progress->finish();
                    $this->progress = null;
                }
                break;
        }
    }
}
