<?php

namespace App\Http\Requests;

use App\Models\HousePhoto;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyHousePhotoRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('house_photo_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:house_photos,id',
        ];
    }
}
