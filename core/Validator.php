<<<<<<< HEAD
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

namespace Core;

/**
 * Description of Validator
 *
 * @author Weverton
 */
class Validator {

    public static function make(array $data, array $rules) {
        $errors = NULL;
        foreach ($rules as $ruleKey => $ruleValue) {

            foreach ($data as $dataKey => $dataValue) {
                if ($ruleKey == $dataKey) {
                    $itemsValue = [];
                    if (strpos($ruleValue, "|")) {
                        $itemsValue = explode('|', $ruleValue);

                        foreach ($itemsValue as $itemValue) {
                            $subItems = [];
                            if (strpos($itemValue, ":")) {
                                $subItems = explode(":", $itemValue);
                                switch ($subItems[0]) {
                                    case 'min':
                                        if (strlen($dataValue) < $subItems[1])
                                            $errors["$ruleKey"] = "o campo deve ter um minimo de {$subItems[1]} caracteres";
                                        break;
                                    case 'max':
                                        if (strlen($dataValue) > $subItems[1])
                                            $errors["$ruleKey"] = "o campo deve ter um máximo de {$subItems[1]} caracteres";
                                        break;
                                    case 'unique':
                                        $objModel = "\\App\\Models\\" . $subItems[1];
                                        $model = new $objModel;
                                        $find = $model->where($subItems[2], $dataValue)->first();
                                        if ($find->id) {
                                            if (isset($subItems[3]) && $find->id == $subItems[3]) {
                                                break;
                                            } else {
                                                $errors["$ruleKey"] = "{$subItems[2]} já esta cadastrado no sistema";
                                            }
                                        }
                                        break;
                                }
                            } else {
                                switch ($itemValue) {
                                    case 'required':
                                        if ($dataValue == '' || empty($dataValue))
                                            $errors["$ruleKey"] = " O campo {$ruleKey} deve ser preenchido";
                                        break;
                                    case 'email':
                                        if (!filter_var($dataValue, FILTER_VALIDATE_EMAIL))
                                            $errors["$ruleKey"] = " O campo {$ruleKey} não contem dados válidos";
                                        break;
                                    case 'float':
                                        if (!filter_var($dataValue, FILTER_VALIDATE_FLOAT))
                                            $errors["$ruleKey"] = " O campo {$ruleKey} deve conter um numero decimal";
                                        break;
                                    case 'int':
                                        if (!filter_var($dataValue, FILTER_VALIDATE_INT))
                                            $errors["$ruleKey"] = " O campo {$ruleKey} deve conter um numero inteiro";
                                        break;
                                    default :
                                        break;
                                }
                            }
                        }
                    } elseif (strpos($ruleValue, ":")) {
                        $items = explode(":", $ruleValue);

                        switch ($items[0]) {
                            case 'min':
                                if (strlen($dataValue) < $items[1])
                                    $errors["$ruleKey"] = "o campo deve ter um minimo de {$items[1]} caracteres";
                                break;
                            case 'max':
                                if (strlen($dataValue) > $items[1])
                                    $errors["$ruleKey"] = "o campo deve ter um máximo de {$items[1]} caracteres";
                                break;
                            case 'unique':
                                $objModel = "\\App\\Models\\" . $items[1];
                                $model = new $objModel;
                                $find = $model->where($items[2], $dataValue)->first();
                                if ($find->id) {
                                    if (isset($items[3]) && $find->id == $items[3]) {
                                        break;
                                    } else {
                                        $errors["$ruleKey"] = "{$items[2]} já esta cadastrado no sistema";
                                    }
                                }
                                break;
                        }
                    }
                } else {
                    switch ($ruleValue) {
                        case 'required':
                            if ($dataValue == '' || empty($dataValue))
                                $errors["$ruleKey"] = " O campo {$ruleKey} deve ser preenchido";
                            break;
                        case 'email':
                            if (!filter_var($dataValue, FILTER_VALIDATE_EMAIL))
                                $errors["$ruleKey"] = " O campo {$ruleKey} não contem dados válidos";
                            break;
                        case 'float':
                            if (!filter_var($dataValue, FILTER_VALIDATE_FLOAT))
                                $errors["$ruleKey"] = " O campo {$ruleKey} deve conter um numero decimal";
                            break;
                        case 'int':
                            if (!filter_var($dataValue, FILTER_VALIDATE_INT))
                                $errors["$ruleKey"] = " O campo {$ruleKey} deve conter um numero inteiro";
                            break;
                        default :
                            break;
                    }
                }
            }
        }
        if ($errors) {
            Session::set('errors', $errors);
            Session::set('inputs', $data);
            return TRUE;
        } else {
            Session::destroy(['errors', 'inputs']);
            return FALSE;
        }
    }

}
=======
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

namespace Core;

/**
 * Description of Validator
 *
 * @author Weverton
 */
class Validator {

    public static function make(array $data, array $rules) {
        $errors = NULL;
        foreach ($rules as $ruleKey => $ruleValue) {

            foreach ($data as $dataKey => $dataValue) {
                if ($ruleKey == $dataKey) {
                    $itemsValue = [];
                    if (strpos($ruleValue, "|")) {
                        $itemsValue = explode('|', $ruleValue);

                        foreach ($itemsValue as $itemValue) {
                            $subItems = [];
                            if (strpos($itemValue, ":")) {
                                $subItems = explode(":", $itemValue);
                                switch ($subItems[0]) {
                                    case 'min':
                                        if (strlen($dataValue) < $subItems[1])
                                            $errors["$ruleKey"] = "o campo deve ter um minimo de {$subItems[1]} caracteres";
                                        break;
                                    case 'max':
                                        if (strlen($dataValue) > $subItems[1])
                                            $errors["$ruleKey"] = "o campo deve ter um máximo de {$subItems[1]} caracteres";
                                        break;
                                    case 'unique':
                                        $objModel = "\\App\\Models\\" . $subItems[1];
                                        $model = new $objModel;
                                        $find = $model->where($subItems[2], $dataValue)->first();
                                        if ($find->id) {
                                            if (isset($subItems[3]) && $find->id == $subItems[3]) {
                                                break;
                                            } else {
                                                $errors["$ruleKey"] = "{$subItems[2]} já esta cadastrado no sistema";
                                            }
                                        }
                                        break;
                                }
                            } else {
                                switch ($itemValue) {
                                    case 'required':
                                        if ($dataValue == '' || empty($dataValue))
                                            $errors["$ruleKey"] = " O campo {$ruleKey} deve ser preenchido";
                                        break;
                                    case 'email':
                                        if (!filter_var($dataValue, FILTER_VALIDATE_EMAIL))
                                            $errors["$ruleKey"] = " O campo {$ruleKey} não contem dados válidos";
                                        break;
                                    case 'float':
                                        if (!filter_var($dataValue, FILTER_VALIDATE_FLOAT))
                                            $errors["$ruleKey"] = " O campo {$ruleKey} deve conter um numero decimal";
                                        break;
                                    case 'int':
                                        if (!filter_var($dataValue, FILTER_VALIDATE_INT))
                                            $errors["$ruleKey"] = " O campo {$ruleKey} deve conter um numero inteiro";
                                        break;
                                    default :
                                        break;
                                }
                            }
                        }
                    } elseif (strpos($ruleValue, ":")) {
                        $items = explode(":", $ruleValue);

                        switch ($items[0]) {
                            case 'min':
                                if (strlen($dataValue) < $items[1])
                                    $errors["$ruleKey"] = "o campo deve ter um minimo de {$items[1]} caracteres";
                                break;
                            case 'max':
                                if (strlen($dataValue) > $items[1])
                                    $errors["$ruleKey"] = "o campo deve ter um máximo de {$items[1]} caracteres";
                                break;
                            case 'unique':
                                $objModel = "\\App\\Models\\" . $items[1];
                                $model = new $objModel;
                                $find = $model->where($items[2], $dataValue)->first();
                                if ($find->id) {
                                    if (isset($items[3]) && $find->id == $items[3]) {
                                        break;
                                    } else {
                                        $errors["$ruleKey"] = "{$items[2]} já esta cadastrado no sistema";
                                    }
                                }
                                break;
                        }
                    }
                } else {
                    switch ($ruleValue) {
                        case 'required':
                            if ($dataValue == '' || empty($dataValue))
                                $errors["$ruleKey"] = " O campo {$ruleKey} deve ser preenchido";
                            break;
                        case 'email':
                            if (!filter_var($dataValue, FILTER_VALIDATE_EMAIL))
                                $errors["$ruleKey"] = " O campo {$ruleKey} não contem dados válidos";
                            break;
                        case 'float':
                            if (!filter_var($dataValue, FILTER_VALIDATE_FLOAT))
                                $errors["$ruleKey"] = " O campo {$ruleKey} deve conter um numero decimal";
                            break;
                        case 'int':
                            if (!filter_var($dataValue, FILTER_VALIDATE_INT))
                                $errors["$ruleKey"] = " O campo {$ruleKey} deve conter um numero inteiro";
                            break;
                        default :
                            break;
                    }
                }
            }
        }
        if ($errors) {
            Session::set('errors', $errors);
            Session::set('inputs', $data);
            return TRUE;
        } else {
            Session::destroy(['errors', 'inputs']);
            return FALSE;
        }
    }

}
>>>>>>> b0472aa6aa2ca3a79035f2b8ecfec1c3f4e267c0
