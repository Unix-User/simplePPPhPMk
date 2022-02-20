<?php

/*
 * The MIT License
 *
 * Copyright 2020 Weverton.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace App\Controllers;

use App\Models\Category;
use App\Models\Product;
use Core\Auth;
use Core\BaseController;
use Core\Redirect;
use Core\Validator;

/**
 * Description of ProductController
 *
 * @author Weverton
 */
class ProductsController extends BaseController {

    private $post;

    public function __construct() {
        parent::__construct();
        $this->product = new Product;
    }

    public function index() {
        $this->setPageTitle("Produtos");
        $this->view->products = $this->product->All();
        return $this->renderView("products/index", 'layout');
    }

    public function show($id) {
        $this->view->product = $this->product->find($id);
        $this->setPageTitle("{$this->view->product->product}");
        return $this->renderView("products/show", "layout");
    }

    public function create() {
        $this->setPageTitle('Novo produto');
        $this->view->categories = Category::all();
        return $this->renderView("products/create", 'layout');
    }

    public function store($request) {
        $data = [
            'user_id' => Auth::id(),
            'product' => $request->post->product,
            'description' => $request->post->description
        ];
        if (Validator::make($data, $this->product->rules())) {
            return Redirect::route('/product/create');
        }
        try {
            $product = $this->product->create($data);
            if(isset($request->post->category_id)){
                $product->category()->attach($request->post->category_id);
            }
            return Redirect::route('/products', [
                        'success' => ['Dados inseridos com sucesso']
            ]);
        } catch (\Exception $ex) {
            return Redirect::route('/products', [
                        'errors' => [$ex->getMessage()]
            ]);
        }
    }

    public function edit($id) {
        $this->view->product = $this->product->find($id);
        $this->view->categories = Category::all();
        if (Auth::id() != $this->view->product->user->id) {
            return Redirect::route('/products', [
                        'errors' => ['Esses dados estão indisponiveis no momento.']
            ]);
        }
        $this->setPageTitle('Editar dados - ' . $this->view->product->product);
        return $this->renderView('products/edit', 'layout');
    }

    public function update($id, $request) {
        $data = [
            'product' => $request->post->product,
            'description' => $request->post->description
        ];

        if (Validator::make($data, $this->product->rules())) {
            return Redirect::route("/product/{$id}/edit");
        }

        try {
            $product = $this->product->find($id);
            $product->update($data);
            if(isset($request->post->category_id)){
                $product->category()->sync($request->post->category_id);
            }else{
                $product->category()->detach();
            }
            $this->product->find($id)->update($data);
            return Redirect::route('/products', [
                        'success' => ['Dados atualizados com sucesso']
            ]);
        } catch (\Exception $ex) {
            return Redirect::route('/products', [
                        'errors' => [$ex->getMessage()]
            ]);
        }
    }

    public function delete($id) {
        try {

            $product = $this->product->find($id);
            if (Auth::id() != $product->product->id) {
                return Redirect::route('/products', [
                            'errors' => ['Você não pode excluir post de outro autor.']
                ]);
            }
            $product->delete();
            return Redirect::route('/products', [
                        'success' => ['Post excluído com sucesso!']
            ]);
        } catch (\Exception $e) {
            return Redirect::route('/posts', [
                        'errors' => [$e->getMessage()]
            ]);
        }
    }

}
