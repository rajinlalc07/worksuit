@php
    $currentDay = \Carbon\Carbon::parse(now(company()->timezone)->toDateTimeString())
        ->startOfDay()
        ->setTimezone('UTC');
@endphp
<div class="col-sm-12 mt-3">
    <x-cards.data class="e-d-info mb-3" :title="__('modules.dashboard.internshipDate')" padding="false">
        <x-table>
            @if (in_array('admin', user_roles()))
                @foreach ($internship as $internship)
                    @php
                        $internshipDate = Carbon\carbon::parse($internship->internship_end_date);
                        $diffInDays = $internshipDate->copy()->diffForHumans($currentDay);
                    @endphp
                    <tr>
                        <td class="pl-20">
                            <x-employee :user="$internship->user" />
                        </td>

                        <td class="pr-20 text-right">
                            @if ($internshipDate->setTimezone(company()->timezone)->isToday())
                                <span class="badge badge-light text-success p-2">@lang('app.today')</span>
                            @elseif ($diffInDays == '1 week after')
                                <span class="badge badge-light text-warning p-2">{{ $diffInDays }}</span>
                            @else
                                <span class="badge badge-light p-2">{{ $diffInDays }}</span>
                            @endif

                            <br>
                            @if ($internshipDate->setTimezone(company()->timezone)->isToday())
                                <span
                                    class="text-success f-12">@lang('messages.internshipMessage'){{ $internshipDate->format($company->date_format) }}</span>
                            @elseif($diffInDays == '1 week after')
                                <span class="text-warning f-12">@lang('messages.internshipMessage')
                                    {{ $internshipDate->format($company->date_format) }}</span>
                            @else
                                <span class="f-12">@lang('messages.internshipMessage')
                                    {{ $internshipDate->format($company->date_format) }}</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            @else
                <tr>
                    @php
                        $internshipDate = Carbon\carbon::parse($internship->internship_end_date);
                        $diffInDays = $internshipDate->copy()->diffForHumans($currentDay);
                    @endphp
                    <td class="pl-20">
                        @if ($internshipDate->setTimezone(company()->timezone)->isToday())
                            <span
                                class="text-success f-12">@lang('messages.internshipMessage'){{ $internshipDate->format($company->date_format) }}</span>
                        @elseif($diffInDays == '1 week after')
                            <span class="text-warning f-12">@lang('messages.internshipMessage')
                                {{ $internshipDate->format($company->date_format) }}</span>
                        @else
                            <span class="f-12">@lang('messages.internshipMessage')
                                {{ $internshipDate->format($company->date_format) }}</span>
                        @endif
                    </td>

                    <td class="pr-20 text-right">
                        @if ($internshipDate->setTimezone(company()->timezone)->isToday())
                            <span class="badge badge-light text-success p-2">@lang('app.today')</span>
                        @elseif ($diffInDays == '1 week after')
                            <span class="badge badge-light text-warning p-2">{{ $diffInDays }}</span>
                        @else
                            <span class="badge badge-light p-2">{{ $diffInDays }}</span>
                        @endif
                    </td>
                </tr>
            @endif
        </x-table>
    </x-cards.data>
</div>
