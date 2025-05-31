<?php

namespace App\Traits;

trait Formatter
{
    public function rupiah($amount)
    {
        return "Rp " . number_format($amount, 2, ',', '.');
    }
}
