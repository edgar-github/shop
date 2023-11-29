<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Models\Accessor;
use App\Models\Authors;
use App\Models\Books;
use App\Models\Categories;
use App\Models\Images;
use App\Models\Translators;
use App\Traits\GeneralTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class BooksController extends Controller
{
    use GeneralTrait;

    /**
     * Display a listing of the resource.
     */
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $books = Books::orderBy('id', 'DESC')->get();
        return view('admin.book.index', compact('books'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $authors = Authors::all();
        $translators = Translators::all();
        $categories = Categories::where('type', Categories::TYPE_BOOK)->get();
        $accessors  = Accessor::where('status', Accessor::ACTIVE)->get();

        return view('admin.book.create', compact('authors', 'translators', 'categories', 'accessors'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBookRequest $request, Books $books): \Illuminate\Http\RedirectResponse
    {
        try {
            $bookImages = [];
            if ($request->hasFile('file')) {
                $imageName = self::imageUpload($request->file, Books::BOOK_IMAGE_PATH);
                $request->merge(['main_image' => $imageName]);

                if ($request->hasFile('images')) {
                    $filesName = self::imagesUpload($request->images, Books::BOOK_IMAGE_PATH);
                    $bookImages = self::changeArrayKeys('image', $filesName);
                }
            }

            $bookCreate = $books::create($request->all());
            $bookCreate->authors()->attach($request->authors);
            $bookCreate->translators()->attach($request->translators);
            $bookCreate->accessors()->attach($request->accessors);
            if (count($bookImages)) $bookCreate->images()->createMany($bookImages);

            DB::commit();

            return redirect()->route('books.create')->with('success', 'Book created successfully');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('books.create')->with('error', 'Something went wrong');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Books $books, $id): \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $authors = Authors::all();
        $translators = Translators::all();
        $categories = Categories::where('type', Categories::TYPE_BOOK)->get();
        $accessors  = Accessor::where('status', Accessor::ACTIVE)->get();
        $book = $books::with(['category', 'authors', 'translators', 'accessors'])
            ->with(['images' => function ($query) {
                $query->orderBy('images.order', 'ASC');
            }])
            ->findOrFail($id);
        $translatorsForSelected = self::filterData($book->translators);
        $authorsForSelected = self::filterData($book->authors);
        $accessorsForSelected = self::filterData($book->accessors);
        $imagesPathAndId = $this->getImagePathAndId($book->images);

        return view('admin.book.edit', compact('book', 'categories', 'authors', 'translators', 'accessors', 'translatorsForSelected', 'authorsForSelected', 'accessorsForSelected', 'imagesPathAndId'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBookRequest $request, Books $books): \Illuminate\Http\RedirectResponse
    {
        DB::beginTransaction();
        try {
            $bookImages = [];
            $book = $books::with('images')->findOrFail($request->id);

            if ($request->has('deleted_images_id')) {
                self::removeBookImage($book, explode(',', $request->deleted_images_id));
            }
            if ($request->hasFile('file')) {
                $imageName = self::imageUpload($request->file, Books::BOOK_IMAGE_PATH);
                $request->merge(['main_image' => $imageName]);
            }
            if ($request->hasFile('images')) {
                $filesName = self::imagesUpload($request->images, Books::BOOK_IMAGE_PATH);
                $bookImages = self::changeArrayKeys('image', $filesName);
            }

            $book->update($request->all());
            $book->authors()->sync($request->authors);
            $book->translators()->sync($request->translators);
            $book->accessors()->sync($request->accessors);
            if (count($bookImages)) $book->images()->createMany($bookImages);

            DB::commit();

            return redirect()->route('books.edit', $request->id)->with('success', 'Book updated successfully');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('books.edit', $request->id)->with('error', 'Something went wrong');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Books $books, $id): \Illuminate\Http\RedirectResponse
    {
        $model = $books::findOrFail($id);
        $model->delete();
        return redirect()->back()->with('success', 'Model deleted successfully.');
    }

    /**
     * @param $book
     * @param $ids
     * @return void
     */
    public static function removeBookImage($book, $ids): void
    {
        $images = $book->images()->whereIn('id', $ids)->get();
        foreach ($images as $image) {
            $path = storage_path('app/public/' . $image->image);
            $image->delete();
            if (File::exists($path)) {
                File::delete($path);
            }
        }
    }

    /**
     * @param Images $image
     * @param $id
     * @return true|void
     */
    public static function deleteBookImage(Images $image, $id)
    {
        try {
            $image = $image::findOrFail($id);
            $path = storage_path('app/public/' . $image->image);
            $image->delete();
            if (File::exists($path)) {
                File::delete($path);
            }
            return true;
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    /**
     * @param Request $request
     * @return true|void
     */
    public static function updateImagesOrder(Request $request)
    {
        try {
            $imageOrder = $request->input('order');
            foreach ($imageOrder as $position => $imageId) {
                Images::where('id', $imageId['key'])->update(['order' => $position]);
            }

            return true;
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

}
