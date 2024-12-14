<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReviewControllerStoreRequest;
use App\Http\Requests\ReviewControllerUpdateRequest;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function index(Request $request): View
    {
        $reviews = Review::all();

        return view('review.index', compact('reviews'));
    }

    public function create(Request $request): Response
    {
        return view('review.create');
    }

    public function store(ReviewControllerStoreRequest $request): Response
    {
        $review = Review::create($request->validated());

        $request->session()->flash('review.id', $review->id);

        return redirect()->route('reviews.index');
    }

    public function show(Request $request, Review $review): Response
    {
        return view('review.show', compact('review'));
    }

    public function edit(Request $request, Review $review): Response
    {
        return view('review.edit', compact('review'));
    }

    public function update(ReviewControllerUpdateRequest $request, Review $review): Response
    {
        $review->update($request->validated());

        $request->session()->flash('review.id', $review->id);

        return redirect()->route('reviews.index');
    }

    public function destroy(Request $request, Review $review): Response
    {
        $review->delete();

        return redirect()->route('app.reviews.index');
    }
}
