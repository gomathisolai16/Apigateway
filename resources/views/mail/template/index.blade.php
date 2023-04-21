<x-mail::message>
{{-- Greeting --}}
@if (! empty($greeting))
# {{ $greeting }}
@else
@if ($level === 'error')
# @lang('Whoops!')
@else
# @lang('Hello!')
@endif
@endif

{{-- Intro Lines --}}
@foreach ($introLines as $line)
<?= $line ?>

@endforeach

{{-- Action Button --}}
@isset($actionText)
<?php
    $color = match ($level) {
        'success', 'error' => $level,
        default => 'primary',
    };
?><br>
<div class="text-center" style="text-align: center !important;"><a href="<?php echo $actionUrl; ?>">
{{ $actionText }}
</a></div><br>
@endisset

{{-- Outro Lines --}}
@foreach ($outroLines as $line)
<?= $line ?>

@endforeach

{{-- Salutation --}}
@if (! empty($salutation))
{{ $salutation }} <br>
{{ $company_name }} <br>
<img style="max-width:200px;max-height:70px;" src="<?php echo $image_url; ?>" />
@else
@lang('Regards'),<br>
{{ config('app.name') }}
@endif

{{-- Subcopy --}}
@isset($actionText)
<x-slot:subcopy>
@lang(
    "If you're having trouble clicking the \":actionText\" button, copy and paste the URL below\n".
    'into your web browser:',
    [
        'actionText' => $actionText,
    ]
) <span class="break-all">[{{ $displayableActionUrl }}]({{ $actionUrl }})</span>
</x-slot:subcopy>
@endisset
</x-mail::message>
