<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;

class RegisterResponse implements RegisterResponseContract
{
    /**
     * Create an HTTP response that represents the object.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function toResponse($request)
    {
        $message = 'Your information has been saved and is waiting for approval. You will receive an email with your temporary password.';

        return $request->wantsJson()
            ? new JsonResponse(['message' => $message], 201)
            : redirect()->route('home')->with('success', $message);
    }
}
