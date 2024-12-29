<?php
namespace App\Http\Controllers\Admin;

use App\Enums\PostingStatus;
use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\AnnouncementCategory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnnouncementController extends Controller
{
    public function __construct(
        private readonly Announcement $announcement,
        private readonly AnnouncementCategory $announcementCategory
    ) {
    }

    /**
     * お知らせ一覧
     *
     * @param Request $request
     * @return inertia
     */
    public function index(Request $request)
    {
        $nowDate = now();
        $categoryList = AnnouncementCategory::all();

        $name = $request->input('name');
        $status = $request->enum('status', PostingStatus::class) ?? PostingStatus::STATUS_ALL;
        $announcement_category_id = $request->input('announcement_category_id');
        $sort_by = $request->input('sort_by', 'id');
        $desc = $request->boolean('desc');

        $announcements = Announcement::with(['announcementCategory'])
            ->when(filled($announcement_category_id), fn ($query) =>
                $query->where('announcement_category_id', $announcement_category_id)
            )
            ->when(filled($name), fn ($query) =>
                $query->where('name', 'LIKE', "%{$name}%")
            )
            ->when(filled($status), function ($query)  use ($status, $nowDate) {
                if ($status === PostingStatus::STATUS_IN_POSTING) {
                    $query->where('start_at', '<=', $nowDate)
                          ->where('end_at', '>=', $nowDate);
                }
                elseif ($status === PostingStatus::STATUS_POSTING_SCHEDULE) {
                    $query->where('start_at', '>', $nowDate);
                }
            })
            ->orderBy($sort_by, $desc ? 'desc' : 'asc')
            ->paginate(10);

        return inertia('Admin/Announcement/Index', compact(
            'categoryList',
            'announcements',
            'name',
            'status',
            'announcement_category_id',
            'sort_by',
            'desc',
        ));
    }

    /**
     * 通貨登録画面
     *
     * @param Request $request
     * @return inertia
     */
    public function create()
    {

        return inertia('Admin/Announcement/Edit');
    }

    /**
     * お知らせ登録処理
     *
     * @param Request $request
     * @return void
     */
    public function store()
    {

        return to_route('admin.announcement.index');
    }

    /**
     * お知らせ編集画面
     *
     * @param Request $request
     * @param Announcement $announcement
     * @return inertia
     */
    public function edit(Request $request, Announcement $announcement)
    {

        return inertia('Admin/Announcement/Edit', compact(
            'announcement'
        ));
    }

    /**
     * お知らせ更新処理
     *
     * @param Request $request
     * @param Announcement $announcement
     * @return void
     */
    public function update(Request $request, Announcement $announcement)
    {

        return to_route('admin.announcement.index');
    }
}
