<?php

namespace App\Http\Controllers;

use App\Models\DownloadLink;
use Illuminate\Http\RedirectResponse;

class DownloadController extends Controller
{
    /**
     * Track download click and redirect to the download URL.
     */
    public function track(DownloadLink $downloadLink): RedirectResponse
    {
        // Increment the click count
        $downloadLink->incrementClick();

        // Redirect to the actual download URL
        return redirect()->away($downloadLink->url);
    }
}
