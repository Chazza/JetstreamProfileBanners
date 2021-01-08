<?php

namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Jetstream\Features;

trait HasProfileBanner
{
    /**
     * Update the user's profile banner.
     *
     * @param  \Illuminate\Http\UploadedFile  $banner
     * @return void
     */
    public function updateProfileBanner(UploadedFile $banner)
    {
        tap($this->profile_banner_path, function ($previous) use ($banner) {
            $this->forceFill([
                'profile_banner_path' => $banner->storePublicly(
                    'profile-banners', ['disk' => $this->profileBannerDisk()]
                ),
            ])->save();

            if ($previous) {
                Storage::disk($this->profileBannerDisk())->delete($previous);
            }
        });
    }

    /**
     * Delete the user's profile banner.
     *
     * @return void
     */
    public function deleteProfileBanner()
    {
        if (! Features::managesProfileBanners()) {
            return;
        }

        Storage::disk($this->profileBannerDisk())->delete($this->profile_banner_path);

        $this->forceFill([
            'profile_banner_path' => null,
        ])->save();
    }

    /**
     * Get the URL to the user's profile banner.
     *
     * @return string
     */
    public function getProfileBannerUrlAttribute()
    {
        return $this->profile_banner_path
                    ? Storage::disk($this->profileBannerDisk())->url($this->profile_banner_path)
                    : $this->defaultProfileBannerUrl();
    }

    /**
     * Get the default profile banner URL if no profile banner has been uploaded.
     *
     * @return string
     */
    protected function defaultProfileBannerUrl()
    {
        return 'https://ui-avatars.com/api/?name='.urlencode($this->name).'&color=7F9CF5&background=EBF4FF';
    }

    /**
     * Get the disk that profile banners should be stored on.
     *
     * @return string
     */
    protected function profileBannerDisk()
    {
        return isset($_ENV['VAPOR_ARTIFACT_NAME']) ? 's3' : 'public';
    }
}
