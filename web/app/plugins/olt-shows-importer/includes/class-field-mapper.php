<?php

declare(strict_types=1);

namespace OLT\ShowsImporter;

class Field_Mapper
{
    /**
     * Transform remote show data to local format.
     *
     * @param array $remote_show API show data
     * @return array Mapped data with post_data, meta, and featured_media_id
     */
    public function map(array $remote_show): array
    {
        $acf = $remote_show['acf'] ?? [];

        return [
            'post_data' => [
                'post_title'   => $this->decode_html($remote_show['title']['rendered'] ?? ''),
                'post_content' => $remote_show['content']['rendered'] ?? '',
                'post_type'    => 'show',
                'post_status'  => 'publish',
            ],
            'meta' => [
                'olt_show_id'        => (int) ($remote_show['id'] ?? 0),
                'show_opening_night' => $this->format_date($acf['show_opening_night'] ?? ''),
                'end_date'           => $this->get_end_date($acf),
                'show_ticket_urls'   => $this->format_ticket_urls($acf['show_ticket_urls'] ?? null),
                'minimum_price'      => $this->format_price($acf['minimum_price'] ?? null),
            ],
            'featured_media_id' => (int) ($remote_show['featured_media'] ?? 0),
        ];
    }

    /**
     * Convert YYYYMMDD to d/m/Y format.
     *
     * @param string $date Date in YYYYMMDD format
     * @return string Date in d/m/Y format or empty string
     */
    private function format_date(string $date): string
    {
        $date = trim($date);

        if (empty($date) || strlen($date) !== 8) {
            return '';
        }

        $datetime = \DateTime::createFromFormat('Ymd', $date);

        if (!$datetime) {
            return '';
        }

        return $datetime->format('d/m/Y');
    }

    /**
     * Get end date from booking_until OR closing_night.
     *
     * @param array $acf ACF field data
     * @return string Formatted date or empty string
     */
    private function get_end_date(array $acf): string
    {
        // show_booking_until takes priority
        if (!empty($acf['show_booking_until'])) {
            return $this->format_date((string) $acf['show_booking_until']);
        }

        if (!empty($acf['show_closing_night'])) {
            return $this->format_date((string) $acf['show_closing_night']);
        }

        return '';
    }

    /**
     * Extract ticket URLs to comma-separated string.
     *
     * @param array|null $ticket_urls Array of ticket URL objects or null
     * @return string Comma-separated URLs or empty string
     */
    private function format_ticket_urls(array|null $ticket_urls): string
    {
        if (empty($ticket_urls) || !is_array($ticket_urls)) {
            return '';
        }

        $urls = [];

        foreach ($ticket_urls as $ticket) {
            if (is_array($ticket) && !empty($ticket['show_ticket_url'])) {
                $urls[] = $ticket['show_ticket_url'];
            }
        }

        return implode(',', $urls);
    }

    /**
     * Format price as string.
     *
     * @param mixed $price Price value
     * @return string Price as string or empty string
     */
    private function format_price(mixed $price): string
    {
        if ($price === null || $price === '') {
            return '';
        }

        return (string) $price;
    }

    /**
     * Decode HTML entities in text.
     *
     * @param string $text Text with potential HTML entities
     * @return string Decoded text
     */
    private function decode_html(string $text): string
    {
        return html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
}
