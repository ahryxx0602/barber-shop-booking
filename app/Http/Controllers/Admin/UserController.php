<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Danh sách users — phân trang, lọc role, tìm kiếm.
     */
    public function index(Request $request): View
    {
        $query = User::where('id', '!=', auth()->id());

        // Lọc theo role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Tìm kiếm theo tên / email / SĐT
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(15)->withQueryString();

        // Đếm theo role cho stats
        $totalUsers     = User::count();
        $totalCustomers = User::where('role', UserRole::Customer)->count();
        $totalBarbers   = User::where('role', UserRole::Barber)->count();
        $totalAdmins    = User::where('role', UserRole::Admin)->count();

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
        $user->update($request->validated());

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

        $user->update(['is_active' => !$user->is_active]);

        $status = $user->is_active ? 'kích hoạt' : 'vô hiệu hoá';

        return redirect()->back()
            ->with('success', "Tài khoản {$user->name} đã được {$status}.");
    }
}
