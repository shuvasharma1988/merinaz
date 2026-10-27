<?php

namespace App\Services\Backend;

use App\Events\Backend\ClientCreated;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ClientService
{
    public function createClient(array $data): User
    {
        return DB::transaction(function () use ($data) {
            // 1. Create base User
            $user = User::create([
                'name' => $data['first_name'].' '.$data['last_name'],
                'email' => $data['email'] ?? null,
                'password' => Hash::make(Str::random(16)), // Dummy password for CRM entry
            ]);

            // 2. Assign 'customer' role
            $customerRole = Role::where('slug', 'customer')->first();
            if ($customerRole) {
                $user->roles()->attach($customerRole->id);
            } else {
                // Optional: Throw an exception so you know the database is missing the role
                throw new \Exception("The 'customer' role does not exist in the database. Please seed your roles.");
            }

            // 3. Create Client Profile
            $user->clientProfile()->create([
                'date_of_birth' => $data['date_of_birth'] ?? null,
                'gender' => $data['gender'] ?? null,
                'phone' => $data['phone'],
                'source' => $data['source'] ?? 'admin',
                'status' => $data['status'] ?? 'active',
                'tag' => $data['tag'] ?? null,
                'has_medical_issue' => $data['has_medical_issue'] ?? false,
                'marketing_consent' => $data['marketing_consent'] ?? false,
                'sms_consent' => $data['sms_consent'] ?? false,
                'email_consent' => $data['email_consent'] ?? false,
            ]);

            ClientCreated::dispatch($user);

            return $user;
        });
    }

    public function updateClient(User $client, array $data): User
    {
        return DB::transaction(function () use ($client, $data) {
            $client->update([
                'name' => $data['first_name'].' '.$data['last_name'],
                'email' => $data['email'] ?? $client->email,
            ]);

            $client->clientProfile()->updateOrCreate(
                ['user_id' => $client->id],
                [
                    'date_of_birth' => $data['date_of_birth'] ?? null,
                    'gender' => $data['gender'] ?? null,
                    'phone' => $data['phone'],
                    'source' => $data['source'] ?? $client->clientProfile->source,
                    'status' => $data['status'] ?? 'active',
                    'tag' => $data['tag'] ?? null,
                    'has_medical_issue' => $data['has_medical_issue'] ?? false,
                    'marketing_consent' => $data['marketing_consent'] ?? false,
                    'sms_consent' => $data['sms_consent'] ?? false,
                    'email_consent' => $data['email_consent'] ?? false,
                ]
            );

            return $client->fresh('clientProfile');
        });
    }
}
