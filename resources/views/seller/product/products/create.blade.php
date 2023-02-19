@extends('seller.layouts.app')

@section('panel_content')

<div class="aiz-titlebar mt-2 mb-4">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h1 class="h3">{{ translate('Add Your Product') }}</h1>
        </div>
    </div>
</div>

<form class="" action="{{route('seller.products.store')}}" method="POST" enctype="multipart/form-data" id="choice_form">
    <div class="row gutters-5">
        <div class="col-lg-8">
            @csrf
            <input type="hidden" name="added_by" value="seller">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 h6">{{translate('Product Information')}}</h5>
                </div>
                <div class="card-body">
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">{{translate('Product Name')}}</label>
                        <div class="col-md-8">
                            <input type="text" name="name"
                                placeholder="{{ translate('Product Name') }}" onchange="update_sku()" value="{{old('name')}}" class="form-control" required>
                                @if ($errors->has('name'))
                                    <span class="error text-danger">{{ $errors->first('name') }}</span>
                                @endif
                        </div>
                    </div>
                    <div class="form-group row" id="category">
                        <label class="col-md-3 col-from-label">{{translate('Category')}}</label>
                        <div class="col-md-8">
                            <select class="form-control aiz-selectpicker" name="category_ids[]" id="category_id" data-live-search="true" multiple onchange="getSellerAttribute()" required>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ translate($category->name) }}</option>
                                @foreach ($category->childrenCategories as $childCategory)
                                    @include('categories.child_category', ['child_category' => $childCategory])
                                @endforeach
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row" id="brand">
                        <label class="col-md-3 col-from-label">{{translate('Brand')}}</label>
                        <div class="col-md-8">
                            <select class="form-control aiz-selectpicker" name="brand_id" id="brand_id"
                                data-live-search="true" required>
                                <option value="">{{ translate('Select Brand') }}</option>
                                @foreach (\App\Models\Brand::where('verify', 1)->where('user_id' , Auth::id())->get() as $brand)
                                    <option value="{{ $brand->id }}">{{ translate($brand->name) }}</option>
                                @endforeach
                                @foreach (\App\Models\Brand::where('user_id' , 1)->get() as $brand)
                                    <option value="{{ $brand->id }}">{{ translate($brand->name) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">{{translate('Unit')}}</label>
                        <div class="col-md-8">
                            <input type="text" name="unit"
                                placeholder="{{ translate('Unit (e.g. KG, Pc etc)') }}" value="{{old('unit')}}" class="form-control" required>
                            @if ($errors->has('unit'))
                                    <span class="error text-danger">{{ $errors->first('unit') }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">{{translate('Weight')}} <small>({{ translate('In Kg') }})</small></label>
                        <div class="col-md-8">
                            <input type="number" name="weight" step="0.01" value="0.00" placeholder="0.00" value="{{old('weight')}}" class="form-control" required>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">{{translate('Dimension Of Each Package:')}} <small>({{ translate('H*W*L') }})</small></label>
                        <div class="col-md-8">
                            <input type="text" name="dimension_hwl" placeholder="H*W*L" value="{{old('dimension_hwl')}}" class="form-control" required>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">{{translate('Minimum Purchase Qty')}}</label>
                        <div class="col-md-8">
                            <input type="number" lang="en" class="form-control" name="min_qty" value="1" min="1" required>
                            @if ($errors->has('min_qty'))
                                    <span class="error text-danger">{{ $errors->first('min_qty') }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">{{translate('Tags')}}</label>
                        <div class="col-md-8">
                            <input type="text" class="form-control aiz-tag-input" name="tags[]"
                                placeholder="{{ translate('Type and hit enter to add a tag') }}">
                        </div>
                    </div>
                    @if (addon_is_activated('pos_system'))
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">{{translate('Barcode')}}</label>
                        <div class="col-md-8">
                            <input type="text" class="form-control" placeholder="{{ translate('Generate by Auto') }}" disabled>
                        </div>
                    </div>
                    @endif
                    @if (addon_is_activated('refund_request'))
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">{{translate('Refundable')}}</label>
                        <div class="col-md-8">
                            <label class="aiz-switch aiz-switch-success mb-0">
                                <input type="checkbox" name="refundable" checked value="1">
                                <span></span>
                            </label>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 h6">{{translate('Product Images')}}</h5>
                </div>
                <div class="card-body">
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label"
                            for="signinSrEmail">{{translate('Gallery Images')}}</label>
                        <div class="col-md-8">
                            <div class="input-group" data-toggle="aizuploader" data-type="image" data-multiple="true">
                                <div class="input-group-prepend">
                                    <div class="input-group-text bg-soft-secondary font-weight-medium">
                                        {{ translate('Browse')}}</div>
                                </div>
                                <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                <input type="hidden" name="photos" class="selected-files">
                            </div>

                            <div class="file-preview box sm">
                            </div>

                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label" for="signinSrEmail">{{translate('Thumbnail Image')}}
                            <small>(290x300)</small></label>
                        <div class="col-md-8">
                            <div class="input-group" data-toggle="aizuploader" data-type="image">
                                <div class="input-group-prepend">
                                    <div class="input-group-text bg-soft-secondary font-weight-medium">
                                        {{ translate('Browse')}}</div>
                                </div>
                                <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                <input type="hidden" name="thumbnail_img" class="selected-files">
                            </div>

                            <div class="file-preview box sm">
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 h6">{{translate('Product Videos')}}</h5>
                </div>
                <div class="card-body">
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">{{translate('Video Provider')}}</label>
                        <div class="col-md-8">
                            <select class="form-control aiz-selectpicker" name="video_provider" id="video_provider">
                                <option value="youtube">{{translate('Youtube')}}</option>
                                <option value="dailymotion">{{translate('Dailymotion')}}</option>
                                <option value="vimeo">{{translate('Vimeo')}}</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">{{translate('Video Link')}}</label>
                        <div class="col-md-8">
                            <input type="text" name="video_link"
                                placeholder="{{ translate('Video Link') }}" value="{{old('video_link')}}" class="form-control" >
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 h6">{{translate('Product Variation')}}</h5>
                </div>
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-md-3">
                            <input type="text" class="form-control" value="{{translate('Colors')}}" disabled>
                        </div>
                        <div class="col-md-8">
                            <select class="form-control aiz-selectpicker" data-live-search="true" name="colors[]"
                                data-selected-text-format="count" id="colors" multiple disabled>
                                @foreach (\App\Models\Color::orderBy('name', 'asc')->get() as $key => $color)
                                <option value="{{ $color->code }}"
                                    data-content="<span><span class='size-15px d-inline-block mr-2 rounded border' style='background:{{ $color->code }}'></span><span>{{ $color->name }}</span></span>">
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-1">
                            <label class="aiz-switch aiz-switch-success mb-0">
                                <input value="1" type="checkbox" name="colors_active">
                                <span></span>
                            </label>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-3">
                            <input type="text" class="form-control" value="{{translate('Attributes')}}" disabled>
                        </div>
                        <div class="col-md-8">
                            <select name="choice_attributes[]" id="choice_attributes"
                                class="form-control aiz-selectpicker" data-live-search="true"
                                data-selected-text-format="count" multiple
                                data-placeholder="{{ translate('Choose Attributes') }}"  onchange="getChoice()">

                            </select>
                        </div>
                    </div>


                    <div>
                        <p>{{ translate('Choose the attributes of this product and then input values of each attribute') }}
                        </p>
                        <br>
                    </div>
                    <div class="form-group row gutters-5">
                        <div class="col-md-3">
                            <input type="text" class="form-control" value="{{translate('Choice')}}" disabled>
                        </div>
                        <div class="col-md-8">
                            <select name="choice_options[]" id="choice_options" class="form-control aiz-selectpicker" data-live-search="true" multiple data-placeholder="{{ translate('Choose Value') }}" onchange="update_sku2()">
                            </select>
                        </div>
                    </div>


                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 h6">{{translate('Product price + stock')}}</h5>
                </div>
                <div class="card-body">
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">{{translate('Unit price')}}</label>
                        <div class="col-md-6">
                            <input type="number" lang="en" min="0" value="0" step="0.01"
                                placeholder="{{ translate('Unit price') }}" name="unit_price" class="form-control" required>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-3 control-label" for="start_date">{{translate('Discount Date Range')}}</label>
                        <div class="col-md-9">
                          <input type="text" class="form-control aiz-date-range" name="date_range" placeholder="{{translate('Select Date')}}" data-time-picker="true" data-format="DD-MM-Y HH:mm:ss" data-separator=" to " autocomplete="off">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">{{translate('Discount')}}</label>
                        <div class="col-md-6">
                            <input type="number" lang="en" min="0" value="0" step="0.01"
                                placeholder="{{ translate('Discount') }}" name="discount" class="form-control" required>

                            @if ($errors->has('discount'))
                                <span class="error text-danger">{{ $errors->first('discount') }}</span>
                            @endif

                        </div>
                        <div class="col-md-3">
                            <select class="form-control aiz-selectpicker" name="discount_type">
                                <option value="amount">{{translate('Flat')}}</option>
                                <option value="percent">{{translate('Percent')}}</option>
                            </select>
                        </div>
                    </div>

                    <div id="show-hide-div">
                        <div class="form-group row">
                            <label class="col-md-3 col-from-label">{{translate('Quantity')}}</label>
                            <div class="col-md-6">
                                <input type="number" lang="en" min="0" value="0" step="1"
                                    placeholder="{{ translate('Quantity') }}" name="current_stock" class="form-control" required>
                            @if ($errors->has('current_stock'))
                                    <span class="error text-danger">{{ $errors->first('current_stock') }}</span>
                            @endif
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">
                            {{translate('External link')}}
                        </label>
                        <div class="col-md-9">
                            <input type="text" placeholder="{{ translate('External link') }}" name="external_link" class="form-control" value="{{old('external_link')}}">
                            <small class="text-muted">{{translate('Leave it blank if you do not use external site link')}}</small>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">
                            {{translate('External link button text')}}
                        </label>
                        <div class="col-md-9">
                            <input type="text" placeholder="{{ translate('External link button text') }}" name="external_link_btn" class="form-control" value="{{old('external_link_btn')}}">
                            <small class="text-muted">{{translate('Leave it blank if you do not use external site link')}}</small>
                        </div>
                    </div>
                    <br>

                    <div class="sku_combination" id="sku_combination">
                    </div>
                    <div class="sku_combination" id="sku_combination2">
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 h6">{{translate('Product Description')}}</h5>
                </div>
                <div class="card-body">
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">{{translate('Description')}}</label>
                        <div class="col-md-8">
                            <textarea class="aiz-text-editor" name="description" required>{{old('description')}}</textarea>
                            <div class="file-preview box sm">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 h6">{{translate('PDF Specification')}}</h5>
                </div>
                <div class="card-body">
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label"
                            for="signinSrEmail">{{translate('PDF Specification')}}</label>
                        <div class="col-md-8">
                            <div class="input-group" data-toggle="aizuploader" data-type="document" data-multiple="true">
                                <div class="input-group-prepend">
                                    <div class="input-group-text bg-soft-secondary font-weight-medium">
                                        {{ translate('Browse')}}</div>
                                </div>
                                <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                <input type="hidden" name="pdf" class="selected-files">
                            </div>
                            <div class="file-preview box sm">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 h6">{{translate('SEO Meta Tags')}}</h5>
                </div>
                <div class="card-body">
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">{{translate('Meta Title')}}</label>
                        <div class="col-md-8">
                            <input type="text" name="meta_title"
                                placeholder="{{ translate('Meta Title') }}" value="{{old('meta_title')}}" class="form-control" >
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">{{translate('Description')}}</label>
                        <div class="col-md-8">
                            <textarea name="meta_description" rows="8" class="form-control" >{{old('meta_description')}}</textarea>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label" for="signinSrEmail">{{ translate('Meta Image') }}</label>
                        <div class="col-md-8">
                            <div class="input-group" data-toggle="aizuploader" data-type="image">
                                <div class="input-group-prepend">
                                    <div class="input-group-text bg-soft-secondary font-weight-medium">
                                        {{ translate('Browse')}}</div>
                                </div>
                                <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                <input type="hidden" name="meta_img" class="selected-files">
                            </div>
                            <div class="file-preview box sm">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 h6">
                        {{translate('Shipping Configuration')}}
                    </h5>
                </div>

                <div class="card-body">
                    @if (get_setting('shipping_type') == 'product_wise_shipping')
                    <div class="form-group row">
                        <label class="col-md-6 col-from-label">{{translate('Free Shipping')}}</label>
                        <div class="col-md-6">
                            <label class="aiz-switch aiz-switch-success mb-0">
                                <input type="radio" name="shipping_type" value="free" checked>
                                <span></span>
                            </label>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-6 col-from-label">{{translate('Flat Rate')}}</label>
                        <div class="col-md-6">
                            <label class="aiz-switch aiz-switch-success mb-0">
                                <input type="radio" name="shipping_type" value="flat_rate">
                                <span></span>
                            </label>
                        </div>
                    </div>

                    <div class="flat_rate_shipping_div" style="display: none">
                        <div class="form-group row">
                            <label class="col-md-6 col-from-label">{{translate('Shipping cost')}}</label>
                            <div class="col-md-6">
                                <input type="number" lang="en" min="0" value="0" step="0.01"
                                    placeholder="{{ translate('Shipping cost') }}" name="flat_shipping_cost"
                                    class="form-control" >
                            </div>
                        </div>
                    </div>

                    @else
                    <p>
                        {{ translate('Shipping configuration is maintained by Admin.') }}
                    </p>
                    @endif

                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 h6">
                        {{translate('Shipping From')}}
                    </h5>
                </div>
                <div class="card-body">
                    <div class="form-group row">
                        <label class="col-sm-2 col-from-label" for="type">
                            {{translate('Country')}}
                        </label>
                        <div class="col-sm-10">
                            <select class="form-control aiz-selectpicker" data-live-search="true" name="country_id" id="country_id" required>
                                <option value="">{{translate('Select Country')}}</option>
                                @foreach ($countries as $country)
                                    <option value="{{ $country->id }}">
                                        {{ $country->name }}
                                    </option>
                                @endforeach
                            </select>
                            @if ($errors->has('country_id'))
                                <span class="error text-danger">{{ $errors->first('country_id') }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-2">
                            <label>{{ translate('City')}}</label>
                        </div>
                        <div class="col-md-10">
                            <select class="form-control mb-3 aiz-selectpicker" data-live-search="true" name="state_id" required></select>
                            @if ($errors->has('state_id'))
                                <span class="error text-danger">{{ $errors->first('state_id') }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-2">
                            <label>{{ translate('District')}}</label>
                        </div>
                        <div class="col-md-10">
                            <select class="form-control mb-3 aiz-selectpicker" data-live-search="true" name="city_id" required></select>
                            @if ($errors->has('city_id'))
                                <span class="error text-danger">{{ $errors->first('city_id') }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 h6">
                        {{translate('Shipping To')}}
                    </h5>
                </div>
                <div class="card-body">
                    <div class="form-group row">
                        <label class="col-sm-2 col-from-label" for="type">
                            {{translate('Country')}}
                        </label>
                        <div class="col-sm-10">
                            <select class="form-control aiz-selectpicker" data-live-search="true" name="country_to_id" id="country_to_id" required>
                                <option value="">{{translate('Select Country')}}</option>
                                @foreach ($countries as $country)
                                    <option value="{{ $country->id }}">
                                        {{ $country->name }}
                                    </option>
                                @endforeach
                            </select>
                            @if ($errors->has('country_to_id'))
                                <span class="error text-danger">{{ $errors->first('country_to_id') }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-2">
                            <label>{{ translate('City')}}</label>
                        </div>
                        <div class="col-md-10">
                            <select class="form-control mb-3 aiz-selectpicker" data-live-search="true" name="state_to_id" required></select>
                            @if ($errors->has('state_to_id'))
                                <span class="error text-danger">{{ $errors->first('state_to_id') }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-2">
                            <label>{{ translate('District')}}</label>
                        </div>
                        <div class="col-md-10">
                            <select class="form-control mb-3 aiz-selectpicker" data-live-search="true" name="city_to_id[]" id="city_to_id" multiple required></select>
                            @if ($errors->has('city_to_id'))
                                <span class="error text-danger">{{ $errors->first('city_to_id') }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>


            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 h6">{{translate('Low Stock Quantity Warning')}}</h5>
                </div>
                <div class="card-body">
                    <div class="form-group mb-3">
                        <label for="name">
                            {{translate('Quantity')}}
                        </label>
                        <input type="number" name="low_stock_quantity" value="1" min="0" step="1" class="form-control" required>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 h6">
                        {{translate('Stock Visibility State')}}
                    </h5>
                </div>

                <div class="card-body">

                    <div class="form-group row">
                        <label class="col-md-6 col-from-label">{{translate('Show Stock Quantity')}}</label>
                        <div class="col-md-6">
                            <label class="aiz-switch aiz-switch-success mb-0">
                                <input type="radio" name="stock_visibility_state" value="quantity" checked>
                                <span></span>
                            </label>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-6 col-from-label">{{translate('Show Stock With Text Only')}}</label>
                        <div class="col-md-6">
                            <label class="aiz-switch aiz-switch-success mb-0">
                                <input type="radio" name="stock_visibility_state" value="text" value="{{old('stock_visibility_state')}}">
                                <span></span>
                            </label>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-6 col-from-label">{{translate('Hide Stock')}}</label>
                        <div class="col-md-6">
                            <label class="aiz-switch aiz-switch-success mb-0">
                                <input type="radio" name="stock_visibility_state" value="hide">
                                <span></span>
                            </label>
                        </div>
                    </div>

                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 h6">{{translate('Cash On Delivery')}}</h5>
                </div>
                <div class="card-body">
                    @if (get_setting('cash_payment') == '1')
                    <div class="form-group row">
                        <label class="col-md-6 col-from-label">{{translate('Status')}}</label>
                        <div class="col-md-6">
                            <label class="aiz-switch aiz-switch-success mb-0">
                                <input type="checkbox" name="cash_on_delivery" value="1" checked="">
                                <span></span>
                            </label>
                        </div>
                    </div>
                    @else
                    <p>
                        {{ translate('Cash On Delivery activation is maintained by Admin.') }}
                    </p>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 h6">{{translate('Estimate Shipping Time')}}</h5>
                </div>
                <div class="card-body">
                    <div class="form-group mb-3">
                        <label for="name">
                            {{translate('Shipping Days')}}
                        </label>
                        <div class="input-group">
                            <input type="number" class="form-control" name="est_shipping_days" min="1" step="1"
                                placeholder="{{translate('Shipping Days')}}" value="{{old('est_shipping_days')}}">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="inputGroupPrepend">{{translate('Days')}}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 h6">{{translate('VAT & Tax')}}</h5>
                </div>
                <div class="card-body">
                    @foreach(\App\Models\Tax::where('tax_status', 1)->get() as $tax)
                    <label for="name">
                        {{$tax->name}}
                        <input type="hidden" value="{{$tax->id}}" name="tax_id[]">
                    </label>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <input type="number" lang="en" min="0" value="0" step="0.01"
                                placeholder="{{ translate('Tax') }}" name="tax[]" class="form-control" >
                        </div>
                        <div class="form-group col-md-6">
                            <select class="form-control aiz-selectpicker" name="tax_type[]">
                                <option value="amount">{{translate('Flat')}}</option>
                                <option value="percent">{{translate('Percent')}}</option>
                            </select>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>




        {{-- customer choice options new --}}
{{-- 
        <div class="form-group row">
            <label class="col-md-12 col-from-label">
                {{translate('Choice Options')}}
            </label>
            <div class="col-md-12">
                <div class="qunatity-price">
                    <div class="form-group row">
                        <div class="col-md-5 col-from-label">
                            <label>{{ translate('Title : ') }}</label>
                            <input type="text" placeholder="{{ translate('Title') }}" name="product_title[]" class="form-control">
                        </div>
                        <div class="col-md-5 col-from-label">
                            <label>{{ translate('Type : ') }}</label>
                            <select class="form-control aiz-selectpicker" name="type_value[]" data-live-search="true">
                                <option selected>--Select Option--</option>
                                <option value="one">Text Input</option>
                                <option value="two">Two Option</option>
                                <option value="two">Two Option</option>
                                <option value="two">Two Option</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label><span class="text-white">.</span></label><br>
                            <button type="button" class="btn btn-soft-secondary btn-sm " data-toggle="add-more"
                                data-content='<div class="row gutters-5">
                                    <div class="col-md-5 col-from-label">
                                        <label>{{ translate('Title : ') }}</label>
                                        <input type="text" placeholder="{{ translate('Title') }}" name="product_title[]" class="form-control">
                                    </div>
                                    <div class="col-md-5 col-from-label">
                                        <label>{{ translate('Type : ') }}</label>
                                        <select class="form-control aiz-selectpicker" name="type_value[]" data-live-search="true">
                                            <option selected>--Select Option--</option>
                                            <option value="one">Text Input</option>
                                            <option value="two">Two Option</option>
                                            <option value="two">Two Option</option>
                                            <option value="two">Two Option</option>
                                        </select>
                                    </div>

                                    <div class="col-auto">
                                        <label><span class="text-white">.</span></label><br>
                                        <button type="button" class="mt-1 btn btn-icon btn-circle btn-sm btn-soft-success" data-toggle="add-details"  data-content="" data-target=".qunatity-price">
                                            +
                                        </button>
                                    </div>
                                    <div class="col-auto">
                                        <label><span class="text-white">.</span></label><br>
                                        <button type="button" class="mt-1 btn btn-icon btn-circle btn-sm btn-soft-danger" data-toggle="remove-parent" data-parent=".row">
                                            <i class="las la-times"></i>
                                        </button>
                                    </div>
                                </div>'
                                data-target=".qunatity-price"> {{ translate('Add More') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div> --}}

        <!-- More Choose Js Button  -->
          {{--   <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0 h6">Customer Choice Options</h5>
                        <div>
                            <input type="button" class="btn btn-danger btn-sm" id="show-form" value="Hide From">
                            <input type="button" class="btn btn-success btn-sm" id="hide-form" value="Show From">
                        </div>
                    </div>

                    <div id="hide-full-div" style="display:none">
                        <div class="col-lg-2" style="padding-top: 45px;">
                            <input type="button" class="btn btn-success btn-sm" id="add-row" value="Add Row">
                            <input type="button" class="btn btn-danger btn-sm" id="remove-row" value="Delete Row">
                        </div>

                        <div class="card-body">
                            <div id="row_field">

                            </div>
                        </div>
                    </div>
                </div>
            </div> --}}
         <!-- More Choose Js End -->


        <div class="col-12">
            <div class="mar-all text-right mb-2">
                <button type="submit" name="button" value="publish"
                    class="btn btn-primary">{{ translate('Upload Product') }}</button>
            </div>
        </div>
    </div>
</form>


@endsection

@section('script')
<script src="{{ static_asset('assets/js/aiz-core.js') }}" ></script>

<script type="text/javascript">
    $("[name=shipping_type]").on("change", function (){
            $(".product_wise_shipping_div").hide();
            $(".flat_rate_shipping_div").hide();
            if($(this).val() == 'product_wise'){
                $(".product_wise_shipping_div").show();
            }
            if($(this).val() == 'flat_rate'){
                $(".flat_rate_shipping_div").show();
            }

        });

        function add_more_customer_choice_option(i, name){
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type:"POST",
                    url:'{{ route('seller.products.add-more-choice-option') }}',
                    data:{
                    attribute_id: i
                    },
                    success: function(data) {
                        var obj = JSON.parse(data);
                        $('#customer_choice_options').append('\
                        <div class="form-group row">\
                            <div class="col-md-3">\
                                <input type="hidden" name="choice_no[]" value="'+i+'">\
                                <input type="text" class="form-control" name="choice[]" value="'+name+'" placeholder="{{ translate('Choice Title') }}" readonly>\
                            </div>\
                            <div class="col-md-8">\
                                <select class="form-control aiz-selectpicker attribute_choice" data-live-search="true" name="choice_options_'+ i +'[]" multiple>\
                                    '+obj+'\
                                </select>\
                            </div>\
                        </div>');
                        AIZ.plugins.bootstrapSelect('refresh');
                }
            });
        }

        $('input[name="colors_active"]').on('change', function() {
            if(!$('input[name="colors_active"]').is(':checked')){
                $('#colors').prop('disabled', true);
                AIZ.plugins.bootstrapSelect('refresh');
            }
            else{
                $('#colors').prop('disabled', false);
                AIZ.plugins.bootstrapSelect('refresh');
            }
            update_sku();
        });

        $(document).on("change", ".attribute_choice",function() {
            update_sku();
        });

        $('#colors').on('change', function() {
            update_sku();
        });

        $('input[name="unit_price"]').on('keyup', function() {
            update_sku();
        });

        $('input[name="name"]').on('keyup', function() {
            update_sku();
        });

        function delete_row(em){
            $(em).closest('.form-group row').remove();
            update_sku();
        }

        function delete_variant(em){
            $(em).closest('.variant').remove();
        }

        function update_sku(){
            $.ajax({
               type:"POST",
               url:'{{ route('seller.products.sku_combination') }}',
               data:$('#choice_form').serialize(),
               success: function(data){
                   $('#sku_combination').html(data);
                    AIZ.plugins.fooTable();
                   if (data.length > 1) {
                       $('#show-hide-div').hide();
                   }
                   else {
                        $('#show-hide-div').show();
                   }
               }
           });
        }

        $('#choice_attributes').on('change', function() {
            $('#customer_choice_options').html(null);
            $.each($("#choice_attributes option:selected"), function(){
                add_more_customer_choice_option($(this).val(), $(this).text());
            });
            update_sku();
        });

        // state dristric
        (function($) {
			"use strict";
            $(document).on('change', '[name=country_id]', function() {
                var country_id = $(this).val();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{route('get-state-frontend')}}",
                    type: 'POST',
                    data: {
                        country_id  : country_id
                    },
                    success: function (response) {
                        var obj = JSON.parse(response);
                        if(obj != '') {
                            $('[name="state_id"]').html(obj);
                            AIZ.plugins.bootstrapSelect('refresh');
                        }
                    }
                });
            });

            $(document).on('change', '[name=state_id]', function() {
                var state_id = $(this).val();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{route('get-city-frontend')}}",
                    type: 'POST',
                    data: {
                        state_id: state_id
                    },
                    success: function (response) {
                        var obj = JSON.parse(response);
                        if(obj != '') {
                            $('[name="city_id"]').html(obj);
                            AIZ.plugins.bootstrapSelect('refresh');
                        }
                    }
                });
            });
        })(jQuery);

        // Shipping to
        // state dristric
        (function($) {
			"use strict";
            $(document).on('change', '[name=country_to_id]', function() {
                var country_to_id = $(this).val();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{route('get-to-state-frontend')}}",
                    type: 'POST',
                    data: {
                        country_to_id  : country_to_id
                    },
                    success: function (response) {
                        var obj = JSON.parse(response);
                        if(obj != '') {
                            $('[name="state_to_id"]').html(obj);
                            AIZ.plugins.bootstrapSelect('refresh');
                        }
                    }
                });
            });

            $(document).on('change', '[name=state_to_id]', function() {
                var state_to_id = $(this).val();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{route('get-to-seller-city-frontend')}}",
                    type: 'POST',
                    data: {
                        state_to_id: state_to_id
                    },
                    success: function (response) {
                        var obj = JSON.parse(response);
                        if(obj != '') {
                            $('[name="city_to_id[]"]').html(obj);
                            AIZ.plugins.bootstrapSelect('refresh');
                        }
                    }
                });
            });
            
        })(jQuery);

</script>
@endsection

@section('kemetro_script')
<script>
        function getSellerAttribute(){
                var category_id=$("#category_id").val();
                $.ajax({
                        url: "{{ route('seller.getSellerAttributes') }}",
                        method: "GET",
                        data: {
                            "category_id": category_id
                        },
                        success: function(result) {
                            //alert(JSON.stringify(result));
                            $('#choice_attributes').html(result);
                            $('#colors').prop('disabled', false);
                                AIZ.plugins.bootstrapSelect('refresh');
                        },
                        error: function(response) {
                            //alert(JSON.stringify(response));
                        },
                        beforeSend: function() {
                            $('#loading').show();
                        },
                        complete: function() {
                            $('#loading').hide();
                        }
                    });
                }


        function getChoice(){
            var attribute_id=$("#choice_attributes").val();
            //alert(attribute_id);
            $.ajax({
                    url: "{{ route('seller.products.get-seller-choice-option') }}",
                    method: "get",
                    data: {
                        "attribute_id": attribute_id
                    },
                    success: function(result) {
                       //alert(JSON.stringify(result));
                        $('#choice_options').html(result);
                            AIZ.plugins.bootstrapSelect('refresh');
                    },
                    error: function(response) {
                        //alert(JSON.stringify(response));
                    },
                    beforeSend: function() {
                        $('#loading').show();
                    },
                    complete: function() {
                        $('#loading').hide();
                    }
                });
            }




        function update_sku2() {
            var choiceOptions=$('#choice_options').val();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{ route('seller.products.sku_combimnation2') }}",
                    method: "POST",
                    data:$('#choice_form').serialize(),
                    success: function(result) {
                        //alert(JSON.stringify(result));
                        $('#sku_combination2').html(result);
                        AIZ.uploader.previewGenerate();
                        AIZ.plugins.fooTable();
                        if (result.length > 1) {
                            $('#show-hide-div2').hide();
                        }
                        else {
                            $('#show-hide-div2').show();
                        }

                    },
                    error: function(response) {
                        //alert(JSON.stringify(response));
                    },
                    beforeSend: function() {
                        $('#loading').show();
                    },
                    complete: function() {
                        $('#loading').hide();
                    }
                });
        };

    $(document).ready(function(){
        // full div hide show
        $("#show-form").click(function(){
            $("#hide-full-div").hide();
        });
        $("#hide-form").click(function(){
            $("#hide-full-div").show();
        });

        var value = 1;
        // add new form row main work
        $("#add-row").click(function(){
            var markup = `
                <div class="add-new-form">
                    <div class="border p-2 my-2">
                        <div class="form-group row">
                            <div class="col-md-6 col-from-label">
                                <div class="row">
                                    <label class="col-md-1 col-from-label mt-2">{{ translate('Title : ') }}</label>
                                    <div class="col-md-11">
                                        <input type="text" placeholder="{{ translate('Title') }}" name="product_title[]" class="form-control">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="row">
                                    <label class="col-md-2 col-from-label mt-2">{{ translate('Type : ') }}</label>
                                    <div class="col-md-10">
                                        <select class="form-control aiz-selectpicker" name="type_value[]" data-live-search="true">
                                            <option selected>--Select Option--</option>
                                            <option value="one">Text Input</option>
                                            <option value="two">Two Option</option>
                                            <option value="two">Two Option</option>
                                            <option value="two">Two Option</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-4 col-from-label">
                                <div class="row">
                                    <label class="col-md-3 col-from-label mt-2">{{ translate('Name : ') }}</label>
                                    <div class="col-md-9">
                                        <input type="text" placeholder="{{ translate('Name') }}" name="product_name[]" class="form-control">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="row">
                                    <label class="col-md-3 col-from-label mt-2">{{ translate('Quantity : ') }}</label>
                                    <div class="col-md-9">
                                        <input type="number" placeholder="{{ translate('Quantity') }}" name="product_quantity[]" class="form-control">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-2 col-from-label">
                                <div class="row">
                                    <label class="col-md-3 col-from-label mt-2">{{ translate('Price : ') }}</label>
                                    <div class="col-md-9">
                                        <input type="number" placeholder="{{ translate('Price') }}" name="product_price[]" class="form-control">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="row">
                                    <div class="col-md-12">
                                        <select class="form-control aiz-selectpicker" name="product_stock[]" data-live-search="true">
                                                <option selected>--Select Option--</option>
                                                <option value="in_stock">In Stock</option>
                                                <option value="out_stock">Out Stock</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="row">
                                    <span class="btn btn-primary col-md-6" onclick="addRow(${value++})"></span>
                                    <span class="btn btn-secondary col-md-6">X</span>
                                </div>
                            </div>
                        </div>
                        <div id="new-row${value}"></div>
                        <input type="checkbox" name="record">
                    </div>
                </div>
        `;
            $("#row_field").append(markup);
        });

        // Find and remove selected table rows
        $("#remove-row").click(function(){
            $("#row_field").find('input[name="record"]').each(function(){
            	if($(this).is(":checked")){
                    $(this).parents(".add-new-form").remove();
                }
            });
        });
    });

      function addRow(i){
        $('#new-row').append('<div class="form-group row"><div class="col-md-4 col-from-label"><div class="row"><label class="col-md-3 col-from-label mt-2">Name</label><div class="col-md-9"><input type="text" placeholder="Name" name="product_name[]" class="form-control"></div> </div></div><div class="col-md-3"><div class="row"><label class="col-md-3 col-from-label mt-2">Quantity</label><div class="col-md-9"><input type="number" placeholder="Quantity" name="product_quantity[]" class="form-control"></div></div></div><div class="col-md-2 col-from-label"><div class="row"><label class="col-md-3 col-from-label mt-2">Price</label><div class="col-md-9"><input type="number" placeholder="Price" name="product_price[]" class="form-control"></div></div></div><div class="col-md-2"><div class="row"><div class="col-md-12"><select class="form-control aiz-selectpicker" name="product_stock[]" data-live-search="true"><option selected>--Select Option--</option><option value="in_stock">In Stock</option><option value="out_stock">Out Stock</option></select></div></div></div><div class="col-md-1"><div class="row"><span class="btn btn-primary col-md-6" onclick="addRow()">+</span><span class="btn btn-secondary col-md-6">X</span></div></div></div><div id="new-row"></div>');
                        AIZ.plugins.bootstrapSelect('refresh');
      }




</script>
@endsection