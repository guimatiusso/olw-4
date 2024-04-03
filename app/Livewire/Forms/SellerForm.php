<?php

namespace App\Livewire\Forms;

use App\Enums\RoleEnum;
use App\Models\Seller;
use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Validate;
use Livewire\Form;

class SellerForm extends Form
{
    public Seller $seller;
    public $name = "";
    public $email = "";
    public $role_id = RoleEnum::MANAGER->value;
    public $password = "";
    public $password_confirmation = "";

    public function setSeller(Seller $seller)
    {
        $this->seller = $seller;
        if (!empty($seller->user->role_id)) {
            $this->name = $seller->name;
            $this->email = $seller->email;
            $this->role_id = $seller->user->role_id;
        }
    }

    public function save()
    {
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => $this->getEmailValidation(),
            'password' => ['required', 'confirmed', Password::defaults()],
            'role_id' => ['required'],
        ]);

        try {
            if (!empty($seller->user->role_id)) {
                $this->seller->user->update($this->only(['name', 'email', 'password', 'role_id']));
            } else {
                User::create($this->only(['name', 'email', 'password', 'role_id']))
                    ->seller()->create();
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', __("Problem creating or updating seller"));
        }

        return redirect()->route('sellers.index');
    }

    private function getEmailValidation(): array
    {
        if (!empty($this->seller->user->role_id)) {
            return ['required', 'email', 'max:255', Rule::unique('users')->ignore($this->seller->user_id)];
        }
        return ['required', 'email', 'max:255', 'unique:users'];
    }
}
