<?php

namespace App\Modules\tasks\Console;

use App\Modules\tasks\Models\Task;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendTaskRemindersCommand extends Command
{
    protected $signature = 'tasks:send-reminders';

    protected $description = 'Log reminders for open tasks that are due within 24 hours';

    public function handle(): int
    {
        $dueSoon = Task::query()
            ->withoutGlobalScopes()
            ->where('status', 'open')
            ->whereNotNull('due_at')
            ->whereBetween('due_at', [now(), now()->addDay()])
            ->get();

        foreach ($dueSoon as $task) {
            Log::info('task.reminder', [
                'task_id' => $task->id,
                'tenant_id' => $task->tenant_id,
                'title' => $task->title,
                'due_at' => $task->due_at?->toIso8601String(),
            ]);
        }

        $this->info('Reminded '.$dueSoon->count().' task(s).');

        return self::SUCCESS;
    }
}
