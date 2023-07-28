<?php
/**
 * @package    GetBible
 *
 * @created    30th May, 2023
 * @author     Llewellyn van der Merwe <https://dev.vdm.io>
 * @git        GetBible <https://git.vdm.dev/getBible>
 * @copyright  Copyright (C) 2015 Vast Development Method. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace VDM\Joomla\GetBible;


use Joomla\CMS\User\UserHelper;
use Joomla\CMS\Language\Text;
use VDM\Joomla\GetBible\Database\Load;
use VDM\Joomla\GetBible\Database\Insert;
use VDM\Joomla\GetBible\Database\Update;
use VDM\Joomla\GetBible\Utilities\SessionHelper as Session;
use VDM\Joomla\Utilities\GuidHelper;


/**
 * The GetBible Linker
 * 
 * @since 2.0.1
 */
final class Linker
{
	/**
	 * The Load class
	 *
	 * @var    Load
	 * @since  2.0.1
	 */
	protected Load $load;

	/**
	 * The Insert class
	 *
	 * @var    Insert
	 * @since  2.0.1
	 */
	protected Insert $insert;

	/**
	 * The Update class
	 *
	 * @var    Update
	 * @since  2.0.1
	 */
	protected Update $update;

	/**
	 * The Session class
	 *
	 * @var    Session
	 * @since  2.0.1
	 */
	protected Session $session;

	/**
	 * The Share His Word Trigger
	 *
	 * @var    bool
	 * @since  2.0.1
	 */
	protected bool $trigger = false;

	/**
	 * Constructor
	 *
	 * @param Load         $load         The load object.
	 * @param Insert       $insert       The insert object.
	 * @param Update       $update       The update object.
	 * @param Session      $session      The session object.
	 *
	 * @since 2.0.1
	 */
	public function __construct(
		Load $load,
		Insert $insert,
		Update $update,
		Session $session)
	{
		$this->load = $load;
		$this->insert = $insert;
		$this->update = $update;
		$this->session = $session;
	}

	/**
	 * The Share His Word Trigger
	 *
	 * @return  bool  True on success
	 * @since 2.0.1
	 **/
	public function share(): bool
	{
		return $this->trigger;
	}

	/**
	 * Get Linker that has Access
	 *
	 * @return  string|null Linker GUID that has access
	 * @since 2.0.1
	 **/
	public function get(): ?string
	{
		if (($linker = $this->session->get('getbible_active_linker_guid', null)) === null)
		{
			return null;
		}

		if (($access = $this->session->get("getbible_active_{$linker}", null)) === null
			|| $access !== 'valid_access')
		{
			return null;
		}

		return $linker;
	}

	/**
	 * Get active Linker
	 *
	 * @param   bool   $setup    The setup switch
	 *
	 * @return  string|null Linker GUID that has access
	 * @since 2.0.1
	 **/
	public function active(bool $setup = true): ?string
	{
		if (($linker = $this->session->get('getbible_active_linker_guid', null)) !== null)
		{
			return $linker;
		}

		if ($setup)
		{
			$guid = (string) GuidHelper::get();
			$this->session->set('getbible_active_linker_guid', $guid);

			return $guid;
		}

		return null;
	}

	/**
	 * Get active Linker details
	 *
	 * @param   bool   $setup    The setup switch
	 *
	 * @return  array|null Linker details
	 * @since 2.0.1
	 **/
	public function activeDetails(bool $setup = true): ?array
	{
		if (($linker = $this->active($setup)) !== null)
		{
			// check if this is a valid linker (already set)
			if (($name = $this->load->value(
				['guid' => $linker, 'published' => 1],
				'name', 'linker'
			)) !== null)
			{
				return ['guid' => $linker, 'name' => $name, 'share' => $this->share()];
			}

			return ['guid' => $linker, 'name' => null, 'share' => null];
		}

		return null;
	}

	/**
	 * Check if a Linker is valid active linker
	 *
	 * @param   string     $linker    The linker GUID value
	 *
	 * @return  bool   True if active valid linker
	 * @since 2.0.1
	 **/
	public function valid(string $linker): bool
	{
		// is only valid if this linker has an active password
		if (GuidHelper::valid($linker) && ($password_name = $this->load->value(
				['linker' => $linker, 'published' => 1],
				'name', 'password'
			)) !== null)
		{
			return true;
		}

		return false;
	}

	/**
	 * The Share His Word Trigger
	 *
	 * @param   string     $linker    The linker GUID value
	 *
	 * @return  bool  True on success
	 * @since 2.0.1
	 **/
	public function trigger(string $linker): bool
	{
		if (!$this->set($linker))
		{
			return false;
		}

		$this->trigger = true;

		return true;
	}

	/**
	 * Set Active Linker
	 *
	 * @param   string     $linker    The linker GUID value
	 *
	 * @return  bool  True on success
	 * @since 2.0.1
	 **/
	public function set(string $linker): bool
	{
		if (!GuidHelper::valid($linker))
		{
			return false;
		}

		$this->session->set('getbible_active_linker_guid', $linker);

		return true;
	}

	/**
	 * Revoke Linker Session
	 *
	 * @param   string       $linker    The linker GUID value
	 *
	 * @return  array|null   The success or error details
	 * @since 2.0.1
	 **/
	public function revokeSession(string $linker): ?array
	{
		// linker not valid GUID
		if (!GuidHelper::valid($linker))
		{
			// hmm we can log this
			return [
				'error' => Text::_('COM_GETBIBLE_ACCESS_REVOKED')
			];
		}

		if (($access = $this->session->get('getbible_active_linker_guid', null)) === null
			|| $access !== $linker)
		{
			// hmm we can log this
			return [
				'success' => Text::_('COM_GETBIBLE_ACCESS_REVOKED')
			];
		}

		$this->session->set('getbible_active_linker_guid', null);

		return [
			'success' => Text::_('COM_GETBIBLE_ACCESS_REVOKED')
		];
	}

	/**
	 * Set a linker name
	 *
	 * @param   string  $name  The linker name
	 *
	 * @return  array|null   Array on success
	 * @since 2.0.1
	 **/
	public function setName(string $name): ?array
	{
		// make sure the linker has access
		if (($linker = $this->get()) === null)
		{
			return [
				'error' => Text::_("COM_GETBIBLE_WITHOUT_SELECTING_THE_CORRECT_FAVOURITE_VERSEBR_YOU_CANT_PERFORM_THE_INITIAL_ACTION"),
				'access_required' => true
			];
		}

		// set the name of this linker
		if (!$this->update->value($name, 'name', $linker, 'guid', 'linker'))
		{
			return [
				'error' => Text::_('COM_GETBIBLE_THE_NAME_COULD_NOT_BE_UPDATED')
			];
		}

		return [
			'guid' => $linker,
			'name' => $name,
			'success' => Text::_('COM_GETBIBLE_THE_NAME_HAS_BEEN_UPDATED')
		];
	}

	/**
	 * Set Access
	 *
	 * @param   string       $linker    The linker GUID value
	 * @param   string       $pass      The linker pass value
	 * @param   string|null  $oldPass   The linker old pass value
	 *
	 * @return  array|null   The success or error details
	 * @since 2.0.1
	 **/
	public function access(string $linker, string $pass, ?string $oldPass): ?array
	{
		// trim all empty space
		$pass = trim($pass);

		// password to short
		if (strlen($pass) <= 3)
		{
			return [
				'error' => Text::_('COM_GETBIBLE_PASSWORD_TO_SHORT_USE_A_LONGER_PASSWORD')
			];
		}
		// linker not valid GUID
		elseif (!GuidHelper::valid($linker))
		{
			return [
				'error' => Text::_('COM_GETBIBLE_INVALID_SESSION_KEY_VALUE')
			];
		}

		// get linker
		if (($_linker = $this->load->item(['guid' => $linker],'linker')) !== null)
		{
			// publish the linker if needed
			if ($_linker->published != 1 && $this->update->value(1, 'published', $_linker->id, 'id', 'linker'))
			{
				return [
					'error' => Text::_('COM_GETBIBLE_ACCESS_ALREADY_EXIST_BUT_COULD_NOT_BE_REACTIVATED')
				];
			}

			if (!empty($oldPass))
			{

				$oldPass = trim($oldPass);

				if (($guid = $this->getPassGuid($linker, $oldPass)) === null)
				{
					return [
						'error' => Text::_('COM_GETBIBLE_INCORRECT_FAVOURITE_VERSE_SELECTED')
					];
				}

				if (!$this->setPassword($linker, $pass))
				{
					return [
						'error' => Text::_('COM_GETBIBLE_FAVOURITE_VERSE_COULD_NOT_BE_CHANGED')
					];
				}

				// unpublished the old pass word only if new password was set
				$this->update->value(-2, 'published', $guid, 'guid', 'password');
			}
			elseif (!$this->hasAccess($linker, $pass))
			{
				return [
					'error' => Text::_('COM_GETBIBLE_INCORRECT_FAVOURITE_VERSE_SELECTED')
				];
			}
		}
		elseif (!$this->setLinker($linker))
		{
			return [
				'error' => Text::_('COM_GETBIBLE_SESSION_KEY_COULD_NOT_BE_STORED')
			];
		}
		elseif (!$this->setPassword($linker, $pass))
		{
			return [
				'error' => Text::_('COM_GETBIBLE_FAVOURITE_VERSE_COULD_NOT_BE_STORED')
			];
		}
		elseif (($_linker = $this->load->item(['guid' => $linker],'linker')) === null)
		{
			return null;
		}

		$_linker->published = 1;
		$_linker->success = Text::_('COM_GETBIBLE_FAVOURITE_VERSE_SUCCESSFULLY_SET');

		// add to session
		$this->session->set('getbible_active_linker_guid', $linker);
		$this->session->set("getbible_active_{$linker}", 'valid_access');

		return (array) $_linker;
	}

	/**
	 * Revoke Access
	 *
	 * @param   string       $linker    The linker GUID value
	 *
	 * @return  array|null   The success or error details
	 * @since 2.0.1
	 **/
	public function revoke(string $linker): ?array
	{
		// linker not valid GUID
		if (!GuidHelper::valid($linker))
		{
			// hmm we can log this
			return [
				'success' => Text::_('COM_GETBIBLE_ACCESS_REVOKED')
			];
		}

		if (($access = $this->session->get("getbible_active_{$linker}", null)) === null
			|| $access !== 'valid_access')
		{
			// hmm we can log this
			return [
				'success' => Text::_('COM_GETBIBLE_ACCESS_REVOKED')
			];
		}

		$this->session->set("getbible_active_{$linker}", null);

		return [
			'success' => Text::_('COM_GETBIBLE_ACCESS_REVOKED')
		];
	}

	/**
	 * Has Access
	 *
	 * @param   string       $linker    The linker GUID value
	 * @param   string       $pass      The linker pass value
	 *
	 * @return  bool True on success
	 * @since 2.0.1
	 **/
	private function hasAccess(string $linker, string $pass): bool
	{
		if (($password = $this->getPassword($linker, $pass)) !== null)
		{
			return true;
		}

		return false;
	}

	/**
	 * Get Password GUID
	 *
	 * @param   string       $linker    The linker GUID value
	 * @param   string       $pass      The linker pass value
	 *
	 * @return  string|null  The GUID on success
	 * @since 2.0.1
	 **/
	private function getPassGuid(string $linker, string $pass): ?string
	{
		if (($password = $this->getPassword($linker, $pass)) !== null)
		{
			return $password->guid;
		}

		return null;
	}

	/**
	 * Get Password
	 *
	 * @param   string       $linker    The linker GUID value
	 * @param   string       $pass      The linker pass value
	 *
	 * @return  object|null  The GUID on success
	 * @since 2.0.1
	 **/
	private function getPassword(string $linker, string $pass): ?object
	{
		if (strlen($pass) > 3 && ($passwords = $this->load->items(
				['linker' => $linker, 'published' => 1],
				'password'
			)) !== null)
		{
			foreach ($passwords as $password)
			{
				if (UserHelper::verifyPassword($pass, $password->password))
				{
					return $password;
				}
			}
		}

		return null;
	}

	/**
	 * Set Linker
	 *
	 * @param   string       $linker    The linker GUID value
	 *
	 * @return  bool   True on success
	 * @since 2.0.1
	 **/
	private function setLinker(string $linker): bool
	{
		return $this->insert->row([
			'name' => 'Default-Name',
			'guid' => $linker
		], 'linker');
	}

	/**
	 * Set Password
	 *
	 * @param   string       $linker    The linker GUID value
	 * @param   string       $pass      The linker pass value
	 *
	 * @return  bool   True on success
	 * @since 2.0.1
	 **/
	private function setPassword(string $linker, string $pass): bool
	{
		if (strlen($pass) <= 3)
		{
			return false;
		}

		$guid = (string) GuidHelper::get();
		while (!GuidHelper::valid($guid, 'password', 0, 'getbible'))
		{
			// must always be set
			$guid = (string) GuidHelper::get();
		}

		return $this->insert->row([
			'name' => 'Favourite-Verse',
			'linker' => $linker,
			'password' => UserHelper::hashPassword($pass),
			'guid' => $guid
		], 'password');
	}
}

