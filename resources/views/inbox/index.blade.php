@extends('layouts.app')

@section('title', 'Inbox')

@section('content')
@php
    $tones = [
        'success' => 'bg-emerald-500',
        'warning' => 'bg-amber-500',
        'critical' => 'bg-rose-500',
        'info' => 'bg-sky-500',
    ];
@endphp
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-5">
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Inbox</h1>
            <p class="text-xs text-slate-500 mt-0.5">Decisions, reviews and requests that concern you.</p>
        </div>
        @if(auth()->user()->unreadNotifications()->exists())
            <form method="POST" action="{{ route('inbox.read_all') }}">
                @csrf
                <button type="submit" class="px-3.5 py-2 rounded-xl bg-white hover:bg-slate-50 border border-slate-200 text-xs font-semibold text-slate-700">Mark all as read</button>
            </form>
        @endif
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs overflow-hidden">
        @forelse($notifications as $note)
            <a href="{{ route('inbox.open', $note->id) }}" class="flex items-start gap-3 px-5 py-4 border-b last:border-0 border-slate-100 hover:bg-slate-50 transition {{ $note->read_at ? '' : 'bg-red-50/40' }}">
                <span class="mt-1.5 w-2 h-2 rounded-full shrink-0 {{ $tones[$note->data['tone'] ?? 'info'] ?? 'bg-sky-500' }}" aria-hidden="true"></span>
                <div class="min-w-0 flex-1">
                    <p class="text-sm {{ $note->read_at ? 'font-medium text-slate-700' : 'font-bold text-slate-900' }}">
                        @unless($note->read_at)<span class="sr-only">Unread: </span>@endunless{{ $note->data['title'] ?? 'Notification' }}
                    </p>
                    <p class="text-xs text-slate-500 mt-0.5">{{ $note->data['message'] ?? '' }}</p>
                </div>
                <time class="text-[11px] text-slate-400 shrink-0" datetime="{{ $note->created_at->toIso8601String() }}">{{ $note->created_at->diffForHumans() }}</time>
            </a>
        @empty
            <div class="px-5 py-12 text-center">
                <p class="text-sm font-semibold text-slate-800">No notifications yet</p>
                <p class="text-xs text-slate-500 mt-1">You'll be told here when proposals are decided, reports reviewed or action is needed.</p>
            </div>
        @endforelse
    </div>

    @if($notifications->hasPages())
        <div>{{ $notifications->links() }}</div>
    @endif
</div>
@endsection
