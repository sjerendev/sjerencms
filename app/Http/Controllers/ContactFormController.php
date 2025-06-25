<?php

namespace App\Http\Controllers;

use App\Mail\ContactFormMail;
use App\Mail\ContactFormConfirmationMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class ContactFormController extends Controller
{
    public function submit(Request $request)
    {
        try {
            // Log the incoming request data
            Log::info('Contact form submission:', $request->all());

            $validated = $request->validate([
                'form_data.name' => 'required|string',
                'form_data.email' => 'required|email',
                'form_data.subject' => 'required|string',
                'form_data.message' => 'required|string',
                'notification_email' => 'required|email',
                'success_message' => 'required|string'
            ]);

            // Send confirmation to the submitter
            Mail::to($validated['form_data']['email'])
                ->send(new ContactFormConfirmationMail($validated['form_data']));

            // Send notification to the admin
            Mail::to($validated['notification_email'])
                ->send(new ContactFormMail($validated['form_data']));

            Log::info('Email sent successfully');

            return response()->json([
                'message' => $validated['success_message']
            ]);
        } catch (\Exception $e) {
            Log::error('Contact form error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to send message: ' . $e->getMessage()
            ], 422);
        }
    }
}
