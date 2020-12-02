<?php

namespace App\Http\Controllers;

use App\Http\Controllers\PageControllers\PagesController;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Product::with('VatTariff', 'Category')->get();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $product = new Product();
        $products = $this->index();
        error_log('Hello');
        return view('pages.products.addProduct', compact('product', 'products'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {


        return Product::create($request->all());

    }

    /**
     * Implements the previous function on the frond end.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeFromForm(Request $request)
    {

        $validator = Validator::make($request->all(), ['name' => 'required|unique:products', 'description' => 'required',
            'vat_tariff_id' => 'required|digits_between:0,5000',
            'category_id' => 'required|digits_between:0,5000'],
            ['digits_between' => 'Please select :attribute']);

        if ($validator->fails()) {
            return redirect('createProductView')->withErrors($validator)->withInput();
        }
        $this->store($request);
        return redirect()->action([PagesController::class, 'productsView'])->with('success', 'Product successfully Created');
    }


    /**
     * Display the specified resource.
     *
     * @param \App\Models\Product $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $product = Product::with('VatTariff', 'Category')->whereId($id)->firstOrFail();

        return response($product, 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $product = Product::whereId($id)->firstOrfail();
        return view('pages.products.editProduct')->with('product', $product);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Product $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $product->update($request->all());

        return $product;
    }

    /**
     * Implements the previous function on the frond end.
     *
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateWithUi(Request $request, $id)
    {

        $product = Product::find($id);

        if ($product->name == $request->name) {
            $this->validate($request, ['name' => 'required']);

        } else {


            $this->validate($request, ['name' => 'required|unique:products']);
        }

        $validator = Validator::make($request->all(), ['name' => 'required',
            'vat_tariff_id' => 'required|digits_between:0,5000',
            'category_id' => 'required|digits_between:0,5000'],
            ['digits_between' => 'Please select :attribute']);

        if ($validator->fails()) {
            return Redirect::back()->withErrors($validator)->withInput();
        }


        $product->update($request->all());
        error_log('Hello');
        return redirect()->action([PagesController::class, 'productsView'])->with('success', 'Product successfully Updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     * @return int
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return 204;
    }


    /**
     * Implements the previous function on the frond end.
     *
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteFromUi($id)
    {
        $this->destroy($id);
        return redirect()->action([PagesController::class, 'productsView'])->with('success', 'Product successfully Deleted');

    }



}
