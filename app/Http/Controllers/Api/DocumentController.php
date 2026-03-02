<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UploadDocumentRequest;
use App\Http\Resources\DocumentResource;
use App\Models\Document;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    public function upload(UploadDocumentRequest $request)
    {
        $file = $request->file('file');
        $path = $file->store('documents', 'public');

        $document = Document::create([
            'user_id' => $request->user()->user_id,
            'request_id' => $request->request_id,
            'title' => $request->title,
            'file_url' => $path,
            'file_type' => $request->file_type ?? $file->getClientOriginalExtension(),
            'is_active' => true,
        ]);

        return $this->success(new DocumentResource($document), 'تم رفع الملف بنجاح', 201);
    }
}
