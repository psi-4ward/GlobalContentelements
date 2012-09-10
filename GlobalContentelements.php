<?php if (!defined('TL_ROOT')) die('You cannot access this file directly!');

/**
 * GlobalContentelements
 * Use contentelements in every extension
 *
 * @author Christoph Wiechert <wio@psitrax.de>
 * @copyright 4ward.media 2011
 * @licence LGPL
 */


/**
 * Helperclass to handle for the GlobalContentelements
 * @author Christoph Wiechert <wio@psitrax.de>
 */
class GlobalContentElements extends System
{

	/**
	 * Construct the class
	 */
	public function __construct()
	{
		parent::__construct();
		$this->import('Database');
	}


	/**
	 * Callback to save the modules name in the tl_content.do row
	 * if a entry gets copied
	 * @param int $insertID
	 * @return void
	 */
	public function copyCallback($insertID)
	{
		$this->Database->prepare("UPDATE tl_content SET do=? WHERE id=?")->execute($this->Input->get('do'),$insertID);
	}

	/**
	 * Callback to save the modules name in the tl_content.do row
	 * if a entry gets cutted
	 * @param DataContainer $dc
	 * @return void
	 */
	public function cutCallback($dc)
	{
		$this->Database->prepare("UPDATE tl_content SET do=? WHERE id=?")->execute($this->Input->get('do'),$dc->id);
	}


	/**
	 * Callback to save the modules name in the tl_content.do row
	 * also wors for the cut_callback
	 * @param DataContainer $dc
	 * @return void
	 */
	public function setDo($dc)
	{
		// Return if there is no active record (override all)
		if (!$dc->activeRecord)
		{
			return;
		}
		$this->Database->prepare("UPDATE tl_content SET do=? WHERE id=?")->execute($this->Input->get('do'),$dc->id);
	}


	/**
	 * Callback to preserve the tl_content entities which belongs
	 * to other modules
	 *
	 * @param string $strName
	 */
	public function loadDataContainer($strName)
	{
		if ($this->Input->get('act') == 'delete' ||
			$this->Input->get('act') == 'deleteAll')
		{
			// remove tl_content from ctable
			foreach ($GLOBALS['TL_DCA']['tl_article']['config']['ctable'] as $k => $v)
			{
				if ($v == 'tl_content')
				{
					unset($GLOBALS['TL_DCA']['tl_article']['config']['ctable'][$k]);
				}
			}
		}
	}


	/**
	 * Callback to preserve the tl_content entities which belongs
	 * to other modules
	 *
	 * @param string $strTable
	 * @param array $new_records
	 * @param string $ptable
	 * @param array $ctable
	 * @return bool
	 */
	public function reviseTable($strTable, $new_records, &$ptable, &$ctable)
	{
		$reload = false;

		// only for tl_content table or child-table
		if($strTable != 'tl_content' && is_array($ctable) && !in_array('tl_content',$ctable)) return $reload;

		// Delete all records of the current table that are not related to the parent table
		if (strlen($ptable))
		{
			$objStmt = $this->Database->execute
			("
				DELETE FROM " . $strTable . "
				WHERE
					NOT EXISTS (SELECT * FROM " . $ptable . " WHERE " . $strTable . ".pid = " . $ptable . ".id)"
					.(($strTable == 'tl_content') ? " AND do='".$this->Input->get('do')."'" : '' )
			);

			if ($objStmt->affectedRows > 0)
			{
				$reload = true;
			}
		}

		// Delete all records of the child table that are not related to the current table
		if (is_array($ctable) && count($ctable))
		{
			foreach ($ctable as $v)
			{
				if (strlen($v))
				{
					$objStmt = $this->Database->execute
					("
						DELETE FROM " . $v . "
						WHERE
							NOT EXISTS (SELECT * FROM " . $strTable . " WHERE " . $v . ".pid = " . $strTable . ".id)"
							.(($v == 'tl_content') ? " AND do='".$this->Input->get('do')."'" : '' )
					);
					if ($objStmt->affectedRows > 0)
					{
						$reload = true;
					}
				}
			}
		}

		// Set $ptable and $ctable to an empty string to prevent DC_Table::reviseTable() doing its deletion
		$ptable = $ctable = '';
		return $reload;
	}


	/**
	 * Handle database update
	 * its a callback, executed within install-tool
	 * @param array $arrData SQL-Statements
	 * @return array SQL-Statements
	 */
	public function sqlCompileCommands($arrData)
	{
		$objCnt = $this->Database->execute('SELECT count(*) as anz FROM tl_content WHERE do=""');
		if($objCnt->anz > 0)
		{
			$arrData['ALTER_CHANGE'][] = 'UPDATE `tl_content` SET `do`="article" WHERE `do`="";';
		}
		return $arrData;
	}


	/**
	 * Delete child-records from tl_content
	 *
	 * @param DataContainer $dc
	 */
	public function deleteChildRecords(DataContainer $dc)
	{
		$objChilds = $this->Database->prepare('SELECT id FROM tl_content WHERE pid=? AND do=?')
									->execute($dc->id,$this->Input->get('do'));

		while($objChilds->next())
		{
			$this->Input->setGet('id',$objChilds->id);
			$dc = new DC_Table('tl_content');
			$dc->delete(true);
		}
		$this->Input->setGet('id',$dc->id);
	}

}
