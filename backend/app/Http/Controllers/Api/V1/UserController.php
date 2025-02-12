<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Filters\V1\AuthorFilter;
use App\Http\Requests\Api\V1\ReplaceUserRequest;
use App\Http\Requests\Api\V1\StoreUserRequest;
use App\Http\Requests\Api\V1\UpdateUserRequest;
use App\Http\Resources\V1\UserResource;
use App\Models\User;
use App\Policies\v1\UserPolicy;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserController extends ApiController
{
    protected $policyClass = UserPolicy::class;

    public function index(AuthorFilter $filters)
    {
        return UserResource::collection(
            User::filter($filters)->paginate()
        );
    }

    public function store(StoreUserRequest $request)
    {
        if ($this->isAble('store', User::class)) {
            return new UserResource(User::create($request->mappedAttributes()));
        }

        return $this->error('You are not authorized to create that resource', 403);
    }

    public function show(User $user)
    {
        if ($this->include('tickets')) {
            return new UserResource($user->load('tickets'));
        }
        return new UserResource($user);
    }

    public function update(UpdateUserRequest $request, $user_id)
    {
        try {
            $user = User::findOrFail($user_id);

            if ($this->isAble('update', $user)) {
                $user->update($request->mappedAttributes());
                return new UserResource($user);
            }

            return $this->error('You are not authorized to update that resource', 403);

        } catch (ModelNotFoundException) {
            return $this->error('User cannot be found.', 404);
        }
    }

    public function replace(ReplaceUserRequest $request, $user_id)
    {
        try {
            $user = User::findOrFail($user_id);

            if ($this->isAble('replace', $user)) {
                $user->update($request->mappedAttributes());
                return new UserResource($user);
            }

            return $this->error('You are not authorized to update that resource', 403);

        } catch (ModelNotFoundException) {
            return $this->error('User cannot be found.', 404);
        }
    }

    public function destroy($user_id)
    {
        try {
            $user = User::findOrFail($user_id);

            if ($this->isAble('delete', $user)) {
                $user->delete();
                return $this->ok('User successfully deleted');
            }

            return $this->error('You are not authorized to delete that resource', 403);

        } catch (ModelNotFoundException) {
            return $this->error('User cannot be found.', 404);
        }
    }
}
