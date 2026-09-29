<?php

namespace App\Http\Controllers;

use App\Services\EmailThreadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Resend\Exceptions\WebhookSignatureVerificationException;
use Resend\WebhookSignature;

class ResendInboundController extends Controller
{
    public function __invoke(Request $request, EmailThreadService $threads): JsonResponse|Response
    {
        $secret = (string) config('services.resend.webhook_secret');
        if ($secret === '') {
            return response('Inbound email is not configured.', 503);
        }

        try {
            WebhookSignature::verify($request->getContent(), [
                'svix-id' => (string) $request->header('svix-id'),
                'svix-timestamp' => (string) $request->header('svix-timestamp'),
                'svix-signature' => (string) $request->header('svix-signature'),
            ], $secret);
        } catch (WebhookSignatureVerificationException) {
            return response('Invalid signature.', 401);
        }

        $event = $request->json()->all();
        if (($event['type'] ?? '') !== 'email.received') {
            return response()->json(['ok' => true]);
        }

        $threads->ingestFromResend($event['data'] ?? []);

        return response()->json(['ok' => true]);
    }
}
