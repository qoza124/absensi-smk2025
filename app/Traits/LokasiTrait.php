<?php
// app/Traits/LokasiTrait.php

namespace App\Traits;

trait LokasiTrait
{
    /**
     * Menghitung jarak antara dua titik koordinat (Lat/Long)
     * Menggunakan formula Haversine.
     *
     * @param float $lat1 Latitude titik 1
     * @param float $lon1 Longitude titik 1
     * @param float $lat2 Latitude titik 2
     * @param float $lon2 Longitude titik 2
     * @return float Jarak dalam METER
     */
    function hitungJarak($lat1, $lon1, $lat2, $lon2) {
        if (($lat1 == $lat2) && ($lon1 == $lon2)) {
            return 0;
        }

        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;

        // Konversi ke Meter (1 mil = 1609.344 meter)
        return ($miles * 1609.344);
    }
}