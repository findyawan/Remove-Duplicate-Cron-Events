<?php
/*
Plugin Name: Remove Duplicate Cron Events
Description: Plugin ini akan menghapus duplicate cron events di WordPress.
Version: 1.0
Author: Farras Indyawan
Author URI: https://wa.me/6281212833425
 */

function remove_duplicate_cron_events($arg = null) {
    // Jika argumen tidak null, keluarkan pesan debug
    if ($arg !== null) {
        error_log("Unexpected argument passed to remove_duplicate_cron_events: " . print_r($arg, true));
    }

    // Mendapatkan semua cron events
    $crons = wp_get_scheduled_events();

    // Jika tidak ada cron events, keluar dari fungsi
    if (empty($crons)) {
        return;
    }

    // Array untuk menyimpan cron events unik
    $unique_crons = array();

    // Loop melalui setiap cron event
    foreach ($crons as $cron) {
        $hook = $cron->hook;
        $args = $cron->args;
        $key = $hook . serialize($args);

        // Jika cron event sudah ada dalam array unik, hapusnya
        if (isset($unique_crons[$key])) {
            wp_unschedule_event($cron->time, $hook, $args);
        } else {
            // Jika tidak, tambahkan ke array unik
            $unique_crons[$key] = true;
        }
    }
}

// Jalankan fungsi saat plugin diaktifkan
register_activation_hook(__FILE__, 'remove_duplicate_cron_events');


// Jalan di action init
add_action('init', 'remove_duplicate_cron_events');
