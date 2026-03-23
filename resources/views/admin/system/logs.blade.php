@extends('layouts.tailadmin')

@section('title', 'Nhật ký hệ thống')

@section('content')
<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">

  {{-- Header --}}
  <div class="px-5 py-4 sm:px-6 sm:py-5 border-b border-gray-200 dark:border-gray-800">
    <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Nhật ký hệ thống</h3>
  </div>

  {{-- Filter bar --}}
  <div class="px-5 py-3 sm:px-6 flex flex-wrap items-center gap-3 border-b border-gray-100 dark:border-gray-800">
    <form method="GET" action="{{ route('admin.system.logs') }}" class="flex flex-wrap items-center gap-3">
      {{-- Channel --}}
      <div class="flex items-center gap-2">
        <label class="text-sm font-medium text-gray-600 dark:text-gray-400">Channel:</label>
        <select name="channel" onchange="this.form.submit()"
          class="rounded-lg border border-gray-300 px-3 py-1.5 text-sm dark:border-gray-600 dark:bg-gray-800 dark:text-white">
          @foreach ($channels as $ch)
            <option value="{{ $ch }}" @selected($ch === $channel)>{{ $ch }}</option>
          @endforeach
        </select>
      </div>

      {{-- Level --}}
      <div class="flex items-center gap-2">
        <label class="text-sm font-medium text-gray-600 dark:text-gray-400">Level:</label>
        <select name="level" onchange="this.form.submit()"
          class="rounded-lg border border-gray-300 px-3 py-1.5 text-sm dark:border-gray-600 dark:bg-gray-800 dark:text-white">
          <option value="all" @selected($level === 'all')>Tất cả</option>
          <option value="info" @selected($level === 'info')>INFO</option>
          <option value="warning" @selected($level === 'warning')>WARNING</option>
          <option value="error" @selected($level === 'error')>ERROR</option>
          <option value="debug" @selected($level === 'debug')>DEBUG</option>
        </select>
      </div>
    </form>

    <span class="text-xs text-gray-400">{{ count($entries) }} bản ghi</span>
  </div>

  {{-- Log entries --}}
  <div class="overflow-x-auto">
    @if (count($entries) === 0)
      <div class="px-6 py-12 text-center text-gray-400">
        <svg class="mx-auto h-12 w-12 text-gray-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        <p>Không có bản ghi nào.</p>
      </div>
    @else
      <table class="min-w-full text-sm">
        <thead>
          <tr class="border-b border-gray-100 dark:border-gray-800">
            <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap">Thời gian</th>
            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">Level</th>
            <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nội dung</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
          @foreach ($entries as $entry)
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
              <td class="px-5 py-2.5 whitespace-nowrap text-xs text-gray-500 font-mono">
                {{ $entry['timestamp'] }}
              </td>
              <td class="px-3 py-2.5 whitespace-nowrap">
                @php
                  $levelColors = [
                    'ERROR'   => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                    'WARNING' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400',
                    'INFO'    => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
                    'DEBUG'   => 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300',
                  ];
                  $color = $levelColors[$entry['level']] ?? 'bg-gray-100 text-gray-600';
                @endphp
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold {{ $color }}">
                  {{ $entry['level'] }}
                </span>
              </td>
              <td class="px-5 py-2.5 text-gray-700 dark:text-gray-300 font-mono text-xs max-w-xl">
                <div class="whitespace-pre-wrap break-all leading-relaxed">{{ Str::limit($entry['message'], 500) }}</div>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    @endif
  </div>

</div>
@endsection
