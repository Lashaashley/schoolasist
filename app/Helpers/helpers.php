<?php

use App\Models\AuditTrail;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Log;

if (!function_exists('logAuditTrail')) {
    function logAuditTrail(
        $userId,
        $action,
        $tableName,
        $recordId = null,
        $oldValues = null,
        $newValues = null,
        $contextData = null
    ) {
        try {
            AuditTrail::create([
                'user_id' => $userId,
                'action' => $action,
                'table_name' => $tableName,
                'record_id' => $recordId,
                'old_values' => $oldValues ? json_encode($oldValues) : null,
                'new_values' => $newValues ? json_encode($newValues) : null,
                'context_data' => $contextData ? json_encode($contextData) : null,
                'ip_address' => Request::ip(),
                'user_agent' => Request::userAgent(),
            ]);
        } catch (\Exception $e) {
            Log::error('Audit trail logging failed: ' . $e->getMessage());
        }
    }

}

if (!function_exists('formatPhoneNumber')) {
    function formatPhoneNumber($phone)
    {
        // Remove all non-digits
        $phone = preg_replace('/\D/', '', $phone);

        // +254, 0, 7, or 1
        if (substr($phone, 0, 3) === '254') {
            return $phone;
        }

        if (substr($phone, 0, 1) === '0') {
            return '254' . substr($phone, 1);
        }

        if (substr($phone, 0, 1) === '7' || substr($phone, 0, 1) === '1') {
            return '254' . $phone;
        }

        return $phone; // fallback, leave as-is
    }
}