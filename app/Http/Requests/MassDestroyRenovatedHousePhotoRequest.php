<?php

namespace App\Http\Requests;

use App\Models\RenovatedHousePhoto;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyRenovatedHousePhotoRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('renovated_house_photo_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:renovated_house_photos,id',
        ];
    }
}
