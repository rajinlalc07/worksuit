@php
$notificationUser = \App\Models\User::findOrFail($notification->data['user_id']);
@endphp

<x-cards.notification :notification="$notification"  :link="route('tickets.show', $notification->data['ticket_number'])" :image="$notificationUser->image_url"
    :title="__('email.newTicketRequester.subject') . ' #' . $notification->data['ticket_number']"
    :text="$notification->data['subject']" :time="$notification->created_at" />
