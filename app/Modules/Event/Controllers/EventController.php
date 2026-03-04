<?php

namespace App\Modules\Event\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::with('page')
            ->where('start_time', '>=', now())
            ->orderBy('start_time')
            ->paginate(15);

        return view('event.index', compact('events'));
    }

    public function myEvents(Request $request)
    {
        $events = $request->user()
            ->events()
            ->with('page')
            ->orderBy('start_time')
            ->paginate(15);

        return view('event.my', compact('events'));
    }

    public function show(Event $event)
    {
        $event->load(['page', 'forms.fields', 'participants']);
        return view('event.show', compact('event'));
    }

    public function create()
    {
        return view('event.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'         => ['required', 'string', 'max:300'],
            'description'   => ['nullable', 'string'],
            'location'      => ['nullable', 'string', 'max:500'],
            'page_id'       => ['nullable', 'exists:pages,id'],
            'start_time'    => ['required', 'date', 'after:now'],
            'end_time'      => ['required', 'date', 'after:start_time'],
            'form_open_at'  => ['nullable', 'date'],
            'form_close_at' => ['nullable', 'date', 'after:form_open_at'],
        ]);

        $event = Event::create($data);

        return redirect()->route('events.show', $event)->with('success', 'Sự kiện đã được tạo!');
    }

    public function join(Request $request, Event $event)
    {
        $user   = $request->user();
        $status = $request->input('status', 'going');

        $event->participants()->syncWithoutDetaching([
            $user->id => ['status' => $status],
        ]);

        return back()->with('success', 'Đã đăng ký tham gia sự kiện!');
    }
}