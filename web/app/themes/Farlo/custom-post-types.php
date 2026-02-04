<?php
/**
 * Custom Post Types registration.
 */

declare(strict_types=1);

/**
 * Register the Shows custom post type.
 */
add_action('init', function (): void {
    $labels = [
        'name'                  => 'Shows',
        'singular_name'         => 'Show',
        'add_new'               => 'Add New',
        'add_new_item'          => 'Add New Show',
        'edit_item'             => 'Edit Show',
        'new_item'              => 'New Show',
        'view_item'             => 'View Show',
        'view_items'            => 'View Shows',
        'search_items'          => 'Search Shows',
        'not_found'             => 'No shows found',
        'not_found_in_trash'    => 'No shows found in Trash',
        'all_items'             => 'All Shows',
        'archives'              => 'Show Archives',
        'attributes'            => 'Show Attributes',
        'insert_into_item'      => 'Insert into show',
        'uploaded_to_this_item' => 'Uploaded to this show',
        'filter_items_list'     => 'Filter shows list',
        'items_list_navigation' => 'Shows list navigation',
        'items_list'            => 'Shows list',
        'item_published'        => 'Show published.',
        'item_updated'          => 'Show updated.',
    ];

    $args = [
        'labels'              => $labels,
        'public'              => true,
        'publicly_queryable'  => true,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'show_in_rest'        => true,
        'query_var'           => true,
        'rewrite'             => ['slug' => 'show'],
        'capability_type'     => 'post',
        'has_archive'         => true,
        'hierarchical'        => false,
        'menu_position'       => 5,
        'menu_icon'           => 'dashicons-tickets-alt',
        'supports'            => ['title', 'editor', 'thumbnail', 'custom-fields'],
    ];

    register_post_type('show', $args);
});
