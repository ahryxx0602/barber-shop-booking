{{-- Mobile overlay: ẩn sidebar khi click bên ngoài trên mobile --}}
<div
  :class="sidebarToggle ? 'block' : 'hidden'"
  @click="sidebarToggle = false"
  class="fixed inset-0 z-9998 bg-gray-900/50 lg:hidden"
></div>
