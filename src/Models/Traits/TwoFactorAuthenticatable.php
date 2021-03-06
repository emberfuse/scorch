<?php

namespace Emberfuse\Scorch\Models\Traits;

use BaconQrCode\Writer;
use BaconQrCode\Renderer\Color\Rgb;
use BaconQrCode\Renderer\ImageRenderer;
use Emberfuse\Scorch\Scorch\Config;
use BaconQrCode\Renderer\RendererStyle\Fill;
use Emberfuse\Scorch\Support\RecoveryCode;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use Emberfuse\Scorch\Contracts\Actions\ProvidesTwoFactorAuthentication;

trait TwoFactorAuthenticatable
{
    /**
     * Determine if two-factor authentication is enabled for this user.
     *
     * @return bool
     */
    public function getTwoFactorEnabledAttribute(): bool
    {
        return ! is_null($this->two_factor_secret);
    }

    /**
     * Get the user's two factor authentication recovery codes.
     *
     * @return array
     */
    public function recoveryCodes(): array
    {
        return json_decode(decrypt($this->two_factor_recovery_codes), true);
    }

    /**
     * Replace the given recovery code with a new one in the user's stored codes.
     *
     * @param string $code
     *
     * @return void
     */
    public function replaceRecoveryCode(string $code): void
    {
        $this->forceFill([
            'two_factor_recovery_codes' => encrypt(str_replace(
                $code,
                RecoveryCode::generate(),
                decrypt($this->two_factor_recovery_codes)
            )),
        ])->save();
    }

    /**
     * Get the QR code SVG of the user's two factor authentication QR code URL.
     *
     * @return string
     */
    public function twoFactorQrCodeSvg(): string
    {
        $svg = (new Writer(
            new ImageRenderer(
                new RendererStyle(192, 0, null, null, Fill::uniformColor(new Rgb(255, 255, 255), new Rgb(45, 55, 72))),
                new SvgImageBackEnd()
            )
        ))->writeString($this->twoFactorQrCodeUrl());

        return trim(substr($svg, strpos($svg, "\n") + 1));
    }

    /**
     * Get the two factor authentication QR code URL.
     *
     * @return string
     */
    public function twoFactorQrCodeUrl(): string
    {
        return app(ProvidesTwoFactorAuthentication::class)->qrCodeUrl(
            config('app.name'),
            $this->{Config::username('email')},
            decrypt($this->two_factor_secret)
        );
    }
}
