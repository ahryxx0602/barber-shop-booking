<aside
  :class="sidebarToggle ? 'translate-x-0 lg:w-[90px]' : '-translate-x-full'"
  class="sidebar fixed left-0 top-0 z-9999 flex h-screen w-[290px] flex-col overflow-y-hidden border-r border-gray-200 bg-white px-5 dark:border-gray-800 dark:bg-gray-900 lg:static lg:translate-x-0"
>
  <!-- SIDEBAR HEADER -->
  <div
    :class="sidebarToggle ? 'justify-center' : 'justify-between'"
    class="flex items-center gap-2 pt-8 pb-7"
  >
    <a href="{{ route('admin.dashboard') }}">
      <span :class="sidebarToggle ? 'hidden' : 'flex'" class="items-center gap-2.5">
        <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-brand-500">
          <svg class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="6" cy="6" r="3"/>
            <circle cx="6" cy="18" r="3"/>
            <line x1="20" y1="4" x2="8.12" y2="15.88"/>
            <line x1="14.47" y1="14.48" x2="20" y2="20"/>
            <line x1="8.12" y1="8.12" x2="12" y2="12"/>
          </svg>
        </span>
        <span class="text-lg font-bold text-gray-800 dark:text-white">BarberBook</span>
      </span>
      <span
        :class="sidebarToggle ? 'lg:flex' : 'hidden'"
        class="items-center justify-center"
      >
        <span class="flex items-center justify-center w-9 h-9 rounded-lg bg-brand-500">
          <svg class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="6" cy="6" r="3"/>
            <circle cx="6" cy="18" r="3"/>
            <line x1="20" y1="4" x2="8.12" y2="15.88"/>
            <line x1="14.47" y1="14.48" x2="20" y2="20"/>
            <line x1="8.12" y1="8.12" x2="12" y2="12"/>
          </svg>
        </span>
      </span>
    </a>
  </div>
  <!-- SIDEBAR HEADER -->

  <div class="flex flex-col overflow-y-auto duration-300 ease-linear no-scrollbar">
    <!-- Sidebar Menu -->
    <nav x-data="{ selected: $persist('Dashboard') }">

      <!-- Menu Group: CHÍNH -->
      <div>
        <h3 class="mb-4 text-xs uppercase leading-5 text-gray-400">
          <span :class="sidebarToggle ? 'lg:hidden' : ''">CHÍNH</span>
          <svg
            :class="sidebarToggle ? 'lg:block hidden' : 'hidden'"
            class="mx-auto fill-current" width="24" height="24" viewBox="0 0 24 24" fill="none"
          >
            <path fill-rule="evenodd" clip-rule="evenodd"
              d="M5.99915 10.2451C6.96564 10.2451 7.74915 11.0286 7.74915 11.9951V12.0051C7.74915 12.9716 6.96564 13.7551 5.99915 13.7551C5.03265 13.7551 4.24915 12.9716 4.24915 12.0051V11.9951C4.24915 11.0286 5.03265 10.2451 5.99915 10.2451ZM17.9991 10.2451C18.9656 10.2451 19.7491 11.0286 19.7491 11.9951V12.0051C19.7491 12.9716 18.9656 13.7551 17.9991 13.7551C17.0326 13.7551 16.2491 12.9716 16.2491 12.0051V11.9951C16.2491 11.0286 17.0326 10.2451 17.9991 10.2451ZM13.7491 11.9951C13.7491 11.0286 12.9656 10.2451 11.9991 10.2451C11.0326 10.2451 10.2491 11.0286 10.2491 11.9951V12.0051C10.2491 12.9716 11.0326 13.7551 11.9991 13.7551C12.9656 13.7551 13.7491 12.9716 13.7491 12.0051V11.9951Z"
            />
          </svg>
        </h3>

        <ul class="flex flex-col gap-4 mb-6">

          <!-- Menu: Dashboard -->
          <li>
            <a
              href="{{ route('admin.dashboard') }}"
              @click="selected = 'Dashboard'"
              class="menu-item group"
              :class="selected === 'Dashboard' ? 'menu-item-active' : 'menu-item-inactive'"
            >
              <svg
                :class="selected === 'Dashboard' ? 'menu-item-icon-active' : 'menu-item-icon-inactive'"
                width="24" height="24" viewBox="0 0 24 24" fill="none"
              >
                <path fill-rule="evenodd" clip-rule="evenodd"
                  d="M5.5 3.25C4.25736 3.25 3.25 4.25736 3.25 5.5V8.99998C3.25 10.2426 4.25736 11.25 5.5 11.25H9C10.2426 11.25 11.25 10.2426 11.25 8.99998V5.5C11.25 4.25736 10.2426 3.25 9 3.25H5.5ZM4.75 5.5C4.75 5.08579 5.08579 4.75 5.5 4.75H9C9.41421 4.75 9.75 5.08579 9.75 5.5V8.99998C9.75 9.41419 9.41421 9.74998 9 9.74998H5.5C5.08579 9.74998 4.75 9.41419 4.75 8.99998V5.5ZM5.5 12.75C4.25736 12.75 3.25 13.7574 3.25 15V18.5C3.25 19.7426 4.25736 20.75 5.5 20.75H9C10.2426 20.75 11.25 19.7427 11.25 18.5V15C11.25 13.7574 10.2426 12.75 9 12.75H5.5ZM4.75 15C4.75 14.5858 5.08579 14.25 5.5 14.25H9C9.41421 14.25 9.75 14.5858 9.75 15V18.5C9.75 18.9142 9.41421 19.25 9 19.25H5.5C5.08579 19.25 4.75 18.9142 4.75 18.5V15ZM12.75 5.5C12.75 4.25736 13.7574 3.25 15 3.25H18.5C19.7426 3.25 20.75 4.25736 20.75 5.5V8.99998C20.75 10.2426 19.7426 11.25 18.5 11.25H15C13.7574 11.25 12.75 10.2426 12.75 8.99998V5.5ZM15 4.75C14.5858 4.75 14.25 5.08579 14.25 5.5V8.99998C14.25 9.41419 14.5858 9.74998 15 9.74998H18.5C18.9142 9.74998 19.25 9.41419 19.25 8.99998V5.5C19.25 5.08579 18.9142 4.75 18.5 4.75H15ZM15 12.75C13.7574 12.75 12.75 13.7574 12.75 15V18.5C12.75 19.7426 13.7574 20.75 15 20.75H18.5C19.7426 20.75 20.75 19.7427 20.75 18.5V15C20.75 13.7574 19.7426 12.75 18.5 12.75H15ZM14.25 15C14.25 14.5858 14.5858 14.25 15 14.25H18.5C18.9142 14.25 19.25 14.5858 19.25 15V18.5C19.25 18.9142 18.9142 19.25 18.5 19.25H15C14.5858 19.25 14.25 18.9142 14.25 18.5V15Z"
                />
              </svg>
              <span class="menu-item-text" :class="sidebarToggle ? 'lg:hidden' : ''">Dashboard</span>
            </a>
          </li>
          <!-- /Dashboard -->

          <!-- Menu: Dịch vụ -->
          <li>
            <a
              href="#"
              @click.prevent="selected = (selected === 'DichVu' ? '' : 'DichVu')"
              class="menu-item group"
              :class="selected === 'DichVu' ? 'menu-item-active' : 'menu-item-inactive'"
            >
              <svg
                :class="selected === 'DichVu' ? 'menu-item-icon-active' : 'menu-item-icon-inactive'"
                width="24" height="24" viewBox="0 0 24 24" fill="none"
              >
                <path fill-rule="evenodd" clip-rule="evenodd"
                  d="M3.25 5.5C3.25 4.25736 4.25736 3.25 5.5 3.25H18.5C19.7426 3.25 20.75 4.25736 20.75 5.5V18.5C20.75 19.7426 19.7426 20.75 18.5 20.75H5.5C4.25736 20.75 3.25 19.7426 3.25 18.5V5.5ZM5.5 4.75C5.08579 4.75 4.75 5.08579 4.75 5.5V8.58325L19.25 8.58325V5.5C19.25 5.08579 18.9142 4.75 18.5 4.75H5.5ZM19.25 10.0833H15.416V13.9165H19.25V10.0833ZM13.916 10.0833L10.083 10.0833V13.9165L13.916 13.9165V10.0833ZM8.58301 10.0833H4.75V13.9165H8.58301V10.0833ZM4.75 18.5V15.4165H8.58301V19.25H5.5C5.08579 19.25 4.75 18.9142 4.75 18.5ZM10.083 19.25V15.4165L13.916 15.4165V19.25H10.083ZM15.416 19.25V15.4165H19.25V18.5C19.25 18.9142 18.9142 19.25 18.5 19.25H15.416Z"
                />
              </svg>
              <span class="menu-item-text" :class="sidebarToggle ? 'lg:hidden' : ''">Dịch vụ</span>
              <svg
                class="menu-item-arrow"
                :class="[selected === 'DichVu' ? 'menu-item-arrow-active' : 'menu-item-arrow-inactive', sidebarToggle ? 'lg:hidden' : '']"
                width="20" height="20" viewBox="0 0 20 20" fill="none"
              >
                <path d="M4.79175 7.39584L10.0001 12.6042L15.2084 7.39585" stroke="" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
              </svg>
            </a>
            <!-- Dropdown -->
            <div :class="selected === 'DichVu' ? 'block' : 'hidden'">
              <ul :class="sidebarToggle ? 'lg:hidden' : 'flex'" class="flex flex-col gap-1 mt-2 pl-9">
                <li>
                  <a href="{{ route('admin.services.index') }}"
                    class="menu-dropdown-item group"
                    :class="page === 'services' ? 'menu-dropdown-item-active' : 'menu-dropdown-item-inactive'"
                  >
                    Danh sách dịch vụ
                  </a>
                </li>
                <li>
                  <a href="{{ route('admin.services.create') }}"
                    class="menu-dropdown-item group"
                    :class="page === 'servicesCreate' ? 'menu-dropdown-item-active' : 'menu-dropdown-item-inactive'"
                  >
                    Thêm dịch vụ
                  </a>
                </li>
              </ul>
            </div>
            <!-- /Dropdown -->
          </li>
          <!-- /Dịch vụ -->

          <!-- Menu: Thợ cắt -->
          <li>
            <a
              href="#"
              @click.prevent="selected = (selected === 'ThoCat' ? '' : 'ThoCat')"
              class="menu-item group"
              :class="selected === 'ThoCat' ? 'menu-item-active' : 'menu-item-inactive'"
            >
              <svg
                :class="selected === 'ThoCat' ? 'menu-item-icon-active' : 'menu-item-icon-inactive'"
                width="24" height="24" viewBox="0 0 24 24" fill="none"
              >
                <path fill-rule="evenodd" clip-rule="evenodd"
                  d="M12 3.25C9.37665 3.25 7.25 5.37665 7.25 8C7.25 10.6234 9.37665 12.75 12 12.75C14.6234 12.75 16.75 10.6234 16.75 8C16.75 5.37665 14.6234 3.25 12 3.25ZM8.75 8C8.75 6.20507 10.2051 4.75 12 4.75C13.7949 4.75 15.25 6.20507 15.25 8C15.25 9.79493 13.7949 11.25 12 11.25C10.2051 11.25 8.75 9.79493 8.75 8ZM12 14.25C9.68695 14.25 7.55773 14.908 5.94817 15.9757C4.34515 17.0391 3.25 18.5421 3.25 20.2C3.25 20.6142 3.58579 20.95 4 20.95H20C20.4142 20.95 20.75 20.6142 20.75 20.2C20.75 18.5421 19.6549 17.0391 18.0518 15.9757C16.4423 14.908 14.3131 14.25 12 14.25ZM4.83691 19.45C5.15318 18.5576 5.93631 17.6588 7.13653 16.8627C8.42691 16.006 10.138 15.45 12 15.45C13.862 15.45 15.5731 16.006 16.8635 16.8627C18.0637 17.6588 18.8468 18.5576 19.1631 19.45H4.83691Z"
                />
              </svg>
              <span class="menu-item-text" :class="sidebarToggle ? 'lg:hidden' : ''">Thợ cắt</span>
              <svg
                class="menu-item-arrow"
                :class="[selected === 'ThoCat' ? 'menu-item-arrow-active' : 'menu-item-arrow-inactive', sidebarToggle ? 'lg:hidden' : '']"
                width="20" height="20" viewBox="0 0 20 20" fill="none"
              >
                <path d="M4.79175 7.39584L10.0001 12.6042L15.2084 7.39585" stroke="" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
              </svg>
            </a>
            <!-- Dropdown -->
            <div :class="selected === 'ThoCat' ? 'block' : 'hidden'">
              <ul :class="sidebarToggle ? 'lg:hidden' : 'flex'" class="flex flex-col gap-1 mt-2 pl-9">
                <li>
                  <a href="{{ route('admin.barbers.index') }}"
                    class="menu-dropdown-item group"
                    :class="page === 'barbers' ? 'menu-dropdown-item-active' : 'menu-dropdown-item-inactive'"
                  >
                    Danh sách thợ
                  </a>
                </li>
                <li>
                  <a href="{{ route('admin.barbers.create') }}"
                    class="menu-dropdown-item group"
                    :class="page === 'barbersCreate' ? 'menu-dropdown-item-active' : 'menu-dropdown-item-inactive'"
                  >
                    Thêm thợ
                  </a>
                </li>
              </ul>
            </div>
            <!-- /Dropdown -->
          </li>
          <!-- /Thợ cắt -->

          <!-- Menu: Lịch làm việc -->
          <li>
            <a
              href="{{ route('admin.schedules.index') }}"
              @click="selected = 'LichLam'"
              class="menu-item group"
              :class="selected === 'LichLam' ? 'menu-item-active' : 'menu-item-inactive'"
            >
              <svg
                :class="selected === 'LichLam' ? 'menu-item-icon-active' : 'menu-item-icon-inactive'"
                width="24" height="24" viewBox="0 0 24 24" fill="none"
              >
                <path fill-rule="evenodd" clip-rule="evenodd"
                  d="M6.75 2.5C6.75 2.08579 7.08578 1.75 7.5 1.75C7.91421 1.75 8.25 2.08579 8.25 2.5V3.25H15.75V2.5C15.75 2.08579 16.0858 1.75 16.5 1.75C16.9142 1.75 17.25 2.08579 17.25 2.5V3.25H19C20.2426 3.25 21.25 4.25736 21.25 5.5V19.5C21.25 20.7426 20.2426 21.75 19 21.75H5C3.75736 21.75 2.75 20.7426 2.75 19.5V5.5C2.75 4.25736 3.75736 3.25 5 3.25H6.75V2.5ZM15.75 4.75V5.5C15.75 5.91422 16.0858 6.25 16.5 6.25C16.9142 6.25 17.25 5.91422 17.25 5.5V4.75H19C19.4142 4.75 19.75 5.08579 19.75 5.5V8.25H4.25V5.5C4.25 5.08579 4.58579 4.75 5 4.75H6.75V5.5C6.75 5.91422 7.08578 6.25 7.5 6.25C7.91421 6.25 8.25 5.91422 8.25 5.5V4.75H15.75ZM4.25 9.75V19.5C4.25 19.9142 4.58579 20.25 5 20.25H19C19.4142 20.25 19.75 19.9142 19.75 19.5V9.75H4.25Z"
                />
              </svg>
              <span class="menu-item-text" :class="sidebarToggle ? 'lg:hidden' : ''">Lịch làm việc</span>
            </a>
          </li>
          <!-- /Lịch làm việc -->

        </ul>
      </div>
      <!-- /Menu Group CHÍNH -->

    </nav>
    <!-- /Sidebar Menu -->
  </div>
</aside>
