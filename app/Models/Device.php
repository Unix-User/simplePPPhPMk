<?php

/*
 * The MIT License
 *
 * Copyright 2020 weverton.
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

namespace App\Models;

use \Core\BaseModelElloquent;

/**
 * Description of Product
 *
 * @author weverton
 */
class Device extends BaseModelElloquent {

    public $table = 'devices';
    public $timestamps = false;
    protected $fillable = ['user_id', 'name', 'address', 'user', 'password', 'ikev2'];

    public function rulesCreate() {
        return [
            'name' => 'normalize|required|min:2|max:20',
            'user' => 'normalize|required|min:2|max:20',
            'password' => 'required|min:2|max:20',
            'address' => 'ip|unique:Device:address'
        ];
    }
    
    public function rulesUpdate($id) {
        return [
            'name' => 'normalize|required|min:2|max:20',
            'user' => 'normalize|required|min:2|max:20',
            'password' => 'required|min:2|max:20',
            'address' => 'ip|unique:Device:address:'.$id
        ];
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function category() {
        return $this->belongsToMany(Category::class);
    }

}
