<?php

declare(strict_types=1);

namespace OLT\ShowsImporter;

class API_Client
{
    private const API_BASE = 'https://officiallondontheatre.com/wp-json/wp/v2';
    private const SHOWS_ENDPOINT = '/show';
    private const MEDIA_ENDPOINT = '/media';
    private const PER_PAGE = 100;
    private const REQUEST_TIMEOUT = 30;

    /**
     * Fetch all shows with pagination handling.
     *
     * @return \Generator<array> Yields show data arrays
     * @throws \RuntimeException On API failure
     */
    public function get_all_shows(): \Generator
    {
        $page = 1;
        $total_pages = 1;

        do {
            $response = $this->request(self::SHOWS_ENDPOINT, [
                'per_page' => self::PER_PAGE,
                'page' => $page,
            ]);

            if (is_wp_error($response)) {
                throw new \RuntimeException(
                    sprintf('API request failed: %s', $response->get_error_message())
                );
            }

            $status_code = wp_remote_retrieve_response_code($response);
            if ($status_code !== 200) {
                throw new \RuntimeException(
                    sprintf('API returned status %d', $status_code)
                );
            }

            // Extract pagination from headers on first request
            if ($page === 1) {
                $total_pages = (int) wp_remote_retrieve_header($response, 'X-WP-TotalPages');
                if ($total_pages === 0) {
                    $total_pages = 1;
                }
            }

            $body = wp_remote_retrieve_body($response);
            $shows = json_decode($body, true);

            if (!is_array($shows)) {
                throw new \RuntimeException('Invalid API response format');
            }

            foreach ($shows as $show) {
                yield $show;
            }

            $page++;
        } while ($page <= $total_pages);
    }

    /**
     * Fetch media details for multiple IDs in batch.
     *
     * @param int[] $media_ids Array of media IDs
     * @return array<int, array> Map of media_id => media_data
     */
    public function get_media_batch(array $media_ids): array
    {
        if (empty($media_ids)) {
            return [];
        }

        // WP REST API supports include parameter for batch fetching
        $response = $this->request(self::MEDIA_ENDPOINT, [
            'include' => implode(',', $media_ids),
            'per_page' => count($media_ids),
        ]);

        if (is_wp_error($response)) {
            return [];
        }

        $status_code = wp_remote_retrieve_response_code($response);
        if ($status_code !== 200) {
            return [];
        }

        $body = wp_remote_retrieve_body($response);
        $media_items = json_decode($body, true);

        $result = [];
        if (is_array($media_items)) {
            foreach ($media_items as $item) {
                if (isset($item['id'])) {
                    $result[$item['id']] = $item;
                }
            }
        }

        return $result;
    }

    /**
     * Make HTTP request to API.
     *
     * @param string $endpoint API endpoint path
     * @param array $params Query parameters
     * @return array|\WP_Error Response or error
     */
    private function request(string $endpoint, array $params = []): array|\WP_Error
    {
        $url = self::API_BASE . $endpoint;

        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }

        return wp_remote_get($url, [
            'timeout' => self::REQUEST_TIMEOUT,
            'headers' => [
                'Accept' => 'application/json',
            ],
        ]);
    }
}
