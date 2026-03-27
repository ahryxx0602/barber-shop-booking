<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\User;
use App\Repositories\Contracts\Admin\UserRepositoryInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function __construct(
        protected UserRepositoryInterface $userRepo,
    ) {}

    /**
     * Danh sách users — phân trang, lọc role, tìm kiếm.
     */
    public function index(Request $request): View
    {
        $users = $this->userRepo->paginateWithFilters(
            $request->only(['role', 'search']),
            15
        );

        // Đếm theo role cho stats
        $totalUsers     = User::count();
        $totalCustomers = $this->userRepo->countByRole(UserRole::Customer);
        $totalBarbers   = $this->userRepo->countByRole(UserRole::Barber);
        $totalAdmins    = $this->userRepo->countByRole(UserRole::Admin);

        return view('admin.users.index', compact(
            'users', 'totalUsers', 'totalCustomers', 'totalBarbers', 'totalAdmins'
        ));
    }

    /**
     * Chi tiết user — nếu customer thì load lịch sử booking.
     */
    public function show(User $user): View
    {
        if ($user->role === UserRole::Customer) {
            $user->load(['bookings' => function ($q) {
                $q->with(['barber.user', 'services'])->latest()->take(10);
            }]);
        }

        if ($user->role === UserRole::Barber) {
            $user->load('barber');
        }

        return view('admin.users.show', compact('user'));
    }

    /**
     * Form sửa thông tin user.
     */
    public function edit(User $user): View
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Cập nhật thông tin user.
     */
    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $this->userRepo->update($user, $request->validated());

        return redirect()->route('admin.users.index')
            ->with('success', 'Cập nhật tài khoản thành công.');
    }

    /**
     * Toggle kích hoạt / vô hiệu hoá tài khoản.
     */
    public function toggleActive(User $user): RedirectResponse
    {
        // Không cho phép admin tự vô hiệu hoá chính mình
        if ($user->id === auth()->id()) {
            return redirect()->back()
                ->with('error', 'Bạn không thể vô hiệu hoá tài khoản của chính mình.');
        }

        $this->userRepo->update($user, ['is_active' => !$user->is_active]);

        $status = !$user->is_active ? 'kích hoạt' : 'vô hiệu hoá';

        return redirect()->back()
            ->with('success', "Tài khoản {$user->name} đã được {$status}.");
    }
}
