<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Define granular permissions by module
        $permissions = [
            'Staff' => [
                ['name' => 'View Staff', 'slug' => 'staff.view'],
                ['name' => 'View Staff Detail', 'slug' => 'staff.view.detail'],
                ['name' => 'Create Staff', 'slug' => 'staff.create'],
                ['name' => 'Edit Staff', 'slug' => 'staff.edit'],
                ['name' => 'Delete Staff', 'slug' => 'staff.delete'],
                ['name' => 'Manage Staff', 'slug' => 'staff.manage'],
            ],

            'Role' => [
                ['name' => 'View Role', 'slug' => 'role.view'],
                ['name' => 'View Role Detail', 'slug' => 'role.view.detail'],
                ['name' => 'Create Role', 'slug' => 'role.create'],
                ['name' => 'Edit Role', 'slug' => 'role.edit'],
                ['name' => 'Delete Role', 'slug' => 'role.delete'],
                ['name' => 'Manage Roles', 'slug' => 'roles.manage'],
            ],

            'Service' => [
                ['name' => 'View Service', 'slug' => 'service.view'],
                ['name' => 'View Service Detail', 'slug' => 'service.view.detail'],
                ['name' => 'Create Service', 'slug' => 'service.create'],
                ['name' => 'Edit Service', 'slug' => 'service.edit'],
                ['name' => 'Delete Service', 'slug' => 'service.delete'],

                ['name' => 'View Service Category', 'slug' => 'service.category.view'],
                ['name' => 'View Category Detail', 'slug' => 'service.category.view.detail'],
                ['name' => 'Create Service Category', 'slug' => 'service.category.create'],
                ['name' => 'Edit Service Category', 'slug' => 'service.category.edit'],
                ['name' => 'Delete Service Category', 'slug' => 'service.category.delete'],
            ],

            'Booking' => [
                ['name' => 'View Booking', 'slug' => 'booking.view'],
                ['name' => 'View Booking Detail', 'slug' => 'booking.view.detail'],
                ['name' => 'View Own Bookings', 'slug' => 'booking.view_own'],
                ['name' => 'Create Booking', 'slug' => 'booking.create'],
                ['name' => 'Edit Booking', 'slug' => 'booking.edit'],
                ['name' => 'Delete Booking', 'slug' => 'booking.delete'],
            ],

            'Client' => [
                ['name' => 'View Client', 'slug' => 'client.view'],
                ['name' => 'View Client Detail', 'slug' => 'client.view.detail'],
                ['name' => 'View Served Clients', 'slug' => 'client.view_served'],
                ['name' => 'Create Client', 'slug' => 'client.create'],
                ['name' => 'Edit Client', 'slug' => 'client.edit'],
                ['name' => 'Delete Client', 'slug' => 'client.delete'],

                // Client Notes
                ['name' => 'Create Own Note', 'slug' => 'notes.create_own'],
            ],

            'Location' => [
                ['name' => 'View Location', 'slug' => 'location.view'],
                ['name' => 'View Location Detail', 'slug' => 'location.view.detail'],
                ['name' => 'Create Location', 'slug' => 'location.create'],
                ['name' => 'Edit Location', 'slug' => 'location.edit'],
                ['name' => 'Delete Location', 'slug' => 'location.delete'],
            ],

            'Content' => [
                // Pages
                ['name' => 'View Pages', 'slug' => 'pages.view'],
                ['name' => 'View Page Detail', 'slug' => 'pages.view.detail'],
                ['name' => 'Create Pages', 'slug' => 'pages.create'],
                ['name' => 'Edit Pages', 'slug' => 'pages.edit'],
                ['name' => 'Delete Pages', 'slug' => 'pages.delete'],
                ['name' => 'Manage Content', 'slug' => 'content.manage'],

                // Reviews
                ['name' => 'View Reviews', 'slug' => 'reviews.view'],
                ['name' => 'View Review Detail', 'slug' => 'reviews.view.detail'],
                ['name' => 'Create Reviews', 'slug' => 'reviews.create'],
                ['name' => 'Edit Reviews', 'slug' => 'reviews.edit'],
                ['name' => 'Delete Reviews', 'slug' => 'reviews.delete'],

                // Messages
                ['name' => 'View Messages', 'slug' => 'messages.view'],
                ['name' => 'View Message Detail', 'slug' => 'messages.view.detail'],
                ['name' => 'Reply to Messages', 'slug' => 'messages.reply'],
                ['name' => 'Delete Messages', 'slug' => 'messages.delete'],

                // Articles (Blog)
                ['name' => 'View Articles', 'slug' => 'posts.view'],
                ['name' => 'View Article Detail', 'slug' => 'posts.view.detail'],
                ['name' => 'Create Articles', 'slug' => 'posts.create'],
                ['name' => 'Edit Articles', 'slug' => 'posts.edit'],
                ['name' => 'Delete Articles', 'slug' => 'posts.delete'],

                // Gallery
                ['name' => 'View Gallery', 'slug' => 'gallery.view'],
                ['name' => 'View Gallery Item Detail', 'slug' => 'gallery.view.detail'],
                ['name' => 'Create Gallery Items', 'slug' => 'gallery.create'],
                ['name' => 'Edit Gallery Items', 'slug' => 'gallery.edit'],
                ['name' => 'Delete Gallery Items', 'slug' => 'gallery.delete'],

                // FAQs
                ['name' => 'View FAQs', 'slug' => 'faqs.view'],
                ['name' => 'View FAQ Detail', 'slug' => 'faqs.view.detail'],
                ['name' => 'Create FAQs', 'slug' => 'faqs.create'],
                ['name' => 'Edit FAQs', 'slug' => 'faqs.edit'],
                ['name' => 'Delete FAQs', 'slug' => 'faqs.delete'],

                // Uploads
                ['name' => 'Upload Files', 'slug' => 'uploads.create'],
            ],

            'Settings' => [
                ['name' => 'View Settings', 'slug' => 'settings.view'],
                ['name' => 'Edit Settings', 'slug' => 'settings.edit'],
                ['name' => 'Manage Settings', 'slug' => 'settings.manage'],
            ],
        ];

        // 1. Create Permissions
        foreach ($permissions as $group => $perms) {
            foreach ($perms as $perm) {
                Permission::firstOrCreate(
                    ['slug' => $perm['slug']],
                    ['name' => $perm['name'], 'group' => $group]
                );
            }
        }

        // 2. Create Default Roles
        $owner = Role::firstOrCreate(['slug' => 'owner'], ['name' => 'Owner', 'description' => 'Super administrator with absolute access.']);
        $manager = Role::firstOrCreate(['slug' => 'manager'], ['name' => 'Manager', 'description' => 'Studio manager with staff and scheduling access.']);
        $staff = Role::firstOrCreate(['slug' => 'staff'], ['name' => 'Staff', 'description' => 'Standard staff member.']);

        // 3. Define the Permissions for Each Role

        // Owner gets everything assigned physically so the UI permission matrix populates correctly
        $ownerPermissions = Permission::all();

        // Manager gets everything EXCEPT Roles and Locations (which are typically Owner-only)
        $managerPermissions = Permission::whereNotIn('group', ['Role', 'Location'])
            ->whereNotIn('slug', ['booking.view_own', 'client.view_served'])
            ->get();

        // Staff gets restricted, service-provider-level permissions
        $staffPermissions = Permission::whereIn('slug', [
            'booking.view_own',
            'booking.create',
            'client.view_served',
            'notes.create_own',
            'service.view',
            'service.view.detail',
            'staff.view',
            'staff.view.detail',
        ])->get();

        // 4. Attach/Sync Permissions
        $owner->permissions()->syncWithoutDetaching($ownerPermissions);
        $manager->permissions()->syncWithoutDetaching($managerPermissions);
        $staff->permissions()->syncWithoutDetaching($staffPermissions);
    }
}
