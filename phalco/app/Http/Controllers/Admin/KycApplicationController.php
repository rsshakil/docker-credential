<?php

namespace App\Http\Controllers\Admin;

use App\Enums\KycStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\KycApplication\ApproveRequest;
use App\Http\Requests\Admin\KycApplication\RejectRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class KycApplicationController extends Controller
{
    public function show(Request $request, User $user)
    {
        $kycApplication = $user
            ->kycApplication()
            ->with('kycImages')
            ->first();

        $rejectedReason = null;
        if (collect([KycStatus::REVIEWING, KycStatus::REJECTED])->contains($user->kyc_status)) {
            $rejectedReason = $user->kycApplicationRejectedReason?->rejected_reason;
        }

        return inertia('Admin/KycApplication/Show', compact(
            'user',
            'kycApplication',
            'rejectedReason',
        ));
    }

    public function approve(ApproveRequest $request, User $user)
    {
        DB::transaction(function () use ($request, $user) {
            $user->update([
                ...$request->validated(),
                'kyc_status' => KycStatus::CONFIRMED,
                'administrator_id' => $request->user()->id,
            ]);
        });

        return to_route('admin.kyc_applications.show', $user);
    }

    public function reject(RejectRequest $request, User $user)
    {
        DB::transaction(function () use ($request, $user) {
            $user->kycApplication->kycImages
                ->each(function ($kycImage) {
                    Storage::disk('s3_secure')->delete($kycImage->image_path);
                });
            $user->kycApplication->kycImages()->delete();
            $user->kycApplication->update($request->validated());
            $user->update([
                'kyc_status' => KycStatus::REJECTED,
            ]);
        });

        return to_route('admin.kyc_applications.show', $user);
    }
}
