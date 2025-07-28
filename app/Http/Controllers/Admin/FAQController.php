<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FAQ;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FAQController extends Controller
{
    /**
     * Display the FAQ management page.
     */
    public function index()
    {
        $faqs = FAQ::active()->ordered()->get();

        return view('admin.settings.faqs.index', compact('faqs'));
    }

    /**
     * Store a new FAQ.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'title_ar' => 'nullable|string|max:255',
            'content' => 'required|string',
            'content_ar' => 'nullable|string',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'nullable',
            'is_expanded' => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $data = $request->all();
            $data['is_active'] = $request->has('is_active') && $request->input('is_active') === 'on';
            $data['is_expanded'] = $request->has('is_expanded') && $request->input('is_expanded') === 'on';

            $faq = FAQ::create($data);

            return response()->json([
                'success' => true,
                'message' => __('FAQ created successfully'),
                'faq' => $faq
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Error creating FAQ: ') . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update an FAQ.
     */
    public function update(Request $request, FAQ $faq)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'title_ar' => 'nullable|string|max:255',
            'content' => 'required|string',
            'content_ar' => 'nullable|string',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'nullable',
            'is_expanded' => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $data = $request->all();
            $data['is_active'] = $request->has('is_active') && $request->input('is_active') === 'on';
            $data['is_expanded'] = $request->has('is_expanded') && $request->input('is_expanded') === 'on';

            $faq->update($data);

            return response()->json([
                'success' => true,
                'message' => __('FAQ updated successfully'),
                'faq' => $faq
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Error updating FAQ: ') . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete an FAQ.
     */
    public function destroy(FAQ $faq)
    {
        try {
            $faq->delete();

            return response()->json([
                'success' => true,
                'message' => __('FAQ deleted successfully')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Error deleting FAQ: ') . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle FAQ status.
     */
    public function toggleStatus(FAQ $faq)
    {
        try {
            $faq->update(['is_active' => !$faq->is_active]);

            return response()->json([
                'success' => true,
                'message' => __('FAQ status updated successfully'),
                'is_active' => $faq->is_active
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Error updating FAQ status: ') . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle FAQ expanded state.
     */
    public function toggleExpanded(FAQ $faq)
    {
        try {
            $faq->update(['is_expanded' => !$faq->is_expanded]);

            return response()->json([
                'success' => true,
                'message' => __('FAQ expanded state updated successfully'),
                'is_expanded' => $faq->is_expanded
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Error updating FAQ expanded state: ') . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update FAQs order.
     */
    public function updateOrder(Request $request)
    {
        try {
            $request->validate([
                'orders' => 'required|array',
                'orders.*.id' => 'required|exists:f_a_q_s,id',
                'orders.*.order' => 'required|integer|min:0'
            ]);

            foreach ($request->orders as $item) {
                FAQ::where('id', $item['id'])->update(['order' => $item['order']]);
            }

            return response()->json([
                'success' => true,
                'message' => __('FAQs order updated successfully')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Error updating FAQs order: ') . $e->getMessage()
            ], 500);
        }
    }
}
