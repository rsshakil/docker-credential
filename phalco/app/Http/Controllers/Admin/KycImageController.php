<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KycImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class KycImageController extends Controller
{
    public function getImage(KycImage $kycImage)
    {
        return Storage::disk('s3_secure')->response($kycImage->image_path);
    }

    public function delete(Request $request, KycImage $kycImage)
    {
        Storage::disk('s3_secure')->delete($kycImage->image_path);
        $kycImage->delete();

        return to_route('admin.kyc_applications.show', $request->user()->id);
    }
}
