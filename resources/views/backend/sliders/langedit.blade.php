@extends('backend.layouts.app')

@php
    CoreComponentRepository::instantiateShopRepository();
    CoreComponentRepository::initializeCache();
@endphp

@section('content')
    <div class="aiz-titlebar text-left mt-2 mb-3">
        <h5 class="mb-0 h6">{{ translate('Slider Information') }}</h5>
        <h5 class="mb-0 h6 text-center">{{ translate('Languaage Edit page') }}</h5>
    </div>

    <div class="col-lg-8 mx-auto">
        <div class="card">
            <div class="card-body p-0">
                <ul class="nav nav-tabs nav-fill border-light">
                    @foreach (\App\Models\Language::all() as $key => $language)
                        <li class="nav-item">
                            <a class="nav-link text-reset @if ($language->code == $lang) active @else bg-soft-dark border-light border-left-0 @endif py-3" href="{{ route('sliders.langedit', ['id'=>$slider->id, 'lang'=> $language->code] ) }}">
                                <img src="{{ static_asset('assets/img/flags/'.$language->code.'.png') }}" height="11" class="mr-1">
                                <span>{{$language->name}}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>

                <form class="p-4" action="{{ route('sliders.langupdate', $slider->id) }}" method="POST"
                      enctype="multipart/form-data">
                    <input name="_method" type="hidden" value="PATCH">
                    <input type="hidden" name="lang" value="{{ $lang }}">
                    @csrf

                    <div class="form-group row">
                        <label class="col-sm-3 col-from-label" for="heading">{{ translate('Heading') }} <i
                                class="las la-language text-danger" title="{{ translate('Heading') }}"></i></label>
                        <div class="col-sm-9">
                            <input type="text" placeholder="{{ translate('heading') }}" id="heading" name="heading"
                                   value="{{ translate($slider->heading) }}" class="form-control" required>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 col-from-label" for="title">{{ translate('Title') }} <i
                                class="las la-language text-danger" title="{{ translate('Title') }}"></i></label>
                        <div class="col-sm-9">
                            <input type="text" placeholder="{{ translate('title') }}" id="title" name="title"
                                   value="{{ translate($slider->title) }}" class="form-control" required>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-3 col-form-label" for="signinSrEmail">{{ translate('Photo') }}
                            <small>({{ translate('120x80') }})</small></label>
                        <div class="col-md-9">
                            <div class="input-group" data-toggle="aizuploader" data-type="image">
                                <div class="input-group-prepend">
                                    <div class="input-group-text bg-soft-secondary font-weight-medium">
                                        {{ translate('Browse') }}</div>
                                </div>
                                <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                <input type="hidden" name="photo" value="{{ $slider->photo }}" class="selected-files">
                            </div>
                            <div class="file-preview box sm">
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 col-from-label" for="link">{{ translate('Link') }} <i
                                class="las la-language text-danger" title="{{ translate('Link') }}"></i></label>
                        <div class="col-sm-9">
                            <input type="text" placeholder="{{ translate('link') }}" id="link" name="link"
                                   value="{{ translate($slider->link) }}" class="form-control" required>
                        </div>
                    </div>

                    <div class="form-group mb-0 text-right">
                        <button type="submit" class="btn btn-primary">{{ translate('Update') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
