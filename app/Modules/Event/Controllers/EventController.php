<?php

namespace App\Modules\Event\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\User;
use Illuminate\Http\Request;

class EventController extends Controller
{
    /**
     * HIỂN THỊ DANH SÁCH TẤT CẢ SỰ KIỆN
     */
    public function index()
    {
        $events = Event::with('page')
            ->where('start_time', '>=', now())
            ->orderBy('start_time')
            ->paginate(15);

        return view('event.index', compact('events'));
    }

    /**
     * HIỂN THỊ SỰ KIỆN CỦA TÔI
     */
    public function myEvents(Request $request)
    {
        // HACK CHO VIỆC TEST: Lấy User thật, nếu chưa có thì lấy User đầu tiên
        $user = $request->user() ?? User::first();

        if (!$user) {
            return back()->with('error', 'Chưa có user nào trong Database. Hãy chạy php artisan db:seed');
        }

        $events = $user->events()
            ->with('page')
            ->orderBy('start_time')
            ->paginate(15);

        return view('event.my', compact('events'));
    }

    /**
     * HIỂN THỊ CHI TIẾT 1 SỰ KIỆN
     */
    public function show(Event $event)
    {
        $event->load(['page', 'forms.fields', 'participants']);
        return view('event.show', compact('event'));
    }

    /**
     * GIAO DIỆN TẠO SỰ KIỆN MỚI
     */
    public function create()
    {
        return view('event.create');
    }

    /**
     * XỬ LÝ LƯU SỰ KIỆN VÀO DATABASE
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title'              => ['required', 'string', 'max:300'],
            'description'        => ['nullable', 'string'],
            'location'           => ['nullable', 'string', 'max:500'],
            'page_id'            => ['nullable', 'exists:pages,id'],
            'start_time'         => ['required', 'date'], 
            'end_time'           => ['required', 'date', 'after:start_time'],
            'point_amount'       => ['nullable', 'integer', 'min:0'],
            'point_category'     => ['nullable', 'string'],
        ]);

        // Xử lý Checkbox bật/tắt điểm rèn luyện
        $data['has_training_point'] = $request->has('has_training_point');
        if (!$data['has_training_point']) {
            $data['point_amount'] = 0;
            $data['point_category'] = null;
        }

        // Nếu người tạo không truyền page_id (tạo bằng tài khoản cá nhân), tạm gán vào page 1 để test
        if (empty($data['page_id'])) {
            $data['page_id'] = 1; 
        }

        $event = Event::create($data);

        return redirect()->route('events.show', $event)
                         ->with('success', 'Sự kiện đã được tạo thành công! Bạn có thể copy link này để chia sẻ.');
    }

    /**
     * XỬ LÝ NÚT BẤM THAM GIA / KHÔNG THAM GIA
     */
    public function join(Request $request, Event $event)
    {
        // HACK CHO VIỆC TEST: Lấy User thật, nếu chưa đăng nhập thì tự lấy User ảo số 1
        $user = $request->user() ?? User::first();

        if (!$user) {
            return back()->with('error', 'Lỗi: Chưa có user nào trong Database để test.');
        }

        $status = $request->input('status', 'going'); // Mặc định là 'going' (Tham gia)

        if ($status === 'not_going') {
            // Nếu chọn Không tham gia -> Xóa khỏi danh sách (detach)
            $event->participants()->detach($user->id);
            return back()->with('success', 'Bạn đã hủy tham gia sự kiện này!');
        }

        // Nếu chọn Tham gia -> Thêm vào danh sách, nếu có rồi thì cập nhật trạng thái
        $event->participants()->syncWithoutDetaching([
            $user->id => ['status' => $status],
        ]);

        return back()->with('success', 'Tuyệt vời! Bạn đã đăng ký tham gia sự kiện!');
    }
}