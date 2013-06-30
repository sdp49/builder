<?php

abstract class PL_CRM_Base {

	abstract private function getAPIOptionKey ();

	public function getAPIkey () {
		$option_key = $this->getAPIOptionKey();
		return PL_Options::get($option_key, null);
	}

	public function setAPIkey ($api_key) {
		$option_key = $this->getAPIOptionKey();
		return PL_Options::set($option_key, $api_key);
	}

	abstract public function constructURL ($endpoint);

	abstract public function callAPI ($args);

	/*
	 * Contacts
	 */

	abstract public function getContacts ($filters);

	abstract public function createContact ($args);

	abstract public function updateContact ($contact_id, $args);

	abstract public function deleteContact ($contact_id);

	/*
	 * Tasks
	 */

	abstract public function getTasks ($filters);

	abstract public function createTask ($contact_id, $args);

	abstract public function updateTask ($task_id, $args);

	abstract public function deleteTask ($task_id);

	/*
	 * Notes
	 */

	abstract public function getNotes ($filters);

	abstract public function createNote ($contact_id, $args);

	abstract public function updateNote ($note_id, $args);

	abstract public function deleteNote ($note_id);

	/*
	 * Tags
	 */

	abstract public function getTags ($filters);

	abstract public function createTag ($contact_id, $args);

	abstract public function updateTag ($tag_id, $args);

	abstract public function deleteTag ($tag_id);

	/* 
	 * Groups/Buckets
	 */

	abstract public function getGroups ($filters);

	abstract public function createGroup ($args);

	abstract public function updateGroup ($group_id, $args);

	abstract public function deleteGroup ($group_id);

	/*
	 * Events
	 */
}

?>