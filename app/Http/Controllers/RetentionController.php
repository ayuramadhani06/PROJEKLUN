<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\RetentionPolicyService;

class RetentionController extends Controller
{
    public function update(Request $request, RetentionPolicyService $service)
    {
        $validated = $request->validate([
            'retention_days' => 'required|integer|min:1|max:365'
        ]);

        $days = $validated['retention_days'];

        DB::transaction(function () use ($days, $service) {

            // Simpan ke DB
            DB::table('system_settings')->updateOrInsert(
                ['key' => 'retention_days'],
                [
                    'value' => $days,
                    'updated_at' => now()
                ]
            );

            // Sync ke TimescaleDB
            $service->sync($days);
        });

        return back()->with('success', 'Retention policy updated successfully!');
    }
}