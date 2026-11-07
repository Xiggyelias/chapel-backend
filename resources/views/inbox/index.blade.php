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
<div class="page-shell max-w-3xl space-y-6">
    <x-page-header eyebrow="Correspondence" title="Inbox" description="Decisions, reviews and requests that concern you.">
        @if(auth()->user()->unreadNotifications()->exists())
            <form method="POST" action="{{ route('inbox.read_all') }}">
                @csrf
                <button type="submit" class="btn btn-secondary btn-sm">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M4.5 12.75l6 6 9-13.5"/></svg>
                    Mark all as read
                </button>
            </form>
        @endif
    </x-page-header>

    <div class="card overflow-hidden">
        @forelse($notifications as $note)
            <a href="{{ route('inbox.open', $note->id) }}" class="group relative flex items-start gap-3.5 px-5 py-4 border-b last:border-0 border-slate-100 transition-colors {{ $note->read_at ? 'hover:bg-slate-50/70' : 'bg-brand-soft/40 hover:bg-brand-soft/70' }}">
                @unless($note->read_at)
                    <span class="absolute left-0 top-3 bottom-3 w-[3px] rounded-r bg-brand" aria-hidden="true"></span>
                @endunless
                <span class="mt-[7px] w-2 h-2 rounded-full shrink-0 {{ $tones[$note->data['tone'] ?? 'info'] ?? 'bg-sky-500' }} {{ $note->read_at ? 'opacity-50' : '' }}" aria-hidden="true"></span>
                <div class="min-w-0 flex-1">
                    <p class="text-[13.5px] leading-snug {{ $note->read_at ? 'font-medium text-slate-600' : 'font-semibold text-slate-900' }} group-hover:text-brand transition-colors">
                        @unless($note->read_at)<span class="sr-only">Unread: </span>@endunless{{ $note->data['title'] ?? 'Notification' }}
                    </p>
                    <p class="text-[12.5px] text-slate-500 mt-0.5 leading-relaxed">{{ $note->data['message'] ?? '' }}</p>
                </div>
                <time class="text-[12px] font-medium text-slate-400 shrink-0 mt-0.5 tabular-nums" datetime="{{ $note->created_at->toIso8601String() }}">{{ $note->created_at->diffForHumans() }}</time>
            </a>
        @empty
            <div class="empty-state">
                <div class="empty-state-mark">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M2.25 13.5h3.86a2.25 2.25 0 012.012 1.244l.256.512a2.25 2.25 0 002.013 1.244h3.218a2.25 2.25 0 002.013-1.244l.256-.512a2.25 2.25 0 012.013-1.244h3.859M2.25 13.5V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18v-4.5M2.25 13.5l2.4-7.2A2.25 2.25 0 016.785 4.75h10.43a2.25 2.25 0 012.135 1.55l2.4 7.2"/></svg>
                </div>
                <p class="empty-state-title">No notifications yet</p>
                <p class="empty-state-text">You'll be told here when proposals are decided, reports reviewed or action is needed.</p>
            </div>
        @endforelse
    </div>

    @if($notifications->hasPages())
        <div>{{ $notifications->links() }}</div>
    @endif
</div>
@endsection
