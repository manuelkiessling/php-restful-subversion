<?php

/**
 * Represents a path within a repository
 *
 * @package MergeHelper
 * @subpackage Repository
 * @uses MergeHelper_RepoPathInvalidPathException
 */
class MergeHelper_RepoPath extends MergeHelper_Base {

	/**
	 * Internal string representation of the path
	 */
	private $sPath = NULL;

	/**
	 * Creates the path object based on a given string
	 *
	 * @param string $sPath Path to create the object for
	 * @return void
	 * @throws MergeHelper_RepoPathInvalidPathException if the given string doesn't have the correct format
	 */
	public function __construct($sPath) {

		parent::__preConstruct();
		if (mb_substr($sPath, -1) === '/') throw new MergeHelper_RepoPathInvalidPathException();
		if ($sPath[0] !== '/') throw new MergeHelper_RepoPathInvalidPathException();
		if (mb_substr($sPath, -1) === '.') throw new MergeHelper_RepoPathInvalidPathException();
		if (mb_substr($sPath, -5) === '/.svn') throw new MergeHelper_RepoPathInvalidPathException();
		if (mb_strstr($sPath, '..')) throw new MergeHelper_RepoPathInvalidPathException();
		$this->sPath = $sPath;
		parent::__construct();

	}

	public function sGetAsString() {
		return $this->sPath;
	}
	
	public function __toString() {
		return $this->sGetAsString();
	}

}

/**
 * Indicates an invalid repository path
 *
 * @package MergeHelper
 * @subpackage Exception
 */
class MergeHelper_RepoPathInvalidPathException extends MergeHelper_Exception {};
