@props([
    'url',
    'color' => 'primary',
    'align' => 'center',
])
<table class="action" align="{{ $align }}" width="100%" cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td align="{{ $align }}" style="padding: 24px 0;">
<table width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td align="{{ $align }}">
<table border="0" cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td style="border-radius: 12px; background: {{ $color === 'primary' ? 'linear-gradient(135deg, #2a5298 0%, #1e3c72 100%)' : '#f3f5fa' }}; box-shadow: {{ $color === 'primary' ? '0 4px 16px rgba(42, 82, 152, 0.25)' : 'none' }};">
<a href="{{ $url }}"
   class="button button-{{ $color }}"
   target="_blank"
   rel="noopener"
   style="border-radius: 12px; padding: 14px 32px; color: #fff; text-decoration: none; font-weight: 700; font-size: 16px; display: inline-block; letter-spacing: 0.3px;">
    {{ $slot }}
</a>
</td>
</tr>
</table>
</td>
</tr>
</table>
</td>
</tr>
</table>
