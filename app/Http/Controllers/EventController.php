<?php

namespace App\Http\Controllers;

use App\Models\Community;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $query = Event::with('community');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%'.$request->search.'%')
                    ->orWhere('location', 'like', '%'.$request->search.'%');
            });
        }

        if ($request->filled('community_id')) {
            $query->where('community_id', $request->community_id);
        }

        if ($request->filled('price_type')) {
            if ($request->price_type === 'free') {
                $query->where('price', 0);
            } elseif ($request->price_type === 'paid') {
                $query->where('price', '>', 0);
            }
        }

        $events = $query->orderBy('event_date', 'asc')->paginate(9);
        $communities = Community::where('status', 'active')->get();

        return view('events.index', compact('events', 'communities'));
    }

    public function create()
    {
        $user = auth()->user();
        if ($user->isSuperAdmin()) {
            $communities = Community::where('status', 'active')->get();
        } else {
            $communities = Community::where('user_id', $user->id)->where('status', 'active')->get();
        }

        return view('events.create', compact('communities'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'community_id' => 'required|exists:communities,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'location' => 'required|string|max:255',
            'event_date' => 'required|date',
            'price' => 'required|integer|min:0',
            'admin_fee' => 'nullable|integer|min:0',
            'quota' => 'required|integer|min:1',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $bannerPath = null;
        if ($request->hasFile('banner')) {
            $bannerPath = $request->file('banner')->store('banners', 'public');
        }

        $slug = Str::slug($request->title).'-'.Str::random(5);

        Event::create([
            'community_id' => $request->community_id,
            'title' => $request->title,
            'slug' => $slug,
            'description' => $request->description,
            'location' => $request->location,
            'event_date' => $request->event_date,
            'price' => $request->price,
            'admin_fee' => $request->admin_fee ?? 3000,
            'quota' => $request->quota,
            'banner' => $bannerPath,
        ]);

        return redirect()->route('events.index')->with('success', 'Event berhasil dibuat!');
    }

    public function show($slug)
    {
        $event = Event::with('community.owner')->where('slug', $slug)->firstOrFail();
        $userTicket = null;
        if (auth()->check()) {
            $userTicket = auth()->user()->tickets()->where('event_id', $event->id)->latest()->first();
        }

        return view('events.show', compact('event', 'userTicket'));
    }
}
