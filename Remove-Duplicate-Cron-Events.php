<?php
/*
Plugin Name: Remove Duplicate Cron Events
Description: Plugin ini akan menghapus duplicate cron events di WordPress.
Version: 1.0
Author: Farras Indyawan
Author URI: https://wa.me/6281212833425
 */

function remove_duplicate_cron_events() {
    // Mendapatkan semua cron events dari opsi 'cron'
    $crons = get_option('cron');

    // Jika tidak ada cron events, keluar dari fungsi
    if (empty($crons)) {
        return;
    }

    // Mendapatkan daftar jadwal cron yang tersedia
    $schedules = wp_get_schedules();

    // Array untuk menyimpan cron events unik
    $unique_crons = array();

    // Loop melalui setiap cron event
    foreach ($crons as $timestamp => $cron) {
        // Periksa apakah timestamp adalah angka dan jadwalnya valid
        if (!is_numeric($timestamp) || !isset($schedules[$timestamp])) {
            continue;
        }

        foreach ($cron as $hook => $events) {
            foreach ($events as $key => $event) {
                $args = $event['args'];
                $event_key = $hook . serialize($args);

                // Jika cron event sudah ada dalam array unik, hapusnya
                if (isset($unique_crons[$event_key])) {
                    wp_unschedule_event($timestamp, $hook, $args);
                } else {
                    // Jika tidak, tambahkan ke array unik
                    $unique_crons[$event_key] = true;
                }
            }
        }
    }
}

// Jalankan fungsi saat plugin diaktifkan
register_activation_hook(__FILE__, 'remove_duplicate_cron_events');


// Jalan di action init
add_action('init', 'remove_duplicate_cron_events');
