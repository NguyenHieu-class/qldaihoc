<?php

namespace App\Observers;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

class ModelChangeObserver
{
    public function created(Model $model): void
    {
        if ($this->shouldSkip($model)) {
            return;
        }

        $this->logChange('created', $model, [
            'attributes' => $this->filterAttributes($model->getAttributes()),
        ]);
    }

    public function updated(Model $model): void
    {
        if ($this->shouldSkip($model)) {
            return;
        }

        $dirty = $this->filterAttributes($model->getDirty());

        if (empty($dirty)) {
            return;
        }

        $original = Arr::only($this->filterAttributes($model->getOriginal()), array_keys($dirty));

        $this->logChange('updated', $model, [
            'old' => $original,
            'new' => $dirty,
        ]);
    }

    public function deleted(Model $model): void
    {
        if ($this->shouldSkip($model)) {
            return;
        }

        $this->logChange('deleted', $model, [
            'attributes' => $this->filterAttributes($model->getOriginal()),
        ]);
    }

    protected function shouldSkip(Model $model): bool
    {
        return $model instanceof AuditLog;
    }

    protected function logChange(string $action, Model $model, array $changes = []): void
    {
        $request = request();

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'auditable_type' => get_class($model),
            'auditable_id' => $model->getKey(),
            'changes' => $changes,
            'ip_address' => $request ? $request->ip() : null,
        ]);
    }

    protected function filterAttributes(array $attributes): array
    {
        unset($attributes['updated_at'], $attributes['created_at'], $attributes['password']);

        return $attributes;
    }
}
