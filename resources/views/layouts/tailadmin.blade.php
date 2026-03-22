<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{
    page: '{{ View::getSections()['page'] ?? 'dashboard' }}',
    loaded: true,
    darkMode: false,
    stickyMenu: false,
    sidebarToggle: false,
    scrollTop: false
  }" x-init="
    darkMode = JSON.parse(localStorage.getItem('darkMode') ?? 'false');
    $watch('darkMode', value => localStorage.setItem('darkMode', JSON.stringify(value)))
  " :class="{ 'dark': darkMode === true }">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <title>@yield('title', 'Admin') — {{ config('app.name', 'Classic Cut') }}</title>

  @vite(['resources/css/app.css', 'resources/js/app.js'])

  @stack('styles')
</head>

<body class="font-sans antialiased bg-gray-50 dark:bg-gray-900">
  <!-- ===== Page Wrapper ===== -->
  <div class="flex h-screen overflow-hidden">

    <!-- ===== Sidebar ===== -->
    @include('partials.tailadmin-sidebar')

    <!-- ===== Content Area ===== -->
    <div class="relative flex flex-col flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 dark:bg-gray-900">

      <!-- Mobile overlay -->
      @include('partials.tailadmin-overlay')

      <!-- ===== Header ===== -->
      @include('partials.tailadmin-header')

      <!-- ===== Main Content ===== -->
      <main>
        <div class="p-4 mx-auto max-w-screen-2xl md:p-6">
          @yield('content')
        </div>
      </main>

    </div>
    <!-- ===== Content Area End ===== -->

  </div>
  <!-- ===== Page Wrapper End ===== -->

  {{-- Confirm Modal --}}
  @include('partials.confirm-modal')

  @stack('scripts')
</body>

</html>