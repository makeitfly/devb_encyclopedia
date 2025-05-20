<?php

/**
 * @file
 * Provide views data for DEVB Encyclopedia module.
 */

/**
 * Implements hook_views_data().
 */
function devb_encyclopedia_views_data(): array {
  $data['views']['devb_encyclopedia_search_summary'] = [
    'title' => t('Encyclopedia search summary'),
    'help' => t('Shows encyclopedia search query summary'),
    'area' => [
      'id' => 'devb_encyclopedia_search_summary',
    ],
  ];
  return $data;
}
