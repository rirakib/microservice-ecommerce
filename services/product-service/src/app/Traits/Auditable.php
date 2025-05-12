<?php 

namespace App\Traits;

use App\Models\AuditLog;

trait Auditable
{
    public function logAudit($action, $model = null, $data = [])
    {
        AuditLog::create([
            'action'       => $action,
            'model_type'   => $model ? get_class($model) : null,
            'model_id'     => $model->id ?? null,
            'request_data' => json_encode($data),
            'ip_address'   => request()->ip(),
            'agent_user'   => request()->header('User-Agent'),
        ]);
    }

    public function logError(string $errorMessage, array $context = [], $model = null)
    {
        AuditLog::create([
            'action'       => 'error',
            'model_type'   => $model ? get_class($model) : null,
            'model_id'     => $model->id ?? null,
            'request_data' => json_encode([
                'message' => $errorMessage,
                'context' => $context
            ]),
            'ip_address'   => request()->ip(),
            'agent_user'   => request()->header('User-Agent'),
        ]);
    }
}
