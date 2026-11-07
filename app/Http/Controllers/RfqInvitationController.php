<?php

namespace App\Http\Controllers;

use App\Models\RfqInvitation;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RfqInvitationController extends Controller
{
    public function __invoke(Request $request, RfqInvitation $invitation): View
    {
        if (! $request->hasValidSignature()) {
            abort(403, 'Invalid or expired invitation link.');
        }

        if ($invitation->status === 'invited') {
            $invitation->update([
                'status' => 'viewed',
                'viewed_at' => now(),
            ]);
        }

        $invitation->load(['rfq.lines', 'supplier']);

        return view('rfq.invitation', [
            'invitation' => $invitation,
        ]);
    }
}
