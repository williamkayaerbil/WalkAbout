<?php


class AccountAdminSeeder extends Seeder {

	public function run()
	{
		$admin = new Role;
		$admin->name = 'Account Admin';
		$admin->save();


		$accountAdmin=Role::find(4);
		$permissions=Permission::all();
		$id=array();
		foreach ($permissions as $key => $value) {
			$id[]=$value->id;
		}
		$accountAdmin->perms()->sync($id);
		$manageAccounts = new Permission;
		$manageAccounts->name = 'manage_accounts';
		$manageAccounts->display_name = 'Manage accounts';
		$manageAccounts->save();
		$admin=Role::find(1);
		$permissions=Permission::all();
		$id=array();
		foreach ($permissions as $key => $value) {
			$id[]=$value->id;
		}

		$admin->perms()->sync($id);
	}

}