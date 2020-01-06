<?php

class UsersTableSeeder extends Seeder {

	public function run()
	{

			$userAdmin=User::create([
				"email"=>'admin@gmail.com',
				"password"=>"123",
				"name"=>"Admin",
				"group_id"=>Group::first()->id
			]);

			$userMapAdmin=User::create([
				"email"=>'map_admin@gmail.com',
				"password"=>"123",
				"name"=>"Map Admin",
				"group_id"=>Group::first()->id
			]);

			$userGroupAdmin=User::create([
				"email"=>'group_admin@gmail.com',
				"password"=>"123",
				"name"=>"Group Admin",
				"group_id"=>Group::first()->id
			]);
			$admin = new Role;
			$admin->name = 'Admin';
			$admin->save();

			$mapAdmin = new Role;
			$mapAdmin->name = 'Map Admin';
			$mapAdmin->save();

			$groupAdmin = new Role;
			$groupAdmin->name = 'Group Admin';
			$groupAdmin->save();

			$userAdmin->attachRole( $admin );
			$userMapAdmin->attachRole($mapAdmin);
			$userGroupAdmin->attachRole($groupAdmin);

			$manageUsers = new Permission;
			$manageUsers->name = 'manage_users';
			$manageUsers->display_name = 'Manage Users';
			$manageUsers->save();

			$manageGroups = new Permission;
			$manageGroups->name = 'manage_groups';
			$manageGroups->display_name = 'Manage Groups';
			$manageGroups->save();

			$manageCategories = new Permission;
			$manageCategories->name = 'manage_categories';
			$manageCategories->display_name = 'Manage Categories';
			$manageCategories->save();

			$manageMaps = new Permission;
			$manageMaps->name = 'manage_maps';
			$manageMaps->display_name = 'Manage Maps';
			$manageMaps->save();

			$editMaps = new Permission;
			$editMaps->name = 'edit_maps';
			$editMaps->display_name = 'Edit Maps';
			$editMaps->save();

			
			$admin->perms()->sync(array($manageUsers->id,$manageGroups->id,$manageCategories->id,$manageMaps->id,$editMaps->id));
			$groupAdmin->perms()->sync(array($manageCategories->id,$manageMaps->id,$editMaps->id));
			$mapAdmin->perms()->sync(array($editMaps->id));
	}

}