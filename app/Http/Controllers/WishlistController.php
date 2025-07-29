<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\WishlistItem;
use App\Notifications\CourseAddedToWishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Display the user's wishlist
     */
    public function index()
    {
        $user = Auth::user();
        $wishlistItems = $user->wishlistItems()->with('course.category', 'course.instructor')->paginate(12);

        return view('wishlist.index', compact('wishlistItems'));
    }

    /**
     * Add a course to wishlist
     */
    public function add(Request $request, Course $course)
    {
        $user = Auth::user();

        // Check if already in wishlist
        if ($user->hasInWishlist($course)) {
            return response()->json([
                'success' => false,
                'message' => 'Course is already in your wishlist'
            ]);
        }

        // Add to wishlist
        WishlistItem::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
        ]);

        // Send notification
        $user->notify(new CourseAddedToWishlist($course));

        return response()->json([
            'success' => true,
            'message' => 'Course added to wishlist successfully'
        ]);
    }

    /**
     * Remove a course from wishlist
     */
    public function remove(Request $request, Course $course)
    {
        $user = Auth::user();

        // Remove from wishlist
        $user->wishlistItems()->where('course_id', $course->id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Course removed from wishlist successfully'
        ]);
    }

    /**
     * Toggle wishlist status
     */
    public function toggle(Request $request, Course $course)
    {
        $user = Auth::user();

        if ($user->hasInWishlist($course)) {
            // Remove from wishlist
            $user->wishlistItems()->where('course_id', $course->id)->delete();
            $message = 'Course removed from wishlist';
            $inWishlist = false;
        } else {
            // Add to wishlist
            WishlistItem::create([
                'user_id' => $user->id,
                'course_id' => $course->id,
            ]);

            // Send notification
            $user->notify(new CourseAddedToWishlist($course));

            $message = 'Course added to wishlist';
            $inWishlist = true;
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'inWishlist' => $inWishlist
        ]);
    }
}
