<!DOCTYPE html>
<html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Token {{ $token->token_no }} • Queue-Pro</title>
<style>
    * { box-sizing: border-box; }
    body { font-family: 'Segoe UI', monospace; margin: 0; padding: 16px; color: #0f172a; background: #f1f5f9; }
    .ticket { width: 300px; margin: 0 auto; text-align: center; background: #fff; border-radius: 18px; padding: 20px 18px; box-shadow: 0 12px 40px rgba(0,0,0,.12); }
    .brand { display: inline-flex; align-items: center; gap: 8px; font-weight: 800; }
    .brand span.logo { width: 30px; height: 30px; border-radius: 10px; background: linear-gradient(135deg,#6366f1,#a855f7); color: #fff; display: inline-flex; align-items: center; justify-content: center; }
    .clinic { font-weight: 800; font-size: 17px; margin-top: 6px; }
    .addr { font-size: 12px; color: #64748b; }
    .no { font-size: 52px; font-weight: 800; letter-spacing: 3px; margin: 10px 0 2px; background: linear-gradient(135deg,#4f46e5,#a855f7); -webkit-background-clip: text; background-clip: text; color: transparent; }
    .meta { font-size: 12px; color: #64748b; }
    .rows { margin-top: 12px; border-top: 2px dashed #e2e8f0; padding-top: 10px; }
    .row { font-size: 13px; display: flex; justify-content: space-between; gap: 8px; text-align: left; margin: 5px 0; }
    .row b { text-align: right; }
    .footer { font-size: 11px; margin-top: 10px; background: #f8fafc; border-radius: 10px; padding: 8px; color: #475569; }
    .noprint { margin-top: 12px; display: flex; gap: 8px; justify-content: center; }
    .noprint button, .noprint a { font-size: 13px; font-weight: 700; border-radius: 10px; padding: 8px 14px; border: 1px solid #e2e8f0; background: #fff; text-decoration: none; color: #0f172a; cursor: pointer; }
    @media print { body { padding: 0; background: #fff; } .ticket { box-shadow: none; border: 1px dashed #94a3b8; border-radius: 0; } .noprint { display: none; } @page { size: 80mm auto; margin: 4mm; } }
</style></head>
<body onload="window.print()">
<div class="ticket">
    <div class="brand"><span class="logo">Q</span> Queue-Pro</div>
    <div class="clinic">{{ \App\Models\Setting::get('clinic_name', config('app.name')) }}</div>
    <div class="addr">{{ \App\Models\Setting::get('clinic_address', '') }}</div>
    <div class="meta">{{ $token->token_date->format('d M Y') }} • {{ now()->format('h:i A') }}</div>
    <div class="no">{{ $token->token_no }}</div>
    <div class="meta">Please watch the live display</div>
    <div class="rows">
        <div class="row"><span>Patient</span><b>{{ $token->patient->name }}</b></div>
        <div class="row"><span>Service</span><b>{{ $token->service->name }}</b></div>
        <div class="row"><span>Doctor</span><b>{{ $token->doctor->name }}</b></div>
        <div class="row"><span>Counter</span><b>{{ $token->counter->name }}@if($token->counter->room_no) / R-{{ $token->counter->room_no }}@endif</b></div>
    </div>
    <div class="footer">{{ \App\Models\Setting::get('token_footer', 'Please wait in waiting area') }}</div>
    @if($token->status === \App\Models\Token::COMPLETED && $token->canBeReviewed())
        <div class="noprint" style="margin-top: 8px;">
            <a href="{{ route('review.show', $token->token_no) }}"
               style="display: inline-block; font-size: 12px; font-weight: 700; border-radius: 8px; padding: 6px 12px; background: #059669; color: #fff; text-decoration: none;">
                ⭐ Rate Your Visit
            </a>
        </div>
    @elseif($token->status === \App\Models\Token::COMPLETED && $token->review?->exists)
        <div class="noprint" style="margin-top: 8px;">
            <a href="{{ route('review.success', $token->token_no) }}"
               style="display: inline-block; font-size: 12px; font-weight: 700; border-radius: 8px; padding: 6px 12px; background: #6366f1; color: #fff; text-decoration: none;">
                📝 View Your Review
            </a>
        </div>
    @endif
    <div class="noprint"><button onclick="window.print()">🖨 Reprint</button><a href="{{ route('tokens.show', $token) }}">← Back</a></div>
</div>
</body></html>
