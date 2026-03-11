<?php

namespace App\Modules\Auth\Actions;

use App\Modules\Auth\Models\User;
use PragmaRX\Google2FA\Google2FA;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class Enable2FAAction
{
    public function __construct(private Google2FA $google2fa)
    {
    }

    public function execute(User $user): array
    {
        $secret = $this->google2fa->generateSecretKey();

        $user->update([
            'two_factor_secret'    => encrypt($secret),
            'two_factor_confirmed' => false,
        ]);

        $qrCodeUrl = $this->google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            $secret
        );

        $renderer = new ImageRenderer(
            new RendererStyle(200),
            new SvgImageBackEnd()
        );

        $qrCodeSvg = base64_encode((new Writer($renderer))->writeString($qrCodeUrl));

        return [
            'secret'  => $secret,
            'qr_code' => 'data:image/svg+xml;base64,' . $qrCodeSvg,
        ];
    }
}
