<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCouponRequest;
use App\Models\Accessor;
use App\Models\Books;
use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $coupons = Coupon::get();

        return view('admin.coupon.index', compact('coupons'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $books = Books::where('status', Books::ACTIVE)->get();
        $accessors = Accessor::where('status', Books::ACTIVE)->get();

        if ($books->isEmpty()) {
            abort(404, 'There is no book to create coupon');
        }

        return view('admin.coupon.create', compact('books', 'accessors'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCouponRequest $request)
    {
        try {
            if(isset($request->all_products) && $request->all_products == Coupon::ALL_PRODUCTS) {
                $request->merge(['book_id' => Coupon::ALL_PRODUCTS, 'accessor_id' => Coupon::ALL_PRODUCTS]);
            } else {
                if(isset($request->book_id)) {
                    $request->merge(['book_id' => json_encode($request->book_id)]);
                }
                if(isset($request->accessor_id)) {
                    $request->merge(['accessor_id' => json_encode($request->accessor_id)]);
                }
            }

            $couponData = $request->except(['_token', '_method']);
            Coupon::create($couponData);
            return redirect()->back()->with('success', 'Coupon created successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Coupon not created');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $coupon = Coupon::findOrFail($id);
        $books = Books::where('status', Books::ACTIVE)->get();
        $accessors = Accessor::where('status', Accessor::ACTIVE)->get();
        $allProducts = false;

        if ($coupon->accessor_id !== Coupon::ALL_PRODUCTS && $coupon->book_id !== Coupon::ALL_PRODUCTS) {
            $booksForSelected = $coupon->book_id ? json_decode($coupon->book_id) : [];
            $accessorsForSelected = $coupon->accessor_id ? json_decode($coupon->accessor_id) : [];
        } else {
            $allProducts = true;
            $booksForSelected = [];
            $accessorsForSelected = [];
        }

        return view('admin.coupon.edit', compact('coupon', 'books', 'accessors', 'booksForSelected', 'accessorsForSelected', 'allProducts'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {

            $coupon = Coupon::findOrFail($id);

            if(isset($request->all_products) && $request->all_products == Coupon::ALL_PRODUCTS) {
                $request->merge(['book_id' => Coupon::ALL_PRODUCTS, 'accessor_id' => Coupon::ALL_PRODUCTS]);
            } else {
                if(isset($request->book_id)) {
                    $request->merge(['book_id' => json_encode($request->book_id)]);
                } else {
                    $request->merge(['book_id' => null]);
                }
                if(isset($request->accessor_id)) {
                    $request->merge(['accessor_id' => json_encode($request->accessor_id)]);
                } else {
                    $request->merge(['accessor_id' => null]);
                }
            }

            $couponData = $request->except(['_token', '_method']);
            $coupon->update($couponData);

            return redirect()->back()->with('success', 'Coupon updated successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Coupon not updated');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $coupon = Coupon::findOrFail($id);
            $coupon->delete();

            return redirect()->back()->with('success', 'Coupon deleted successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Coupon not deleted');
        }
    }
}
